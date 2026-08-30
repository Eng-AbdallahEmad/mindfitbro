<?php

namespace App\Services;

use App\Exceptions\OrderNotApprovableException;
use App\Mail\OrderApprovedMail;
use App\Models\Coupon;
use App\Models\FamilyInvitation;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Extraction of SubscriptionsController::approve()'s transaction body
 * (audit :127-216). Zero knowledge of HTTP, sessions, auth guards, or
 * requests by design — must be callable identically from the admin
 * controller and the future Paymob webhook (Batch 6), which is why it sends
 * the confirmation mail itself rather than leaving that to each caller: both
 * paths must produce the exact same email.
 */
class OrderApprovalService
{
    public function approve(Subscription $subscription, ?int $reviewedBy = null): ApprovalResult
    {
        $result = DB::transaction(function () use ($subscription, $reviewedBy) {
            // Row lock FIRST, then re-check status. A check performed before
            // the lock is worthless — two concurrent callers could both pass
            // it before either commits (audit Risk D-2).
            $locked = Subscription::whereKey($subscription->getKey())->lockForUpdate()->firstOrFail();

            // Two valid "not yet approved" entry states, one per caller:
            // pending_review is the legacy manual-flow's awaiting-admin-
            // review status; awaiting_payment is the Paymob webhook's
            // (Batch 6) — the webhook has already set paid_at/
            // paymob_transaction_id on $locked before calling this, but the
            // status itself is still awaiting_payment at this point.
            if (!in_array($locked->status, [Subscription::STATUS_PENDING_REVIEW, Subscription::STATUS_AWAITING_PAYMENT], true)) {
                throw new OrderNotApprovableException($locked->id, $locked->status);
            }

            $locked->update([
                'status'      => Subscription::STATUS_APPROVED,
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => now(),
            ]);

            // Approval wins over expiry — mark the linked family invitation
            // redeemed from any status (pending, used, or even expired)
            // unless already redeemed.
            if ($locked->coupon_code) {
                $famCoupon = Coupon::where('code', $locked->coupon_code)->first();
                if ($famCoupon) {
                    FamilyInvitation::where('coupon_id', $famCoupon->id)
                        ->where('status', '!=', 'redeemed')
                        ->first()
                        ?->markRedeemed();
                }
            }

            $accountAutoCreated = false;
            $passwordSetUrl     = null;
            $customerName       = '';
            $customerEmail      = null;
            $isGuest            = false;

            if (is_null($locked->user_id) && $locked->guest_email) {
                $isGuest    = true;
                $guestEmail = $locked->guest_email;
                $guestName  = $locked->guest_name ?: 'العميل';

                $existingUser = User::where('email', $guestEmail)->first();

                if ($existingUser && !is_null($existingUser->profile_completed_at)) {
                    // ── Sub-case A: إيميل موجود وحساب مكتمل — ربط الاشتراك فقط ──
                    $locked->update([
                        'user_id'     => $existingUser->id,
                        'guest_name'  => null,
                        'guest_email' => null,
                        'guest_token' => null,
                    ]);
                    $customerName  = $existingUser->name;
                    $customerEmail = $existingUser->email;
                } else {
                    // ── Sub-case B: إيميل جديد ──────────────────────────────
                    // ── Sub-case A': إيميل موجود لكن profile_completed_at = null ─
                    if ($existingUser) {
                        $targetUser = $existingUser;
                        $locked->update([
                            'user_id'     => $targetUser->id,
                            'guest_name'  => null,
                            'guest_email' => null,
                            // guest_token: محفوظ — يُستخدم كمفتاح صفحة الإعداد
                        ]);
                    } else {
                        $targetUser = $this->createGuestUser($guestEmail, $guestName);

                        $locked->update([
                            'user_id'     => $targetUser->id,
                            'guest_name'  => null,
                            'guest_email' => null,
                        ]);
                    }

                    $passwordSetUrl     = route('setup-account.show', $locked->guest_token);
                    $accountAutoCreated = true;
                    $customerName       = $targetUser->name ?: $guestName;
                    $customerEmail      = $guestEmail;
                }
            } else {
                $locked->loadMissing('user');
                $customerName  = $locked->user?->name  ?: 'العميل';
                $customerEmail = $locked->user?->email ?: null;
            }

            return new ApprovalResult($accountAutoCreated, $passwordSetUrl, $customerName, $customerEmail, $isGuest);
        });

        // Sync the caller's instance with what the transaction actually
        // wrote (the transaction operated on a separately-locked copy).
        $subscription->refresh();

        Cache::forget('popular_plan_id');

        if ($result->customerEmail) {
            // Batch 6: deferred until AFTER the HTTP response is sent, via
            // Laravel's afterResponse() dispatch — NOT a real queue (decision
            // D6, no worker required), just Kernel::terminate() running this
            // closure inline once the response is on its way out. This is
            // what lets the Paymob webhook return its 200 immediately instead
            // of blocking on SMTP (verified empirically on this host — see
            // docs/paymob-migration-audit.md Batch 6 notes on flush()
            // fallback behavior under mod_php/apache2handler vs FastCGI).
            // A mail failure here can never affect the approval that already
            // committed above, nor the HTTP status already sent.
            dispatch(function () use ($subscription, $result) {
                try {
                    Mail::to($result->customerEmail)->send(
                        new OrderApprovedMail($subscription, $result->customerName, $result->accountAutoCreated, $result->passwordSetUrl, $result->isGuest)
                    );
                } catch (\Throwable $e) {
                    Log::error('OrderApprovedMail failed', ['sub' => $subscription->id, 'err' => $e->getMessage()]);
                }
            })->afterResponse();
        }

        return $result;
    }

    /**
     * users.email HAS a unique index (users_email_unique — confirmed via
     * SHOW CREATE TABLE users). That constraint is the race guard: on a
     * duplicate-key error from a concurrent request that won the insert,
     * re-fetch the row it created rather than adding a separate
     * application-level lock on User.
     */
    private function createGuestUser(string $guestEmail, string $guestName): User
    {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $guestEmail)[0])) ?: 'user';

        try {
            do {
                $username = $base . rand(1000, 9999);
            } while (User::where('username', $username)->exists());

            return User::create([
                'name'              => $guestName,
                'username'          => $username,
                'email'             => $guestEmail,
                'password'          => Hash::make(Str::random(32)),
                'role'              => 'user',
                'status'            => 'active',
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                // profile_completed_at: null — يكتمل في setup-account
            ]);
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1062) {
                return User::where('email', $guestEmail)->firstOrFail();
            }
            throw $e;
        }
    }
}

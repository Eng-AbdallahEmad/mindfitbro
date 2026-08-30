<?php

namespace App\Services;

use App\Exceptions\OrderNotRejectableException;
use App\Mail\OrderRejectedMail;
use App\Models\Coupon;
use App\Models\FamilyInvitation;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Mirror of OrderApprovalService for the reject path (audit :246-265).
 */
class OrderRejectionService
{
    public function reject(Subscription $subscription, string $rejectionReason, ?int $reviewedBy = null): RejectionResult
    {
        $result = DB::transaction(function () use ($subscription, $rejectionReason, $reviewedBy) {
            $locked = Subscription::whereKey($subscription->getKey())->lockForUpdate()->firstOrFail();

            if ($locked->status !== Subscription::STATUS_PENDING_REVIEW) {
                throw new OrderNotRejectableException($locked->id, $locked->status);
            }

            $locked->update([
                'status'           => Subscription::STATUS_REJECTED,
                'rejection_reason' => $rejectionReason,
                'reviewed_by'      => $reviewedBy,
                'reviewed_at'      => now(),
            ]);

            // Revert the family invitation from 'used' → 'pending' only for
            // THIS subscription's coupon — keeps the code alive for a retry.
            if ($locked->coupon_code) {
                $famCoupon = Coupon::where('code', $locked->coupon_code)->first();
                if ($famCoupon) {
                    FamilyInvitation::where('coupon_id', $famCoupon->id)
                        ->where('status', 'used')
                        ->first()
                        ?->update(['status' => 'pending']);
                }
            }

            $isGuest = is_null($locked->user_id);
            $locked->loadMissing('user');

            return new RejectionResult(
                customerName: $isGuest ? ($locked->guest_name ?: 'العميل') : ($locked->user?->name ?: 'العميل'),
                customerEmail: $isGuest ? $locked->guest_email : $locked->user?->email,
                isGuest: $isGuest,
            );
        });

        $subscription->refresh();

        if ($result->customerEmail) {
            try {
                Mail::to($result->customerEmail)->send(new OrderRejectedMail($subscription, $result->customerName));
            } catch (\Throwable $e) {
                Log::error('OrderRejectedMail failed', ['sub' => $subscription->id, 'err' => $e->getMessage()]);
            }
        }

        return $result;
    }
}

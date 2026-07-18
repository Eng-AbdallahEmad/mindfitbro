<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderApprovedMail;
use App\Mail\OrderRejectedMail;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubscriptionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'plan']);

        // ── Search ──────────────────────────────────────────────
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                })
                ->orWhere('guest_name', 'like', "%{$search}%")
                ->orWhere('guest_email', 'like', "%{$search}%")
                ->orWhere('coupon_code', 'like', "%{$search}%");
            });
        }

        // ── Filters ─────────────────────────────────────────────
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($plan = $request->input('plan')) {
            $query->where('plan_id', $plan);
        }

        if ($type = $request->input('type')) {
            if ($type === 'legacy') {
                $query->whereNull('duration_months');
            } else {
                $query->where('duration_months', (int) $type);
            }
        }

        if ($member = $request->input('member')) {
            if ($member === 'guest') {
                $query->whereNull('user_id');
            } else {
                $query->whereNotNull('user_id');
            }
        }

        $subscriptions = $query->latest()->paginate(15)->withQueryString();

        $plans = Plan::orderBy('sort_order')->get(['id', 'name']);

        $revenueRows = Subscription::whereIn('status', ['active', 'approved', 'expired'])
            ->selectRaw('currency, SUM(total) as total')
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        $stats = [
            'total'          => Subscription::count(),
            'active'         => Subscription::where('status', 'active')->count(),
            'pending_review' => Subscription::where('status', 'pending_review')->count(),
            'approved'       => Subscription::where('status', 'approved')->count(),
            'expired'        => Subscription::where('status', 'expired')->count(),
            'revenue_by_currency' => $revenueRows->pluck('total', 'currency')->toArray(),
        ];

        return view('app.admin.subscriptions.index', compact('subscriptions', 'plans', 'stats'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan', 'meetingBookings', 'reviewer']);

        return view('app.admin.subscriptions.show', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'status'          => 'required|in:pending_review,approved,active,expired,rejected,cancelled,waiting',
            'duration_months' => 'nullable|in:3,6',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
        ], [
            'status.required'         => 'الحالة مطلوبة',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
        ]);

        $subscription->update($request->only('status', 'duration_months', 'start_date', 'end_date'));

        return back()->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    // ── Phase B: Approve ────────────────────────────────────────
    public function approve(Request $request, Subscription $subscription)
    {
        abort_if($subscription->status !== Subscription::STATUS_PENDING_REVIEW, 422);

        $accountAutoCreated = false;
        $passwordSetUrl     = null;
        $customerName       = '';
        $customerEmail      = '';
        $isGuest            = false;

        DB::transaction(function () use ($subscription, &$accountAutoCreated, &$passwordSetUrl, &$customerName, &$customerEmail, &$isGuest) {
            $subscription->update([
                'status'      => Subscription::STATUS_APPROVED,
                'reviewed_by' => Auth::guard('admin')->id(),
                'reviewed_at' => now(),
            ]);

            if (is_null($subscription->user_id) && $subscription->guest_email) {
                $isGuest    = true;
                $guestEmail = $subscription->guest_email;
                $guestName  = $subscription->guest_name ?: 'العميل';

                $existingUser = User::where('email', $guestEmail)->first();

                if ($existingUser) {
                    $subscription->update([
                        'user_id'     => $existingUser->id,
                        'guest_name'  => null,
                        'guest_email' => null,
                        'guest_token' => null,
                    ]);
                    $customerName  = $existingUser->name;
                    $customerEmail = $existingUser->email;
                } else {
                    $base = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $guestEmail)[0])) ?: 'user';
                    do {
                        $username = $base . rand(1000, 9999);
                    } while (User::where('username', $username)->exists());

                    $newUser = User::create([
                        'name'              => $guestName,
                        'username'          => $username,
                        'email'             => $guestEmail,
                        'password'          => Hash::make(Str::random(32)),
                        'role'              => 'user',
                        'status'            => 'active',
                        'email_verified_at' => now(),
                        'terms_accepted_at' => now(),
                        // gender, phone: null — filled in complete-profile step
                        // profile_completed_at: null — triggers profile completion redirect
                    ]);

                    $subscription->update([
                        'user_id'     => $newUser->id,
                        'guest_name'  => null,
                        'guest_email' => null,
                        'guest_token' => null,
                    ]);

                    $token          = Password::createToken($newUser);
                    $passwordSetUrl = route('password.reset', ['token' => $token, 'email' => $newUser->email]);
                    $accountAutoCreated = true;
                    $customerName  = $guestName;
                    $customerEmail = $guestEmail;
                }
            } else {
                $subscription->load('user');
                $customerName  = $subscription->user?->name  ?: 'العميل';
                $customerEmail = $subscription->user?->email ?: null;
            }
        });

        if ($customerEmail) {
            try {
                Mail::to($customerEmail)->send(
                    new OrderApprovedMail($subscription, $customerName, $accountAutoCreated, $passwordSetUrl, $isGuest)
                );
            } catch (\Throwable $e) {
                Log::error('OrderApprovedMail failed', ['sub' => $subscription->id, 'err' => $e->getMessage()]);
            }
        }

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'تم الموافقة على الاشتراك وإرسال إيميل التأكيد للعميل');
    }

    // ── Phase B: Reject ──────────────────────────────────────────
    public function reject(Request $request, Subscription $subscription)
    {
        abort_if($subscription->status !== Subscription::STATUS_PENDING_REVIEW, 422);

        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:1000',
        ], [
            'rejection_reason.required' => 'سبب الرفض مطلوب',
            'rejection_reason.min'      => 'اكتب سبباً واضحاً (5 أحرف على الأقل)',
        ]);

        $subscription->update([
            'status'           => Subscription::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by'      => Auth::guard('admin')->id(),
            'reviewed_at'      => now(),
        ]);

        $isGuest      = is_null($subscription->user_id);
        $customerName = $isGuest ? ($subscription->guest_name  ?: 'العميل') : ($subscription->user?->name  ?: 'العميل');
        $customerEmail= $isGuest ? ($subscription->guest_email ?: null)     : ($subscription->user?->email ?: null);

        if ($customerEmail) {
            try {
                Mail::to($customerEmail)->send(new OrderRejectedMail($subscription, $customerName));
            } catch (\Throwable $e) {
                Log::error('OrderRejectedMail failed', ['sub' => $subscription->id, 'err' => $e->getMessage()]);
            }
        }

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'تم رفض الاشتراك وإرسال إيميل الإشعار للعميل');
    }

    // ── View Receipt (private file) ──────────────────────────────
    public function viewReceipt(Subscription $subscription)
    {
        abort_if(! $subscription->receipt_path, 404);
        abort_unless(Storage::disk('local')->exists($subscription->receipt_path), 404);

        $path     = Storage::disk('local')->path($subscription->receipt_path);
        $mimeType = mime_content_type($path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="receipt-' . $subscription->id . '"',
        ]);
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')->with('success', 'تم حذف الاشتراك بنجاح');
    }
}

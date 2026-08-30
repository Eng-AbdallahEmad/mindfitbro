<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\CouponExhaustedException;
use App\Exceptions\FxRateNotConfiguredException;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\FamilyInvitation;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\FxConverter;
use App\Services\Paymob\PaymobClient;
use App\Services\Web\CurrencyService;
use App\Services\Web\SeasonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function __construct(
        private CurrencyService $currency,
        private SeasonService   $seasonService,
        private PaymobClient    $paymobClient,
        private FxConverter     $fxConverter,
    ) {}

    // ── Step 1: Show purchase form ───────────────────────────────
    public function showForm(Plan $plan, Request $request)
    {
        abort_if(! $plan->is_active, 404);

        if (Auth::check() && Auth::user()->role !== 'user') {
            return redirect()->route('home')->with('warning', 'صفحة الاشتراك مخصصة للمتدربين فقط');
        }

        if (Auth::check()) {
            $blocking = Auth::user()->subscriptions()
                ->whereIn('status', [
                    Subscription::STATUS_PENDING_REVIEW,
                    Subscription::STATUS_APPROVED,
                    Subscription::STATUS_ACTIVE,
                ])
                ->latest('id')
                ->first();

            if ($blocking) {
                $label = match ($blocking->status) {
                    Subscription::STATUS_PENDING_REVIEW => 'قيد المراجعة',
                    Subscription::STATUS_APPROVED       => 'تم قبوله وسيُفعَّل قريباً',
                    Subscription::STATUS_ACTIVE         => 'نشط حالياً',
                    default                             => 'جارٍ معالجته',
                };
                return redirect()->route('home')
                    ->with('warning', "لديك اشتراك #{$blocking->id} ({$blocking->plan?->name}) {$label}. لا يمكن تقديم طلب جديد في الوقت الحالي.");
            }
        }

        $currency       = $this->currency->current();
        $durationMonths = in_array((int) $request->input('duration'), [3, 6])
            ? (int) $request->input('duration')
            : 3;

        $price3m = (float) ($plan->priceFor($currency, 3)?->price ?? $plan->priceFor('SAR', 3)?->price ?? $plan->price);
        $price6m = (float) ($plan->priceFor($currency, 6)?->price ?? $plan->priceFor('SAR', 6)?->price ?? $plan->price);

        $activeSeason  = $this->seasonService->getActive();
        $sPrice3m      = $activeSeason ? $this->seasonService->applyToPrice($price3m, $activeSeason) : $price3m;
        $sPrice6m      = $activeSeason ? $this->seasonService->applyToPrice($price6m, $activeSeason) : $price6m;

        $paymobEnabled = (bool) config('services.paymob.enabled');

        // Display-only EGP estimate for the pre-redirect summary (spec: show
        // both the displayed price and the exact EGP charge). The rate/
        // markup baked in here are NOT secrets — safe to expose client-side
        // for a live estimate. The actual charge is still always computed
        // authoritatively server-side by FxConverter at submission time;
        // this never feeds back into what gets persisted or charged.
        $fxRateConfigured = $currency === 'EGP' || ! is_null(config("payment.fx.egp_rates.{$currency}"));
        $fxBaseRate       = $currency === 'EGP' ? 1.0 : (float) (config("payment.fx.egp_rates.{$currency}") ?? 0);
        $fxMarkupPercent  = (float) config('payment.fx.markup_percent', 0);
        $fxEffectiveRate  = $currency === 'EGP' ? 1.0 : $fxBaseRate * (1 + $fxMarkupPercent / 100);
        $fxRounding       = (string) config('payment.fx.rounding', 'none');

        return view('app.web.purchase.form', compact(
            'plan', 'currency', 'durationMonths',
            'price3m', 'price6m', 'sPrice3m', 'sPrice6m',
            'activeSeason', 'paymobEnabled',
            'fxRateConfigured', 'fxEffectiveRate', 'fxRounding'
        ));
    }

    // ── AJAX: Validate coupon ────────────────────────────────────
    public function checkCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'code'            => 'required|string|max:50',
            'plan_id'         => 'required|integer|exists:plans,id',
            'duration_months' => 'required|integer|in:3,6',
        ]);

        $plan     = Plan::findOrFail($request->plan_id);
        $currency = $this->currency->current();

        $price3m = (float) ($plan->priceFor($currency, 3)?->price ?? $plan->priceFor('SAR', 3)?->price ?? $plan->price);
        $price6m = (float) ($plan->priceFor($currency, 6)?->price ?? $plan->priceFor('SAR', 6)?->price ?? $plan->price);

        // Apply season discount server-side
        $activeSeason  = $this->seasonService->getActive();
        $sPrice3m      = $activeSeason ? $this->seasonService->applyToPrice($price3m, $activeSeason) : $price3m;
        $sPrice6m      = $activeSeason ? $this->seasonService->applyToPrice($price6m, $activeSeason) : $price6m;

        $coupon = Coupon::findActive(trim($request->code));

        if (! $coupon) {
            return response()->json([
                'valid'   => false,
                'message' => 'الكوبون غير صالح أو منتهي الصلاحية',
            ]);
        }

        // Coupon discount is calculated on the post-season price
        return response()->json([
            'valid'        => true,
            'type'         => $coupon->type,
            'value'        => (float) $coupon->value,
            'discount3m'   => $coupon->calculateDiscount($sPrice3m),
            'discount6m'   => $coupon->calculateDiscount($sPrice6m),
            'season'       => $activeSeason ? [
                'name'       => app()->getLocale() === 'ar' ? $activeSeason->name_ar : $activeSeason->name_en,
                'pct'        => (float) $activeSeason->discount_percentage,
                'sPrice3m'   => $sPrice3m,
                'sPrice6m'   => $sPrice6m,
            ] : null,
        ]);
    }

    // ── AJAX: Check if email already registered ──────────────────
    public function checkEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|max:255']);

        return response()->json([
            'exists' => User::where('email', trim($request->email))->exists(),
        ]);
    }

    // ── Step 2: Create the order, convert to EGP, redirect to Paymob ──
    public function initiatePayment(Plan $plan, Request $request)
    {
        abort_if(! $plan->is_active, 404);

        if (Auth::check() && Auth::user()->role !== 'user') {
            return redirect()->route('home')->with('warning', 'صفحة الاشتراك مخصصة للمتدربين فقط');
        }

        /*
         * Duplicate-order guard (audit Risk D-9, extended to guest emails):
         *   - pending_review / approved / active  → BLOCK. A real order
         *     already exists (under review, accepted, or live) — never
         *     create another.
         *   - awaiting_payment, AUTHENTICATED owner → don't block. Silently
         *     resume that exact row with a fresh Paymob intention instead of
         *     creating a duplicate — this is what "not blocked forever"
         *     means. Ownership is proven via Auth::user()->subscriptions().
         *   - awaiting_payment, GUEST (matched only by email) → BLOCK like
         *     the statuses above. Matching by email alone is not proof of
         *     ownership (no guest_token check at this step) — auto-resuming
         *     here would let anyone who knows someone else's email advance
         *     or observe their pending payment. Guests resume via their own
         *     original link (guest_token), not by resubmitting this form.
         *   - payment_failed (and expired/rejected/cancelled/refunded) →
         *     never blocks, for guest or authenticated. This is exactly what
         *     a "fresh attempt" means.
         */
        if (Auth::check()) {
            $existing = Auth::user()->subscriptions()
                ->whereIn('status', [
                    Subscription::STATUS_PENDING_REVIEW,
                    Subscription::STATUS_APPROVED,
                    Subscription::STATUS_ACTIVE,
                    Subscription::STATUS_AWAITING_PAYMENT,
                ])
                ->latest('id')
                ->first();

            if ($existing && $existing->status === Subscription::STATUS_AWAITING_PAYMENT) {
                return $this->createPaymobIntentionAndRedirect(
                    $existing,
                    Auth::user()->name,
                    Auth::user()->email,
                    $existing->billing_phone
                );
            }

            if ($existing) {
                $label = match ($existing->status) {
                    Subscription::STATUS_PENDING_REVIEW => 'قيد المراجعة',
                    Subscription::STATUS_APPROVED       => 'تم قبوله وسيُفعَّل قريباً',
                    Subscription::STATUS_ACTIVE         => 'نشط حالياً',
                    default                              => 'جارٍ معالجته',
                };
                return redirect()->route('home')
                    ->with('warning', "لديك اشتراك #{$existing->id} ({$existing->plan?->name}) {$label}. لا يمكن تقديم طلب جديد في الوقت الحالي.");
            }
        } elseif ($request->filled('email')) {
            $existingGuest = Subscription::whereIn('status', [
                    Subscription::STATUS_PENDING_REVIEW,
                    Subscription::STATUS_APPROVED,
                    Subscription::STATUS_ACTIVE,
                    Subscription::STATUS_AWAITING_PAYMENT,
                ])
                ->where(function ($q) use ($request) {
                    $q->where('guest_email', $request->email)
                      ->orWhereHas('user', fn ($u) => $u->where('email', $request->email));
                })
                ->latest('id')
                ->first();

            if ($existingGuest) {
                return redirect()->route('home')
                    ->with('warning', 'يوجد بالفعل طلب مرتبط بهذا البريد الإلكتروني قيد المعالجة. إذا كنت قد بدأت عملية دفع من قبل، تحقق من بريدك الإلكتروني لمتابعتها، أو تواصل معنا للمساعدة.');
            }
        }

        // Kill switch (Batch 1's PAYMOB_ENABLED flag, wired here as promised):
        // no order row, no external call — just a clear message.
        if (! config('services.paymob.enabled')) {
            return redirect()->route('purchase.form', $plan)
                ->with('warning', 'خدمة الدفع الإلكتروني غير متاحة حالياً. برجاء المحاولة لاحقاً أو التواصل معنا.')
                ->withInput();
        }

        $currency = $this->currency->current();

        $request->validate([
            'full_name'       => 'required|string|min:2|max:150',
            'email'           => 'required|email|max:255',
            'phone'           => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{8,20}$/'],
            'duration_months' => 'required|integer|in:3,6',
            'coupon_code'     => 'nullable|string|max:50',
        ], [
            'full_name.required'       => 'الاسم الكامل مطلوب',
            'full_name.min'            => 'الاسم يجب أن يكون حرفين على الأقل',
            'email.required'           => 'البريد الإلكتروني مطلوب',
            'email.email'              => 'صيغة البريد الإلكتروني غير صحيحة',
            'phone.required'           => 'رقم الهاتف مطلوب',
            'phone.regex'              => 'صيغة رقم الهاتف غير صحيحة',
            'duration_months.required' => 'مدة الاشتراك مطلوبة',
            'duration_months.in'       => 'مدة الاشتراك يجب أن تكون 3 أو 6 أشهر',
        ]);

        $durationMonths = (int) $request->duration_months;
        $planPrice      = $plan->priceFor($currency, $durationMonths)
                        ?? $plan->priceFor('SAR', $durationMonths);
        $subtotal       = $planPrice ? (float) $planPrice->price : (float) $plan->price;

        // Season discount — detect if season expired between form load and submit
        $activeSeason     = $this->seasonService->getActive();
        $expectedSeasonId = (int) $request->input('expected_season_id', 0);
        if ($expectedSeasonId > 0 && ($activeSeason === null || $activeSeason->id !== $expectedSeasonId)) {
            return redirect()->route('purchase.form', $plan)
                ->with('info', __('messages.purchase.season_expired_notice'))
                ->withInput();
        }
        $priceAfterSeason = $activeSeason
            ? (float) $this->seasonService->applyToPrice($subtotal, $activeSeason)
            : $subtotal;
        $seasonDiscount = $subtotal - $priceAfterSeason;

        // Apply coupon on the integer post-season price — UNCHANGED pricing
        // block (audit :190-219 / Batch 5 instructions).
        $couponDiscount = 0.0;
        $couponCode     = null;
        $coupon         = null;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::findActive(trim($request->coupon_code));
            if ($coupon) {
                $couponDiscount = $coupon->calculateDiscount($priceAfterSeason);
                $couponCode     = strtoupper(trim($request->coupon_code));
            }
        }
        $total = (float) round(max(0.0, $priceAfterSeason - $couponDiscount));

        // FX conversion happens BEFORE any DB write — a currency we can't
        // price in EGP must never produce an order row (decision D4).
        try {
            $fx = $this->fxConverter->toEgpCents($total, $currency);
        } catch (FxRateNotConfiguredException) {
            return redirect()->route('purchase.form', $plan)
                ->with('warning', 'عذراً، الدفع بهذه العملة غير متاح حالياً. برجاء المحاولة لاحقاً أو التواصل معنا.')
                ->withInput();
        }

        $phoneNormalized = preg_replace('/[^\d+]/', '', $request->phone);

        try {
            $subscription = DB::transaction(function () use (
                $request, $plan, $currency, $durationMonths,
                $subtotal, $activeSeason, $seasonDiscount,
                $couponDiscount, $couponCode, $total, $coupon, $fx, $phoneNormalized
            ) {
                // Atomic max_uses gate (audit Risk D-5): lock the coupon row
                // so two concurrent checkouts against the same limited
                // coupon can't both pass the count check before either
                // commits. Coupon::usageCount() (Batch 6, C3) stays the
                // single source of truth — shared with Coupon::findActive()
                // so the two can never drift apart — the row lock just
                // serializes access to it. Only genuinely consumed statuses
                // count (see Coupon::CONSUMED_STATUSES): an abandoned or
                // declined Paymob checkout must not permanently burn a use.
                if ($couponCode) {
                    $lockedCoupon = Coupon::where('code', $couponCode)->lockForUpdate()->first();

                    if ($lockedCoupon && $lockedCoupon->max_uses !== null
                        && $lockedCoupon->usageCount() >= $lockedCoupon->max_uses) {
                        throw new CouponExhaustedException($couponCode);
                    }
                }

                $sub = Subscription::create([
                    'user_id'                    => Auth::id(),
                    'guest_name'                 => Auth::check() ? null : $request->full_name,
                    'guest_email'                => Auth::check() ? null : $request->email,
                    'guest_token'                => Auth::check() ? null : Str::random(64),
                    'plan_id'                    => $plan->id,
                    'status'                     => Subscription::STATUS_AWAITING_PAYMENT,
                    'duration_months'            => $durationMonths,
                    'currency'                   => $currency,
                    'subtotal'                   => $subtotal,
                    'season_id'                  => $activeSeason?->id,
                    'season_name'                => $activeSeason?->name_ar,
                    'season_discount_percentage' => $activeSeason?->discount_percentage,
                    'season_discount'            => $seasonDiscount,
                    'coupon_code'                => $couponCode,
                    'coupon_discount'            => $couponDiscount,
                    'total'                      => $total,
                    'payment_gateway'            => Subscription::GATEWAY_PAYMOB,
                    'payment_intended_at'        => now(),
                    'charged_currency'           => 'EGP',
                    'charged_amount_cents'       => $fx['cents'],
                    'fx_rate'                    => $fx['rate'],
                    'fx_rate_source'             => $fx['source'],
                    'billing_phone'              => $phoneNormalized,
                    'plans_snapshot'             => [[
                        'plan_id'                    => $plan->id,
                        'plan_name'                  => $plan->name,
                        'quantity'                   => 1,
                        'subtotal'                   => $subtotal,
                        'season_name'                => $activeSeason?->name_ar,
                        'season_discount_percentage' => $activeSeason?->discount_percentage,
                        'season_discount'            => $seasonDiscount,
                        'coupon_discount'            => $couponDiscount,
                        'final_price'                => $total,
                        'currency'                   => $currency,
                        'duration_months'            => $durationMonths,
                    ]],
                ]);

                // If a family-reward coupon was used, advance the linked invitation to 'used'
                if ($coupon) {
                    FamilyInvitation::where('coupon_id', $coupon->id)
                        ->where('status', 'pending')
                        ->first()
                        ?->markUsed();
                }

                return $sub;
            });
        } catch (CouponExhaustedException) {
            return redirect()->route('purchase.form', $plan)
                ->with('warning', 'عذراً، تم استخدام هذا الكوبون بالكامل. برجاء إزالته والمتابعة بدونه.')
                ->withInput();
        }

        session(['last_purchase_id' => $subscription->id]);

        $customerEmail = Auth::check() ? Auth::user()->email : $request->email;
        $customerName  = Auth::check() ? Auth::user()->name  : $request->full_name;

        // No OrderReceivedMail / OrderPendingReviewMail here (Batch 5
        // recommendation): under Paymob, "order created but unpaid" isn't a
        // customer-facing event worth emailing about, and there's no admin
        // review step left to notify staff of. OrderApprovedMail (unchanged)
        // remains the real confirmation, fired automatically by
        // OrderApprovalService once the webhook confirms payment (Batch 6).

        return $this->createPaymobIntentionAndRedirect($subscription, $customerName, $customerEmail, $phoneNormalized);
    }

    // ── Retry: fresh intention on an existing awaiting_payment/payment_failed order ──
    public function retryPayment(Request $request, Subscription $subscription): RedirectResponse
    {
        $authorized = (Auth::check() && $subscription->user_id === Auth::id())
            || (! Auth::check()
                && $subscription->guest_token
                && hash_equals($subscription->guest_token, (string) $request->input('guest_token', '')));

        abort_unless($authorized, 403);

        abort_unless(in_array($subscription->status, [
            Subscription::STATUS_AWAITING_PAYMENT,
            Subscription::STATUS_PAYMENT_FAILED,
        ], true), 422);

        $fullName = $subscription->user?->name ?? $subscription->guest_name ?? 'العميل';
        $email    = $subscription->user?->email ?? $subscription->guest_email;

        return $this->createPaymobIntentionAndRedirect($subscription, $fullName, $email, $subscription->billing_phone);
    }

    /**
     * Shared by initiatePayment() (new order) and retryPayment() (existing
     * row): creates a fresh Paymob intention and either redirects to the
     * checkout URL, or — if Paymob's API throws — marks the order
     * payment_failed with a reason and redirects to a page with a retry
     * link. Never a raw 500, never a silent redirect to nowhere.
     */
    private function createPaymobIntentionAndRedirect(
        Subscription $subscription,
        string $fullName,
        ?string $email,
        ?string $phone
    ): RedirectResponse {
        try {
            $intention = $this->paymobClient->createIntention($subscription, [
                'full_name' => $fullName,
                'email' => $email,
                'phone_number' => $phone,
            ]);
        } catch (\Throwable $e) {
            Log::error('Paymob intention creation failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            $subscription->update([
                'status' => Subscription::STATUS_PAYMENT_FAILED,
                'payment_failure_reason' => 'تعذر إنشاء عملية الدفع مع بوابة الدفع. برجاء المحاولة مرة أخرى.',
            ]);

            session(['last_purchase_id' => $subscription->id]);

            return redirect()->route('purchase.success', $subscription->id)
                ->with('warning', 'حدث خطأ أثناء تجهيز عملية الدفع. يمكنك إعادة المحاولة أدناه.');
        }

        $subscription->update([
            'paymob_order_id' => $intention->paymobOrderId,
            'paymob_order_ids' => array_values(array_unique(array_merge(
                $subscription->paymob_order_ids ?? [],
                [$intention->paymobOrderId]
            ))),
            'paymob_intention_id' => $intention->intentionId,
        ]);

        return redirect()->away($intention->checkoutUrl);
    }

    // ── Step 3: Success / status page ─────────────────────────────
    public function success(int $id)
    {
        if (Auth::check()) {
            $subscription = Subscription::where('user_id', Auth::id())->findOrFail($id);
        } else {
            abort_unless(session('last_purchase_id') === $id, 403);
            $subscription = Subscription::findOrFail($id);
        }

        return view('app.web.purchase.success', compact('subscription'));
    }
}

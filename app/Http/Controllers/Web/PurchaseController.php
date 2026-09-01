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
use App\Mail\OrderPendingReviewMail;
use App\Mail\OrderReceivedMail;
use App\Services\FxConverter;
use App\Services\Paymob\PaymobClient;
use App\Services\Web\CurrencyService;
use App\Services\Web\PaymentEligibilityService;
use App\Services\Web\SeasonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function __construct(
        private CurrencyService $currency,
        private SeasonService   $seasonService,
        private PaymobClient    $paymobClient,
        private FxConverter     $fxConverter,
        private PaymentEligibilityService $eligibility,
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
        //
        // Goes through the SAME FxConverter (DB fx_rates → config fallback)
        // that submission uses — it never makes an HTTP call, only reads the
        // DB — so this can't disagree with what actually happens on submit.
        try {
            $fxEffectiveRate  = (float) $this->fxConverter->toEgpCents(1, $currency)['rate'];
            $fxRateConfigured = true;
        } catch (FxRateNotConfiguredException $e) {
            $fxEffectiveRate  = 0.0;
            $fxRateConfigured = false;
        }
        $fxRounding = (string) config('payment.fx.rounding', 'none');

        // Manual transfer, when eligible, is priced independently in the
        // ELIGIBLE currency (tied to the visitor's detected country, via
        // PaymentEligibilityService), never in whatever display currency the
        // customer has switched session('currency') to — see
        // docs/dual-payment-plan.md A5. A bank account denominated in TND
        // must never be paired with an amount computed in EGP just because
        // the visitor toggled the page's display currency.
        $manualMethod   = $this->eligibility->manualMethodFor(session('detected_country'));
        $manualPrice3m  = null;
        $manualPrice6m  = null;
        $manualSPrice3m = null;
        $manualSPrice6m = null;

        if ($manualMethod) {
            $manualCurrency = $manualMethod['currency'];
            $manualPrice3m  = (float) ($plan->priceFor($manualCurrency, 3)?->price ?? $plan->priceFor('SAR', 3)?->price ?? $plan->price);
            $manualPrice6m  = (float) ($plan->priceFor($manualCurrency, 6)?->price ?? $plan->priceFor('SAR', 6)?->price ?? $plan->price);
            $manualSPrice3m = $activeSeason ? $this->seasonService->applyToPrice($manualPrice3m, $activeSeason) : $manualPrice3m;
            $manualSPrice6m = $activeSeason ? $this->seasonService->applyToPrice($manualPrice6m, $activeSeason) : $manualPrice6m;
        }

        return view('app.web.purchase.form', compact(
            'plan', 'currency', 'durationMonths',
            'price3m', 'price6m', 'sPrice3m', 'sPrice6m',
            'activeSeason', 'paymobEnabled',
            'fxRateConfigured', 'fxEffectiveRate', 'fxRounding',
            'manualMethod', 'manualPrice3m', 'manualPrice6m', 'manualSPrice3m', 'manualSPrice6m'
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
        $code     = trim($request->code);

        $activeSeason = $this->seasonService->getActive();

        // C5: same routine initiatePayment() uses for both real paths — this
        // AJAX preview can never disagree with what submission actually
        // charges, for either currency, because it's the same code.
        $p3 = $this->computePricing($plan, $currency, 3, $activeSeason, $code);
        $p6 = $this->computePricing($plan, $currency, 6, $activeSeason, $code);

        if (! $p3['coupon']) {
            return response()->json([
                'valid'   => false,
                'message' => 'الكوبون غير صالح أو منتهي الصلاحية',
            ]);
        }
        $coupon  = $p3['coupon'];
        $sPrice3m = $p3['subtotal'] - $p3['seasonDiscount'];
        $sPrice6m = $p6['subtotal'] - $p6['seasonDiscount'];

        // Manual-transfer price lives in its OWN currency (detected-country-
        // derived, not the switchable display currency — A5), so its coupon
        // discount must be computed against ITS OWN season-adjusted price,
        // not scaled from the display-currency discount above.
        $manualMethod = $this->eligibility->manualMethodFor(session('detected_country'));
        $manualDiscount3m = null;
        $manualDiscount6m = null;
        $manualSPrice3m   = null;
        $manualSPrice6m   = null;
        if ($manualMethod) {
            $mp3 = $this->computePricing($plan, $manualMethod['currency'], 3, $activeSeason, $code);
            $mp6 = $this->computePricing($plan, $manualMethod['currency'], 6, $activeSeason, $code);
            $manualDiscount3m = $mp3['couponDiscount'];
            $manualDiscount6m = $mp6['couponDiscount'];
            $manualSPrice3m   = $mp3['subtotal'] - $mp3['seasonDiscount'];
            $manualSPrice6m   = $mp6['subtotal'] - $mp6['seasonDiscount'];
        }

        return response()->json([
            'valid'        => true,
            'type'         => $coupon->type,
            'value'        => (float) $coupon->value,
            'discount3m'   => $p3['couponDiscount'],
            'discount6m'   => $p6['couponDiscount'],
            'manualDiscount3m' => $manualDiscount3m,
            'manualDiscount6m' => $manualDiscount6m,
            'season'       => $activeSeason ? [
                'name'       => app()->getLocale() === 'ar' ? $activeSeason->name_ar : $activeSeason->name_en,
                'pct'        => (float) $activeSeason->discount_percentage,
                'sPrice3m'   => $sPrice3m,
                'sPrice6m'   => $sPrice6m,
                'manualSPrice3m' => $manualSPrice3m,
                'manualSPrice6m' => $manualSPrice6m,
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

    // ── Step 2: Create the order — branches card (Paymob) vs manual transfer ──
    public function initiatePayment(Plan $plan, Request $request)
    {
        abort_if(! $plan->is_active, 404);

        if (Auth::check() && Auth::user()->role !== 'user') {
            return redirect()->route('home')->with('warning', 'صفحة الاشتراك مخصصة للمتدربين فقط');
        }

        /*
         * Duplicate-order guard (audit Risk D-9, extended to guest emails;
         * restated for mid-flight method switching, step 5). Gateway-
         * agnostic on purpose — pending_review (manual, awaiting admin
         * review) blocks exactly like awaiting_payment (Paymob, awaiting the
         * webhook) does; a customer with either kind of order already open
         * must resolve it — by finishing it, or by SWITCHING method on that
         * same row via switchMethod() — before this guard ever lets them
         * start a brand new one:
         *   - pending_review / approved / active  → BLOCK a new order. A real
         *     order already exists (under review, accepted, or live). This
         *     is now a SETTLED state as far as method choice goes too —
         *     switchMethod() also refuses pending_review (a receipt is
         *     already under human review; approved/active are simply done).
         *   - awaiting_payment, AUTHENTICATED owner → don't block a resubmit
         *     of THIS form. Silently resume that exact row with a fresh
         *     Paymob intention instead of creating a duplicate. Ownership is
         *     proven via Auth::user()->subscriptions().
         *   - awaiting_payment, GUEST (matched only by email) → BLOCK like
         *     the statuses above. Matching by email alone is not proof of
         *     ownership (no guest_token check at this step) — auto-resuming
         *     here would let anyone who knows someone else's email advance
         *     or observe their pending payment. Guests resume via their own
         *     original link (guest_token), not by resubmitting this form.
         *   - payment_failed / rejected (and expired/cancelled/refunded) →
         *     never blocks, for guest or authenticated. This is exactly what
         *     a "fresh attempt" means — this was ALREADY true for rejected
         *     before step 7 (OrderRejectionService never touches user_id/
         *     guest_token/anything a duplicate-check keys on, so a rejected
         *     row was never sticky here); step 7 didn't change this guard,
         *     it just gave `rejected` a SECOND recovery path (below).
         *
         * Restated: a customer is NOT "locked into" whichever method they
         * started with for the life of the order (that WAS true before step
         * 5), NOR are they stuck with no way to pay after a rejection (true
         * before step 7). They may switch — via POST /purchase/{subscription}/
         * switch-method, never by resubmitting this form — for as long as
         * the row sits in awaiting_payment, payment_failed, OR rejected.
         * They may ALSO, for rejected specifically, just submit this form
         * again for a brand new row (this guard already allows that, unlike
         * pending_review/approved/active). Both paths work; switching keeps
         * the original order's reference/audit trail, a fresh submission
         * doesn't. The instant a switch produces pending_review (manual,
         * awaiting review) or a paid/approved outcome, the row is settled
         * again: this guard blocks any new order, and switchMethod() blocks
         * any further switch.
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

        // Never trust the client's claim of which method they picked — the
        // 'manual' branch re-validates eligibility server-side from scratch
        // (PaymentEligibilityService, keyed on detected_country) regardless
        // of what the form's hidden field says. See docs/dual-payment-plan.md A5.
        if ($request->input('payment_method') === 'manual') {
            return $this->initiateManualTransfer($plan, $request);
        }

        return $this->initiateCardPayment($plan, $request);
    }

    private function initiateCardPayment(Plan $plan, Request $request)
    {
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

        // Season discount — detect if season expired between form load and submit
        $activeSeason     = $this->seasonService->getActive();
        $expectedSeasonId = (int) $request->input('expected_season_id', 0);
        if ($expectedSeasonId > 0 && ($activeSeason === null || $activeSeason->id !== $expectedSeasonId)) {
            return redirect()->route('purchase.form', $plan)
                ->with('info', __('messages.purchase.season_expired_notice'))
                ->withInput();
        }

        // C5: the ONLY pricing math either payment path runs — see
        // computePricing()'s docblock. Same code, different currency in, so
        // card and manual can never drift out of parity by construction.
        ['subtotal' => $subtotal, 'seasonDiscount' => $seasonDiscount,
         'couponDiscount' => $couponDiscount, 'total' => $total,
         'coupon' => $coupon, 'couponCode' => $couponCode] = $this->computePricing(
            $plan, $currency, $durationMonths, $activeSeason, $request->input('coupon_code')
        );

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
        // recommendation, reconfirmed valid under the dual-payment plan):
        // under Paymob, "order created but unpaid" isn't a customer-facing
        // event worth emailing about, and there's no admin review step left
        // to notify staff of. OrderApprovedMail (unchanged) remains the real
        // confirmation, fired automatically by OrderApprovalService once the
        // webhook confirms payment (Batch 6). The manual-transfer branch
        // below DOES send OrderReceivedMail/OrderPendingReviewMail — that
        // asymmetry is deliberate, not an oversight.

        return $this->createPaymobIntentionAndRedirect($subscription, $customerName, $customerEmail, $phoneNormalized);
    }

    /**
     * Manual bank transfer / InstaPay — restored from the pre-Paymob flow
     * (docs/dual-payment-plan.md). Eligibility, pricing currency, and the
     * bank account shown are ALL derived from the visitor's IP-detected
     * country (never the freely-switchable display currency) — see A5.
     */
    private function initiateManualTransfer(Plan $plan, Request $request)
    {
        $manualMethod = $this->eligibility->manualMethodFor(session('detected_country'));

        // Fail closed: ineligible, or detection unavailable — never silently
        // charge a different amount/method than what the customer saw.
        if (! $manualMethod) {
            return redirect()->route('purchase.form', $plan)
                ->with('warning', 'التحويل البنكي غير متاح لمنطقتك حالياً. برجاء الدفع عبر البطاقة.')
                ->withInput();
        }

        $currency = $manualMethod['currency'];

        $request->validate([
            'full_name'       => 'required|string|min:2|max:150',
            'email'           => 'required|email|max:255',
            'phone'           => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{8,20}$/'],
            'duration_months' => 'required|integer|in:3,6',
            'coupon_code'     => 'nullable|string|max:50',
            // Restored unchanged from the pre-Batch-5 flow (audit :170-187).
            'receipt'         => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
        ], [
            'full_name.required'       => 'الاسم الكامل مطلوب',
            'full_name.min'            => 'الاسم يجب أن يكون حرفين على الأقل',
            'email.required'           => 'البريد الإلكتروني مطلوب',
            'email.email'              => 'صيغة البريد الإلكتروني غير صحيحة',
            'phone.required'           => 'رقم الهاتف مطلوب',
            'phone.regex'              => 'صيغة رقم الهاتف غير صحيحة',
            'duration_months.required' => 'مدة الاشتراك مطلوبة',
            'duration_months.in'       => 'مدة الاشتراك يجب أن تكون 3 أو 6 أشهر',
            'receipt.required'         => 'إيصال الدفع مطلوب',
            'receipt.file'             => 'الملف غير صحيح',
            'receipt.mimes'            => 'يُقبل فقط: صور (JPG, PNG) أو PDF',
            'receipt.max'              => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت',
        ]);

        $durationMonths = (int) $request->duration_months;

        $activeSeason     = $this->seasonService->getActive();
        $expectedSeasonId = (int) $request->input('expected_season_id', 0);
        if ($expectedSeasonId > 0 && ($activeSeason === null || $activeSeason->id !== $expectedSeasonId)) {
            return redirect()->route('purchase.form', $plan)
                ->with('info', __('messages.purchase.season_expired_notice'))
                ->withInput();
        }

        // C5: same shared routine the card path uses (see computePricing()).
        ['subtotal' => $subtotal, 'seasonDiscount' => $seasonDiscount,
         'couponDiscount' => $couponDiscount, 'total' => $total,
         'coupon' => $coupon, 'couponCode' => $couponCode] = $this->computePricing(
            $plan, $currency, $durationMonths, $activeSeason, $request->input('coupon_code')
        );

        // Stored to PRIVATE disk BEFORE the DB transaction — restored
        // unchanged from the pre-Batch-5 flow (audit :222-226):
        // storage/app/private/receipts/{Y}/{m}/{uuid}.{ext}
        $receiptPath = $request->file('receipt')->storeAs(
            'receipts/' . now()->format('Y/m'),
            Str::uuid() . '.' . $request->file('receipt')->extension(),
            'local'
        );

        $phoneNormalized = preg_replace('/[^\d+]/', '', $request->phone);

        try {
            $subscription = DB::transaction(function () use (
                $request, $plan, $currency, $durationMonths,
                $subtotal, $activeSeason, $seasonDiscount,
                $couponDiscount, $couponCode, $total, $coupon, $receiptPath, $phoneNormalized
            ) {
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
                    'status'                     => Subscription::STATUS_PENDING_REVIEW,
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
                    'payment_gateway'            => Subscription::GATEWAY_MANUAL,
                    'payment_intended_at'        => now(),
                    // Currency IS the account identity now (config/payment.php
                    // 'manual' is keyed by currency, one account per currency)
                    // — reusing this pre-existing column rather than adding one.
                    'payment_method_key'         => $currency,
                    'receipt_path'               => $receiptPath,
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

                if ($coupon) {
                    FamilyInvitation::where('coupon_id', $coupon->id)
                        ->where('status', 'pending')
                        ->first()
                        ?->markUsed();
                }

                return $sub;
            });
        } catch (CouponExhaustedException) {
            // The upload happened before the transaction (see above) — an
            // aborted order must not leave an orphaned file on disk.
            Storage::disk('local')->delete($receiptPath);

            return redirect()->route('purchase.form', $plan)
                ->with('warning', 'عذراً، تم استخدام هذا الكوبون بالكامل. برجاء إزالته والمتابعة بدونه.')
                ->withInput();
        }

        // A5: the pattern worth watching — a manual order created while the
        // visitor's (switchable) display currency disagrees with the
        // (detection-derived) currency they're actually transferring in.
        // Not blocked, just logged for review.
        $displayCurrency = session('currency');
        if ($displayCurrency && $displayCurrency !== $currency) {
            Log::warning('Manual order created while detected country and display currency disagree', [
                'subscription_id' => $subscription->id,
                'detected_country' => session('detected_country'),
                'manual_currency' => $currency,
                'display_currency' => $displayCurrency,
            ]);
        }

        session(['last_purchase_id' => $subscription->id]);

        $customerEmail = Auth::check() ? Auth::user()->email : $request->email;
        $customerName  = Auth::check() ? Auth::user()->name  : $request->full_name;

        try {
            Mail::to($customerEmail)->send(new OrderReceivedMail($subscription, $customerName));
        } catch (\Throwable $e) {
            Log::error('OrderReceivedMail failed', ['sub' => $subscription->id, 'err' => $e->getMessage()]);
        }

        try {
            $staff = User::whereIn('role', ['coach', 'admin'])->where('status', '!=', 'banned')->get();
            if ($staff->isNotEmpty()) {
                Mail::to($staff)->send(new OrderPendingReviewMail($subscription, $customerName, $customerEmail));
            }
        } catch (\Throwable $e) {
            Log::error('OrderPendingReviewMail failed', ['sub' => $subscription->id, 'err' => $e->getMessage()]);
        }

        return redirect()->route('paymob.callback', array_filter([
            'sid' => $subscription->id,
            'guest_token' => $subscription->guest_token,
        ]));
    }

    /**
     * C5: the single pricing routine both initiateCardPayment() and
     * initiateManualTransfer() call — same plan/season/coupon math, only the
     * CURRENCY passed in differs (display currency for card, eligible/
     * detected currency for manual). Extracted specifically so the two paths
     * cannot drift out of parity from independently-edited duplicate code;
     * a change to season or coupon math now only exists in one place.
     *
     * @return array{subtotal: float, seasonDiscount: float, couponDiscount: float,
     *               total: float, coupon: ?Coupon, couponCode: ?string}
     */
    private function computePricing(
        Plan $plan,
        string $currency,
        int $durationMonths,
        ?\App\Models\Season $activeSeason,
        ?string $rawCouponCode
    ): array {
        $planPrice = $plan->priceFor($currency, $durationMonths)
                   ?? $plan->priceFor('SAR', $durationMonths);
        $subtotal  = $planPrice ? (float) $planPrice->price : (float) $plan->price;

        $priceAfterSeason = $activeSeason
            ? (float) $this->seasonService->applyToPrice($subtotal, $activeSeason)
            : $subtotal;
        $seasonDiscount = $subtotal - $priceAfterSeason;

        $couponDiscount = 0.0;
        $couponCode     = null;
        $coupon         = null;
        if ($rawCouponCode) {
            $coupon = Coupon::findActive(trim($rawCouponCode));
            if ($coupon) {
                $couponDiscount = $coupon->calculateDiscount($priceAfterSeason);
                $couponCode     = strtoupper(trim($rawCouponCode));
            }
        }
        $total = (float) round(max(0.0, $priceAfterSeason - $couponDiscount));

        return compact('subtotal', 'seasonDiscount', 'couponDiscount', 'total', 'coupon', 'couponCode');
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
     * Mid-flight method switching (docs/dual-payment-plan.md, steps 5 & 7) —
     * a customer who started one way can finish the OTHER way, on the SAME
     * row, without starting a new order. Same authorization as retry: owner
     * or correct guest_token, never id alone.
     *
     * Allowed ONLY from a still-open OR rejected state:
     *   awaiting_payment / payment_failed / rejected  →  manual  (receipt required NOW)
     *   awaiting_payment / payment_failed / rejected  →  card    (== retryPayment())
     * `rejected` was added in step 7 deliberately: it's the ONLY recovery
     * path for a customer whose manual receipt was refused (wrong amount,
     * unreadable, etc.) — without it they'd have no way to pay at all short
     * of starting a brand new order via the form. `pending_review` is still
     * hard-blocked and always will be — a receipt already under human review
     * must never be orphaned by a switch mid-check; approved/active/expired/
     * cancelled/refunded are all resolved/final.
     */
    public function switchMethod(Request $request, Subscription $subscription): RedirectResponse
    {
        $authorized = (Auth::check() && $subscription->user_id === Auth::id())
            || (! Auth::check()
                && $subscription->guest_token
                && hash_equals($subscription->guest_token, (string) $request->input('guest_token', '')));

        abort_unless($authorized, 403);

        abort_unless(in_array($subscription->status, [
            Subscription::STATUS_AWAITING_PAYMENT,
            Subscription::STATUS_PAYMENT_FAILED,
            Subscription::STATUS_REJECTED,
        ], true), 422, match ($subscription->status) {
            Subscription::STATUS_PENDING_REVIEW => 'إيصالك قيد المراجعة بالفعل — لا يمكن تغيير طريقة الدفع الآن.',
            default => 'لا يمكن تغيير طريقة الدفع لهذا الطلب في حالته الحالية.',
        });

        return $request->input('to') === 'manual'
            ? $this->switchToManual($request, $subscription)
            : $this->switchToCard($request, $subscription);
    }

    /**
     * Never re-prices: keeps the row's already-locked currency/total exactly
     * as they are ("no recomputed total, no re-run FX" per spec). Because of
     * that, the manual account shown must match the row's OWN currency, not
     * whatever PaymentEligibilityService would recommend fresh right now —
     * those two can disagree (e.g. the original card attempt was priced in a
     * display currency the visitor had switched to). Eligibility is still
     * re-checked against detected_country (never spoofable), AND the
     * eligible currency must equal the row's own currency — if either fails,
     * there is no correct manual account to show for this exact order, so
     * the switch is refused rather than silently improvising one.
     */
    private function switchToManual(Request $request, Subscription $subscription): RedirectResponse
    {
        $manualMethod = $this->eligibility->manualMethodFor(session('detected_country'));

        abort_unless(
            $manualMethod !== null && $manualMethod['currency'] === $subscription->currency,
            403,
            'التحويل البنكي غير متاح بعملة طلبك الحالية.'
        );

        // Validated BEFORE any write — a rejected upload leaves the row
        // completely untouched (still on its original method/status), never
        // a half-switched state.
        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
        ], [
            'receipt.required' => 'إيصال الدفع مطلوب',
            'receipt.file'     => 'الملف غير صحيح',
            'receipt.mimes'    => 'يُقبل فقط: صور (JPG, PNG) أو PDF',
            'receipt.max'      => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت',
        ]);

        // The OLD receipt (if this row is coming from `rejected`) is
        // deliberately KEPT on disk, never deleted — it's the evidence tied
        // to whatever the admin's rejection_reason described, useful for a
        // dispute later. Only the DB pointer moves to the new file; the old
        // one becomes an orphan on disk, same disposal choice already made
        // for the manual→card switch (receipt_path nulled, file untouched).
        $receiptPath = $request->file('receipt')->storeAs(
            'receipts/' . now()->format('Y/m'),
            Str::uuid() . '.' . $request->file('receipt')->extension(),
            'local'
        );

        $fromGateway = $subscription->payment_gateway;

        $subscription->update([
            'status'               => Subscription::STATUS_PENDING_REVIEW,
            'payment_gateway'      => Subscription::GATEWAY_MANUAL,
            'receipt_path'         => $receiptPath,
            'payment_method_key'   => $manualMethod['currency'],
            'payment_intended_at'  => now(),
            // A row that's never been priced through Paymob has none of
            // these; a row that WAS (card attempt first) had them — clear
            // them, manual never carries an FX charge.
            'charged_currency'     => null,
            'charged_amount_cents' => null,
            'fx_rate'              => null,
            'fx_rate_source'       => null,
            // A fresh review cycle — any PRIOR rejection decision no longer
            // describes the current (new) receipt under review.
            'rejection_reason'     => null,
            'reviewed_by'          => null,
            'reviewed_at'          => null,
        ]);

        $this->logMethodSwitch($request, $subscription, $fromGateway, 'manual');

        $customerName  = $subscription->user?->name  ?? $subscription->guest_name  ?? 'العميل';
        $customerEmail = $subscription->user?->email ?? $subscription->guest_email;

        try {
            Mail::to($customerEmail)->send(new OrderReceivedMail($subscription, $customerName));
        } catch (\Throwable $e) {
            Log::error('OrderReceivedMail failed', ['sub' => $subscription->id, 'err' => $e->getMessage()]);
        }
        try {
            $staff = User::whereIn('role', ['coach', 'admin'])->where('status', '!=', 'banned')->get();
            if ($staff->isNotEmpty()) {
                Mail::to($staff)->send(new OrderPendingReviewMail($subscription, $customerName, $customerEmail));
            }
        } catch (\Throwable $e) {
            Log::error('OrderPendingReviewMail failed', ['sub' => $subscription->id, 'err' => $e->getMessage()]);
        }

        return redirect()->route('paymob.callback', array_filter([
            'sid' => $subscription->id,
            'guest_token' => $subscription->guest_token,
        ]));
    }

    /**
     * Functionally identical to retryPayment() when the row is already
     * gateway=paymob (the common case, since awaiting_payment/payment_failed
     * only exist for paymob rows today — manual goes straight to
     * pending_review). Written generically rather than assuming that,
     * though: a row with no charged_amount_cents yet (i.e. genuinely
     * switching FROM manual) gets one derived HERE, using TODAY's FX rate —
     * per instruction, never the rate an original order might have locked
     * days ago — while the customer's own-currency total is never touched.
     * Both rates are logged for anyone reconciling later.
     */
    private function switchToCard(Request $request, Subscription $subscription): RedirectResponse
    {
        $fromGateway = $subscription->payment_gateway;

        if (is_null($subscription->charged_amount_cents)) {
            try {
                $fx = $this->fxConverter->toEgpCents((float) $subscription->total, $subscription->currency);
            } catch (FxRateNotConfiguredException) {
                return redirect()->route('paymob.callback', array_filter([
                    'sid' => $subscription->id,
                    'guest_token' => $subscription->guest_token,
                ]))->with('warning', 'عذراً، الدفع بالبطاقة بهذه العملة غير متاح حالياً. برجاء المحاولة لاحقاً.');
            }

            Log::info("Subscription switched {$fromGateway} → card: derived a fresh EGP charge (today's rate, not the original order's)", [
                'subscription_id' => $subscription->id,
                'display_currency' => $subscription->currency,
                'display_total_unchanged' => (float) $subscription->total,
                'new_fx_rate' => $fx['rate'],
                'new_fx_rate_source' => $fx['source'],
            ]);

            $subscription->update([
                'charged_currency'     => 'EGP',
                'charged_amount_cents' => $fx['cents'],
                'fx_rate'              => $fx['rate'],
                'fx_rate_source'       => $fx['source'],
            ]);
        }

        // The old receipt file (if any) is deliberately KEPT on disk — only
        // the DB pointer is cleared. It's evidence tied to whatever decision
        // (rejection or otherwise) preceded this switch; deleting it would
        // destroy an audit trail for no operational benefit.
        $subscription->update([
            'payment_gateway'    => Subscription::GATEWAY_PAYMOB,
            'receipt_path'       => null,
            'payment_method_key' => null,
            // A fresh attempt — any PRIOR rejection decision no longer
            // describes the current (now card) attempt.
            'rejection_reason'   => null,
            'reviewed_by'        => null,
            'reviewed_at'        => null,
        ]);

        $this->logMethodSwitch($request, $subscription, $fromGateway, 'card');

        $fullName = $subscription->user?->name ?? $subscription->guest_name ?? 'العميل';
        $email    = $subscription->user?->email ?? $subscription->guest_email;

        return $this->createPaymobIntentionAndRedirect($subscription, $fullName, $email, $subscription->billing_phone);
    }

    private function logMethodSwitch(Request $request, Subscription $subscription, string $from, string $to): void
    {
        Log::info('Subscription payment method switched', [
            'subscription_id' => $subscription->id,
            'from_method' => $from,
            'to_method' => $to,
            'actor' => Auth::check() ? 'user:' . Auth::id() : 'guest',
            'timestamp' => now()->toIso8601String(),
        ]);
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
            // A fresh intention was just created — the row is awaiting that
            // payment now, regardless of what it was before (payment_failed
            // on a retry, rejected on a step-7 recovery). Without this, a
            // later webhook's OrderApprovalService::approve() guard (only
            // pending_review/awaiting_payment are approvable) would reject
            // a genuinely successful payment because the row was still
            // sitting on its OLD terminal status. Fixed here, at the single
            // choke point retryPayment(), switchToCard(), and
            // initiateCardPayment() all funnel through, not duplicated in each.
            'status' => Subscription::STATUS_AWAITING_PAYMENT,
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

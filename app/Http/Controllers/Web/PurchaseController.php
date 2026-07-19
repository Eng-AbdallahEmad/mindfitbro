<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\OrderPendingReviewMail;
use App\Mail\OrderReceivedMail;
use App\Models\Coupon;
use App\Models\FamilyInvitation;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Web\CurrencyService;
use App\Services\Web\SeasonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function __construct(
        private CurrencyService $currency,
        private SeasonService   $seasonService,
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

        $paymentInstructions = $this->currency->paymentInstructions();

        return view('app.web.purchase.form', compact(
            'plan', 'currency', 'durationMonths',
            'price3m', 'price6m', 'sPrice3m', 'sPrice6m',
            'activeSeason', 'paymentInstructions'
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

    // ── Step 2: Submit order ─────────────────────────────────────
    public function submit(Plan $plan, Request $request)
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

        $currency = $this->currency->current();

        $request->validate([
            'full_name'      => 'required|string|min:2|max:150',
            'email'          => 'required|email|max:255',
            'duration_months'=> 'required|integer|in:3,6',
            'coupon_code'    => 'nullable|string|max:50',
            'receipt'        => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
        ], [
            'full_name.required'       => 'الاسم الكامل مطلوب',
            'full_name.min'            => 'الاسم يجب أن يكون حرفين على الأقل',
            'email.required'           => 'البريد الإلكتروني مطلوب',
            'email.email'              => 'صيغة البريد الإلكتروني غير صحيحة',
            'duration_months.required' => 'مدة الاشتراك مطلوبة',
            'duration_months.in'       => 'مدة الاشتراك يجب أن تكون 3 أو 6 أشهر',
            'receipt.required'         => 'إيصال الدفع مطلوب',
            'receipt.file'             => 'الملف غير صحيح',
            'receipt.mimes'            => 'يُقبل فقط: صور (JPG, PNG) أو PDF',
            'receipt.max'              => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت',
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

        // Apply coupon on the integer post-season price
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
        // Round total to nearest integer — clean bank transfer amount
        $total = (float) round(max(0.0, $priceAfterSeason - $couponDiscount));

        // Store receipt in private disk (never publicly accessible)
        $receiptPath = $request->file('receipt')->storeAs(
            'receipts/' . now()->format('Y/m'),
            Str::uuid() . '.' . $request->file('receipt')->extension(),
            'local'
        );

        $subscription = DB::transaction(function () use (
            $request, $plan, $currency, $durationMonths,
            $subtotal, $activeSeason, $seasonDiscount,
            $couponDiscount, $couponCode, $total, $receiptPath, $coupon
        ) {
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
                'payment_method_key'         => config('payment.currency_to_method.' . $currency, 'sa_world'),
                'receipt_path'               => $receiptPath,
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

        session(['last_purchase_id' => $subscription->id]);

        $customerEmail = Auth::check() ? Auth::user()->email : $request->email;
        $customerName  = Auth::check() ? Auth::user()->name  : $request->full_name;

        try {
            Mail::to($customerEmail)->send(new OrderReceivedMail($subscription, $customerName));
        } catch (\Throwable $e) {
            Log::error('OrderReceivedMail failed', ['sub' => $subscription->id, 'err' => $e->getMessage()]);
        }

        try {
            $staff = User::whereIn('role', ['coach', 'admin'])
                ->where('status', '!=', 'banned')
                ->get();
            if ($staff->isNotEmpty()) {
                Mail::to($staff)->send(new OrderPendingReviewMail($subscription, $customerName, $customerEmail));
            }
        } catch (\Throwable $e) {
            Log::error('OrderPendingReviewMail failed', ['sub' => $subscription->id, 'err' => $e->getMessage()]);
        }

        return redirect()->route('purchase.success', $subscription->id);
    }

    // ── Step 3: Success page ─────────────────────────────────────
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

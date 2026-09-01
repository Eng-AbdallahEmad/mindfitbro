@extends('layouts.web.app')
@section('title', __('messages.purchase.page_title'))

@section('style')
<style>
.purchase-hero {
    background: linear-gradient(135deg, #174DAD 0%, #0f3a87 100%);
    min-height: 220px;
}
.plan-badge {
    display: inline-block;
    background: #D4ED57;
    color: #1C1C1C;
    font-size: 12px;
    font-weight: 900;
    padding: 4px 14px;
    border-radius: 20px;
}
.dur-btn {
    flex: 1;
    padding: 12px;
    border-radius: 12px;
    border: 2px solid #E5EAF3;
    background: #F4F7FF;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: all .2s;
    text-align: center;
    color: #6B7280;
}
.dur-btn.active {
    background: #174DAD;
    color: #fff;
    border-color: #174DAD;
    box-shadow: 0 4px 14px rgba(23,77,173,0.25);
}
.price-display {
    font-size: 36px;
    font-weight: 900;
    color: #174DAD;
    line-height: 1;
}
.price-strike {
    font-size: 18px;
    font-weight: 700;
    color: #9CA3AF;
    text-decoration: line-through;
    margin-inline-start: 8px;
}
.price-sub { font-size: 13px; color: #9CA3AF; margin-top: 4px; }
.form-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 6px;
}
.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid #E5EAF3;
    border-radius: 12px;
    font-size: 14px;
    color: #1C1C1C;
    background: #F9FAFB;
    transition: border-color .2s, box-shadow .2s;
    font-family: inherit;
}
.form-input:focus {
    outline: none;
    border-color: #174DAD;
    box-shadow: 0 0 0 3px rgba(23,77,173,0.12);
    background: #fff;
}
.form-input.has-error { border-color: #EF4444; }
.error-msg { font-size: 12px; color: #EF4444; margin-top: 4px; }
.warning-box {
    background: #FFF8E1;
    border: 1.5px solid #FFC107;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13px;
    color: #B45309;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
}
.coupon-row {
    display: flex;
    gap: 8px;
    align-items: stretch;
}
.coupon-row .form-input {
    flex: 1;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.coupon-btn {
    padding: 0 20px;
    background: #174DAD;
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    white-space: nowrap;
    transition: opacity .2s;
    font-family: inherit;
}
.coupon-btn:hover { opacity: .88; }
.coupon-btn:disabled { opacity: .6; cursor: not-allowed; }
.coupon-btn.remove-btn {
    background: #EF4444;
}
.coupon-applied-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ECFDF5;
    border: 1.5px solid #10B981;
    color: #065F46;
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 700;
    margin-top: 8px;
}
.discount-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    color: #10B981;
    font-weight: 700;
    margin-top: 6px;
    padding: 6px 4px;
}
.payment-card {
    background: #F4F7FF;
    border: 1.5px solid #E0E8FF;
    border-radius: 16px;
    padding: 20px 24px;
}
.payment-card-title {
    font-size: 13px;
    font-weight: 700;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 14px;
}
.payment-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #E5EAF3;
    font-size: 13px;
    color: #374151;
}
.payment-row:last-child { border-bottom: none; }
.payment-row .key { color: #9CA3AF; min-width: 110px; }
.payment-row .val { font-weight: 700; color: #1C1C1C; word-break: break-all; }
.upload-zone {
    border: 2px dashed #C7D7F5;
    border-radius: 14px;
    padding: 28px 20px;
    text-align: center;
    background: #F4F7FF;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    position: relative;
}
.upload-zone:hover, .upload-zone.dragging {
    border-color: #174DAD;
    background: #EBF0FF;
}
.upload-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
}
.file-preview-box {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #fff;
    border: 1.5px solid #174DAD;
    border-radius: 12px;
    padding: 12px 16px;
    margin-top: 12px;
}
.file-preview-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #E5EAF3; }
.file-preview-icon { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #F4F7FF; border-radius: 8px; font-size: 28px; }
.file-preview-name { font-size: 13px; font-weight: 700; color: #174DAD; word-break: break-all; }
.file-preview-size { font-size: 11px; color: #9CA3AF; margin-top: 2px; }
.submit-btn {
    width: 100%;
    padding: 16px;
    background: #D4ED57;
    color: #1C1C1C;
    font-size: 16px;
    font-weight: 900;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: opacity .2s, transform .2s;
    font-family: inherit;
    letter-spacing: 0.3px;
}
.submit-btn:hover { opacity: .92; transform: translateY(-1px); }
.submit-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }
</style>
@endsection

@section('content')
@php
    $isRtl    = app()->getLocale() === 'ar';
    $currency = session('currency', 'SAR');
    $symbol   = \App\Services\Web\CurrencyService::META[$currency]['symbol'] ?? 'ر.س';
    $dec      = \App\Services\Web\CurrencyService::META[$currency]['decimals'] ?? 0;
    $locale   = \App\Services\Web\CurrencyService::META[$currency]['locale'] ?? 'ar-SA';
    $isAuth   = Auth::check() ? 'true' : 'false';
    $sPct     = $activeSeason ? (float) $activeSeason->discount_percentage : 0;
    $pctStr   = $sPct > 0 ? rtrim(rtrim(number_format($sPct, 2), '0'), '.') : '';
    $manualSymbol = $manualMethod ? (\App\Services\Web\CurrencyService::META[$manualMethod['currency']]['symbol'] ?? '') : '';
@endphp

<div
    class="min-h-screen bg-[#F0F4FB] font-arabic"
    dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
    x-data="purchaseForm({{ $durationMonths }}, {{ $price3m }}, {{ $price6m }}, {{ $sPrice3m }}, {{ $sPrice6m }}, {{ $fxEffectiveRate }}, '{{ $fxRounding }}', {{ $manualMethod ? 'true' : 'false' }}, {{ $manualPrice3m ?? 'null' }}, {{ $manualPrice6m ?? 'null' }}, {{ $manualSPrice3m ?? 'null' }}, {{ $manualSPrice6m ?? 'null' }})"
>
    {{-- ── Hero Banner ── --}}
    <div class="purchase-hero flex flex-col items-center justify-center text-center px-6 py-12 relative">

        {{-- X Cancel Button ── --}}
        <a href="{{ route('home') }}"
           class="absolute top-4 end-4 w-10 h-10 rounded-full flex items-center justify-center text-white/80 hover:text-white hover:bg-white/20 transition-all"
           title="إلغاء والعودة للرئيسية">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </a>

        <span class="plan-badge mb-4">{{ $plan->name }}</span>
        <h1 class="text-3xl font-black text-white mb-2">{{ __('messages.purchase.hero_title') }}</h1>
        <p class="text-white/70 text-sm">{{ __('messages.purchase.hero_sub') }}</p>
    </div>

    @unless($paymobEnabled)
    <div class="max-w-2xl mx-auto px-4 -mt-4 relative z-50">
        <div class="warning-box" style="background:#fff;">
            <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1;flex-shrink:0">warning</span>
            <span>{{ __('messages.purchase.maintenance_notice') }}</span>
        </div>
    </div>
    @endunless

    {{-- ── Main Card ── --}}
    <div class="max-w-2xl mx-auto px-4 -mt-8 pb-20 relative z-50">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

            {{-- Duration + Price ── --}}
            <div class="p-8 border-b border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('messages.purchase.choose_duration') }}</p>

                <div class="flex gap-3 mb-6">
                    <button type="button" class="dur-btn" :class="{ active: months === 3 }" @click="months = 3">
                        {{ __('messages.programs.duration_3months') }}
                    </button>
                    <button type="button" class="dur-btn" :class="{ active: months === 6 }" @click="months = 6">
                        {{ __('messages.programs.duration_6months') }}
                    </button>
                </div>

                @if($activeSeason)
                <div class="mb-3">
                    <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 border border-red-200 rounded-full px-3 py-1.5 text-xs font-black">
                        <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 1">local_offer</span>
                        {{ $activeSeason->localName() }} — {{ $pctStr }}%
                    </span>
                </div>
                @endif
                <div class="flex items-end gap-3">
                    <div>
                        <div class="price-display" style="display:flex;align-items:baseline;gap:6px;">
                            <span x-text="finalPriceFormatted"></span>
                            <span class="text-lg text-gray-400 font-bold">{{ $symbol }}</span>
                            <span class="price-strike" x-show="showStrikethrough" x-text="rawPriceFormatted + ' {{ $symbol }}'"></span>
                        </div>
                        @if($activeSeason)
                        <div class="discount-row" style="color:#DC2626;font-size:12px;padding:4px 0 0;margin-top:4px;" x-show="hasSeason && currentSeasonDiscount > 0">
                            <span>{{ $activeSeason->localName() }} — {{ __('messages.purchase.season_discount_label') }} ({{ $pctStr }}%)</span>
                            <span x-text="'− ' + formatAmount(currentSeasonDiscount)"></span>
                        </div>
                        @endif
                        <div class="price-sub" x-text="months === 3 ? '{{ __('messages.programs.duration_3months') }}' : '{{ __('messages.programs.duration_6months') }}'"></div>
                    </div>
                </div>
            </div>

            {{-- ── Purchase Form ── --}}
            <form
                action="{{ route('purchase.submit', $plan) }}"
                method="POST"
                enctype="multipart/form-data"
                class="p-8 space-y-6"
                @submit.prevent="handleSubmit"
                id="purchase-form"
            >
                @csrf
                <input type="hidden" name="duration_months" :value="months">
                <input type="hidden" name="coupon_code" :value="couponApplied">
                <input type="hidden" name="expected_season_id" value="{{ $activeSeason?->id ?? '' }}">
                <input type="hidden" name="payment_method" :value="paymentMethod">

                @if(session('info'))
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 text-sm font-bold">
                    <span class="material-symbols-rounded flex-shrink-0" style="font-size:18px;font-variation-settings:'FILL' 1">info</span>
                    <span>{{ session('info') }}</span>
                </div>
                @endif

                {{-- Customer Info ── --}}
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">{{ __('messages.purchase.your_info') }}</p>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label" for="full_name">{{ __('messages.purchase.full_name') }} <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                class="form-input {{ $errors->has('full_name') ? 'has-error' : '' }}"
                                value="{{ old('full_name', Auth::user()?->name) }}"
                                placeholder="{{ __('messages.purchase.full_name_placeholder') }}"
                                required
                                {{ Auth::check() ? 'readonly' : '' }}
                            >
                            @error('full_name')<p class="error-msg">{{ $message }}</p>@enderror
                        </div>

                        {{-- Email with live duplicate check (guests only) ── --}}
                        <div>
                            <label class="form-label" for="email">{{ __('messages.purchase.email') }} <span class="text-red-500">*</span></label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input {{ $errors->has('email') ? 'has-error' : '' }}"
                                :class="{ 'has-error': emailExists }"
                                value="{{ old('email', Auth::user()?->email) }}"
                                placeholder="{{ __('messages.purchase.email_placeholder') }}"
                                required
                                {{ Auth::check() ? 'readonly' : '' }}
                                dir="ltr"
                                @blur="!{{ $isAuth }} && $event.target.value ? checkEmail($event.target.value) : null"
                            >
                            @error('email')<p class="error-msg">{{ $message }}</p>@enderror

                            {{-- Email already registered warning ── --}}
                            <div class="warning-box" x-show="emailExists" x-cloak>
                                <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1;flex-shrink:0">warning</span>
                                <span>البريد الإلكتروني الذي قمت بإدخاله موجود بالفعل، يرجى
                                    <a href="{{ route('login') }}" class="underline font-bold hover:opacity-80">تسجيل دخولك</a>
                                    للمتابعة.
                                </span>
                            </div>
                        </div>

                        {{-- Phone (required — sent to Paymob as billing_data.phone_number) ── --}}
                        <div class="sm:col-span-2">
                            <label class="form-label" for="phone">{{ __('messages.purchase.phone') }} <span class="text-red-500">*</span></label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                class="form-input {{ $errors->has('phone') ? 'has-error' : '' }}"
                                value="{{ old('phone') }}"
                                placeholder="{{ __('messages.purchase.phone_placeholder') }}"
                                required
                                dir="ltr"
                            >
                            @error('phone')<p class="error-msg">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- ── Coupon / Discount ── --}}
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">كوبون الخصم (اختياري)</p>

                    <div x-show="!couponApplied">
                        <div class="coupon-row">
                            <input
                                type="text"
                                class="form-input"
                                placeholder="أدخل كود الخصم"
                                x-model="couponCode"
                                @keydown.enter.prevent="applyCoupon"
                                maxlength="50"
                                dir="ltr"
                                style="text-transform:uppercase;letter-spacing:1px;"
                            >
                            <button
                                type="button"
                                class="coupon-btn"
                                @click="applyCoupon"
                                :disabled="couponChecking || !couponCode.trim()"
                            >
                                <span x-show="!couponChecking">تطبيق</span>
                                <span x-show="couponChecking" x-cloak>...</span>
                            </button>
                        </div>
                        <p class="error-msg" x-show="couponError" x-text="couponError" x-cloak></p>
                    </div>

                    <div x-show="couponApplied" x-cloak>
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                            <div class="coupon-applied-chip">
                                <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                                <span x-text="couponApplied"></span>
                            </div>
                            <button type="button" class="coupon-btn remove-btn" @click="removeCoupon" style="padding:6px 14px;font-size:12px;">
                                إزالة الكوبون
                            </button>
                        </div>
                        <div class="discount-row">
                            <span>وفّرت</span>
                            <span x-text="'− ' + currentDiscount.toLocaleString('ar', { minimumFractionDigits: {{ $dec }}, maximumFractionDigits: {{ $dec }} }) + ' {{ $symbol }}'"></span>
                        </div>
                    </div>
                </div>

                {{-- ── Inline Login (guests only) ── --}}
                @guest
                <div>
                    {{-- Divider with toggle ── --}}
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <button
                            type="button"
                            class="text-sm font-bold text-[#174DAD] whitespace-nowrap hover:underline focus:outline-none flex items-center gap-1"
                            @click="showLogin = !showLogin"
                        >
                            <span x-text="showLogin ? 'إخفاء' : 'أو سجّل دخولك'"></span>
                            <svg x-show="!showLogin" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            <svg x-show="showLogin" x-cloak xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                        </button>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    {{-- Login Card ── --}}
                    <div
                        x-show="showLogin"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="mt-4 bg-[#F4F7FF] border border-[#D0DCFF] rounded-2xl p-5 space-y-4"
                    >
                        <p class="text-sm font-bold text-gray-600 text-center">سجّل الدخول لإكمال طلبك</p>

                        <div>
                            <label class="form-label" for="login_username">اسم المستخدم</label>
                            <input
                                type="text"
                                id="login_username"
                                class="form-input"
                                :class="{ 'has-error': loginError }"
                                x-model="loginUsername"
                                placeholder="username"
                                autocomplete="username"
                                @keydown.enter.prevent="doLogin"
                            >
                        </div>

                        <div>
                            <label class="form-label" for="login_pass">كلمة المرور</label>
                            <div style="position:relative;">
                                <input
                                    :type="showLoginPw ? 'text' : 'password'"
                                    id="login_pass"
                                    class="form-input"
                                    :class="{ 'has-error': loginError }"
                                    x-model="loginPassword"
                                    placeholder="••••••••"
                                    autocomplete="current-password"
                                    @keydown.enter.prevent="doLogin"
                                    style="padding-inline-end:44px;"
                                >
                                <button
                                    type="button"
                                    @click="showLoginPw = !showLoginPw"
                                    class="absolute inset-y-0 end-3 flex items-center text-gray-400 hover:text-gray-600"
                                    tabindex="-1"
                                >
                                    <svg x-show="!showLoginPw" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg x-show="showLoginPw" x-cloak xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                        </div>

                        <p class="error-msg text-center" x-show="loginError" x-text="loginError" x-cloak></p>

                        <button
                            type="button"
                            class="submit-btn"
                            style="background:#174DAD;color:#fff;padding:12px;"
                            @click="doLogin"
                            :disabled="loginLoading"
                        >
                            <span x-show="!loginLoading">دخول وإكمال الطلب</span>
                            <span x-show="loginLoading" x-cloak>جاري الدخول...</span>
                        </button>

                        <p class="text-center text-xs text-gray-400">
                            ليس لديك حساب؟
                            <a href="{{ route('register') }}" class="text-[#174DAD] font-bold hover:underline">سجّل الآن</a>
                        </p>
                    </div>
                </div>
                @endguest

                {{-- ── Payment method — card is always first/default; manual only
                     appears at all when the visitor's DETECTED country is eligible
                     (never a self-service currency switch — docs/dual-payment-plan.md A5) ── --}}
                @if($manualMethod)
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('messages.purchase.pay_via') ?: 'طريقة الدفع' }}</p>
                    <div class="flex gap-3">
                        <button type="button" class="dur-btn" :class="{ active: paymentMethod === 'card' }" @click="paymentMethod = 'card'">
                            بطاقة / محفظة إلكترونية
                        </button>
                        <button type="button" class="dur-btn" :class="{ active: paymentMethod === 'manual' }" @click="paymentMethod = 'manual'">
                            تحويل بنكي محلي
                        </button>
                    </div>
                </div>
                @endif

                {{-- ── Card path: EGP Charge Summary — pre-redirect notice (decision D4) ── --}}
                <div x-show="paymentMethod === 'card'" @if($manualMethod) x-cloak @endif>
                    @if($fxRateConfigured)
                    <div class="payment-card">
                        <div class="payment-card-title">{{ __('messages.purchase.egp_charge_title') }}</div>
                        <div class="payment-row" style="background:#EBF0FF;border-radius:10px;padding:10px 12px;">
                            <span class="key font-bold text-[#174DAD]">{{ __('messages.purchase.amount_to_pay') }}</span>
                            <span class="val text-[#174DAD] text-base" dir="ltr" x-text="egpAmountFormatted + ' ' + '{{ __('messages.purchase.egp_symbol') }}'"></span>
                        </div>
                        <p style="font-size:11px;color:#9ca3af;margin-top:10px;line-height:1.6;">
                            {{ __('messages.purchase.egp_charge_notice') }}
                        </p>
                    </div>
                    @else
                    <div class="warning-box" style="margin-top:0;">
                        <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1;flex-shrink:0">warning</span>
                        <span>{{ __('messages.purchase.currency_unavailable_notice') }}</span>
                    </div>
                    @endif
                </div>

                {{-- ── Manual path: bank/InstaPay details + receipt upload ── --}}
                @if($manualMethod)
                <div x-show="paymentMethod === 'manual'" x-cloak>
                    <x-web.payment-instructions
                        :method="$manualMethod"
                        :currency="$manualMethod['currency']"
                        :total="$manualPrice3m ?? 0"
                        liveAmountExpr="manualFinalPriceFormatted"
                    />

                    <div>
                        <label class="form-label">{{ __('messages.purchase.upload_receipt') }} <span class="text-red-500">*</span></label>
                        <div class="upload-zone" :class="{ dragging: isDragging }"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="handleFileDrop($event)"
                             x-show="!selectedFile"
                        >
                            <input type="file" name="receipt" x-ref="receiptInput"
                                   accept=".jpg,.jpeg,.png,.gif,.pdf"
                                   :required="paymentMethod === 'manual'"
                                   @change="handleFileSelect($event)">
                            <span class="material-symbols-rounded" style="font-size:32px;color:#174DAD;display:block;margin-bottom:8px">cloud_upload</span>
                            <p class="text-sm font-bold text-gray-600">{{ __('messages.purchase.upload_hint') }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ __('messages.purchase.upload_formats') }}</p>
                        </div>

                        <template x-if="selectedFile">
                            <div class="file-preview-box">
                                <div class="file-preview-icon">
                                    <span class="material-symbols-rounded" style="font-size:26px;color:#174DAD">description</span>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <p class="file-preview-name" x-text="selectedFile?.name"></p>
                                    <p class="file-preview-size" x-text="selectedFileSizeFormatted"></p>
                                </div>
                                <button type="button" @click="clearFile" class="text-gray-400 hover:text-red-500" title="إزالة الملف">
                                    <span class="material-symbols-rounded" style="font-size:20px">close</span>
                                </button>
                            </div>
                        </template>
                        @error('receipt')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                </div>
                @endif

                {{-- Submit ── --}}
                <div class="pt-2">
                    <button type="submit" class="submit-btn"
                        :disabled="submitting
                            || (paymentMethod === 'card' && {{ ($fxRateConfigured && $paymobEnabled) ? 'false' : 'true' }})
                            || (paymentMethod === 'manual' && !selectedFile)">
                        <span x-show="!submitting" x-text="paymentMethod === 'manual' ? 'إرسال الطلب' : '{{ __('messages.purchase.submit_btn') }}'"></span>
                        <span x-show="submitting" x-cloak>{{ __('messages.purchase.submitting') }}</span>
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-3" x-text="paymentMethod === 'manual' ? 'سيراجع فريقنا طلبك بعد إرسال الإيصال' : '{{ __('messages.purchase.secure_notice') }}'"></p>
                </div>

            </form>
        </div>
    </div>
</div>

@section('script')
<script>
function purchaseForm(initialMonths, price3m, price6m, sPrice3m, sPrice6m, fxEffectiveRate, fxRounding, manualAvailable, manualPrice3m, manualPrice6m, manualSPrice3m, manualSPrice6m) {
    return {
        months: initialMonths,
        _price3m: price3m,
        _price6m: price6m,
        _sPrice3m: sPrice3m,
        _sPrice6m: sPrice6m,
        _fxRate: fxEffectiveRate,
        _fxRounding: fxRounding,
        hasSeason: sPrice3m < price3m || sPrice6m < price6m,

        // ── Payment method (card is always the default) ───────────
        paymentMethod: 'card',
        manualAvailable: manualAvailable,
        _manualPrice3m: manualPrice3m,
        _manualPrice6m: manualPrice6m,
        _manualSPrice3m: manualSPrice3m,
        _manualSPrice6m: manualSPrice6m,
        manualDiscount3m: 0,
        manualDiscount6m: 0,
        selectedFile: null,
        isDragging: false,

        submitting: false,

        // Email check (guests only)
        emailChecking: false,
        emailExists: false,

        // Inline login (guests only)
        showLogin: false,
        loginUsername: '',
        loginPassword: '',
        showLoginPw: false,
        loginError: '',
        loginLoading: false,

        // Coupon
        couponCode: '',
        couponApplied: '',
        couponChecking: false,
        couponError: '',
        discount3m: 0,
        discount6m: 0,

        // ── Computed prices ──────────────────────────────────────
        get rawPrice() {
            return this.months === 3 ? this._price3m : this._price6m;
        },
        get basePrice() {
            return this.months === 3 ? this._sPrice3m : this._sPrice6m;
        },
        get currentSeasonDiscount() {
            return Math.max(0, this.rawPrice - this.basePrice);
        },
        get currentDiscount() {
            return this.months === 3 ? this.discount3m : this.discount6m;
        },
        get finalPrice() {
            return Math.round(Math.max(0, this.basePrice - this.currentDiscount));
        },
        get showStrikethrough() {
            return this.hasSeason || this.currentDiscount > 0;
        },
        get finalPriceFormatted() {
            return this._smartFmt(this.finalPrice);
        },
        get rawPriceFormatted() {
            return Math.round(this.rawPrice).toLocaleString('{{ $locale }}', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        },
        formatAmount(n) {
            return this._smartFmt(n) + ' {{ $symbol }}';
        },
        _smartFmt(n) {
            return Math.round(n).toLocaleString('{{ $locale }}', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        },

        // ── Manual-transfer price — its OWN currency, independent of the
        // display currency above (docs/dual-payment-plan.md A5) ─────
        get manualBasePrice() {
            if (!this.manualAvailable) return 0;
            return this.months === 3 ? this._manualSPrice3m : this._manualSPrice6m;
        },
        get manualCurrentDiscount() {
            return this.months === 3 ? this.manualDiscount3m : this.manualDiscount6m;
        },
        get manualFinalPrice() {
            return Math.round(Math.max(0, this.manualBasePrice - this.manualCurrentDiscount));
        },
        get manualFinalPriceFormatted() {
            return this._smartFmt(this.manualFinalPrice);
        },

        // ── Receipt upload (manual path) ─────────────────────────
        get selectedFileSizeFormatted() {
            if (!this.selectedFile) return '';
            const kb = this.selectedFile.size / 1024;
            return kb > 1024 ? (kb / 1024).toFixed(1) + ' MB' : Math.round(kb) + ' KB';
        },
        handleFileSelect(e) {
            this.selectedFile = e.target.files[0] || null;
        },
        handleFileDrop(e) {
            this.isDragging = false;
            const file = e.dataTransfer.files[0];
            if (file) {
                this.$refs.receiptInput.files = e.dataTransfer.files;
                this.selectedFile = file;
            }
        },
        clearFile() {
            this.selectedFile = null;
            this.$refs.receiptInput.value = '';
        },

        // ── EGP charge estimate (display only) ────────────────────
        // Mirrors App\Services\FxConverter's formula for the pre-redirect
        // summary. The actual charge is always computed authoritatively by
        // FxConverter server-side at submission — this never feeds back
        // into what gets persisted or charged, it only shows the customer
        // what to expect before they submit.
        _applyRounding(n) {
            if (this._fxRounding === 'up_to_nearest_5') return Math.ceil(n / 5) * 5;
            if (this._fxRounding === 'up_to_nearest_10') return Math.ceil(n / 10) * 10;
            return n;
        },
        get egpAmount() {
            return this._applyRounding(this.finalPrice * this._fxRate);
        },
        get egpAmountFormatted() {
            return Math.round(this.egpAmount).toLocaleString('ar-EG', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        },

        // ── Email duplicate check ────────────────────────────────
        async checkEmail(email) {
            if (!email) return;
            this.emailChecking = true;
            this.emailExists   = false;
            try {
                const resp = await fetch('{{ route('purchase.check-email') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email }),
                });
                const data = await resp.json();
                this.emailExists = data.exists === true;
            } catch (_) {}
            this.emailChecking = false;
        },

        // ── Inline login ─────────────────────────────────────────
        async doLogin() {
            if (!this.loginUsername || !this.loginPassword) {
                this.loginError = 'يرجى إدخال اسم المستخدم وكلمة المرور';
                return;
            }
            this.loginLoading = true;
            this.loginError   = '';
            try {
                const resp = await fetch('{{ route('login.post') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ username: this.loginUsername, password: this.loginPassword }),
                });
                if (resp.ok) {
                    window.location.reload();
                    return;
                }
                const data = await resp.json();
                const firstErrors = Object.values(data.errors || {});
                this.loginError = firstErrors.length
                    ? (Array.isArray(firstErrors[0]) ? firstErrors[0][0] : firstErrors[0])
                    : (data.message || 'بيانات الدخول غير صحيحة');
            } catch (_) {
                this.loginError = 'حدث خطأ، يرجى المحاولة مرة أخرى';
            }
            this.loginLoading = false;
        },

        // ── Coupon apply / remove ─────────────────────────────────
        async applyCoupon() {
            const code = this.couponCode.trim();
            if (!code) return;
            this.couponChecking = true;
            this.couponError    = '';
            try {
                const resp = await fetch('{{ route('purchase.check-coupon') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        code,
                        plan_id: {{ $plan->id }},
                        duration_months: this.months,
                    }),
                });
                const data = await resp.json();
                if (data.valid) {
                    this.couponApplied = code.toUpperCase();
                    this.discount3m    = data.discount3m;
                    this.discount6m    = data.discount6m;
                    this.manualDiscount3m = data.manualDiscount3m || 0;
                    this.manualDiscount6m = data.manualDiscount6m || 0;
                    // Sync season prices from server (handles time-edge: season expired/started mid-session)
                    if (data.season) {
                        this._sPrice3m = data.season.sPrice3m;
                        this._sPrice6m = data.season.sPrice6m;
                        this.hasSeason = true;
                        if (this.manualAvailable && data.season.manualSPrice3m != null) {
                            this._manualSPrice3m = data.season.manualSPrice3m;
                            this._manualSPrice6m = data.season.manualSPrice6m;
                        }
                    } else {
                        this._sPrice3m = this._price3m;
                        this._sPrice6m = this._price6m;
                        this.hasSeason = false;
                        if (this.manualAvailable) {
                            this._manualSPrice3m = this._manualPrice3m;
                            this._manualSPrice6m = this._manualPrice6m;
                        }
                    }
                    this.couponError = '';
                } else {
                    this.couponError = data.message || 'الكوبون غير صالح أو منتهي الصلاحية';
                    this.discount3m  = 0;
                    this.discount6m  = 0;
                    this.manualDiscount3m = 0;
                    this.manualDiscount6m = 0;
                }
            } catch (_) {
                this.couponError = 'حدث خطأ، يرجى المحاولة مرة أخرى';
            }
            this.couponChecking = false;
        },

        removeCoupon() {
            this.couponApplied = '';
            this.couponCode    = '';
            this.discount3m    = 0;
            this.discount6m    = 0;
            this.manualDiscount3m = 0;
            this.manualDiscount6m = 0;
            this.couponError   = '';
        },

        handleSubmit() {
            this.submitting = true;
            document.getElementById('purchase-form').submit();
        },
    };
}
</script>
@endsection
@endsection

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
    $isAuth   = Auth::check() ? 'true' : 'false';
@endphp

<div
    class="min-h-screen bg-[#F0F4FB] font-arabic"
    dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
    x-data="purchaseForm({{ $durationMonths }}, {{ $price3m }}, {{ $price6m }})"
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

                <div class="flex items-end gap-3">
                    <div>
                        <div class="price-display" style="display:flex;align-items:baseline;gap:6px;">
                            <span x-text="finalPriceFormatted"></span>
                            <span class="text-lg text-gray-400 font-bold">{{ $symbol }}</span>
                            <span class="price-strike" x-show="currentDiscount > 0" x-text="rawPriceFormatted + ' {{ $symbol }}'"></span>
                        </div>
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

                {{-- Payment Instructions ── --}}
                @if(!empty($paymentInstructions))
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">{{ __('messages.purchase.payment_instructions') }}</p>
                    <div class="payment-card">
                        <div class="payment-card-title">{{ $paymentInstructions['country_label'] ?? '' }}</div>

                        @if(($paymentInstructions['type'] ?? '') === 'instapay')
                            @isset($paymentInstructions['instapay_id'])
                            <div class="payment-row">
                                <span class="key">InstaPay ID</span>
                                <span class="val">{{ $paymentInstructions['instapay_id'] }}</span>
                            </div>
                            @endisset
                            @isset($paymentInstructions['phone'])
                            <div class="payment-row">
                                <span class="key">{{ __('messages.purchase.phone') }}</span>
                                <span class="val" dir="ltr">{{ $paymentInstructions['phone'] }}</span>
                            </div>
                            @endisset
                            @isset($paymentInstructions['link'])
                            <div class="payment-row">
                                <span class="key">{{ __('messages.purchase.pay_via') }}</span>
                                <a href="{{ $paymentInstructions['link'] }}" target="_blank" rel="noopener" class="val text-[#174DAD] underline">
                                    {{ __('messages.purchase.pay_link') }}
                                </a>
                            </div>
                            @endisset
                        @else
                            @isset($paymentInstructions['bank_name'])
                            <div class="payment-row">
                                <span class="key">{{ __('messages.purchase.bank') }}</span>
                                <span class="val">{{ $paymentInstructions['bank_name'] }}</span>
                            </div>
                            @endisset
                            @isset($paymentInstructions['account_name'])
                            <div class="payment-row">
                                <span class="key">{{ __('messages.purchase.account_name') }}</span>
                                <span class="val">{{ $paymentInstructions['account_name'] }}</span>
                            </div>
                            @endisset
                            @isset($paymentInstructions['account_number'])
                            <div class="payment-row">
                                <span class="key">{{ __('messages.purchase.account_number') }}</span>
                                <span class="val" dir="ltr">{{ $paymentInstructions['account_number'] }}</span>
                            </div>
                            @endisset
                            @isset($paymentInstructions['iban'])
                            <div class="payment-row">
                                <span class="key">IBAN</span>
                                <span class="val" dir="ltr">{{ $paymentInstructions['iban'] }}</span>
                            </div>
                            @endisset
                            @isset($paymentInstructions['rib'])
                            <div class="payment-row">
                                <span class="key">RIB</span>
                                <span class="val" dir="ltr">{{ $paymentInstructions['rib'] }}</span>
                            </div>
                            @endisset
                        @endif

                        {{-- Amount reflects final price after coupon ── --}}
                        <div class="payment-row" style="background:#EBF0FF;border-radius:10px;margin-top:10px;padding:10px 12px;">
                            <span class="key font-bold text-[#174DAD]">{{ __('messages.purchase.amount_to_pay') }}</span>
                            <span class="val text-[#174DAD] text-base" x-text="finalPriceFormatted + ' {{ $symbol }}'"></span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Receipt Upload ── --}}
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">{{ __('messages.purchase.upload_receipt') }}</p>

                    <div
                        class="upload-zone"
                        :class="{ dragging: isDragging }"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop($event)"
                    >
                        <input
                            type="file"
                            name="receipt"
                            id="receipt-input"
                            accept=".jpg,.jpeg,.png,.gif,.pdf"
                            @change="handleFile($event)"
                            required
                        >
                        <div x-show="!previewFile">
                            <div class="mb-3"><span class="material-symbols-rounded text-gray-400" style="font-size:40px">attach_file</span></div>
                            <p class="font-bold text-gray-600 text-sm mb-1">{{ __('messages.purchase.upload_hint') }}</p>
                            <p class="text-gray-400 text-xs">{{ __('messages.purchase.upload_formats') }}</p>
                        </div>
                        <div x-show="previewFile" x-cloak>
                            <div class="mb-2"><span class="material-symbols-rounded text-green-500" style="font-size:32px;font-variation-settings:'FILL' 1">check_circle</span></div>
                            <p class="font-bold text-green-600 text-sm">{{ __('messages.purchase.file_selected') }}</p>
                        </div>
                    </div>

                    {{-- File Preview ── --}}
                    <div x-show="previewFile" x-cloak class="file-preview-box mt-3">
                        <template x-if="previewUrl && !isPdf">
                            <img :src="previewUrl" class="file-preview-img" alt="preview">
                        </template>
                        <template x-if="isPdf || !previewUrl">
                            <div class="file-preview-icon"><span class="material-symbols-rounded text-gray-400" style="font-size:36px;font-variation-settings:'FILL' 1">description</span></div>
                        </template>
                        <div>
                            <div class="file-preview-name" x-text="fileName"></div>
                            <div class="file-preview-size" x-text="fileSize"></div>
                        </div>
                        <button type="button" @click="clearFile" class="ms-auto text-gray-400 hover:text-red-500 text-xl leading-none">&times;</button>
                    </div>

                    @error('receipt')<p class="error-msg mt-2">{{ $message }}</p>@enderror
                </div>

                {{-- Submit ── --}}
                <div class="pt-2">
                    <button type="submit" class="submit-btn" :disabled="submitting">
                        <span x-show="!submitting">{{ __('messages.purchase.submit_btn') }}</span>
                        <span x-show="submitting" x-cloak>{{ __('messages.purchase.submitting') }}</span>
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-3">{{ __('messages.purchase.secure_notice') }}</p>
                </div>

            </form>
        </div>
    </div>
</div>

@section('script')
<script>
function purchaseForm(initialMonths, price3m, price6m) {
    return {
        months: initialMonths,
        _price3m: price3m,
        _price6m: price6m,

        // File upload
        isDragging: false,
        previewFile: false,
        previewUrl: null,
        isPdf: false,
        fileName: '',
        fileSize: '',
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
        get currentDiscount() {
            return this.months === 3 ? this.discount3m : this.discount6m;
        },
        get finalPrice() {
            return Math.max(0, this.rawPrice - this.currentDiscount);
        },
        get finalPriceFormatted() {
            return this.finalPrice.toLocaleString('{{ app()->getLocale() === 'ar' ? 'ar-SA' : 'en-US' }}', {
                minimumFractionDigits: {{ $dec }},
                maximumFractionDigits: {{ $dec }}
            });
        },
        get rawPriceFormatted() {
            return this.rawPrice.toLocaleString('{{ app()->getLocale() === 'ar' ? 'ar-SA' : 'en-US' }}', {
                minimumFractionDigits: {{ $dec }},
                maximumFractionDigits: {{ $dec }}
            });
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
                    body: JSON.stringify({ code, price3m: this._price3m, price6m: this._price6m }),
                });
                const data = await resp.json();
                if (data.valid) {
                    this.couponApplied = code.toUpperCase();
                    this.discount3m    = data.discount3m;
                    this.discount6m    = data.discount6m;
                    this.couponError   = '';
                } else {
                    this.couponError = data.message || 'الكوبون غير صالح أو منتهي الصلاحية';
                    this.discount3m  = 0;
                    this.discount6m  = 0;
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
            this.couponError   = '';
        },

        // ── File upload ──────────────────────────────────────────
        handleFile(event) {
            const file = event.target.files[0];
            if (file) this.setFile(file);
        },

        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (!file) return;
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('receipt-input').files = dt.files;
            this.setFile(file);
        },

        setFile(file) {
            this.previewFile = true;
            this.fileName    = file.name;
            this.fileSize    = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            this.isPdf       = file.type === 'application/pdf';
            if (!this.isPdf) {
                const reader = new FileReader();
                reader.onload = (e) => { this.previewUrl = e.target.result; };
                reader.readAsDataURL(file);
            } else {
                this.previewUrl = null;
            }
        },

        clearFile() {
            this.previewFile = false;
            this.previewUrl  = null;
            this.isPdf       = false;
            this.fileName    = '';
            this.fileSize    = '';
            document.getElementById('receipt-input').value = '';
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

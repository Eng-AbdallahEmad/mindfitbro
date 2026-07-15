@extends('layouts.web.app')
@section('title', __('messages.purchase.success_title'))

@section('style')
<style>
.success-hero {
    background: linear-gradient(135deg, #174DAD 0%, #0f3a87 100%);
    padding: 52px 36px 64px;
    text-align: center;
}
.check-circle {
    width: 72px;
    height: 72px;
    background: #D4ED57;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 0 0 10px rgba(212,237,87,0.15), 0 0 0 20px rgba(212,237,87,0.07);
}
.success-badge {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff;
    font-size: 12px;
    font-weight: 900;
    padding: 5px 16px;
    border-radius: 20px;
    letter-spacing: 0.5px;
    margin-bottom: 14px;
}
.order-card {
    margin: -28px 24px 0;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(23,77,173,0.12);
    padding: 22px 24px;
    position: relative;
    z-index: 2;
}
.info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #F0F4FB;
    font-size: 14px;
}
.info-row:last-child { border-bottom: none; }
.info-row .label { color: #9CA3AF; font-weight: 600; }
.info-row .value { font-weight: 800; color: #1C1C1C; }
.info-row .value.blue { color: #174DAD; }
.info-row .value.green { color: #10B981; }
.total-row {
    background: #F4F7FF;
    border-radius: 14px;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 14px;
}
.total-row .total-label { font-size: 13px; font-weight: 700; color: #6B7280; }
.total-row .total-amount { font-size: 24px; font-weight: 900; color: #174DAD; }
.step-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid #F0F4FB;
}
.step-item:last-child { border-bottom: none; }
.step-num {
    width: 32px;
    height: 32px;
    background: #174DAD;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 900;
    flex-shrink: 0;
    margin-top: 1px;
}
.step-text { font-size: 13px; color: #374151; line-height: 1.6; }
.step-text strong { font-weight: 800; color: #1C1C1C; display: block; margin-bottom: 2px; }
.cta-btn {
    display: block;
    width: 100%;
    padding: 15px;
    background: #174DAD;
    color: #fff;
    font-size: 15px;
    font-weight: 900;
    border-radius: 14px;
    text-align: center;
    text-decoration: none;
    transition: opacity .2s;
    font-family: inherit;
}
.cta-btn:hover { opacity: .9; }
.guest-notice {
    background: #F0FDF4;
    border: 1.5px solid #BBF7D0;
    border-radius: 16px;
    padding: 16px 20px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 13px;
    color: #065F46;
    line-height: 1.7;
}
</style>
@endsection

@section('content')
@php
    $isRtl    = app()->getLocale() === 'ar';
    $currency = $subscription->currency ?? 'SAR';
    $symbol   = \App\Services\Web\CurrencyService::META[$currency]['symbol'] ?? 'ر.س';
    $dec      = \App\Services\Web\CurrencyService::META[$currency]['decimals'] ?? 0;
    $hasDiscount = $subscription->coupon_discount > 0;
@endphp

<div
    class="min-h-screen bg-[#EEF2FB] font-arabic py-10 px-4"
    dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
>
    <div class="w-full max-w-md mx-auto">

        {{-- ── Main Card ── --}}
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

            {{-- Hero ── --}}
            <div class="success-hero">
                <div class="check-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24"
                         fill="none" stroke="#1C1C1C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div class="success-badge">{{ __('messages.purchase.success_badge') }}</div>
                <h1 class="text-2xl font-black text-white mb-2">{{ __('messages.purchase.success_heading') }}</h1>
                <p class="text-white/65 text-sm">{{ __('messages.purchase.success_sub') }}</p>
            </div>

            {{-- Order Summary Card (floating over hero) ── --}}
            <div class="order-card">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">تفاصيل الطلب</p>

                <div class="info-row">
                    <span class="label">رقم الطلب</span>
                    <span class="value blue">#{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="info-row">
                    <span class="label">الباقة</span>
                    <span class="value">{{ $subscription->plan?->name ?? '—' }}</span>
                </div>

                <div class="info-row">
                    <span class="label">المدة</span>
                    <span class="value">{{ $subscription->duration_months }} {{ __('messages.purchase.months') }}</span>
                </div>

                @if($hasDiscount)
                <div class="info-row">
                    <span class="label">السعر الأصلي</span>
                    <span class="value" style="text-decoration:line-through;color:#9CA3AF;">
                        {{ number_format($subscription->subtotal, $dec) }} {{ $symbol }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">الخصم ({{ $subscription->coupon_code }})</span>
                    <span class="value green">− {{ number_format($subscription->coupon_discount, $dec) }} {{ $symbol }}</span>
                </div>
                @endif

                <div class="total-row">
                    <span class="total-label">المبلغ الإجمالي</span>
                    <span class="total-amount">{{ number_format($subscription->total, $dec) }} {{ $symbol }}</span>
                </div>
            </div>

            {{-- Body ── --}}
            <div class="px-6 pt-6 pb-8 space-y-5">

                {{-- What happens next ── --}}
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">ماذا سيحدث بعد ذلك؟</p>
                    <div class="bg-[#F8FAFF] border border-[#E0E8FF] rounded-2xl px-4 py-2">
                        <div class="step-item">
                            <div class="step-num">١</div>
                            <div class="step-text">
                                <strong>مراجعة الإيصال</strong>
                                سيقوم فريق MindFitBro بمراجعة إيصال الدفع الخاص بك.
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-num">٢</div>
                            <div class="step-text">
                                <strong>تفعيل الاشتراك</strong>
                                بعد التأكيد ستصلك رسالة بريد إلكتروني بتفعيل اشتراكك.
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-num">٣</div>
                            <div class="step-text">
                                <strong>ابدأ رحلتك</strong>
                                ادخل لحسابك وابدأ برنامجك التدريبي مباشرةً.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CTA ── --}}
                @if(! $subscription->user_id)
                    {{-- Guest ── --}}
                    <div class="guest-notice">
                        <span class="material-symbols-rounded" style="font-size:22px;flex-shrink:0;font-variation-settings:'FILL' 1">mail</span>
                        <span>
                            ستصلك رسالة بريد إلكتروني بمجرد مراجعة إيصالك وتفعيل اشتراكك —
                            تتضمن رابطاً لإعداد كلمة المرور والدخول لحسابك.
                        </span>
                    </div>
                @else
                    <a href="{{ route('dashboard') }}" class="cta-btn">
                        {{ __('messages.purchase.go_dashboard_btn') }}
                    </a>
                @endif

                <a
                    href="{{ route('home') }}"
                    class="block text-center text-sm text-gray-400 hover:text-gray-600 transition-colors pt-1"
                >
                    {{ __('messages.purchase.back_home') }}
                </a>

            </div>
        </div>

    </div>
</div>
@endsection

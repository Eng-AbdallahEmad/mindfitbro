@extends('layouts.web.app')

@section('title', 'تم الطلب بنجاح')

@section('style')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800;900&family=Cairo:wght@400;500;600;700;800;900&display=swap');

    :root {
        --primary:      #174DAD;
        --primary-dark: #0f3a87;
        --primary-light:#EFF5FF;
        --accent:       #D4ED57;
        --accent-hover: #c8e040;
        --text:         #1C1C1C;
        --bg:           #EEEEEE;
        --white:        #ffffff;
        --gray-muted:   #6b7280;
        --green:        #22c55e;
        --green-light:  #f0fdf4;
        --border:       rgba(23, 77, 173, 0.12);
        --shadow-sm:    0 2px 8px rgba(23,77,173,0.08);
        --shadow-md:    0 8px 32px rgba(23,77,173,0.12);
        --shadow-lg:    0 24px 64px rgba(23,77,173,0.16);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    .success-page {
        min-height: 100vh;
        background: var(--bg);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 120px 16px 80px;
        position: relative;
        overflow: hidden;
        font-family: 'Cairo', sans-serif;
    }

    /* ─── Background Decoration ─── */
    .bg-blob {
        position: fixed;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.18;
        pointer-events: none;
        z-index: 0;
    }
    .bg-blob-1 {
        width: 500px; height: 500px;
        background: var(--primary);
        top: -100px; right: -100px;
        animation: blobFloat 8s ease-in-out infinite;
    }
    .bg-blob-2 {
        width: 400px; height: 400px;
        background: var(--accent);
        bottom: -80px; left: -80px;
        animation: blobFloat 10s ease-in-out infinite reverse;
    }
    .bg-blob-3 {
        width: 250px; height: 250px;
        background: var(--green);
        top: 40%; left: 50%;
        transform: translate(-50%, -50%);
        animation: blobFloat 12s ease-in-out infinite 2s;
    }

    @keyframes blobFloat {
        0%, 100% { transform: translateY(0) scale(1); }
        50%       { transform: translateY(-20px) scale(1.05); }
    }

    /* ─── Confetti ─── */
    .confetti-wrap {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 1;
        overflow: hidden;
    }

    .confetti-piece {
        position: absolute;
        top: -20px;
        width: 8px; height: 8px;
        border-radius: 2px;
        animation: confettiFall linear forwards;
        opacity: 0;
    }

    @keyframes confettiFall {
        0%   { transform: translateY(0) rotate(0deg); opacity: 1; }
        100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
    }

    /* ─── Main Card ─── */
    .success-card {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 560px;
        background: var(--white);
        border-radius: 28px;
        border: 1.5px solid var(--border);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: cardReveal 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }

    @keyframes cardReveal {
        from { opacity: 0; transform: translateY(40px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* ─── Card Header ─── */
    .card-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 48px 40px 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .card-header::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        height: 40px;
        background: var(--white);
        border-radius: 50% 50% 0 0 / 100% 100% 0 0;
    }

    .success-icon-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .success-icon-ring {
        position: absolute;
        width: 100px; height: 100px;
        border-radius: 50%;
        border: 2px solid rgba(212, 237, 87, 0.4);
        animation: ringPulse 2s ease-out infinite;
    }

    .success-icon-ring-2 {
        position: absolute;
        width: 120px; height: 120px;
        border-radius: 50%;
        border: 2px solid rgba(212, 237, 87, 0.2);
        animation: ringPulse 2s ease-out infinite 0.4s;
    }

    @keyframes ringPulse {
        0%   { transform: scale(0.8); opacity: 0; }
        50%  { opacity: 1; }
        100% { transform: scale(1.4); opacity: 0; }
    }

    .success-icon-circle {
        width: 80px; height: 80px;
        border-radius: 50%;
        background: var(--accent);
        display: flex; align-items: center; justify-content: center;
        position: relative; z-index: 1;
        box-shadow: 0 8px 24px rgba(212, 237, 87, 0.5);
        animation: iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s both;
    }

    @keyframes iconBounce {
        from { transform: scale(0); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }

    .success-icon-circle svg {
        animation: checkDraw 0.5s ease 0.7s both;
        stroke-dasharray: 30;
        stroke-dashoffset: 30;
    }

    @keyframes checkDraw {
        to { stroke-dashoffset: 0; }
    }

    .header-title {
        font-family: 'Sora', sans-serif;
        font-size: 26px;
        font-weight: 900;
        color: white;
        margin-bottom: 8px;
        animation: fadeUp 0.5s ease 0.4s both;
    }

    .header-sub {
        font-size: 14px;
        color: rgba(255,255,255,0.75);
        font-weight: 500;
        animation: fadeUp 0.5s ease 0.5s both;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ─── Card Body ─── */
    .card-body {
        padding: 32px 36px 36px;
        animation: fadeUp 0.5s ease 0.5s both;
    }

    /* ─── Order ID Badge ─── */
    .order-id-badge {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 24px;
    }

    .order-id-label {
        font-size: 12px;
        color: var(--gray-muted);
        font-weight: 600;
    }

    .order-id-value {
        font-family: 'Sora', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .copy-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--gray-muted);
        display: flex;
        align-items: center;
        padding: 4px;
        border-radius: 6px;
        transition: color 0.15s, background 0.15s;
    }
    .copy-btn:hover { color: var(--primary); background: var(--primary-light); }
    .copy-btn.copied { color: var(--green); }

    /* ─── Status Badge ─── */
    .status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .status-label {
        font-size: 13px;
        color: var(--gray-muted);
        font-weight: 600;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 800;
    }

    .status-badge.waiting {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fde047;
    }

    .status-badge.active {
        background: var(--green-light);
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .status-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: currentColor;
        animation: dotPulse 1.5s ease infinite;
    }

    @keyframes dotPulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.4; }
    }

    /* ─── Divider ─── */
    .divider {
        height: 1px;
        background: var(--border);
        margin: 20px 0;
    }

    /* ─── Plans Section ─── */
    .section-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--gray-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 12px;
    }

    .plans-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 24px;
    }

    .plan-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg);
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid var(--border);
        transition: border-color 0.2s;
    }

    .plan-row:hover { border-color: var(--primary); }

    .plan-row-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .plan-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--primary);
        flex-shrink: 0;
    }

    .plan-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
    }

    .plan-qty {
        font-size: 11px;
        color: var(--gray-muted);
        font-weight: 500;
        margin-top: 1px;
    }

    .plan-price {
        font-family: 'Sora', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 3px;
        direction: ltr;
    }

    /* ─── Summary Rows ─── */
    .summary-rows {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 16px;
    }

    .s-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .s-row-label {
        font-size: 13px;
        color: var(--gray-muted);
        font-weight: 500;
    }

    .s-row-value {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 3px;
        direction: ltr;
    }

    .s-row-value.green { color: var(--green); }

    /* ─── Total Row ─── */
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--primary);
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 28px;
    }

    .total-label {
        font-size: 14px;
        font-weight: 700;
        color: rgba(255,255,255,0.85);
    }

    .total-value {
        font-family: 'Sora', sans-serif;
        font-size: 24px;
        font-weight: 900;
        color: var(--accent);
        display: flex;
        align-items: center;
        gap: 4px;
        direction: ltr;
    }

    /* ─── Period Badge ─── */
    .period-info {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--primary-light);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
    }

    .period-info svg { flex-shrink: 0; color: var(--primary); }

    .period-text { font-size: 13px; font-weight: 600; color: var(--primary); }
    .period-text span { font-weight: 800; }

    /* ─── CTA Buttons ─── */
    .btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        background: var(--accent);
        color: var(--text);
        border: none;
        border-radius: 14px;
        padding: 16px 20px;
        font-size: 15px;
        font-weight: 900;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, transform 0.1s;
        margin-bottom: 10px;
    }

    .btn-primary:hover  { background: var(--accent-hover); transform: translateY(-1px); }
    .btn-primary:active { transform: scale(0.98); }

    .btn-secondary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        background: transparent;
        color: var(--primary);
        border: 1.5px solid var(--primary);
        border-radius: 14px;
        padding: 13px 20px;
        font-size: 13px;
        font-weight: 700;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s;
    }

    .btn-secondary:hover { background: var(--primary-light); }

    /* ─── Toast ─── */
    .toast {
        position: fixed;
        bottom: 32px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: var(--text);
        color: white;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 22px;
        border-radius: 20px;
        box-shadow: var(--shadow-md);
        opacity: 0;
        transition: opacity 0.2s, transform 0.2s;
        pointer-events: none;
        z-index: 100;
        white-space: nowrap;
    }

    .toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    /* ─── Responsive ─── */
    @media (max-width: 600px) {
        .card-header  { padding: 36px 24px 32px; }
        .card-body    { padding: 24px 20px 28px; }
        .header-title { font-size: 22px; }
    }
</style>
@endsection


@section('content')

@php
    $sarIcon = '<svg width="13" height="15" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="inline-block flex-shrink-0" style="vertical-align:middle"><path d="M9.36633 2.59339C10.0415 1.83554 10.4564 1.4953 11.2713 1.06514V13.6848L9.36633 14.0784V2.59339Z" fill="currentColor"/><path d="M15.4529 8.93793C15.8478 8.10434 15.8943 7.73386 16 6.87871L1.39805 10.0494C1.05179 10.8207 0.940326 11.2518 0.886964 12.0176L15.4529 8.93793Z" fill="currentColor"/><path d="M15.4529 12.8033C15.8478 11.9697 15.8943 11.5992 16 10.744L9.43602 12.1334C9.38956 12.8975 9.44292 13.2895 9.38956 14.0552L15.4529 12.8033Z" fill="currentColor"/><path d="M15.4529 16.668C15.8478 15.8345 15.8943 15.464 16 14.6088L10.0168 15.9077C9.7148 16.3245 9.52895 17.0191 9.38956 17.92L15.4529 16.668Z" fill="currentColor"/><path d="M5.95136 15.3519C6.53213 14.6341 7.13614 13.7311 7.5543 12.9901L0.51109 14.5167C0.164822 15.2881 0.0533618 15.7192 0 16.4849L5.95136 15.3519Z" fill="currentColor"/><path d="M5.64935 1.52825C6.32448 0.770398 6.73938 0.430158 7.5543 0V13.0364L5.64935 13.4301V1.52825Z" fill="currentColor"/></svg>';

    $plans   = $subscription->plans_snapshot ?? [];
    $isYearly = $subscription->is_yearly;
    $period   = $isYearly ? 'سنة' : 'شهر';
@endphp

<x-web.navbar :transparent="false" />

{{-- Background Blobs --}}
<div class="bg-blob bg-blob-1"></div>
<div class="bg-blob bg-blob-2"></div>
<div class="bg-blob bg-blob-3"></div>

{{-- Confetti --}}
<div class="confetti-wrap" id="confettiWrap"></div>

<section class="success-page">
    <div class="success-card">

        {{-- ── Header ── --}}
        <div class="card-header">
            <div class="success-icon-wrap">
                <div class="success-icon-ring-2"></div>
                <div class="success-icon-ring"></div>
                <div class="success-icon-circle">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                         stroke="#1C1C1C" stroke-width="3"
                         stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
            </div>

            <h1 class="header-title font-display">تم استلام طلبك! 🎉</h1>
            <p class="header-sub">سيتم تفعيل اشتراكك بعد تأكيد الدفع</p>
        </div>

        {{-- ── Body ── --}}
        <div class="card-body font-arabic">

            {{-- Order ID --}}
            <div class="order-id-badge">
                <span class="order-id-label">رقم الطلب</span>
                <span class="order-id-value">
                    #{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}
                    <button class="copy-btn" id="copyBtn" title="نسخ">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>
                    </button>
                </span>
            </div>

            {{-- Status --}}
            <div class="status-row">
                <span class="status-label">حالة الطلب</span>
                <span class="status-badge {{ $subscription->status === 'active' ? 'active' : 'waiting' }}">
                    <span class="status-dot"></span>
                    {{ $subscription->status === 'active' ? 'مفعّل' : 'في انتظار التأكيد' }}
                </span>
            </div>

            <div class="divider"></div>

            {{-- Plans --}}
            @if(!empty($plans))
            <div class="section-label">الباقات المشتراة</div>
            <div class="plans-list">
                @foreach($plans as $plan)
                <div class="plan-row">
                    <div class="plan-row-left">
                        <div class="plan-dot"></div>
                        <div>
                            <div class="plan-name">{{ $plan['plan_name'] }}</div>
                            @if($plan['quantity'] > 1)
                                <div class="plan-qty">× {{ $plan['quantity'] }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="plan-price">
                        {!! $sarIcon !!}
                        {{ number_format($plan['final_price'], 0) }}
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Period --}}
            <div class="period-info">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                @if ($subscription->status === 'waiting')
                    <span class="period-text">
                        يرجى تحديد موعد مناسب لعقد اجتماع عبر الإنترنت لمناقشة الخطة المقترحة وآلية التنفيذ.
                    </span>
                @else
                    <span class="period-text">
                        من <span>{{ $subscription->start_date->format('d M Y') }}</span>
                        إلى <span>{{ $subscription->end_date->format('d M Y') }}</span>
                        — اشتراك <span>{{ $isYearly ? 'سنوي' : 'شهري' }}</span>
                    </span>
                @endif
            </div>

            {{-- Summary --}}
            <div class="summary-rows">
                <div class="s-row">
                    <span class="s-row-label">المجموع قبل الخصم</span>
                    <span class="s-row-value">
                        {!! $sarIcon !!}
                        {{ number_format($subscription->total + $subscription->coupon_discount + $subscription->yearly_discount, 0) }}
                    </span>
                </div>

                @if($subscription->yearly_discount > 0)
                <div class="s-row">
                    <span class="s-row-label">خصم سنوي</span>
                    <span class="s-row-value green">
                        − {!! $sarIcon !!}
                        {{ number_format($subscription->yearly_discount, 0) }}
                    </span>
                </div>
                @endif

                @if($subscription->coupon_discount > 0)
                <div class="s-row">
                    <span class="s-row-label">
                        خصم الكوبون
                        @if($subscription->coupon_code)
                            <span style="background:var(--accent);color:var(--text);font-size:10px;font-weight:900;padding:1px 8px;border-radius:20px;margin-right:4px;">
                                {{ $subscription->coupon_code }}
                            </span>
                        @endif
                    </span>
                    <span class="s-row-value green">
                        − {!! $sarIcon !!}
                        {{ number_format($subscription->coupon_discount, 0) }}
                    </span>
                </div>
                @endif
            </div>

            {{-- Total --}}
            <div class="total-row">
                <span class="total-label">إجمالي المدفوع</span>
                <span class="total-value">
                    {!! str_replace('fill="currentColor"', 'fill="#D4ED57"', $sarIcon) !!}
                    {{ number_format($subscription->total, 0) }}
                </span>
            </div>

            {{-- CTAs --}}
            <a href="{{ url('/dashboard') }}" class="btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                الذهاب إلى لوحة التحكم
            </a>

            <a href="{{ route('booking.show', $subscription->id) }}" class="btn-primary flex items-center justify-center gap-2">
                <span class="material-symbols-rounded text-[18px]">
                    calendar_month
                </span>
                احجز جلستك الأولى
            </a>

            <a href="{{ url('/') }}#programs" class="btn-secondary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                تصفح المزيد من الباقات
            </a>

        </div>
    </div>
</section>

{{-- Toast --}}
<div class="toast" id="toast">تم نسخ رقم الطلب ✓</div>

<x-web.footer :hidden="false" />
@endsection


@section('script')
<script>
// ─── Confetti ──────────────────────────────────────────────────
const COLORS = ['#174DAD','#D4ED57','#22c55e','#f59e0b','#ec4899','#8b5cf6','#06b6d4'];

function spawnConfetti() {
    const wrap = document.getElementById('confettiWrap');
    const count = 90;

    for (let i = 0; i < count; i++) {
        const piece = document.createElement('div');
        piece.className = 'confetti-piece';

        const color  = COLORS[Math.floor(Math.random() * COLORS.length)];
        const left   = Math.random() * 100;
        const delay  = Math.random() * 1.8;
        const dur    = 2.5 + Math.random() * 2;
        const size   = 6 + Math.random() * 8;
        const shapes = ['50%', '0%', '2px'];
        const radius = shapes[Math.floor(Math.random() * shapes.length)];

        piece.style.cssText = `
            left: ${left}%;
            width: ${size}px;
            height: ${size}px;
            background: ${color};
            border-radius: ${radius};
            animation-duration: ${dur}s;
            animation-delay: ${delay}s;
        `;

        wrap.appendChild(piece);
        setTimeout(() => piece.remove(), (delay + dur) * 1000 + 100);
    }
}

// Run on load
window.addEventListener('load', () => {
    spawnConfetti();
    // Second wave
    setTimeout(spawnConfetti, 1200);
});

// ─── Copy Order ID ─────────────────────────────────────────────
document.getElementById('copyBtn').addEventListener('click', function () {
    const orderId = '#{{ str_pad($subscription->id, 6, "0", STR_PAD_LEFT) }}';

    navigator.clipboard.writeText(orderId).then(() => {
        this.classList.add('copied');
        const toast = document.getElementById('toast');
        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
            this.classList.remove('copied');
        }, 2200);
    });
});
</script>
@endsection
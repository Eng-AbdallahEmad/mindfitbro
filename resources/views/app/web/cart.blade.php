@extends('layouts.web.app')

@section('title', __('messages.cart.title'))

@section('style')
<style>
    :root {
        --primary: #174DAD;
        --primary-dark: #0f3a87;
        --accent: #D4ED57;
        --accent-hover: #c8e040;
        --text: #1C1C1C;
        --bg: #F4F7FF;
        --white: #ffffff;
        --gray-light: #e8eef8;
        --gray-muted: #6b7280;
        --green: #22c55e;
        --red: #ef4444;
        --border: rgba(23, 77, 173, 0.15);
    }

    .cart-wrapper {
        max-width: 960px;
        margin: 0 auto;
        padding: 32px 16px 64px;
    }

    .cart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
    }

    .cart-title {
        font-family: var(--font-display, 'Sora', sans-serif);
        font-size: 2rem;
        font-weight: 900;
        color: var(--text);
    }

    .cart-count {
        background: var(--primary);
        color: white;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 12px;
        border-radius: 20px;
        font-family: var(--font-arabic, 'Cairo', sans-serif);
    }

    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 768px) {
        .cart-layout { grid-template-columns: 1fr; }
    }

    .cart-items { display: flex; flex-direction: column; gap: 14px; }

    .cart-item {
        background: var(--white);
        border-radius: 18px;
        border: 1.5px solid var(--border);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        transition: border-color 0.2s, transform 0.2s, opacity 0.35s;
    }

    .cart-item:hover { border-color: var(--primary); transform: translateY(-1px); }

    .cart-item.removing {
        opacity: 0;
        transform: translateX(30px);
        pointer-events: none;
    }

    .item-icon {
        width: 54px; height: 54px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .icon-blue   { background: #EFF5FF; }
    .icon-amber  { background: #fffbe8; }
    .icon-green  { background: #f0fdf4; }
    .icon-purple { background: #f5f3ff; }

    .item-info { flex: 1; min-width: 0; }
    .item-name { font-weight: 700; font-size: 15px; color: var(--text); margin-bottom: 3px; }
    .item-sub  { font-size: 12px; color: var(--gray-muted); font-weight: 500; }

    .item-badge {
        display: inline-block;
        margin-top: 6px;
        background: var(--accent);
        color: var(--text);
        font-size: 10px; font-weight: 900;
        padding: 2px 9px; border-radius: 20px;
    }

    .item-qty {
        display: flex; align-items: center; gap: 8px;
        background: var(--bg); border-radius: 12px; padding: 4px 10px;
    }

    .qty-btn {
        width: 28px; height: 28px; border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--white); color: var(--primary);
        font-size: 17px; font-weight: 700;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.15s, color 0.15s;
    }

    .qty-btn:hover { background: var(--primary); color: white; border-color: var(--primary); }
    .qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    .qty-num {
        font-size: 15px; font-weight: 700; color: var(--text);
        min-width: 22px; text-align: center;
    }

    .item-price {
        font-family: var(--font-display, 'Sora', sans-serif);
        font-size: 18px; font-weight: 900;
        color: var(--primary);
        white-space: nowrap;
        display: flex; align-items: center; gap: 3px;
        direction: ltr;
    }

    .item-price .unit {
        font-size: 11px; font-weight: 500;
        color: var(--gray-muted);
        font-family: var(--font-arabic, 'Cairo', sans-serif);
        display: flex; align-items: center; gap: 2px;
    }

    .remove-btn {
        position: absolute; top: 12px; left: 14px;
        width: 28px; height: 28px; border-radius: 8px;
        border: 1px solid var(--border); background: transparent;
        color: var(--gray-muted); cursor: pointer; font-size: 13px;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.15s, color 0.15s, border-color 0.15s;
    }

    .remove-btn:hover { background: #fef2f2; color: var(--red); border-color: #fecaca; }

    .empty-state {
        display: none;
        flex-direction: column; align-items: center; justify-content: center;
        padding: 64px 20px; text-align: center; gap: 14px;
        background: var(--white); border-radius: 20px;
        border: 1.5px dashed var(--border);
    }

    .empty-state.show { display: flex; }
    .empty-icon  { font-size: 52px; opacity: 0.3; }
    .empty-title { font-size: 17px; font-weight: 700; color: var(--text); }
    .empty-sub   { font-size: 13px; color: var(--gray-muted); font-weight: 500; }

    .coupon-box {
        background: var(--white);
        border-radius: 18px; border: 1.5px solid var(--border);
        padding: 18px 20px; margin-top: 14px;
    }

    .coupon-row { display: flex; gap: 10px; }

    .coupon-input {
        flex: 1; background: var(--bg);
        border: 1.5px solid var(--border); border-radius: 12px;
        padding: 10px 14px; font-size: 13px;
        font-family: var(--font-arabic, 'Cairo', sans-serif);
        color: var(--text); outline: none;
        transition: border-color 0.2s;
    }

    .coupon-input::placeholder { color: var(--gray-muted); }
    .coupon-input:focus { border-color: var(--primary); }

    .coupon-btn {
        background: var(--primary); color: white; border: none;
        border-radius: 12px; padding: 10px 20px;
        font-size: 13px; font-weight: 700;
        font-family: var(--font-arabic, 'Cairo', sans-serif);
        cursor: pointer; transition: background 0.15s; white-space: nowrap;
    }

    .coupon-btn:hover { background: var(--primary-dark); }
    .coupon-btn:disabled { opacity: 0.6; cursor: not-allowed; }

    .coupon-success {
        display: none; align-items: center; gap: 6px;
        color: var(--green); font-size: 13px; font-weight: 700; margin-top: 8px;
    }

    .coupon-error {
        display: none; align-items: center; gap: 6px;
        color: var(--red); font-size: 13px; font-weight: 700; margin-top: 8px;
    }

    .coupon-success.show { display: flex; }
    .coupon-error.show   { display: flex; }

    .summary-card {
        background: var(--white); border-radius: 20px;
        border: 1.5px solid var(--border); padding: 24px;
        position: sticky; top: 100px;
    }

    .summary-title {
        font-family: var(--font-display, 'Sora', sans-serif);
        font-size: 17px; font-weight: 900; color: var(--text);
        margin-bottom: 18px;
    }

    .billing-toggle {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--bg); border-radius: 12px;
        padding: 12px 14px; margin-bottom: 16px;
    }

    .billing-label { font-size: 12px; font-weight: 700; color: var(--text); }
    .billing-sub   { font-size: 11px; color: var(--green); font-weight: 600; }

    .toggle-wrap  { display: flex; align-items: center; gap: 6px; }
    .toggle-text  { font-size: 11px; color: var(--gray-muted); font-weight: 600; }

    .toggle-pill {
        width: 40px; height: 22px; border-radius: 20px;
        background: var(--gray-light); border: none; cursor: pointer;
        position: relative; transition: background 0.2s; flex-shrink: 0;
    }

    .toggle-pill.on { background: var(--primary); }

    .toggle-pill::after {
        content: ''; position: absolute;
        top: 3px; right: 3px;
        width: 16px; height: 16px;
        border-radius: 50%; background: white;
        transition: transform 0.2s;
    }

    .toggle-pill.on::after { transform: translateX(-18px); }

    .summary-rows { display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; }

    .summary-row { display: flex; align-items: center; justify-content: space-between; }
    .s-label { font-size: 13px; color: var(--gray-muted); font-weight: 500; }
    .s-value {
        font-size: 14px; font-weight: 700; color: var(--text);
        display: flex; align-items: center; gap: 3px;
        direction: ltr;
    }
    .s-value.discount { color: var(--green); }
    .s-value.free     { color: var(--green); }

    .summary-divider { height: 1px; background: var(--border); margin: 4px 0 16px; }

    .summary-total-row {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--bg); border-radius: 12px;
        padding: 14px 16px; margin-bottom: 18px;
    }

    .total-label { font-size: 14px; font-weight: 700; color: var(--text); }

    .total-value {
        font-family: var(--font-display, 'Sora', sans-serif);
        font-size: 26px; font-weight: 900; color: var(--primary);
        display: flex; align-items: center; gap: 4px;
        direction: ltr;
    }

    .checkout-btn {
        width: 100%; background: var(--accent); color: var(--text);
        border: none; border-radius: 14px; padding: 15px 20px;
        font-size: 15px; font-weight: 900;
        font-family: var(--font-arabic, 'Cairo', sans-serif);
        cursor: pointer; transition: background 0.15s, transform 0.1s;
        display: flex; align-items: center; justify-content: center; gap: 10px;
        margin-bottom: 10px; text-decoration: none;
    }

    .checkout-btn:hover  { background: var(--accent-hover); transform: translateY(-1px); }
    .checkout-btn:active { transform: scale(0.98); }

    .continue-btn {
        width: 100%; background: transparent; color: var(--primary);
        border: 1.5px solid var(--primary); border-radius: 14px;
        padding: 12px 20px; font-size: 13px; font-weight: 700;
        font-family: var(--font-arabic, 'Cairo', sans-serif);
        cursor: pointer; transition: background 0.15s;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none;
    }

    .continue-btn:hover { background: #EFF5FF; }

    .guarantee-badge {
        display: flex; align-items: center; gap: 8px;
        margin-top: 14px; padding: 10px 14px;
        background: #f0fdf4; border-radius: 10px; border: 1px solid #bbf7d0;
    }

    .guarantee-badge span {
        font-size: 12px; color: #166534; font-weight: 700;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25%       { transform: translateX(-6px); }
        75%       { transform: translateX(6px); }
    }

    .loading-overlay {
        position: absolute; inset: 0;
        background: rgba(255,255,255,0.6);
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        pointer-events: none; opacity: 0;
        transition: opacity 0.15s;
    }

    .cart-item.loading .loading-overlay { opacity: 1; pointer-events: all; }

    .spinner {
        width: 20px; height: 20px;
        border: 2px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endsection


@section('content')

@php
    $sarIcon = '<svg width="14" height="16" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="inline-block flex-shrink-0" style="vertical-align:middle"><path d="M9.36633 2.59339C10.0415 1.83554 10.4564 1.4953 11.2713 1.06514V13.6848L9.36633 14.0784V2.59339Z" fill="currentColor"/><path d="M15.4529 8.93793C15.8478 8.10434 15.8943 7.73386 16 6.87871L1.39805 10.0494C1.05179 10.8207 0.940326 11.2518 0.886964 12.0176L15.4529 8.93793Z" fill="currentColor"/><path d="M15.4529 12.8033C15.8478 11.9697 15.8943 11.5992 16 10.744L9.43602 12.1334C9.38956 12.8975 9.44292 13.2895 9.38956 14.0552L15.4529 12.8033Z" fill="currentColor"/><path d="M15.4529 16.668C15.8478 15.8345 15.8943 15.464 16 14.6088L10.0168 15.9077C9.7148 16.3245 9.52895 17.0191 9.38956 17.92L15.4529 16.668Z" fill="currentColor"/><path d="M5.95136 15.3519C6.53213 14.6341 7.13614 13.7311 7.5543 12.9901L0.51109 14.5167C0.164822 15.2881 0.0533618 15.7192 0 16.4849L5.95136 15.3519Z" fill="currentColor"/><path d="M5.64935 1.52825C6.32448 0.770398 6.73938 0.430158 7.5543 0V13.0364L5.64935 13.4301V1.52825Z" fill="currentColor"/></svg>';
@endphp

<x-web.navbar :transparent="false" />

{{-- Success Message --}}
@if(session('success'))
<div class="max-w-[960px] mx-auto px-4 pt-6">
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 font-arabic font-bold text-sm px-5 py-3 rounded-2xl">
        <span class="material-symbols-rounded text-green-500" style="font-size:18px">check_circle</span>
        {{ session('success') }}
    </div>
</div>
@endif

<section class="w-full bg-lightBg min-h-screen pt-28 lg:pt-36">
    <div class="cart-wrapper font-arabic">

        {{-- Header --}}
        <div class="cart-header">
            <div class="flex items-center gap-3">
                <h1 class="cart-title font-display">{{ __('messages.cart.title') }}</h1>
                <span class="cart-count" id="cartCountBadge">{{ $cart->items->count() }} {{ __('messages.cart.items_label') }}</span>
            </div>
            <span class="text-sm text-gray-400 font-semibold" id="totalItemsText">
                @if($cart->items->count() > 0)
                    {{ __('messages.cart.total_items', ['count' => $cart->items->count()]) }}
                @endif
            </span>
        </div>

        <div class="cart-layout">

            {{-- ══ LEFT: Items + Coupon ══ --}}
            <div>
                <div class="cart-items" id="cartItems">

                    @forelse($cart->items as $item)
                    <div class="cart-item pt-12"
                         id="item-{{ $item->id }}"
                         data-item-id="{{ $item->id }}"
                         data-plan-id="{{ $item->plan_id }}">

                        {{-- Icon --}}
                        <div class="item-icon {{ $item->plan->icon_bg ? '' : 'icon-blue' }}"
                             style="{{ $item->plan->icon_bg ? 'background:' . $item->plan->icon_bg : '' }}">
                            <span class="material-symbols-rounded"
                                  style="font-size:26px; color:{{ $item->plan->icon_color ?? '#174DAD' }}; font-variation-settings:'FILL' 1">
                                {{ $item->plan->icon ?? 'star' }}
                            </span>
                        </div>

                        {{-- Info --}}
                        <div class="item-info">
                            <div class="item-name">{{ $item->plan->name }}</div>
                            <div class="item-sub">{{ $item->plan->desc }}</div>
                            @if($item->plan->popular)
                                <span class="item-badge">{{ __('messages.cart.popular_badge') }}</span>
                            @endif
                        </div>

                        {{-- Qty --}}
                        <div class="item-qty">
                            <button class="qty-btn"
                                    onclick="changeQty({{ $item->id }}, {{ $item->quantity - 1 }})"
                                    {{ $item->quantity <= 1 ? 'disabled' : '' }}>−</button>
                            <span class="qty-num" id="qty-{{ $item->id }}">{{ $item->quantity }}</span>
                            <button class="qty-btn"
                                    onclick="changeQty({{ $item->id }}, {{ $item->quantity + 1 }})">+</button>
                        </div>

                        {{-- Price --}}
                        <div class="item-price">
                            <span id="original-price-{{ $item->id }}"
                                style="{{ $cart->is_yearly ? '' : 'display:none' }}; text-decoration:line-through; font-size:13px; color:var(--gray-muted); font-weight:500;">
                                {{ number_format($item->monthly_price * 12 * $item->quantity, 0) }}
                            </span>
                            <span id="price-{{ $item->id }}">{{ number_format($item->final_price, 0) }}</span>
                            <span class="unit">
                                {!! $sarIcon !!}/<span class="unit-period">{{ $cart->is_yearly ? __('messages.cart.period_yearly') : __('messages.cart.period_monthly') }}</span>
                            </span>
                        </div>

                        {{-- Remove --}}
                        <button class="remove-btn" onclick="removeItem({{ $item->id }})" title="{{ __('messages.cart.remove_title') }}">✕</button>

                        {{-- Loading overlay --}}
                        <div class="loading-overlay"><div class="spinner"></div></div>
                    </div>
                    @empty
                    @endforelse

                </div>

                {{-- Empty State --}}
                <div class="empty-state {{ $cart->items->isEmpty() ? 'show' : '' }}" id="emptyState">
                    <div class="empty-icon">🛒</div>
                    <div class="empty-title">{{ __('messages.cart.empty_title') }}</div>
                    <div class="empty-sub">{{ __('messages.cart.empty_sub') }}</div>
                    <a href="{{ url('/') }}#programs"
                       class="group font-arabic text-textColor bg-accent px-6 py-3 rounded-full text-sm font-black flex items-center gap-2 transition mt-2 hover:bg-yellow-300">
                        {{ __('messages.cart.empty_cta') }}
                    </a>
                </div>

                {{-- Coupon --}}
                <div class="coupon-box" id="couponSection"
                     style="{{ $cart->items->isEmpty() ? 'display:none' : '' }}">
                    <div class="text-sm font-bold text-textColor mb-3">{{ __('messages.cart.coupon_label') }}</div>
                    <div class="coupon-row">
                        <input
                            class="coupon-input"
                            id="couponInput"
                            placeholder="{{ __('messages.cart.coupon_placeholder') }}"
                            autocomplete="off"
                            value="{{ $cart->coupon_code ?? '' }}"
                        />
                        <button class="coupon-btn" id="couponBtn" onclick="applyCoupon()">{{ __('messages.cart.coupon_btn') }}</button>
                    </div>
                    <div class="coupon-success {{ $cart->coupon_code ? 'show' : '' }}" id="couponSuccess">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        {{ __('messages.cart.coupon_success') }}
                    </div>
                    <div class="coupon-error" id="couponError">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ __('messages.cart.coupon_error') }}
                    </div>
                </div>
            </div>

            {{-- ══ RIGHT: Summary ══ --}}
            <div>
                <div class="summary-card font-arabic">
                    <div class="summary-title font-arabic">{{ __('messages.cart.summary_title') }}</div>

                    {{-- Yearly Toggle --}}
                    <div class="billing-toggle">
                        <div>
                            <div class="billing-label">{{ __('messages.cart.billing_label') }}</div>
                            <div class="billing-sub">{{ __('messages.cart.billing_sub') }}</div>
                        </div>
                        <div class="toggle-wrap">
                            <span class="toggle-text" id="toggleText">{{ $cart->is_yearly ? __('messages.cart.toggle_yearly') : __('messages.cart.toggle_monthly') }}</span>
                            <button class="toggle-pill {{ $cart->is_yearly ? 'on' : '' }}"
                                    id="yearlyToggle"
                                    onclick="toggleYearly()"></button>
                        </div>
                    </div>

                    {{-- Rows --}}
                    <div class="summary-rows">

                        {{-- Subtotal --}}
                        <div class="summary-row">
                            <span class="s-label">{{ __('messages.cart.subtotal_label') }}</span>
                            <span class="s-value" id="subtotalVal">
                                {!! $sarIcon !!}
                                <span id="subtotalNum">{{ number_format($cart->subtotal, 0) }}</span>
                            </span>
                        </div>

                        {{-- Coupon Discount --}}
                        <div class="summary-row" id="discountRow"
                             style="{{ $cart->coupon_discount > 0 ? '' : 'display:none' }}">
                            <span class="s-label">{{ __('messages.cart.coupon_disc_label') }}</span>
                            <span class="s-value discount" id="discountVal">
                                {!! $sarIcon !!}
                                <span id="discountNum">{{ number_format($cart->coupon_discount, 0) }}</span>
                                <span>−</span>
                            </span>
                        </div>

                        {{-- Yearly Discount --}}
                        <div class="summary-row" id="yearlyDiscRow"
                             style="{{ $cart->yearly_discount > 0 ? '' : 'display:none' }}">
                            <span class="s-label">{{ __('messages.cart.yearly_disc_label') }}</span>
                            <span class="s-value discount" id="yearlyDiscVal">
                                {!! $sarIcon !!}
                                <span id="yearlyDiscNum">{{ number_format($cart->yearly_discount, 0) }}</span>
                                <span>−</span>
                            </span>
                        </div>
                    </div>

                    <div class="summary-divider"></div>

                    {{-- Total --}}
                    <div class="summary-total-row">
                        <div class="total-label">{{ __('messages.cart.total_label') }}</div>
                        <div class="total-value">
                            {!! $sarIcon !!}
                            <span id="totalVal">{{ number_format($cart->total, 0) }}</span>
                        </div>
                    </div>

                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <button type="submit" class="checkout-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                            {{ __('messages.cart.checkout_btn') }}
                        </button>
                    </form>

                    <a href="{{ url('/') }}#programs" class="continue-btn">
                        {{ __('messages.cart.continue_btn') }}
                    </a>

                    <div class="guarantee-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="#16a34a" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>{{ __('messages.cart.guarantee') }}</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<x-web.footer :hidden="false" />

@endsection


@section('script')
<script>
const CART_TRANS = {
    items_label:    @json(__('messages.cart.items_label')),
    total_items:    @json(__('messages.cart.total_items')),
    toggle_yearly:  @json(__('messages.cart.toggle_yearly')),
    toggle_monthly: @json(__('messages.cart.toggle_monthly')),
    period_yearly:  @json(__('messages.cart.period_yearly')),
    period_monthly: @json(__('messages.cart.period_monthly')),
    coupon_btn:     @json(__('messages.cart.coupon_btn')),
};

const ROUTES = {
    updateQty:    '{{ route('cart.updateQty') }}',
    remove:       '{{ route('cart.remove') }}',
    toggleYearly: '{{ route('cart.toggleYearly') }}',
    applyCoupon:  '{{ route('cart.applyCoupon') }}',
};

const CSRF = '{{ csrf_token() }}';

// ─── AJAX Helper ──────────────────────────────────────────────
async function cartRequest(url, data) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify(data),
    });

    if (!res.ok) throw new Error('Request failed');
    return res.json();
}

// ─── Update Summary UI ────────────────────────────────────────
// ✅ الـ JS بس بيغير الأرقام — الـ SAR icon فاضل في الـ HTML
function updateSummaryUI(data) {
    const count = data.count;

    document.getElementById('cartCountBadge').textContent = count + ' ' + CART_TRANS.items_label;
    document.getElementById('totalItemsText').textContent  = count > 0 ? CART_TRANS.total_items.replace(':count', count) : '';

    document.getElementById('emptyState').classList.toggle('show', count === 0);

    const couponSec = document.getElementById('couponSection');
    if (couponSec) couponSec.style.display = count === 0 ? 'none' : '';

    // ✅ بس بنغير الـ span الرقم مش الـ element كله
    document.getElementById('subtotalNum').textContent = formatNum(data.subtotal);
    document.getElementById('totalVal').textContent    = formatNum(data.total);

    const couponDisc = parseFloat(data.coupon_discount);
    document.getElementById('discountRow').style.display = couponDisc > 0 ? '' : 'none';
    document.getElementById('discountNum').textContent   = formatNum(data.coupon_discount);

    const yearlyDisc = parseFloat(data.yearly_discount);
    document.getElementById('yearlyDiscRow').style.display = yearlyDisc > 0 ? '' : 'none';
    document.getElementById('yearlyDiscNum').textContent   = formatNum(data.yearly_discount);

    // ─── Update each item ─────────────────────────────────────
    data.items.forEach(item => {
        const itemEl = document.getElementById('item-' + item.id);
        if (!itemEl) return;

        // ✅ بس بنغير الرقم
        const priceEl = document.getElementById('price-' + item.id);
        if (priceEl) priceEl.textContent = formatNum(item.final_price);

        // ✅ بس بنغير الـ period (شهر/سنة) مش الـ SAR icon
        const periodEl = itemEl.querySelector('.unit-period');
        if (periodEl) periodEl.textContent = data.is_yearly ? CART_TRANS.period_yearly : CART_TRANS.period_monthly;

        const origEl = document.getElementById('original-price-' + item.id);
        if (origEl) {
            origEl.textContent = formatNum(item.original_price);
            origEl.style.display = data.is_yearly ? '' : 'none';
        }

        // qty
        const qtyEl = document.getElementById('qty-' + item.id);
        if (qtyEl) qtyEl.textContent = item.quantity;

        // qty buttons
        const [minusBtn, plusBtn] = itemEl.querySelectorAll('.qty-btn');
        if (minusBtn) {
            minusBtn.disabled = item.quantity <= 1;
            minusBtn.setAttribute('onclick', `changeQty(${item.id}, ${item.quantity - 1})`);
        }
        if (plusBtn) {
            plusBtn.setAttribute('onclick', `changeQty(${item.id}, ${item.quantity + 1})`);
        }
    });
}

// ─── Format Number ────────────────────────────────────────────
function formatNum(n) {
    const num = parseFloat(String(n).replace(/,/g, ''));
    return new Intl.NumberFormat('en-US').format(Math.round(num));
}

// ─── Change Quantity ──────────────────────────────────────────
async function changeQty(itemId, newQty) {
    if (newQty < 1) return;

    const itemEl = document.getElementById('item-' + itemId);
    if (itemEl) itemEl.classList.add('loading');

    try {
        const data = await cartRequest(ROUTES.updateQty, {
            item_id:  itemId,
            quantity: newQty,
        });
        updateSummaryUI(data);
    } catch (e) {
        console.error('Failed to update qty', e);
    } finally {
        if (itemEl) itemEl.classList.remove('loading');
    }
}

// ─── Remove Item ──────────────────────────────────────────────
async function removeItem(itemId) {
    const itemEl = document.getElementById('item-' + itemId);
    if (itemEl) itemEl.classList.add('removing');

    try {
        const data = await cartRequest(ROUTES.remove, { item_id: itemId });
        setTimeout(() => {
            itemEl?.remove();
            updateSummaryUI(data);
        }, 350);
    } catch (e) {
        if (itemEl) itemEl.classList.remove('removing');
        console.error('Failed to remove item', e);
    }
}

// ─── Toggle Yearly ────────────────────────────────────────────
async function toggleYearly() {
    const btn      = document.getElementById('yearlyToggle');
    const isYearly = !btn.classList.contains('on');

    // Optimistic UI
    btn.classList.toggle('on', isYearly);
    document.getElementById('toggleText').textContent = isYearly ? CART_TRANS.toggle_yearly : CART_TRANS.toggle_monthly;

    try {
        const data = await cartRequest(ROUTES.toggleYearly, { is_yearly: isYearly });
        updateSummaryUI(data);
    } catch (e) {
        // Revert on failure
        btn.classList.toggle('on', !isYearly);
        document.getElementById('toggleText').textContent = !isYearly ? CART_TRANS.toggle_yearly : CART_TRANS.toggle_monthly;
        console.error('Failed to toggle yearly', e);
    }
}

// ─── Apply Coupon ─────────────────────────────────────────────
async function applyCoupon() {
    const input   = document.getElementById('couponInput');
    const btn     = document.getElementById('couponBtn');
    const success = document.getElementById('couponSuccess');
    const error   = document.getElementById('couponError');
    const code    = input.value.trim();

    if (!code) return;

    btn.disabled    = true;
    btn.textContent = '...';
    success.classList.remove('show');
    error.classList.remove('show');
    input.style.borderColor = '';

    try {
        const data = await cartRequest(ROUTES.applyCoupon, { coupon_code: code });

        if (data.coupon_valid) {
            success.classList.add('show');
            input.style.borderColor = 'var(--green)';
        } else if (data.coupon_invalid) {
            error.classList.add('show');
            input.style.borderColor = 'var(--red)';
            input.style.animation   = 'shake 0.3s';
            setTimeout(() => { input.style.animation = ''; }, 400);
        }

        updateSummaryUI(data);
    } catch (e) {
        console.error('Failed to apply coupon', e);
    } finally {
        btn.disabled    = false;
        btn.textContent = CART_TRANS.coupon_btn;
    }
}

// ─── Coupon Input Events ──────────────────────────────────────
document.getElementById('couponInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') applyCoupon();
});

document.getElementById('couponInput').addEventListener('input', function () {
    this.style.borderColor = '';
    document.getElementById('couponSuccess').classList.remove('show');
    document.getElementById('couponError').classList.remove('show');

    if (!this.value.trim()) {
        cartRequest(ROUTES.applyCoupon, { coupon_code: null }).catch(() => {});
    }
});
</script>
@endsection
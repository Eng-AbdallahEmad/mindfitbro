@extends('layouts.web.app')

@section('title', 'عربة التسوق')

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

    /* ─── Layout ─── */
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 768px) {
        .cart-layout { grid-template-columns: 1fr; }
    }

    /* ─── Items ─── */
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
        transition: border-color 0.2s, transform 0.2s;
    }

    .cart-item:hover { border-color: var(--primary); transform: translateY(-1px); }

    .cart-item.removing {
        opacity: 0;
        transform: translateX(30px);
        transition: all 0.35s ease;
    }

    .item-icon {
        width: 54px; height: 54px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 24px;
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

    .qty-num {
        font-size: 15px; font-weight: 700; color: var(--text);
        min-width: 22px; text-align: center;
    }

    .item-price {
        font-family: var(--font-display, 'Sora', sans-serif);
        font-size: 18px; font-weight: 900;
        color: var(--primary);
        white-space: nowrap; text-align: left; direction: ltr;
    }

    .item-price .unit {
        font-size: 11px; font-weight: 500;
        color: var(--gray-muted);
        font-family: var(--font-arabic, 'Cairo', sans-serif);
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

    /* ─── Empty State ─── */
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

    /* ─── Coupon ─── */
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

    .coupon-success {
        display: none; align-items: center; gap: 6px;
        color: var(--green); font-size: 13px; font-weight: 700; margin-top: 8px;
    }

    .coupon-success.show { display: flex; }

    /* ─── Summary Card ─── */
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
    .s-value { font-size: 14px; font-weight: 700; color: var(--text); direction: ltr; text-align: left; }
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
        direction: ltr; text-align: left;
        display: flex; align-items: center; gap: 4px;
    }

    .checkout-btn {
        width: 100%; background: var(--accent); color: var(--text);
        border: none; border-radius: 14px; padding: 15px 20px;
        font-size: 15px; font-weight: 900;
        font-family: var(--font-arabic, 'Cairo', sans-serif);
        cursor: pointer; transition: background 0.15s, transform 0.1s;
        display: flex; align-items: center; justify-content: center; gap: 10px;
        margin-bottom: 10px;
    }

    .checkout-btn:hover  { background: var(--accent-hover); transform: translateY(-1px); }
    .checkout-btn:active { transform: scale(0.98); }

    .continue-btn {
        width: 100%; background: transparent; color: var(--primary);
        border: 1.5px solid var(--primary); border-radius: 14px;
        padding: 12px 20px; font-size: 13px; font-weight: 700;
        font-family: var(--font-arabic, 'Cairo', sans-serif);
        cursor: pointer; transition: background 0.15s;
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

    /* ─── Shake animation for invalid coupon ─── */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25%       { transform: translateX(-6px); }
        75%       { transform: translateX(6px); }
    }
</style>
@endsection


@section('content')

    <x-web.navbar :transparent="false" />

    <section class="w-full bg-lightBg min-h-screen pt-28 lg:pt-36">
        <div class="cart-wrapper font-arabic">

            {{-- Header --}}
            <div class="cart-header">
                <div class="flex items-center gap-3">
                    <h1 class="cart-title">عربة التسوق</h1>
                    <span class="cart-count" id="cartCountBadge">0 عناصر</span>
                </div>
                <span class="text-sm text-gray-400 font-semibold" id="totalItemsText"></span>
            </div>

            <div class="cart-layout">

                {{-- ══════════════════════════════
                     LEFT: Items + Coupon
                ══════════════════════════════ --}}
                <div>
                    <div class="cart-items" id="cartItems">

                        {{-- ─── Item: النخبة ─── --}}
                        <div class="cart-item" id="item-pro" data-key="pro" data-base="599">
                            <div class="item-icon icon-blue">⭐</div>
                            <div class="item-info">
                                <div class="item-name">باقة النخبة</div>
                                <div class="item-sub">متابعة أسبوعية • خطة مخصصة • متابعة طبية</div>
                                <span class="item-badge">الأكثر طلباً</span>
                            </div>
                            <div class="item-qty">
                                <button class="qty-btn" onclick="changeQty('pro', -1)">−</button>
                                <span class="qty-num" id="qty-pro">1</span>
                                <button class="qty-btn" onclick="changeQty('pro', 1)">+</button>
                            </div>
                            <div class="item-price">
                                <span id="price-pro">599</span>
                                <span class="unit"> ر.س/شهر</span>
                            </div>
                            <button class="remove-btn" onclick="removeItem('pro')" title="حذف">✕</button>
                        </div>

                        {{-- ─── Item: الأساسي ─── --}}
                        <div class="cart-item" id="item-starter" data-key="starter" data-base="299">
                            <div class="item-icon icon-amber">⚡</div>
                            <div class="item-info">
                                <div class="item-name">باقة الأساسي</div>
                                <div class="item-sub">متابعة شهرية • برنامج تدريبي وغذائي</div>
                            </div>
                            <div class="item-qty">
                                <button class="qty-btn" onclick="changeQty('starter', -1)">−</button>
                                <span class="qty-num" id="qty-starter">1</span>
                                <button class="qty-btn" onclick="changeQty('starter', 1)">+</button>
                            </div>
                            <div class="item-price">
                                <span id="price-starter">299</span>
                                <span class="unit"> ر.س/شهر</span>
                            </div>
                            <button class="remove-btn" onclick="removeItem('starter')" title="حذف">✕</button>
                        </div>

                        {{-- ─── Item: العائلة ─── --}}
                        <div class="cart-item" id="item-family" data-key="family" data-base="1399">
                            <div class="item-icon icon-green">👨‍👩‍👧‍👦</div>
                            <div class="item-info">
                                <div class="item-name">باقة العائلة</div>
                                <div class="item-sub">لـ 4 أفراد • متابعة يومية • جلسات فيديو</div>
                                <span class="item-badge" style="background:#bbf7d0; color:#166534;">عرض محدود</span>
                            </div>
                            <div class="item-qty">
                                <button class="qty-btn" onclick="changeQty('family', -1)">−</button>
                                <span class="qty-num" id="qty-family">1</span>
                                <button class="qty-btn" onclick="changeQty('family', 1)">+</button>
                            </div>
                            <div class="item-price">
                                <span id="price-family">1,399</span>
                                <span class="unit"> ر.س/شهر</span>
                            </div>
                            <button class="remove-btn" onclick="removeItem('family')" title="حذف">✕</button>
                        </div>

                        {{-- ─── Item: إيليت ─── --}}
                        {{-- أضيف item-elite لو حبيت --}}
                        {{--
                        <div class="cart-item" id="item-elite" data-key="elite" data-base="999">
                            <div class="item-icon icon-purple">🏆</div>
                            <div class="item-info">
                                <div class="item-name">باقة إيليت</div>
                                <div class="item-sub">متابعة يومية • VIP كامل</div>
                            </div>
                            <div class="item-qty">
                                <button class="qty-btn" onclick="changeQty('elite', -1)">−</button>
                                <span class="qty-num" id="qty-elite">1</span>
                                <button class="qty-btn" onclick="changeQty('elite', 1)">+</button>
                            </div>
                            <div class="item-price">
                                <span id="price-elite">999</span>
                                <span class="unit"> ر.س/شهر</span>
                            </div>
                            <button class="remove-btn" onclick="removeItem('elite')" title="حذف">✕</button>
                        </div>
                        --}}

                    </div>

                    {{-- Empty State --}}
                    <div class="empty-state" id="emptyState">
                        <div class="empty-icon">🛒</div>
                        <div class="empty-title">العربة فاضية!</div>
                        <div class="empty-sub">مفيش باقات متضافة لحد دلوقتي</div>
                        <a href="#programs" class="group font-arabic text-textColor bg-accent px-6 py-3 rounded-full text-sm font-black flex items-center gap-2 transition mt-2 hover:bg-yellow-300">
                            تصفح الباقات
                            <svg class="transition-transform duration-300 group-hover:-translate-x-1"
                                width="22" height="12" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Coupon Box --}}
                    <div class="coupon-box" id="couponSection">
                        <div class="text-sm font-bold text-textColor mb-3">عندك كوبون خصم؟</div>
                        <div class="coupon-row">
                            <input
                                class="coupon-input"
                                id="couponInput"
                                placeholder="أدخل كود الخصم..."
                                autocomplete="off"
                            />
                            <button class="coupon-btn" onclick="applyCoupon()">تطبيق</button>
                        </div>
                        <div class="coupon-success" id="couponSuccess">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            تم تطبيق خصم 10% بنجاح 🎉
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════
                     RIGHT: Summary
                ══════════════════════════════ --}}
                <div>
                    <div class="summary-card font-arabic">
                        <div class="summary-title">ملخص الطلب</div>

                        {{-- Yearly Toggle --}}
                        <div class="billing-toggle">
                            <div>
                                <div class="billing-label">دفع سنوي</div>
                                <div class="billing-sub">وفّر 25% مع الاشتراك السنوي</div>
                            </div>
                            <div class="toggle-wrap">
                                <span class="toggle-text" id="toggleText">شهري</span>
                                <button class="toggle-pill" id="yearlyToggle" onclick="toggleYearly()"></button>
                            </div>
                        </div>

                        {{-- Rows --}}
                        <div class="summary-rows">
                            <div class="summary-row">
                                <span class="s-label">الإجمالي الفرعي</span>
                                <span class="s-value" id="subtotalVal">0 ر.س</span>
                            </div>
                            <div class="summary-row" id="discountRow" style="display:none">
                                <span class="s-label">خصم الكوبون (10%)</span>
                                <span class="s-value discount" id="discountVal">−0 ر.س</span>
                            </div>
                            <div class="summary-row" id="yearlyDiscRow" style="display:none">
                                <span class="s-label">خصم سنوي (25%)</span>
                                <span class="s-value discount" id="yearlyDiscVal">−0 ر.س</span>
                            </div>
                            <div class="summary-row">
                                <span class="s-label">الشحن والتوصيل</span>
                                <span class="s-value free">مجاني</span>
                            </div>
                        </div>

                        <div class="summary-divider"></div>

                        {{-- Total --}}
                        <div class="summary-total-row">
                            <div class="total-label">الإجمالي الكلي</div>
                            <div class="total-value">
                                <span id="totalVal">0</span>
                                <span style="font-size:14px; font-weight:600; color:var(--gray-muted); font-family: var(--font-arabic);">ر.س</span>
                            </div>
                        </div>

                        {{-- Checkout Button --}}
                        <a href="" id="checkoutBtn"
                            class="checkout-btn" style="text-decoration:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                            إتمام الطلب والدفع
                        </a>

                        <a href="{{ url('/') }}#programs" class="continue-btn" style="display:flex;align-items:center;justify-content:center;text-decoration:none;">
                            مواصلة التسوق
                        </a>

                        {{-- Guarantee --}}
                        <div class="guarantee-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="#16a34a" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                            <span>ضمان استرداد كامل خلال 7 أيام — بدون أي شروط</span>
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
    // ─── Config ───────────────────────────────────────────────
    const prices = { pro: 599, starter: 299, family: 1399, elite: 999 };
    const qtys   = {};
    let hasCoupon = false;
    let isYearly  = false;

    // ─── Init: build qtys from rendered items ──────────────────
    document.querySelectorAll('.cart-item[data-key]').forEach(el => {
        const key = el.dataset.key;
        qtys[key]  = 1;
    });

    document.querySelectorAll('[id^="price-"]').forEach(el => {
        const num = parseFloat(el.textContent.replace(/,/g, ''));
        el.textContent = formatNum(num);
    });

    // ─── Helpers ───────────────────────────────────────────────
    function getSubtotal() {
        return Object.entries(qtys).reduce((sum, [key, qty]) => {
            return sum + (prices[key] || 0) * qty;
        }, 0);
    }

    function formatNum(n) {
        return new Intl.NumberFormat('ar-EG').format(n);
    }

    function updateSummary() {
        const visibleItems = document.querySelectorAll('.cart-item:not(.removing)');
        const count = visibleItems.length;

        // Header badges
        document.getElementById('cartCountBadge').textContent = count + ' عناصر';
        document.getElementById('totalItemsText').textContent  = count > 0 ? 'إجمالي ' + count + ' خطط' : '';

        // Empty state
        document.getElementById('emptyState').classList.toggle('show', count === 0);
        const couponSec = document.getElementById('couponSection');
        if (couponSec) couponSec.style.display = count === 0 ? 'none' : '';

        // Calculations
        let sub        = getSubtotal();
        let total      = sub;
        let couponDisc = 0;
        let yearlyDisc = 0;

        if (hasCoupon && count > 0) {
            couponDisc = Math.round(sub * 0.1);
            total     -= couponDisc;
            document.getElementById('discountRow').style.display = '';
            document.getElementById('discountVal').textContent   = '−' + formatNum(couponDisc) + ' ر.س';
        } else {
            document.getElementById('discountRow').style.display = 'none';
        }

        if (isYearly && count > 0) {
            yearlyDisc = Math.round(total * 0.25);
            total     -= yearlyDisc;
            document.getElementById('yearlyDiscRow').style.display = '';
            document.getElementById('yearlyDiscVal').textContent    = '−' + formatNum(yearlyDisc) + ' ر.س';
        } else {
            document.getElementById('yearlyDiscRow').style.display = 'none';
        }

        document.getElementById('subtotalVal').textContent = formatNum(sub) + ' ر.س';
        document.getElementById('totalVal').textContent    = formatNum(total);
    }

    // ─── Qty ───────────────────────────────────────────────────
    function changeQty(key, delta) {
        if (qtys[key] === undefined) return;
        qtys[key] = Math.max(1, qtys[key] + delta);
        document.getElementById('qty-'   + key).textContent = qtys[key];
        document.getElementById('price-' + key).textContent = formatNum(prices[key] * qtys[key]);
        updateSummary();
    }

    // ─── Remove ────────────────────────────────────────────────
    function removeItem(key) {
        const el = document.getElementById('item-' + key);
        if (!el) return;
        el.classList.add('removing');
        setTimeout(() => {
            el.remove();
            delete qtys[key];
            updateSummary();
        }, 350);
    }

    // ─── Coupon ────────────────────────────────────────────────
    const VALID_COUPONS = ['MFB10', 'MINDFITBRO', 'WELCOME', 'EID2025'];

    function applyCoupon() {
        const input = document.getElementById('couponInput');
        const val   = input.value.trim().toUpperCase();

        if (VALID_COUPONS.includes(val)) {
            hasCoupon = true;
            document.getElementById('couponSuccess').classList.add('show');
            input.style.borderColor = 'var(--green)';
            updateSummary();
        } else if (val) {
            input.style.borderColor = 'var(--red)';
            input.style.animation   = 'shake 0.3s';
            setTimeout(() => { input.style.animation = ''; }, 400);
        }
    }

    document.getElementById('couponInput').addEventListener('keydown', e => {
        if (e.key === 'Enter') applyCoupon();
    });

    document.getElementById('couponInput').addEventListener('input', function () {
        this.style.borderColor = '';
        if (!this.value.trim()) {
            hasCoupon = false;
            document.getElementById('couponSuccess').classList.remove('show');
            updateSummary();
        }
    });

    // ─── Yearly Toggle ─────────────────────────────────────────
    function toggleYearly() {
        isYearly = !isYearly;
        document.getElementById('yearlyToggle').classList.toggle('on', isYearly);
        document.getElementById('toggleText').textContent = isYearly ? 'سنوي' : 'شهري';
        updateSummary();
    }

    // ─── Init ──────────────────────────────────────────────────
    updateSummary();
</script>
@endsection
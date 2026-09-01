@extends('layouts.web.app')
@section('title', 'حالة الدفع')

@php
    $currency = $subscription->currency ?? 'SAR';
    $meta     = \App\Services\Web\CurrencyService::META[$currency] ?? ['symbol' => 'ر.س', 'decimals' => 0];
    $symbol   = $meta['symbol'];
    $decimals = $meta['decimals'];

    $customerName  = $subscription->user?->name  ?? $subscription->guest_name  ?? '—';
    $customerEmail = $subscription->user?->email ?? $subscription->guest_email ?? '—';
    $customerPhone = $subscription->billing_phone ?: '—';

    $showEgpLine = $subscription->payment_gateway === 'paymob'
        && $subscription->charged_currency
        && strtoupper($subscription->charged_currency) !== strtoupper($currency);

    $statusLabels = [
        'awaiting_payment' => 'بانتظار الدفع',
        'pending_review'   => 'بانتظار مراجعة الإدارة',
        'approved'         => 'تم الدفع — مؤكد',
        'active'           => 'نشط',
        'payment_failed'   => 'فشلت العملية',
        'refunded'         => 'مُسترد',
        'rejected'         => 'تم رفض الطلب',
    ];
    $statusLabel = $statusLabels[$subscription->status] ?? $subscription->status;
@endphp

@section('style')
<style>
.page-wrap {
    background: radial-gradient(circle at 50% 0%, #ffffff 0%, #EEF2FB 55%);
    position: relative;
    overflow: hidden;
}

/* ── Ambient floating particles (brand blue + lime) ── */
.particles-bg { position: fixed; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
.particle {
    position: absolute;
    bottom: -40px;
    left: var(--x);
    width: var(--size);
    height: var(--size);
    border-radius: 50%;
    opacity: 0;
    animation: float-up var(--duration) linear var(--delay) infinite;
}
.particle-blue  { background: #174DAD; }
.particle-lime  { background: #D4ED57; }
.particle-light { background: #8FB3F0; }
@keyframes float-up {
    0%   { transform: translateY(0) translateX(0); opacity: 0; }
    8%   { opacity: .5; }
    50%  { transform: translateY(-48vh) translateX(12px); }
    92%  { opacity: .12; }
    100% { transform: translateY(-96vh) translateX(-14px); opacity: 0; }
}

.status-hero {
    background: linear-gradient(135deg, #174DAD 0%, #0f3a87 100%);
    padding: 44px 36px 56px;
    text-align: center;
    position: relative;
    overflow: hidden;
    animation: hero-in .5s ease both;
}
.hero-orb { position: absolute; border-radius: 50%; filter: blur(28px); pointer-events: none; }
.hero-orb-1 { width: 170px; height: 170px; background: #D4ED57; opacity: .18; top: -70px; inset-inline-end: -50px; }
.hero-orb-2 { width: 130px; height: 130px; background: #8FB3F0; opacity: .20; bottom: -55px; inset-inline-start: -35px; }
.hero-grid {
    position: absolute; inset: 0; opacity: .07; pointer-events: none;
    background-image: radial-gradient(#fff 1px, transparent 1px);
    background-size: 22px 22px;
}

.status-icon {
    width: 68px; height: 68px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px; position: relative;
    box-shadow: 0 8px 24px rgba(0,0,0,.18);
}
.status-icon.success { background: #D4ED57; }
.status-icon.pending { background: #FDE68A; }
.status-icon.failed  { background: #FCA5A5; }
.status-icon.review  { background: #C7D9FB; }
.status-icon.rejected { background: #FCA5A5; }

.status-icon::before {
    content: ''; position: absolute; inset: -10px; border-radius: 50%;
    border: 2px solid currentColor; opacity: 0;
}
.status-icon.pending::before { color: #FDE68A; animation: pulse-ring 2s ease-out infinite; }
.status-icon.success::before { color: #D4ED57; animation: pulse-ring 1.3s ease-out 2; }
.status-icon.failed::before  { color: #FCA5A5; animation: pulse-ring 1.9s ease-out infinite; }
@keyframes pulse-ring {
    0%   { transform: scale(.82); opacity: .65; }
    75%  { transform: scale(1.4); opacity: 0; }
    100% { transform: scale(1.4); opacity: 0; }
}

.status-card {
    margin: -28px 20px 0; background: #fff; border-radius: 20px;
    box-shadow: 0 14px 40px rgba(23,77,173,0.14); padding: 26px 22px;
    position: relative; z-index: 2; text-align: center;
    border: 1px solid #EEF2FB;
    animation: card-in .55s .1s ease both;
}
@keyframes hero-in { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
@keyframes card-in { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }

.cta-btn {
    display: block; width: 100%; padding: 15px; background: #174DAD; color: #fff;
    font-size: 15px; font-weight: 900; border-radius: 14px; text-align: center;
    text-decoration: none; border: none; cursor: pointer; font-family: inherit;
    box-shadow: 0 8px 20px rgba(23,77,173,.28);
    transition: transform .15s ease, opacity .2s ease;
}
.cta-btn:hover { opacity: .92; transform: translateY(-1px); }
.cta-btn.secondary {
    background: #F1F5F9; color: #475569; box-shadow: none;
}

.spinner {
    width: 38px; height: 38px; border-radius: 50%;
    border: 4px solid #E5EAF3; border-top-color: #174DAD; border-inline-end-color: #D4ED57;
    animation: spin .8s linear infinite; margin: 0 auto 16px;
}
@keyframes spin { to { transform: rotate(360deg); } }

.progress-dots { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 6px; }
.progress-dots span {
    width: 6px; height: 6px; border-radius: 50%; background: #174DAD; opacity: .3;
    animation: dot-pulse 1.4s ease-in-out infinite;
}
.progress-dots span:nth-child(2) { animation-delay: .2s; }
.progress-dots span:nth-child(3) { animation-delay: .4s; }
@keyframes dot-pulse { 0%, 80%, 100% { opacity: .25; transform: scale(.85); } 40% { opacity: 1; transform: scale(1.15); } }

/* ── Receipt panel ── */
.receipt-panel { margin: 18px 20px 0; }
.receipt-section { background: #fff; border: 1px solid #EEF2FB; border-radius: 16px; padding: 16px 18px; margin-bottom: 12px; }
.receipt-section:last-child { margin-bottom: 0; }
.receipt-section-title {
    font-size: 11px; font-weight: 900; color: #94A3B8; text-transform: uppercase;
    letter-spacing: .5px; margin-bottom: 10px;
}
.receipt-row { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; padding: 6px 0; font-size: 13px; }
.receipt-row + .receipt-row { border-top: 1px dashed #F1F5F9; }
.receipt-row .label { color: #94A3B8; font-weight: 600; flex-shrink: 0; }
.receipt-row .value { color: #1C1C1C; font-weight: 800; text-align: left; word-break: break-word; }
.receipt-row .value.muted { color: #64748B; font-weight: 600; }
.receipt-total-row {
    display: flex; align-items: center; justify-content: space-between;
    background: #F4F7FF; border-radius: 12px; padding: 12px 14px; margin-top: 4px;
}
.receipt-total-row .t-label { font-size: 12px; font-weight: 700; color: #6B7280; }
.receipt-total-row .t-amount { font-size: 20px; font-weight: 900; color: #174DAD; }
.status-badge {
    display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 999px;
    font-size: 11px; font-weight: 900;
}
.status-badge.paid    { background: #DCFCE7; color: #16A34A; }
.status-badge.pending { background: #FEF9C3; color: #A16207; }
.status-badge.failed  { background: #FEE2E2; color: #DC2626; }
.status-badge.review  { background: #DBEAFE; color: #1D4ED8; }
.status-badge.rejected { background: #FEE2E2; color: #DC2626; }

.upload-zone-callback input[type="file"] {
    width: 100%; font-size: 12px; border: 1.5px dashed #E2E8F0; border-radius: 10px;
    padding: 10px; font-family: inherit; background: #F8FAFC;
}

.next-steps-box {
    display: flex; align-items: flex-start; gap: 10px; border-radius: 14px; padding: 14px 16px;
    font-size: 13px; line-height: 1.8; margin-top: 4px;
}
.next-steps-box.info    { background: #EFF6FF; border: 1.5px solid #BFDBFE; color: #1E40AF; }
.next-steps-box.warning { background: #FEF9C3; border: 1.5px solid #FDE68A; color: #854D0E; }
.next-steps-box.danger  { background: #FEF2F2; border: 1.5px solid #FECACA; color: #991B1B; }

.print-btn {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    width: 100%; padding: 12px; border-radius: 12px; margin-top: 10px;
    background: transparent; border: 1.5px solid #E2E8F0; color: #64748B;
    font-size: 13px; font-weight: 800; cursor: pointer; font-family: inherit;
    transition: border-color .15s ease, color .15s ease;
}
.print-btn:hover { border-color: #174DAD; color: #174DAD; }

/* ── Print stylesheet: a clean, trustworthy receipt on paper ── */
@media print {
    body { background: #fff !important; }
    .particles-bg, .no-print { display: none !important; }
    .page-wrap { background: #fff !important; padding: 0 !important; min-height: 0 !important; }
    .status-hero { background: #fff !important; color: #1C1C1C !important; padding: 12px 0 !important; }
    .status-hero h1, .status-hero p { color: #1C1C1C !important; }
    .hero-orb, .hero-grid, .status-icon { display: none !important; }
    .status-card, .receipt-section {
        box-shadow: none !important; border: 1px solid #CBD5E1 !important;
        animation: none !important;
    }
    .w-full.max-w-md { max-width: 100% !important; }
}
</style>
@endsection

@section('content')
<div class="page-wrap min-h-screen font-arabic py-10 px-4" dir="rtl"
     x-data="paymobStatus('{{ $subscription->status }}', {{ (int) $subscription->isPaid() }}, '{{ $subscription->payment_gateway }}')">

    {{-- ambient particles --}}
    <div class="particles-bg no-print" aria-hidden="true">
        @for ($i = 0; $i < 16; $i++)
            @php
                $colorClass = ['particle-blue', 'particle-lime', 'particle-light'][$i % 3];
                $size = rand(4, 11);
                $left = rand(2, 96);
                $duration = rand(140, 260) / 10;
                $delay = rand(0, 90) / 10;
            @endphp
            <span class="particle {{ $colorClass }}"
                  style="--x:{{ $left }}%; --size:{{ $size }}px; --duration:{{ $duration }}s; --delay:{{ $delay }}s;"></span>
        @endfor
    </div>

    <div class="w-full max-w-md mx-auto relative" style="z-index:1">
        <div class="bg-white pb-[50px] rounded-3xl shadow-xl overflow-hidden">

            {{-- ── Hero ── --}}
            <div class="status-hero">
                <div class="hero-grid no-print"></div>
                <div class="hero-orb hero-orb-1 no-print"></div>
                <div class="hero-orb hero-orb-2 no-print"></div>

                <template x-if="state === 'paid'">
                    <div class="status-icon success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#1C1C1C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </template>
                <template x-if="state === 'pending'">
                    <div class="status-icon pending">
                        <span class="material-symbols-rounded" style="font-size:28px;color:#92400E">hourglass_top</span>
                    </div>
                </template>
                <template x-if="state === 'failed'">
                    <div class="status-icon failed">
                        <span class="material-symbols-rounded" style="font-size:28px;color:#991B1B">error</span>
                    </div>
                </template>
                <template x-if="state === 'manual_review'">
                    <div class="status-icon review">
                        <span class="material-symbols-rounded" style="font-size:28px;color:#1D4ED8">fact_check</span>
                    </div>
                </template>
                <template x-if="state === 'rejected'">
                    <div class="status-icon rejected">
                        <span class="material-symbols-rounded" style="font-size:28px;color:#991B1B">block</span>
                    </div>
                </template>

                <h1 class="text-xl font-black text-white mb-1.5" x-text="heading"></h1>
                <p class="text-white/65 text-sm" x-text="subheading"></p>
            </div>

            {{-- ── Live status card (state-specific action area) ── --}}
            <div class="status-card">
                <template x-if="state === 'pending' && polling">
                    <div class="spinner no-print"></div>
                </template>

                <p class="text-sm text-gray-500 mb-2" x-text="bodyText"></p>

                <template x-if="state === 'pending' && polling">
                    <div class="progress-dots no-print" aria-hidden="true"><span></span><span></span><span></span></div>
                </template>

                <div class="no-print">
                    <template x-if="state === 'paid'">
                        <a href="{{ route('home') }}" class="cta-btn mt-3">الذهاب للرئيسية</a>
                    </template>

                    <template x-if="state === 'pending' && !polling && pollCount >= maxPolls">
                        <a href="{{ route('home') }}" class="cta-btn mt-3">الرجوع للصفحة الرئيسية</a>
                    </template>

                    <template x-if="state === 'manual_review'">
                        <a href="{{ route('home') }}" class="cta-btn mt-3">الرجوع للصفحة الرئيسية</a>
                    </template>

                    <template x-if="state === 'failed'">
                        <form action="{{ route('purchase.retry', $subscription) }}" method="POST" class="mt-3">
                            @csrf
                            @unless($subscription->user_id)
                                <input type="hidden" name="guest_token" value="{{ $subscription->guest_token }}">
                            @endunless
                            <button type="submit" class="cta-btn">{{ __('messages.purchase.retry_payment_btn') }}</button>
                        </form>
                    </template>

                    {{-- Step 7: a rejected manual order has TWO recovery paths,
                         both via switchMethod() on this SAME row — never a new order. --}}
                    <template x-if="state === 'rejected'">
                        <div class="mt-3" style="display:flex;flex-direction:column;gap:12px;">
                            <form action="{{ route('purchase.switch-method', $subscription) }}" method="POST">
                                @csrf
                                <input type="hidden" name="to" value="card">
                                @unless($subscription->user_id)
                                    <input type="hidden" name="guest_token" value="{{ $subscription->guest_token }}">
                                @endunless
                                <button type="submit" class="cta-btn">إعادة المحاولة بالبطاقة</button>
                            </form>

                            @if($canSwitchToManual)
                            <form action="{{ route('purchase.switch-method', $subscription) }}" method="POST" enctype="multipart/form-data"
                                  class="upload-zone-callback" style="display:flex;flex-direction:column;gap:8px;">
                                @csrf
                                <input type="hidden" name="to" value="manual">
                                @unless($subscription->user_id)
                                    <input type="hidden" name="guest_token" value="{{ $subscription->guest_token }}">
                                @endunless
                                <label class="text-xs font-bold text-gray-500" style="text-align:start">أو ارفع إيصال تحويل جديد وصحيح</label>
                                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.gif,.pdf" required>
                                <button type="submit" class="cta-btn secondary">رفع الإيصال الجديد</button>
                            </form>
                            @endif
                        </div>
                    </template>
                </div>
            </div>

            {{-- ── Receipt content — always visible, regardless of state ── --}}
            <div class="receipt-panel">

                {{-- Order --}}
                <div class="receipt-section">
                    <p class="receipt-section-title">تفاصيل الطلب</p>
                    <div class="receipt-row">
                        <span class="label">رقم الطلب</span>
                        <span class="value" dir="ltr">{{ $subscription->invoiceNumber() }}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="label">تاريخ الطلب</span>
                        <span class="value muted">{{ $subscription->created_at?->format('d/m/Y — H:i') }}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="label">الحالة</span>
                        <span class="value">
                            <span class="status-badge" :class="{
                                paid: state === 'paid', pending: state === 'pending',
                                failed: state === 'failed', review: state === 'manual_review',
                                rejected: state === 'rejected'
                            }">{{ $statusLabel }}</span>
                        </span>
                    </div>
                </div>

                {{-- Customer --}}
                <div class="receipt-section">
                    <p class="receipt-section-title">بيانات العميل</p>
                    <div class="receipt-row">
                        <span class="label">الاسم</span>
                        <span class="value">{{ $customerName }}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="label">البريد الإلكتروني</span>
                        <span class="value" dir="ltr" style="font-size:12px">{{ $customerEmail }}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="label">رقم الهاتف</span>
                        <span class="value" dir="ltr">{{ $customerPhone }}</span>
                    </div>
                </div>

                {{-- Plan & Price --}}
                <div class="receipt-section">
                    <p class="receipt-section-title">الباقة والسعر</p>
                    <div class="receipt-row">
                        <span class="label">الباقة</span>
                        <span class="value">{{ $subscription->plan?->name ?? '—' }}</span>
                    </div>
                    <div class="receipt-row">
                        <span class="label">المدة</span>
                        <span class="value">{{ $subscription->duration_months ? $subscription->duration_months . ' شهور' : '—' }}</span>
                    </div>
                    @if((float) $subscription->coupon_discount > 0)
                    <div class="receipt-row">
                        <span class="label">خصم الكوبون ({{ $subscription->coupon_code }})</span>
                        <span class="value" style="color:#16A34A">− {{ number_format((float) $subscription->coupon_discount, $decimals) }} {{ $symbol }}</span>
                    </div>
                    @endif
                    <div class="receipt-total-row">
                        <span class="t-label">الإجمالي</span>
                        <span class="t-amount" dir="ltr">{{ number_format((float) $subscription->total, $decimals) }} {{ $symbol }}</span>
                    </div>
                    @if($showEgpLine)
                    <p class="text-[11px] text-gray-400 mt-2 text-center">
                        تم خصم <strong dir="ltr">{{ number_format($subscription->chargedAmountEgp(), 2) }} ج.م</strong>
                        فعلياً من بطاقتك/محفظتك (سعر الصرف وقت الدفع: {{ number_format((float) $subscription->fx_rate, 4) }})
                    </p>
                    @endif
                </div>

                {{-- Payment --}}
                <div class="receipt-section">
                    <p class="receipt-section-title">تفاصيل الدفع</p>
                    <div class="receipt-row">
                        <span class="label">طريقة الدفع</span>
                        <span class="value">{{ $subscription->paymentMethodLabel() }}</span>
                    </div>
                    @if($subscription->paymob_transaction_id && $subscription->isPaid())
                    <div class="receipt-row">
                        <span class="label">رقم العملية</span>
                        <span class="value" dir="ltr" style="font-size:12px">{{ $subscription->paymob_transaction_id }}</span>
                    </div>
                    @endif
                    @if($subscription->paid_at)
                    <div class="receipt-row">
                        <span class="label">تاريخ الدفع</span>
                        <span class="value muted">{{ $subscription->paid_at->format('d/m/Y — H:i') }}</span>
                    </div>
                    @endif
                </div>

                {{-- What happens next — state-specific --}}
                <template x-if="state === 'paid'">
                    <div class="next-steps-box info">
                        <span class="material-symbols-rounded" style="font-size:18px;flex-shrink:0">mail</span>
                        <span>تم تأكيد اشتراكك ووصلك بريد إلكتروني بالتفاصيل. سجّل دخولك من لوحة التحكم لبدء رحلتك.</span>
                    </div>
                </template>
                <template x-if="state === 'pending'">
                    <div class="next-steps-box warning">
                        <span class="material-symbols-rounded" style="font-size:18px;flex-shrink:0">hourglass_top</span>
                        <span>بمجرد تأكيد بوابة الدفع للعملية سيتفعّل اشتراكك تلقائياً، وسيصلك بريد إلكتروني فور ذلك — حتى لو أغلقت هذه الصفحة.</span>
                    </div>
                </template>
                <template x-if="state === 'failed'">
                    <div class="next-steps-box danger">
                        <span class="material-symbols-rounded" style="font-size:18px;flex-shrink:0">info</span>
                        <span>لم يتم خصم أي مبلغ. يمكنك إعادة المحاولة من الزر أعلاه، أو التواصل معنا لو تكررت المشكلة.</span>
                    </div>
                </template>
                <template x-if="state === 'manual_review'">
                    <div class="next-steps-box info">
                        <span class="material-symbols-rounded" style="font-size:18px;flex-shrink:0">fact_check</span>
                        <span>سيقوم فريقنا بمراجعة إيصال التحويل خلال 24 ساعة عمل غالباً. سيصلك بريد إلكتروني بمجرد التأكيد وتفعيل اشتراكك — لا داعي لإعادة الإرسال أو المتابعة هنا.</span>
                    </div>
                </template>
                <template x-if="state === 'rejected'">
                    <div class="next-steps-box danger">
                        <span class="material-symbols-rounded" style="font-size:18px;flex-shrink:0">info</span>
                        <span><strong>سبب الرفض:</strong> {{ $subscription->rejection_reason ?: 'لم يتم تحديد سبب.' }}</span>
                    </div>
                </template>

                <button type="button" onclick="window.print()" class="print-btn no-print">
                    <span class="material-symbols-rounded" style="font-size:16px">print</span>
                    طباعة / حفظ كملف PDF
                </button>
            </div>

        </div>
    </div>
</div>

@section('script')
<script>
function paymobStatus(initialStatus, initialIsPaid, gateway) {
    return {
        status: initialStatus,
        isPaid: !!initialIsPaid,
        gateway: gateway,
        polling: false,
        pollCount: 0,
        maxPolls: 20, // ~60s at 3s intervals

        get state() {
            if (this.isPaid || ['approved', 'active'].includes(this.status)) return 'paid';
            if (this.status === 'rejected') return 'rejected';
            if (['payment_failed', 'refunded'].includes(this.status)) return 'failed';
            if (this.gateway === 'manual' && this.status === 'pending_review') return 'manual_review';
            return 'pending';
        },
        get heading() {
            return {
                paid: 'تم الدفع بنجاح! 🎉',
                pending: 'جاري تأكيد الدفع...',
                failed: 'تعذر إتمام الدفع',
                manual_review: 'بانتظار مراجعة طلبك',
                rejected: 'تم رفض طلبك',
            }[this.state];
        },
        get subheading() {
            return {
                paid: 'تم تأكيد اشتراكك، ستصلك رسالة بريد إلكتروني بالتفاصيل',
                pending: 'يستغرق هذا عادة لحظات قليلة',
                failed: 'يمكنك إعادة محاولة الدفع أدناه',
                manual_review: 'استلمنا طلبك وسيراجعه فريقنا قريباً',
                rejected: 'يمكنك إعادة المحاولة أدناه',
            }[this.state];
        },
        get bodyText() {
            if (this.state === 'pending' && this.pollCount >= this.maxPolls) {
                return 'يستغرق التأكيد وقتاً أطول من المعتاد. سنُرسل لك بريداً إلكترونياً بمجرد تأكيد الدفع — لا داعي للبقاء في هذه الصفحة.';
            }
            return { paid: '', pending: 'برجاء الانتظار...', failed: '', manual_review: '', rejected: '' }[this.state];
        },

        init() {
            // Manual review is a human process, not something to poll for —
            // no fake "checking..." animation for a state that won't change
            // within this page visit.
            if (this.state === 'pending') this.startPolling();
        },

        async startPolling() {
            this.polling = true;
            const tick = async () => {
                if (this.state !== 'pending' || this.pollCount >= this.maxPolls) {
                    this.polling = false;
                    return;
                }
                this.pollCount++;
                try {
                    const resp = await fetch('{{ route('purchase.status', $subscription) }}?guest_token={{ $subscription->guest_token }}', {
                        headers: { 'Accept': 'application/json' },
                    });
                    if (resp.ok) {
                        const data = await resp.json();
                        this.status = data.status;
                        this.isPaid = data.is_paid;
                    }
                } catch (_) {}

                if (this.state === 'pending' && this.pollCount < this.maxPolls) {
                    setTimeout(tick, 3000);
                } else {
                    this.polling = false;
                }
            };
            setTimeout(tick, 3000);
        },
    };
}
</script>
@endsection
@endsection

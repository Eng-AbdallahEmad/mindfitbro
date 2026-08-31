@extends('layouts.web.app')
@section('title', 'حالة الدفع')

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
    padding: 52px 36px 64px;
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
    width: 76px; height: 76px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 22px; position: relative;
    box-shadow: 0 8px 24px rgba(0,0,0,.18);
}
.status-icon.success { background: #D4ED57; }
.status-icon.pending { background: #FDE68A; }
.status-icon.failed  { background: #FCA5A5; }

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
    margin: -28px 24px 0; background: #fff; border-radius: 20px;
    box-shadow: 0 14px 40px rgba(23,77,173,0.14); padding: 30px 24px;
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
</style>
@endsection

@section('content')
<div class="page-wrap min-h-screen font-arabic py-10 px-4" dir="rtl"
     x-data="paymobStatus('{{ $subscription->status }}', {{ (int) $subscription->isPaid() }})">

    {{-- ambient particles --}}
    <div class="particles-bg" aria-hidden="true">
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

            <div class="status-hero">
                <div class="hero-grid"></div>
                <div class="hero-orb hero-orb-1"></div>
                <div class="hero-orb hero-orb-2"></div>

                <template x-if="state === 'paid'">
                    <div class="status-icon success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#1C1C1C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </template>
                <template x-if="state === 'pending'">
                    <div class="status-icon pending">
                        <span class="material-symbols-rounded" style="font-size:32px;color:#92400E">hourglass_top</span>
                    </div>
                </template>
                <template x-if="state === 'failed'">
                    <div class="status-icon failed">
                        <span class="material-symbols-rounded" style="font-size:32px;color:#991B1B">error</span>
                    </div>
                </template>

                <h1 class="text-2xl font-black text-white mb-2" x-text="heading"></h1>
                <p class="text-white/65 text-sm" x-text="subheading"></p>
            </div>

            <div class="status-card">
                <template x-if="state === 'pending' && polling">
                    <div class="spinner"></div>
                </template>

                <p class="text-sm text-gray-500 mb-2" x-text="bodyText"></p>

                <template x-if="state === 'pending' && polling">
                    <div class="progress-dots" aria-hidden="true"><span></span><span></span><span></span></div>
                </template>

                <template x-if="state === 'paid'">
                    <a href="{{ route('home') }}" class="cta-btn mt-3">الذهاب للرئيسية</a>
                </template>

                <template x-if="state === 'pending' && !polling && pollCount >= maxPolls">
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
            </div>

        </div>
    </div>
</div>

@section('script')
<script>
function paymobStatus(initialStatus, initialIsPaid) {
    return {
        status: initialStatus,
        isPaid: !!initialIsPaid,
        polling: false,
        pollCount: 0,
        maxPolls: 20, // ~60s at 3s intervals

        get state() {
            if (this.isPaid || ['approved', 'active'].includes(this.status)) return 'paid';
            if (['payment_failed', 'refunded'].includes(this.status)) return 'failed';
            return 'pending';
        },
        get heading() {
            return { paid: 'تم الدفع بنجاح! 🎉', pending: 'جاري تأكيد الدفع...', failed: 'تعذر إتمام الدفع' }[this.state];
        },
        get subheading() {
            return {
                paid: 'تم تأكيد اشتراكك، ستصلك رسالة بريد إلكتروني بالتفاصيل',
                pending: 'يستغرق هذا عادة لحظات قليلة',
                failed: 'يمكنك إعادة محاولة الدفع أدناه',
            }[this.state];
        },
        get bodyText() {
            if (this.state === 'pending' && this.pollCount >= this.maxPolls) {
                return 'يستغرق التأكيد وقتاً أطول من المعتاد. سنُرسل لك بريداً إلكترونياً بمجرد تأكيد الدفع — لا داعي للبقاء في هذه الصفحة.';
            }
            return { paid: '', pending: 'برجاء الانتظار...', failed: '' }[this.state];
        },

        init() {
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

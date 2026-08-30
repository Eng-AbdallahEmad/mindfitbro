@extends('layouts.web.app')
@section('title', 'حالة الدفع')

@section('style')
<style>
.status-hero {
    background: linear-gradient(135deg, #174DAD 0%, #0f3a87 100%);
    padding: 52px 36px 64px;
    text-align: center;
}
.status-icon {
    width: 72px; height: 72px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
}
.status-icon.success { background: #D4ED57; }
.status-icon.pending { background: #FDE68A; }
.status-icon.failed  { background: #FCA5A5; }
.status-card {
    margin: -28px 24px 0; background: #fff; border-radius: 20px;
    box-shadow: 0 8px 30px rgba(23,77,173,0.12); padding: 28px 24px;
    position: relative; z-index: 2; text-align: center;
}
.cta-btn {
    display: block; width: 100%; padding: 15px; background: #174DAD; color: #fff;
    font-size: 15px; font-weight: 900; border-radius: 14px; text-align: center;
    text-decoration: none; border: none; cursor: pointer; font-family: inherit;
}
.cta-btn:hover { opacity: .9; }
.spinner {
    width: 36px; height: 36px; border-radius: 50%;
    border: 4px solid #E5EAF3; border-top-color: #174DAD;
    animation: spin 0.8s linear infinite; margin: 0 auto 16px;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#EEF2FB] font-arabic py-10 px-4" dir="rtl"
     x-data="paymobStatus('{{ $subscription->status }}', {{ (int) $subscription->isPaid() }})">

    <div class="w-full max-w-md mx-auto">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

            <div class="status-hero">
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

                <p class="text-sm text-gray-500 mb-5" x-text="bodyText"></p>

                <template x-if="state === 'paid'">
                    <a href="{{ route('home') }}" class="cta-btn">الذهاب للرئيسية</a>
                </template>

                <template x-if="state === 'failed'">
                    <form action="{{ route('purchase.retry', $subscription) }}" method="POST">
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

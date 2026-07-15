<div class="space-y-3">
    @if($subscription->plan)
    <a href="{{ route('purchase.form', $subscription->plan) }}" class="cta-primary">
        <span class="material-symbols-rounded align-middle" style="font-size:18px">refresh</span>
        جدّد نفس الباقة — {{ $subscription->plan->name }}
    </a>
    @endif
    <a href="{{ route('home') }}#programs" class="cta-secondary block text-center">
        <span class="material-symbols-rounded align-middle" style="font-size:18px">shuffle</span>
        اختر باقة مختلفة
    </a>
    @php $wa = config('app.whatsapp', env('CONTACT_PHONE', '')) @endphp
    @if($wa)
    <a href="https://wa.me/{{ ltrim($wa, '+') }}" target="_blank" rel="noopener" class="cta-secondary block text-center">
        <span class="material-symbols-rounded align-middle" style="font-size:18px">chat</span>
        تواصل مع الكوتش
    </a>
    @endif
</div>

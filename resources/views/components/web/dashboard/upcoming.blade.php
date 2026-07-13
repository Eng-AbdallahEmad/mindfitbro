@php
    $startDateFmt = $subscription->start_date->locale($locale)
        ->isoFormat($isRtl ? 'dddd، D MMMM YYYY' : 'dddd, MMMM D, YYYY');
@endphp

@if($daysUntilStart === 0)

{{-- ── TODAY: show CTA, no countdown (countdown would loop to reload) ── --}}
<div class="anim anim-1 flex flex-col gap-5">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden font-arabic">
        <div class="bg-gradient-to-br from-primary to-blue-700 px-8 py-12 text-center relative overflow-hidden">
            <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.08) 1px,transparent 1px);background-size:22px 22px;pointer-events:none;"></div>
            <div class="relative z-10">
                <div class="w-20 h-20 rounded-2xl bg-white/15 flex items-center justify-center mx-auto mb-5">
                    <span class="material-symbols-rounded text-accent" style="font-size:40px;font-variation-settings:'FILL' 1">rocket_launch</span>
                </div>
                <div class="inline-flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-4 py-1.5 text-white text-[11px] font-black mb-4">
                    <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 1">celebration</span>
                    {{ __('messages.user_dashboard.upcoming_today_badge') }}
                </div>
                <h2 class="text-white text-2xl font-black mb-3">{{ __('messages.user_dashboard.upcoming_today_desc') }}</h2>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 bg-accent text-textColor font-black font-arabic text-sm px-7 py-3.5 rounded-2xl hover:bg-accent/90 transition mt-2">
                    <span class="material-symbols-rounded" style="font-size:18px">dashboard</span>
                    {{ __('messages.user_dashboard.upcoming_today_cta') }}
                </a>
            </div>
        </div>
    </div>
</div>

@else

{{-- ── FUTURE: countdown ── --}}
<div class="anim anim-1 flex flex-col gap-5">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden font-arabic">

        {{-- Header gradient --}}
        <div class="bg-gradient-to-br from-primary to-blue-700 px-8 pt-8 pb-12 text-center relative overflow-hidden">
            <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.08) 1px,transparent 1px);background-size:22px 22px;pointer-events:none;"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-4 py-1.5 text-white text-[11px] font-black mb-5">
                    <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 1">event_upcoming</span>
                    {{ __('messages.user_dashboard.upcoming_badge') }}
                </div>
                <h2 class="text-white text-2xl font-black mb-2">{{ __('messages.user_dashboard.upcoming_heading') }}</h2>
                <p class="text-accent text-lg font-black">{{ $startDateFmt }}</p>
            </div>
        </div>

        {{-- Countdown strip (overlaps header) --}}
        <div class="mx-6 -mt-8 relative z-10">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
                <p class="text-center text-[10px] font-black text-gray-400 uppercase tracking-wider mb-3">
                    {{ __('messages.user_dashboard.upcoming_time_label') }}
                </p>
                <div class="grid grid-cols-4 gap-2 text-center" id="countdownGrid">
                    @foreach([
                        ['id'=>'cd-days', 'label'=>__('messages.user_dashboard.day_label')],
                        ['id'=>'cd-hours','label'=>__('messages.user_dashboard.at_time')],
                        ['id'=>'cd-mins', 'label'=>'min'],
                        ['id'=>'cd-secs', 'label'=>'sec'],
                    ] as $unit)
                    <div class="bg-[#F4F7FF] rounded-xl py-3">
                        <p class="font-display text-2xl font-black text-primary leading-none" id="{{ $unit['id'] }}">—</p>
                        <p class="text-[10px] font-bold text-gray-400 mt-0.5">{{ $unit['label'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Plan + dates info --}}
        <div class="px-6 pt-5 pb-6 flex flex-col gap-3">
            {{-- Plan chip --}}
            <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-3.5 border border-gray-100" style="direction:{{ $dir }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:{{ $plan->icon_bg ?? '#EFF5FF' }}">
                    <span class="material-symbols-rounded"
                          style="font-size:20px;font-variation-settings:'FILL' 1;color:{{ $plan->icon_color ?? '#174DAD' }}">
                        {{ $plan->icon ?? 'fitness_center' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-black text-textColor leading-none mb-0.5">
                        {{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}
                    </p>
                    <p class="text-xs text-gray-400 font-bold">
                        {{ $subscription->duration_months }} {{ __('messages.user_dashboard.upcoming_months') }}
                    </p>
                </div>
            </div>

            {{-- Date chips --}}
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-blue-50 rounded-xl p-3 text-center border border-blue-100">
                    <p class="text-[10px] font-bold text-blue-400 mb-1">{{ __('messages.user_dashboard.upcoming_start_date') }}</p>
                    <p class="text-xs font-black text-primary">
                        {{ $subscription->start_date->locale($locale)->isoFormat('D MMMM') }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 mb-1">{{ __('messages.user_dashboard.upcoming_end_date') }}</p>
                    <p class="text-xs font-black text-textColor">
                        {{ $subscription->end_date?->locale($locale)->isoFormat('D MMMM Y') ?? '—' }}
                    </p>
                </div>
            </div>

            {{-- First session (if booked) --}}
            @if($hasBooking)
            <div class="flex items-center gap-3 bg-green-50 rounded-2xl p-3.5 border border-green-100" style="direction:{{ $dir }}">
                <span class="material-symbols-rounded text-green-600 flex-shrink-0" style="font-size:22px;font-variation-settings:'FILL' 1">event_available</span>
                <div>
                    <p class="text-xs font-black text-green-700">{{ __('messages.user_dashboard.upcoming_session_booked') }}</p>
                    <p class="text-[11px] text-green-600 font-bold">
                        {{ \Carbon\Carbon::parse($booking->meeting_date)->locale($locale)->isoFormat($isRtl ? 'dddd، D MMMM' : 'dddd, MMMM D') }}
                        — {{ \Carbon\Carbon::parse($booking->meeting_time)->format('g:i A') }}
                    </p>
                </div>
            </div>
            @endif

            {{-- Motivational notice --}}
            <div class="flex items-start gap-3 bg-accent/10 rounded-2xl p-3.5 border border-accent/20">
                <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">lightbulb</span>
                <p class="text-[12px] text-textColor font-bold leading-relaxed">
                    {{ __('messages.user_dashboard.upcoming_motivation') }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Live countdown (only for future dates — avoids reload loop on same day) --}}
<script>
(function() {
    const start = new Date('{{ $startDateIso }}T00:00:00');

    function pad(n) { return String(n).padStart(2,'0'); }

    function tick() {
        const diff = start - new Date();
        if (diff <= 0) { window.location.reload(); return; }

        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000)  / 60000);
        const s = Math.floor((diff % 60000)    / 1000);

        document.getElementById('cd-days').textContent  = d;
        document.getElementById('cd-hours').textContent = pad(h);
        document.getElementById('cd-mins').textContent  = pad(m);
        document.getElementById('cd-secs').textContent  = pad(s);
    }

    tick();
    setInterval(tick, 1000);
})();
</script>

@endif

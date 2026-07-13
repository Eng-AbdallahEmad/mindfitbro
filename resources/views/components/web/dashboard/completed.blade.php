@php
    $journeyUrl   = \Illuminate\Support\Facades\Route::has('journey.show')
        ? route('journey.show', $subscription)
        : route('home');
    $planName     = $plan
        ? (__('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name)
        : '—';
    $weightChange = ($startWeight > 0 && $currentWeight > 0)
        ? round($currentWeight - $startWeight, 1)
        : null;
@endphp

<div class="flex flex-col gap-5 anim anim-1">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden font-arabic">

        {{-- Header --}}
        <div class="bg-gradient-to-br from-gray-700 to-gray-900 px-8 py-8 text-center relative overflow-hidden">
            <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.06) 1px,transparent 1px);background-size:22px 22px;pointer-events:none;"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-rounded text-white" style="font-size:32px;font-variation-settings:'FILL' 1">workspace_premium</span>
                </div>
                <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-3 py-1 text-white/70 text-[10px] font-black mb-3">
                    {{ __('messages.user_dashboard.completed_badge') }}
                </div>
                <h2 class="text-white text-2xl font-black mb-2">{{ __('messages.user_dashboard.completed_title') }}</h2>
                <p class="text-white/60 text-sm">
                    {{ __('messages.user_dashboard.completed_desc', ['plan' => $planName]) }}
                </p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="p-6 flex flex-col gap-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                {{-- Plan --}}
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 mb-1">{{ __('messages.user_dashboard.completed_period') }}</p>
                    <p class="text-xs font-black text-textColor">
                        @if($subscription->start_date && $subscription->end_date)
                            {{ $subscription->start_date->locale($locale)->isoFormat('D MMM') }}
                            →
                            {{ $subscription->end_date->locale($locale)->isoFormat('D MMM Y') }}
                        @else
                            —
                        @endif
                    </p>
                </div>

                {{-- Weight change --}}
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 mb-1">{{ __('messages.user_dashboard.completed_weight_change') }}</p>
                    @if($weightChange !== null)
                    <p class="text-sm font-black {{ $weightChange < 0 ? 'text-green-600' : ($weightChange > 0 ? 'text-red-500' : 'text-gray-500') }}">
                        {{ $weightChange > 0 ? '+' : '' }}{{ $weightChange }} {{ __('messages.user_dashboard.kg') }}
                    </p>
                    @else
                    <p class="text-sm font-black text-gray-400">—</p>
                    @endif
                </div>

                {{-- Attendance --}}
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 mb-1">{{ __('messages.user_dashboard.completed_attendance') }}</p>
                    <p class="text-sm font-black text-textColor">
                        {{ $attendancePct > 0 ? $attendancePct . '%' : '—' }}
                    </p>
                </div>

                {{-- CTA --}}
                <a href="{{ $journeyUrl }}"
                   class="bg-primary rounded-2xl p-4 border border-primary text-center flex flex-col items-center justify-center gap-1 hover:bg-primary/90 transition">
                    <span class="material-symbols-rounded text-white" style="font-size:22px;font-variation-settings:'FILL' 1">timeline</span>
                    <p class="text-[11px] font-black text-white leading-tight">{{ __('messages.user_dashboard.completed_cta') }}</p>
                </a>

            </div>

            {{-- Subscribe again CTA --}}
            <div class="flex flex-col sm:flex-row items-center gap-3 bg-accent/10 border border-accent/20 rounded-2xl px-5 py-4">
                <span class="material-symbols-rounded text-primary flex-shrink-0" style="font-size:22px;font-variation-settings:'FILL' 1">rocket_launch</span>
                <p class="text-sm font-bold text-textColor flex-1 text-center sm:{{ $isRtl ? 'text-right' : 'text-left' }}">
                    {{ __('messages.user_dashboard.completed_subscribe_again') }}
                </p>
                <a href="{{ route('home') }}#programs"
                   class="flex-shrink-0 flex items-center gap-1.5 bg-primary text-white font-black font-arabic text-xs px-4 py-2.5 rounded-xl hover:bg-primary/90 transition whitespace-nowrap">
                    {{ __('messages.user_dashboard.subscribe_now') }}
                </a>
            </div>
        </div>
    </div>
</div>

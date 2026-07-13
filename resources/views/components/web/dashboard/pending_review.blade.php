@php
    $planName = $plan
        ? (__('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name)
        : '—';
@endphp

<div class="flex flex-col gap-5 anim anim-1">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden font-arabic">

        {{-- Header --}}
        <div class="bg-gradient-to-br from-amber-400 to-orange-500 px-8 py-8 text-center relative overflow-hidden">
            <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.1) 1px,transparent 1px);background-size:20px 20px;pointer-events:none;"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-white/15 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-rounded text-white" style="font-size:32px">schedule</span>
                </div>
                <div class="inline-flex items-center gap-2 bg-white/20 border border-white/30 rounded-full px-4 py-1.5 text-white text-[11px] font-black mb-4">
                    <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 1">hourglass_empty</span>
                    {{ __('messages.user_dashboard.pending_review_badge') }}
                </div>
                <h2 class="text-white text-2xl font-black mb-2">{{ __('messages.user_dashboard.pending_review_title') }}</h2>
                <p class="text-white/80 text-sm leading-relaxed max-w-xs mx-auto">{{ __('messages.user_dashboard.pending_review_desc') }}</p>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 flex flex-col gap-4">

            {{-- Order summary chips --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 mb-1">{{ __('messages.user_dashboard.pending_review_plan') }}</p>
                    <p class="text-sm font-black text-textColor leading-tight">{{ $planName }}</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 mb-1">{{ __('messages.user_dashboard.pending_review_duration') }}</p>
                    <p class="text-sm font-black text-textColor">
                        {{ $subscription->duration_months ?? '—' }}
                        {{ $subscription->duration_months ? __('messages.user_dashboard.pending_review_months') : '' }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 mb-1">{{ __('messages.user_dashboard.pending_review_amount') }}</p>
                    <p class="text-sm font-black text-textColor">
                        {{ $subscription->total ? number_format($subscription->total, 0) : '—' }}
                        {{ $subscription->currency ? strtoupper($subscription->currency) : '' }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-400 mb-1">{{ __('messages.user_dashboard.pending_review_date') }}</p>
                    <p class="text-sm font-black text-textColor">
                        {{ $subscription->created_at->locale($locale)->isoFormat('D MMM Y') }}
                    </p>
                </div>
            </div>

            {{-- Email confirmation note --}}
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-2xl px-4 py-3.5">
                <span class="material-symbols-rounded text-blue-500 flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">mark_email_read</span>
                <p class="text-sm text-blue-700 font-bold leading-relaxed">
                    {{ __('messages.user_dashboard.pending_review_email_note') }}
                </p>
            </div>

        </div>
    </div>
</div>

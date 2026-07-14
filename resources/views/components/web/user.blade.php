{{-- All variables come from DashboardController via compact() --}}
@php
    $user    = auth()->user();
    $isRtl   = app()->getLocale() === 'ar';
    $locale  = app()->getLocale();
    $dir     = $isRtl ? 'rtl' : 'ltr';
    $bsRef   = $isRtl ? 'border-right' : 'border-left';
    $dateStr = $isRtl
        ? now()->locale('ar')->isoFormat('dddd، D MMMM Y')
        : now()->locale('en')->isoFormat('dddd, MMMM D, Y');
@endphp

<div class="dash-wrap" x-data="{ sideOpen: false }">

    {{-- ══════════════════════════════════════
         SIDEBAR
    ══════════════════════════════════════ --}}
    <aside class="dash-sidebar" :class="{ open: sideOpen }">

        {{-- Logo --}}
        <div class="flex items-center justify-between mb-8 px-1">
            <img src="{{ asset('assets/logo/mindfitbro.png') }}" class="w-32" alt="MindFitBro">
            <button class="lg:hidden text-white/50 hover:text-white" @click="sideOpen = false">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        {{-- Avatar --}}
        <div class="flex items-center gap-3 px-1 mb-6 pb-6 border-b border-white/10">
            <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center font-black text-textColor font-arabic text-sm flex-shrink-0">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <p class="text-white font-black text-sm font-display leading-none truncate">{{ $user->name }}</p>
                @if($plan && in_array($dashboardState, ['meeting_phase', 'upcoming', 'active', 'completed', 'start_ceremony']))
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full mt-1 inline-block font-arabic
                        {{ $dashboardState === 'meeting_phase' ? 'bg-amber-400/20 text-amber-300' : 'bg-accent/20 text-accent' }}">
                        @if($dashboardState === 'meeting_phase' && $subscription->status === 'approved')
                            <span class="material-symbols-rounded" style="font-size:11px;font-variation-settings:'FILL' 1">check_circle</span>
                        @elseif($dashboardState === 'meeting_phase')
                            <span class="material-symbols-rounded" style="font-size:11px">hourglass_empty</span>
                        @endif
                        {{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}
                    </span>
                @elseif($dashboardState === 'pending_review' && $plan)
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-400/20 text-amber-300 mt-1 inline-block font-arabic">
                        <span class="material-symbols-rounded" style="font-size:11px">schedule</span>
                        {{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}
                    </span>
                @else
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/10 text-white/40 mt-1 inline-block font-arabic">
                        {{ __('messages.user_dashboard.no_plan') }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex flex-col gap-1 flex-1">
            <a href="{{ route('home') }}" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">home</span>
                {{ __('messages.nav.home') }}
            </a>
            <a href="#overview" class="nav-item active">
                <span class="material-symbols-rounded nav-icon" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">dashboard</span>
                {{ __('messages.user_dashboard.nav_dashboard') }}
            </a>
            <a href="#progress" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">monitoring</span>
                {{ __('messages.user_dashboard.nav_progress') }}
            </a>
            @if(in_array($dashboardState, ['meeting_phase', 'upcoming', 'active', 'start_ceremony']))
            <a href="#subscription" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">workspace_premium</span>
                {{ __('messages.user_dashboard.nav_subscription') }}
            </a>
            @endif
            @if($evaluations && $evaluations->count() > 0)
            <a href="#evaluations" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">assignment</span>
                {{ __('messages.user_dashboard.nav_evaluations') }}
            </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <button type="submit" class="nav-item w-full {{ $isRtl ? 'text-right' : 'text-left' }} mt-4">
                    <span class="material-symbols-rounded nav-icon" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">logout</span>
                    {{ __('messages.nav.logout') }}
                </button>
            </form>
        </nav>

        {{-- Bottom plan card --}}
        <div class="pt-4 border-t border-white/10">
            @if($dashboardState === 'upcoming' && $plan)
            <div class="rounded-2xl p-3 font-arabic {{ $isRtl ? 'text-right' : 'text-left' }} bg-blue-400/10 border border-blue-400/20">
                <p class="text-blue-300 text-[11px] font-black mb-0.5 flex items-center gap-1">
                    <span class="material-symbols-rounded" style="font-size:12px;font-variation-settings:'FILL' 1">event_upcoming</span>
                    {{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}
                </p>
                <p class="text-white/40 text-[10px] mb-1">
                    @if($daysUntilStart === 0)
                        <span class="text-accent font-black">{{ __('messages.user_dashboard.sidebar_starts_today') }}</span>
                    @else
                        {{ __('messages.user_dashboard.sidebar_starts_in', ['days' => $daysUntilStart]) }}
                    @endif
                </p>
                <p class="text-white/30 text-[10px]">
                    {{ $subscription->start_date->locale($locale)->isoFormat('D MMMM YYYY') }}
                </p>
            </div>

            @elseif($dashboardState === 'pending_review' && $plan)
            <div class="rounded-2xl p-3 font-arabic {{ $isRtl ? 'text-right' : 'text-left' }} bg-amber-400/10 border border-amber-400/20">
                <p class="text-amber-300 text-[11px] font-black mb-0.5 flex items-center gap-1">
                    <span class="material-symbols-rounded" style="font-size:12px">schedule</span>
                    {{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}
                </p>
                <p class="text-white/40 text-[10px]">{{ __('messages.user_dashboard.pending_review_badge') }}</p>
            </div>

            @elseif($dashboardState === 'meeting_phase' && $plan)
            <div class="rounded-2xl p-3 font-arabic {{ $isRtl ? 'text-right' : 'text-left' }} bg-amber-400/10 border border-amber-400/20">
                <p class="text-amber-300 text-[11px] font-black mb-0.5">
                    @if($subscription->status === 'approved')
                        <span class="material-symbols-rounded" style="font-size:12px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ __('messages.user_dashboard.approved_awaiting') }}
                    @else
                        <span class="material-symbols-rounded" style="font-size:12px">hourglass_empty</span>
                        {{ __('messages.programs.waiting_activation') }}
                    @endif
                </p>
                <p class="text-white/40 text-[10px]">{{ __('messages.user_dashboard.book_to_activate') }}</p>
                <a href="{{ route('booking.show', $subscription->id) }}"
                   class="mt-2 text-[10px] font-black text-amber-300 hover:underline block">
                    {{ __('messages.user_dashboard.book_session_arrow') }}
                </a>
            </div>

            @elseif(in_array($dashboardState, ['active', 'start_ceremony']) && $plan)
            <div class="rounded-2xl p-3 font-arabic {{ $isRtl ? 'text-right' : 'text-left' }} bg-accent/10 border border-accent/20">
                <p class="text-accent text-[11px] font-black mb-0.5">
                    {{ __('messages.programs.plan_prefix') . (__('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name) }}
                </p>
                <p class="text-white/40 text-[10px]">
                    @if($subscription->end_date)
                        {{ __('messages.programs.expires') }} {{ $subscription->end_date->locale($locale)->isoFormat('D MMMM YYYY') }}
                    @endif
                </p>
                <a href="{{ route('home') }}#programs"
                   class="mt-2 text-[10px] font-black text-accent hover:underline block">
                    {{ __('messages.user_dashboard.upgrade_plan_arrow') }}
                </a>
            </div>

            @elseif($dashboardState === 'completed' && $plan)
            <div class="rounded-2xl bg-white/5 border border-white/10 p-3 font-arabic {{ $isRtl ? 'text-right' : 'text-left' }}">
                <p class="text-white/50 text-[11px] font-black mb-0.5">{{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}</p>
                <p class="text-white/30 text-[10px]">{{ __('messages.user_dashboard.expired') }}</p>
                <a href="{{ route('home') }}#programs"
                   class="mt-2 text-[10px] font-black text-accent hover:underline block">
                    {{ __('messages.user_dashboard.subscribe_arrow') }}
                </a>
            </div>

            @else
            <div class="rounded-2xl bg-white/5 border border-white/10 p-3 font-arabic {{ $isRtl ? 'text-right' : 'text-left' }}">
                <p class="text-white/50 text-[11px] font-black mb-0.5">{{ __('messages.user_dashboard.no_active_sub') }}</p>
                <a href="{{ route('home') }}#programs"
                   class="mt-2 text-[10px] font-black text-accent hover:underline block">
                    {{ __('messages.user_dashboard.subscribe_arrow') }}
                </a>
            </div>
            @endif
        </div>

    </aside>

    {{-- ══════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════ --}}
    <main class="flex flex-col gap-5 p-5 lg:p-8 overflow-y-auto">

        {{-- Topbar --}}
        <div class="flex items-center justify-between">
            <div class="font-arabic" style="direction:{{ $dir }}">
                <p class="text-gray-400 text-sm mb-1 font-bold">{{ $dateStr }}</p>
                <h1 class="font-display text-2xl lg:text-3xl text-textColor font-black">
                    {{ __('messages.user_dashboard.hello') }} {{ explode(' ', $user->name)[0] }}
                </h1>
            </div>
            <div class="flex items-center gap-3">
                @if(in_array($dashboardState, ['active', 'start_ceremony']) && $streak > 0)
                    <div class="hidden md:flex items-center gap-2 bg-white rounded-full px-4 py-2 border border-gray-100 shadow-sm font-arabic">
                        <span class="material-symbols-rounded text-orange-500" style="font-size:20px;font-variation-settings:'FILL' 1">local_fire_department</span>
                        <span class="font-black text-sm text-textColor">{{ $streak }}</span>
                        <span class="text-xs text-gray-400 font-bold">{{ __('messages.user_dashboard.streak_days') }}</span>
                    </div>
                @endif
                <button class="lg:hidden w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center"
                    @click="sideOpen = true">
                    <span class="material-symbols-rounded text-textColor" style="font-size:20px">menu</span>
                </button>
            </div>
        </div>

        {{-- State-specific content --}}
        @include('components.web.dashboard.' . $dashboardState)

    </main>

</div>

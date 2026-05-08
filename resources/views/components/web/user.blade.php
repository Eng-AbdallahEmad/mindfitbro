@props(['subscription' => null, 'progress' => [], 'evaluations' => null])

@php
    $user   = auth()->user();
    $isRtl  = app()->getLocale() === 'ar';
    $locale = app()->getLocale();
    $dir    = $isRtl ? 'rtl' : 'ltr';
    $bsRef  = $isRtl ? 'border-right' : 'border-left';

    $hasSub        = $subscription !== null && in_array($subscription->status, ['active', 'waiting']);
    $isWaiting     = $hasSub && $subscription->status === 'waiting';
    $booking       = $hasSub ? $subscription->meetingBookings->first() : null;
    $hasBooking    = $booking !== null;
    $plan          = $hasSub ? $subscription->plan : null;

    $daysLeft = ($hasSub && $subscription->end_date)
                ? (int) now()->startOfDay()->diffInDays($subscription->end_date->startOfDay(), false)
                : 0;

    $totalDays = ($hasSub && $subscription->start_date && $subscription->end_date)
                 ? (int) $subscription->start_date->diffInDays($subscription->end_date)
                 : 1;

    $daysUsed = ($hasSub && $subscription->start_date)
                ? (int) $subscription->start_date->diffInDays(now())
                : 0;

    $subPct = $totalDays > 0 ? min(100, (int) round(($daysUsed / $totalDays) * 100)) : 0;

    $startsInFuture = $hasSub && !$isWaiting
                  && $subscription->start_date
                  && $subscription->start_date->isFuture();

    // Progress data
    $startWeight   = $progress['startWeight']   ?? 0;
    $currentWeight = $progress['currentWeight'] ?? 0;
    $goalWeight    = $progress['goalWeight']    ?? 0;
    $weeksDone     = $progress['weeksDone']     ?? 0;
    $totalWeeks    = $progress['totalWeeks']    ?? 0;
    $pct           = $progress['pct']           ?? 0;
    $streak        = $progress['streak']        ?? 0;
    $weekDays      = $progress['weekDays']      ?? [];

    // Weight goal progress
    $wRange     = abs($goalWeight - $startWeight);
    $wDone      = abs($currentWeight - $startWeight);
    $wPct       = $wRange > 0 ? min(100, (int) round(($wDone / $wRange) * 100)) : 0;
    $wRemaining = round(abs($goalWeight - $currentWeight), 1);
    $wLosing    = $goalWeight < $startWeight;

    // Today's status
    $todayDayStatus = $weekDays[now()->dayOfWeek]['status'] ?? 'upcoming';

    // Locale-aware date
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
                @if($hasSub)
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full mt-1 inline-block font-arabic
                        {{ $isWaiting ? 'bg-amber-400/20 text-amber-300' : 'bg-accent/20 text-accent' }}">
                        {{ $isWaiting ? '⏳ ' : '' }}{{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}
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
            @if($hasSub)
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
            @if($hasSub)
                <div class="rounded-2xl p-3 font-arabic {{ $isRtl ? 'text-right' : 'text-left' }}
                    {{ $isWaiting ? 'bg-amber-400/10 border border-amber-400/20' : 'bg-accent/10 border border-accent/20' }}">
                    <p class="{{ $isWaiting ? 'text-amber-300' : 'text-accent' }} text-[11px] font-black mb-0.5">
                        {{ $isWaiting
                            ? '⏳ ' . __('messages.programs.waiting_activation')
                            : __('messages.programs.plan_prefix') . (__('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name) }}
                    </p>
                    <p class="text-white/40 text-[10px]">
                        @if($isWaiting)
                            {{ __('messages.user_dashboard.book_to_activate') }}
                        @elseif($subscription->end_date)
                            {{ __('messages.programs.expires') }} {{ $subscription->end_date->locale($locale)->isoFormat('D MMMM YYYY') }}
                        @endif
                    </p>
                    @if($isWaiting)
                        <a href="{{ route('booking.show', $subscription->id) }}"
                           class="mt-2 text-[10px] font-black text-amber-300 hover:underline block">
                            {{ __('messages.user_dashboard.book_session_arrow') }}
                        </a>
                    @else
                        <a href="{{ route('home') }}#programs"
                           class="mt-2 text-[10px] font-black text-accent hover:underline block">
                            {{ __('messages.user_dashboard.upgrade_plan_arrow') }}
                        </a>
                    @endif
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

        {{-- ── Topbar ── --}}
        <div class="flex items-center justify-between">
            <div class="font-arabic" style="direction:{{ $dir }}">
                <p class="text-gray-400 text-sm mb-1 font-bold">{{ $dateStr }}</p>
                <h1 class="font-display text-2xl lg:text-3xl text-textColor font-black">
                    {{ __('messages.user_dashboard.hello') }} {{ explode(' ', $user->name)[0] }}
                </h1>
            </div>
            <div class="flex items-center gap-3">
                @if($hasSub && !$isWaiting && $streak > 0)
                    <div class="hidden md:flex items-center gap-2 bg-white rounded-full px-4 py-2 border border-gray-100 shadow-sm font-arabic">
                        <span class="text-lg">🔥</span>
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

        {{-- ══════════════════════════════════════
             STATE: NO SUBSCRIPTION
        ══════════════════════════════════════ --}}
        @if(! $hasSub)
            <div class="flex items-center justify-center flex-1 py-16 anim anim-1">
                <div class="bg-white rounded-3xl border border-gray-100 p-10 max-w-sm w-full text-center flex flex-col items-center gap-6">
                    <div class="w-[72px] h-[72px] rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center">
                        <span class="material-symbols-rounded text-gray-400" style="font-size:32px">lock</span>
                    </div>
                    <div class="font-arabic">
                        <h2 class="font-black text-textColor text-xl mb-2">{{ __('messages.user_dashboard.no_sub_title') }}</h2>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            {{ __('messages.user_dashboard.no_sub_desc') }}
                        </p>
                    </div>
                    <div class="w-full flex flex-col gap-3">
                        @foreach([
                            ['icon'=>'fitness_center','color'=>'text-primary','bg'=>'bg-blue-50','title'=>__('messages.user_dashboard.feat_training'),'sub'=>__('messages.user_dashboard.feat_training_sub')],
                            ['icon'=>'monitoring','color'=>'text-green-600','bg'=>'bg-green-50','title'=>__('messages.user_dashboard.feat_progress'),'sub'=>__('messages.user_dashboard.feat_progress_sub')],
                            ['icon'=>'sports_score','color'=>'text-amber-500','bg'=>'bg-amber-50','title'=>__('messages.user_dashboard.feat_coach'),'sub'=>__('messages.user_dashboard.feat_coach_sub')],
                        ] as $feat)
                        <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-3 border border-gray-100" style="direction:{{ $dir }}">
                            <div class="w-9 h-9 rounded-xl {{ $feat['bg'] }} flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded {{ $feat['color'] }}" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">{{ $feat['icon'] }}</span>
                            </div>
                            <div class="font-arabic">
                                <p class="text-sm font-black text-textColor leading-none mb-0.5">{{ $feat['title'] }}</p>
                                <p class="text-xs text-gray-400 font-bold">{{ $feat['sub'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="w-full flex flex-col gap-2">
                        <a href="{{ route('home') }}#programs"
                            class="w-full flex items-center justify-center gap-2 bg-primary text-white font-black font-arabic text-sm px-6 py-3.5 rounded-2xl hover:bg-primary/90 transition">
                            <span class="material-symbols-rounded" style="font-size:18px;color:#D4ED57">rocket_launch</span>
                            {{ __('messages.user_dashboard.subscribe_now') }}
                        </a>
                        <a href="{{ route('home') }}"
                            class="w-full text-center text-xs text-gray-400 font-bold font-arabic py-2 hover:text-primary transition">
                            {{ __('messages.user_dashboard.back_home') }}
                        </a>
                    </div>
                </div>
            </div>

        @else

        {{-- ══════════════════════════════════════
             STATE: WAITING
        ══════════════════════════════════════ --}}
        @if($isWaiting)

            {{-- Booking prompt / confirmation --}}
            @if(! $hasBooking)
            <div class="anim anim-1">
                <div class="card waiting-card-pulse border-2 border-dashed border-primary/25 bg-primary/[0.02]" style="direction:{{ $dir }}">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center flex-shrink-0 text-3xl">🗓️</div>
                        <div class="flex-1 font-arabic">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <h3 class="font-black text-textColor text-lg">{{ __('messages.user_dashboard.set_first_session') }}</h3>
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200 animate-pulse">
                                    {{ __('messages.user_dashboard.required_activation') }}
                                </span>
                            </div>
                            <p class="text-gray-400 text-sm font-bold leading-relaxed mb-3">
                                {{ __('messages.user_dashboard.waiting_desc') }}
                            </p>
                            <div class="flex flex-wrap items-center gap-3">
                                @foreach([
                                    ['n'=>'1','label'=>__('messages.user_dashboard.step_book'),'active'=>true],
                                    ['n'=>'2','label'=>__('messages.user_dashboard.step_session'),'active'=>false],
                                    ['n'=>'3','label'=>__('messages.user_dashboard.step_activate'),'active'=>false],
                                ] as $step)
                                    @if(!$loop->first)<span class="text-gray-300">{{ $isRtl ? '←' : '→' }}</span>@endif
                                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-gray-400">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0
                                            {{ $step['active'] ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                            {{ $step['n'] }}
                                        </span>
                                        {{ $step['label'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <a href="{{ route('booking.show', $subscription->id) }}"
                           class="flex-shrink-0 flex items-center gap-2 bg-primary text-white font-black font-arabic text-sm px-5 py-3 rounded-xl hover:bg-primary/90 transition whitespace-nowrap self-stretch sm:self-auto justify-center">
                            <span class="material-symbols-rounded" style="font-size:18px;color:#D4ED57">calendar_add_on</span>
                            {{ __('messages.user_dashboard.book_now_btn') }}
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="anim anim-1">
                <div class="card" style="direction:{{ $dir }}; {{ $bsRef }}: 4px solid #22c55e;">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center flex-shrink-0 text-2xl">📅</div>
                        <div class="flex-1 font-arabic">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-base font-black text-textColor">{{ __('messages.user_dashboard.session_scheduled') }}</span>
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-full border
                                    {{ $booking->status === 'confirmed' ? 'bg-green-50 text-green-600 border-green-200' : 'bg-amber-50 text-amber-600 border-amber-200' }}">
                                    {{ $booking->status === 'confirmed' ? __('messages.user_dashboard.confirmed_badge') : __('messages.user_dashboard.pending_badge') }}
                                </span>
                            </div>
                            <p class="text-gray-500 text-sm font-bold">
                                <span class="text-textColor">
                                    {{ \Carbon\Carbon::parse($booking->meeting_date)->locale($locale)->isoFormat($isRtl ? 'dddd، D MMMM Y' : 'dddd, MMMM D, Y') }}
                                </span>
                                {{ __('messages.user_dashboard.at_time') }}
                                <span class="text-textColor">{{ \Carbon\Carbon::parse($booking->meeting_time)->format('H:i') }}</span>
                            </p>
                            @if($booking->meet_link && !str_contains($booking->meet_link, 'xxx'))
                            <a href="{{ $booking->meet_link }}" target="_blank"
                               class="inline-flex items-center gap-1.5 mt-2 text-[12px] font-black text-primary hover:underline">
                                <span class="material-symbols-rounded" style="font-size:15px">videocam</span>
                                {{ __('messages.user_dashboard.open_meeting_link') }}
                            </a>
                            @endif
                        </div>
                        <a href="{{ route('booking.show', $subscription->id) }}"
                           class="flex-shrink-0 flex items-center gap-1.5 text-[12px] font-black text-gray-400 hover:text-primary font-arabic transition border border-gray-200 hover:border-primary rounded-xl px-3 py-2 whitespace-nowrap">
                            <span class="material-symbols-rounded" style="font-size:15px">edit_calendar</span>
                            {{ __('messages.user_dashboard.change_appointment') }}
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim anim-2">
                <div class="card lg:col-span-2 flex items-center gap-4" style="direction:{{ $dir }}">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background: {{ $plan->icon_bg ?? '#EFF5FF' }}">
                        <span class="material-symbols-rounded"
                              style="font-size:28px;font-variation-settings:'FILL' 1; color: {{ $plan->icon_color ?? '#174DAD' }}">
                            {{ $plan->icon ?? 'star' }}
                        </span>
                    </div>
                    <div class="font-arabic flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <p class="font-black text-textColor text-xl leading-none">{{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}</p>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200">
                                {{ __('messages.programs.waiting_activation') }}
                            </span>
                        </div>
                        <p class="text-gray-400 text-xs font-bold">
                            {{ (__('messages.plans_data.'.$plan->key.'.desc', [], null) ?: $plan->desc) ?? __('messages.programs.activation_note') }}
                        </p>
                    </div>
                </div>
                <div class="card flex items-center gap-4" style="direction:{{ $dir }}">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0 text-3xl">⏳</div>
                    <div class="font-arabic">
                        <p class="text-gray-400 text-xs font-bold mb-0.5">{{ __('messages.user_dashboard.sub_status_label') }}</p>
                        <p class="font-black text-textColor text-base leading-none mb-1">{{ __('messages.user_dashboard.waiting_status') }}</p>
                        <p class="text-amber-500 text-[11px] font-bold">{{ __('messages.user_dashboard.book_activate_sub') }}</p>
                    </div>
                </div>
            </div>

        {{-- ══════════════════════════════════════
             STATE: UPCOMING (starts in future)
        ══════════════════════════════════════ --}}
        @elseif($startsInFuture)
            <div class="flex items-center justify-center flex-1 py-16 anim anim-1">
                <div class="bg-white rounded-3xl border border-gray-100 p-10 max-w-sm w-full text-center flex flex-col items-center gap-6">
                    <div class="w-[72px] h-[72px] rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center">
                        <span class="material-symbols-rounded text-primary" style="font-size:32px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">event_upcoming</span>
                    </div>
                    <div class="font-arabic">
                        <h2 class="font-black text-textColor text-xl mb-2">{{ __('messages.user_dashboard.plan_ready') }}</h2>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            {{ __('messages.user_dashboard.journey_starts_on') }}
                            <span class="font-black text-primary">
                                {{ $subscription->start_date->locale($locale)->isoFormat('D MMMM YYYY') }}
                            </span>
                        </p>
                    </div>
                    <div class="w-full bg-blue-50 border border-blue-100 rounded-2xl p-4 font-arabic">
                        <p class="text-primary text-xs font-bold mb-2">{{ __('messages.user_dashboard.days_until_start') }}</p>
                        <p class="font-display text-4xl font-black text-primary leading-none">
                            {{ (int) now()->startOfDay()->diffInDays($subscription->start_date->startOfDay()) }}
                            <span class="text-sm font-bold text-gray-400">{{ __('messages.user_dashboard.day_unit') }}</span>
                        </p>
                    </div>
                    <div class="w-full flex items-center gap-3 bg-gray-50 rounded-2xl p-3 border border-gray-100" style="direction:{{ $dir }}">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                            style="background: {{ $plan->icon_bg ?? '#EFF5FF' }}">
                            <span class="material-symbols-rounded"
                                style="font-size:20px;font-variation-settings:'FILL' 1; color: {{ $plan->icon_color ?? '#174DAD' }}">
                                {{ $plan->icon ?? 'star' }}
                            </span>
                        </div>
                        <div class="font-arabic">
                            <p class="text-sm font-black text-textColor leading-none mb-0.5">{{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}</p>
                            <p class="text-xs text-gray-400 font-bold">
                                {{ __('messages.programs.expires') }} {{ $subscription->end_date?->locale($locale)->isoFormat('D MMMM YYYY') ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        @else
        {{-- ══════════════════════════════════════
             STATE: ACTIVE — FULL DASHBOARD
        ══════════════════════════════════════ --}}

        {{-- ── ROW 1: Journey + Today + Streak ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5" id="overview">

            {{-- Journey Progress Card --}}
            <div class="card-dark lg:col-span-2 anim anim-1">
                <div class="relative z-10 flex flex-col sm:flex-row items-center gap-6">

                    {{-- Progress ring --}}
                    <div class="relative flex-shrink-0">
                        <svg width="130" height="130" viewBox="0 0 130 130">
                            <circle cx="65" cy="65" r="58" class="ring-bg"/>
                            <circle cx="65" cy="65" r="58" class="ring-fill" id="journeyRing"
                                style="stroke-dashoffset: {{ 408 - (408 * $pct / 100) }}"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center font-arabic">
                            <span class="font-display text-3xl font-black text-white leading-none">{{ $pct }}%</span>
                            <span class="text-white/50 text-[10px] font-bold">{{ __('messages.user_dashboard.completed_pct') }}</span>
                        </div>
                    </div>

                    {{-- Journey info --}}
                    <div class="flex-1 font-arabic text-center sm:{{ $isRtl ? 'text-right' : 'text-left' }}">
                        <span class="inline-block bg-accent/20 text-accent text-[10px] font-black px-3 py-1 rounded-full mb-3">
                            {{ __('messages.user_dashboard.journey_ongoing') }}
                        </span>
                        <h2 class="text-white font-black text-xl mb-1 leading-tight">
                            {{ __('messages.user_dashboard.week_of', ['done' => $weeksDone, 'total' => $totalWeeks]) }}
                        </h2>
                        <p class="text-white/50 text-xs font-bold mb-4">
                            {{ $weeksDone }} {{ $weeksDone == 1 ? __('messages.user_dashboard.week_done_1') : __('messages.user_dashboard.week_done_n') }}
                            &nbsp;·&nbsp;
                            {{ max(0, $totalWeeks - $weeksDone) }} {{ (max(0, $totalWeeks - $weeksDone)) == 1 ? __('messages.user_dashboard.week_left_1') : __('messages.user_dashboard.week_left_n') }}
                        </p>
                        @if($streak > 0)
                        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/10 rounded-full px-3 py-1.5">
                            <span class="text-sm">🔥</span>
                            <span class="text-white font-black text-sm">{{ $streak }}</span>
                            <span class="text-white/50 text-xs font-bold">{{ __('messages.user_dashboard.consecutive_day') }}</span>
                        </div>
                        @endif
                    </div>

                </div>

                {{-- Week strip --}}
                <div class="relative z-10 mt-5 pt-5 border-t border-white/10">
                    <p class="text-white/40 text-[10px] font-bold font-arabic mb-3 {{ $isRtl ? 'text-right' : 'text-left' }}">{{ __('messages.user_dashboard.current_week') }}</p>
                    <div class="flex items-end justify-between gap-1">
                        @foreach($weekDays as $idx => $d)
                        <div class="flex flex-col items-center gap-1.5 flex-1">
                            <div class="day-dot
                                {{ $d['status'] === 'done'     ? 'day-done'     : '' }}
                                {{ $d['status'] === 'today'    ? 'day-today'    : '' }}
                                {{ $d['status'] === 'rest'     ? 'day-rest'     : '' }}
                                {{ $d['status'] === 'upcoming' ? 'day-upcoming' : '' }}
                                {{ $d['status'] === 'missed'   ? 'day-missed'   : '' }}">
                                @if($d['status'] === 'done')
                                    <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check</span>
                                @elseif($d['status'] === 'rest')
                                    <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">hotel</span>
                                @elseif($d['status'] === 'missed')
                                    <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">close</span>
                                @else
                                    <span style="font-size:.65rem;font-weight:800;font-family:'Cairo',sans-serif">{{ $d['label'] }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right column: Today + Streak --}}
            <div class="flex flex-col gap-5">

                {{-- Today status card --}}
                @if($todayDayStatus === 'done')
                <div class="card anim anim-2 flex items-center gap-4" style="direction:{{ $dir }}; {{ $bsRef }}:4px solid #22c55e">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-green-600" style="font-size:24px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">task_alt</span>
                    </div>
                    <div class="font-arabic">
                        <p class="font-black text-textColor text-sm">{{ __('messages.user_dashboard.attended_today') }}</p>
                        <p class="text-green-600 text-xs font-bold mt-0.5">{{ __('messages.user_dashboard.keep_going') }}</p>
                    </div>
                </div>
                @elseif($todayDayStatus === 'rest')
                <div class="card anim anim-2 flex items-center gap-4" style="direction:{{ $dir }}; {{ $bsRef }}:4px solid #94a3b8">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:24px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">bedtime</span>
                    </div>
                    <div class="font-arabic">
                        <p class="font-black text-textColor text-sm">{{ __('messages.user_dashboard.rest_day') }}</p>
                        <p class="text-slate-400 text-xs font-bold mt-0.5">{{ __('messages.user_dashboard.body_recovering') }}</p>
                    </div>
                </div>
                @elseif($todayDayStatus === 'missed')
                <div class="card anim anim-2 flex items-center gap-4" style="direction:{{ $dir }}; {{ $bsRef }}:4px solid #ef4444">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-red-400" style="font-size:24px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">cancel</span>
                    </div>
                    <div class="font-arabic">
                        <p class="font-black text-textColor text-sm">{{ __('messages.user_dashboard.missed_workout') }}</p>
                        <p class="text-red-400 text-xs font-bold mt-0.5">{{ __('messages.user_dashboard.tomorrow_msg') }}</p>
                    </div>
                </div>
                @else
                <div class="card anim anim-2 flex items-center gap-4" style="direction:{{ $dir }}; {{ $bsRef }}:4px solid #174DAD">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:24px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">fitness_center</span>
                    </div>
                    <div class="font-arabic">
                        <p class="font-black text-textColor text-sm">{{ __('messages.user_dashboard.workout_time') }}</p>
                        <p class="text-primary text-xs font-bold mt-0.5">{{ __('messages.user_dashboard.go_train') }}</p>
                    </div>
                </div>
                @endif

                {{-- Streak card --}}
                <div class="card anim anim-3 flex items-center gap-4 flex-1" style="direction:{{ $dir }}">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0 text-3xl">🔥</div>
                    <div class="font-arabic">
                        <p class="text-gray-400 text-xs font-bold mb-0.5">{{ __('messages.user_dashboard.streak_label') }}</p>
                        <p class="font-display text-4xl font-black text-textColor leading-none">
                            {{ $streak }}<span class="text-base font-bold text-gray-300"> {{ __('messages.user_dashboard.day_label') }}</span>
                        </p>
                        <p class="text-[11px] font-bold mt-1
                            {{ $streak === 0 ? 'text-gray-400' : ($streak < 3 ? 'text-gray-500' : ($streak < 7 ? 'text-amber-500' : 'text-orange-500')) }}">
                            @if($streak === 0)     {{ __('messages.user_dashboard.streak_0') }}
                            @elseif($streak < 3)   {{ __('messages.user_dashboard.streak_low') }}
                            @elseif($streak < 7)   {{ __('messages.user_dashboard.streak_good') }}
                            @elseif($streak < 14)  {{ __('messages.user_dashboard.streak_week') }}
                            @elseif($streak < 30)  {{ __('messages.user_dashboard.streak_fire') }}
                            @else                  {{ __('messages.user_dashboard.streak_legend', ['days' => $streak]) }}
                            @endif
                        </p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── ROW 2: Weight Journey + Plan ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5" id="progress">

            {{-- Weight Journey Card --}}
            <div class="card lg:col-span-2 anim anim-4" style="direction:{{ $dir }}">
                <div class="flex items-center justify-between mb-5">
                    <div class="font-arabic">
                        <h3 class="font-black text-textColor text-sm">{{ __('messages.user_dashboard.weight_journey') }}</h3>
                        <p class="text-gray-400 text-xs font-bold mt-0.5">{{ __('messages.user_dashboard.from_start_to_goal') }}</p>
                    </div>
                    <span class="material-symbols-rounded text-gray-200" style="font-size:28px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">monitor_weight</span>
                </div>

                {{-- 3 weight boxes --}}
                <div class="grid grid-cols-3 gap-3 mb-5">
                    {{-- Start --}}
                    <div class="flex flex-col items-center gap-1.5 bg-gray-50 rounded-2xl p-4 border border-gray-100">
                        <span class="material-symbols-rounded text-gray-300" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">flag_circle</span>
                        <p class="text-[10px] font-bold text-gray-400 font-arabic">{{ __('messages.user_dashboard.start_weight') }}</p>
                        <p class="font-display text-2xl font-black text-textColor leading-none">
                            {{ $startWeight > 0 ? $startWeight : '—' }}
                        </p>
                        @if($startWeight > 0)
                        <p class="text-[10px] text-gray-400 font-bold font-arabic">{{ __('messages.user_dashboard.kg') }}</p>
                        @endif
                    </div>

                    {{-- Current --}}
                    <div class="flex flex-col items-center gap-1.5 rounded-2xl p-4 border-2 border-primary/20 bg-primary/5 relative">
                        @if($startWeight > 0 && $currentWeight !== $startWeight)
                        <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 text-[10px] font-black px-2 py-0.5 rounded-full whitespace-nowrap
                            {{ $currentWeight < $startWeight ? 'bg-green-500 text-white' : 'bg-red-400 text-white' }}">
                            {{ $currentWeight < $startWeight ? '▼' : '▲' }}
                            {{ abs(round($startWeight - $currentWeight, 1)) }} {{ __('messages.user_dashboard.kg') }}
                        </span>
                        @endif
                        <span class="material-symbols-rounded text-primary" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">person</span>
                        <p class="text-[10px] font-bold text-primary font-arabic">{{ __('messages.user_dashboard.current_weight') }}</p>
                        <p class="font-display text-2xl font-black text-primary leading-none">
                            {{ $currentWeight > 0 ? $currentWeight : '—' }}
                        </p>
                        @if($currentWeight > 0)
                        <p class="text-[10px] text-primary/50 font-bold font-arabic">{{ __('messages.user_dashboard.kg') }}</p>
                        @endif
                    </div>

                    {{-- Goal --}}
                    <div class="flex flex-col items-center gap-1.5 rounded-2xl p-4 border border-dashed border-accent/50 bg-accent/5">
                        <span class="material-symbols-rounded text-yellow-500" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">emoji_events</span>
                        <p class="text-[10px] font-bold text-yellow-600 font-arabic">{{ __('messages.user_dashboard.goal_weight') }}</p>
                        <p class="font-display text-2xl font-black text-textColor leading-none">
                            {{ $goalWeight > 0 ? $goalWeight : '—' }}
                        </p>
                        @if($goalWeight > 0)
                        <p class="text-[10px] text-gray-400 font-bold font-arabic">{{ __('messages.user_dashboard.kg') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Progress bar toward goal --}}
                @if($wRange > 0)
                <div class="font-arabic">
                    <div class="flex justify-between items-center text-[11px] font-bold text-gray-400 mb-2">
                        <span>{{ __('messages.user_dashboard.progress_kg', ['val' => round($wDone, 1)]) }}</span>
                        <span class="font-black {{ $wPct >= 100 ? 'text-green-600' : 'text-textColor' }}">{{ $wPct }}%</span>
                        <span>{{ __('messages.user_dashboard.remaining_kg', ['val' => $wRemaining]) }}</span>
                    </div>
                    <div class="macro-bar-wrap" style="height:10px">
                        <div class="macro-bar-fill"
                             style="width: {{ $wPct }}%; background: {{ $wPct >= 100 ? '#22c55e' : 'linear-gradient(90deg,#174DAD,#D4ED57)' }}">
                        </div>
                    </div>
                    @if($wPct >= 100)
                    <p class="text-center text-[11px] font-black text-green-600 mt-2">{{ __('messages.user_dashboard.goal_reached') }}</p>
                    @else
                    <p class="text-gray-400 text-[10px] font-bold mt-2 text-center">
                        {{ $wLosing
                            ? __('messages.user_dashboard.lose_to_goal', ['val' => $wRemaining])
                            : __('messages.user_dashboard.gain_to_goal', ['val' => $wRemaining]) }}
                    </p>
                    @endif
                </div>
                @else
                <div class="text-center py-2 font-arabic">
                    <p class="text-gray-300 text-xs font-bold">{{ __('messages.user_dashboard.add_weight_prompt') }}</p>
                </div>
                @endif
            </div>

            {{-- Plan Card --}}
            <div class="card anim anim-5 flex flex-col gap-4" style="direction:{{ $dir }}" id="subscription">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-black text-gray-400 font-arabic">{{ __('messages.user_dashboard.current_plan') }}</span>
                    <a href="{{ route('home') }}#programs" class="text-[11px] font-black text-primary font-arabic hover:underline">{{ __('messages.user_dashboard.upgrade') }}</a>
                </div>

                {{-- Plan icon + name --}}
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background: {{ $plan->icon_bg ?? '#EFF5FF' }}">
                        <span class="material-symbols-rounded"
                              style="font-size:24px;font-variation-settings:'FILL' 1; color: {{ $plan->icon_color ?? '#174DAD' }}">
                            {{ $plan->icon ?? 'star' }}
                        </span>
                    </div>
                    <div class="font-arabic min-w-0">
                        <p class="font-black text-textColor text-base leading-none truncate">
                            {{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}
                        </p>
                        <p class="text-gray-400 text-xs mt-0.5 font-bold">
                            {{ $subscription->start_date?->locale($locale)->isoFormat('D MMMM YYYY') ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- Status badge --}}
                @if($daysLeft <= 0)
                    <span class="self-start text-[10px] font-black font-arabic text-red-500 bg-red-50 px-3 py-1 rounded-full border border-red-100">{{ __('messages.user_dashboard.expired') }}</span>
                @elseif($daysLeft <= 5)
                    <span class="self-start text-[10px] font-black font-arabic text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-100 animate-pulse">{{ __('messages.user_dashboard.expiring_soon') }}</span>
                @else
                    <span class="self-start text-[10px] font-black font-arabic text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">{{ __('messages.user_dashboard.active') }}</span>
                @endif

                {{-- Days remaining bar --}}
                <div class="flex-1 flex flex-col justify-end gap-2 font-arabic">
                    <div class="flex justify-between text-[11px] font-bold text-gray-400">
                        <span>{{ __('messages.user_dashboard.days_left', ['days' => max(0, $daysLeft)]) }}</span>
                        <span>{{ __('messages.user_dashboard.total_days_label', ['days' => $totalDays]) }}</span>
                    </div>
                    <div class="macro-bar-wrap">
                        <div class="macro-bar-fill"
                             style="width: {{ max(0, 100 - $subPct) }}%;
                                    background: {{ $daysLeft <= 5 ? '#f59e0b' : '#174DAD' }}">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold">
                        {{ __('messages.user_dashboard.expires_on') }} {{ $subscription->end_date?->locale($locale)->isoFormat('D MMMM YYYY') ?? '—' }}
                    </p>
                </div>
            </div>

        </div>

        {{-- ── ROW 3: Evaluations ── --}}
        @if($evaluations && $evaluations->count() > 0)
        <div class="flex flex-col gap-4" id="evaluations">

            <div class="flex items-center justify-between font-arabic" style="direction:{{ $dir }}">
                <h3 class="font-black text-textColor text-base">{{ __('messages.user_dashboard.evaluations_title') }}</h3>
                <span class="text-xs text-gray-400 font-bold">
                    {{ $evaluations->count() }} {{ $evaluations->count() == 1 ? __('messages.user_dashboard.eval_count_1') : __('messages.user_dashboard.eval_count_n') }}
                </span>
            </div>

            <div class="flex flex-col gap-3 anim anim-6">
                @foreach($evaluations as $idx => $eval)
                @php
                    $prevEval    = $evaluations->get($idx + 1);
                    $weightDiff  = $prevEval ? round($eval->weight - $prevEval->weight, 1) : null;
                    $fitnessBadge = match($eval->fitness_level) {
                        'beginner'     => ['label' => __('messages.user_dashboard.beginner'),     'class' => 'bg-blue-50 text-blue-600 border-blue-100'],
                        'intermediate' => ['label' => __('messages.user_dashboard.intermediate'), 'class' => 'bg-amber-50 text-amber-600 border-amber-100'],
                        'advanced'     => ['label' => __('messages.user_dashboard.advanced'),     'class' => 'bg-green-50 text-green-600 border-green-100'],
                        default        => ['label' => $eval->fitness_level,                       'class' => 'bg-gray-50 text-gray-600 border-gray-100'],
                    };
                @endphp
                <div class="card" style="direction:{{ $dir }}">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-4">

                        {{-- Date + fitness level --}}
                        <div class="flex-shrink-0 font-arabic">
                            <p class="text-gray-400 text-xs font-bold mb-1.5">
                                {{ $eval->evaluated_at->locale($locale)->isoFormat('D MMMM YYYY') }}
                            </p>
                            <span class="text-[10px] font-black px-2.5 py-1 rounded-full border {{ $fitnessBadge['class'] }}">
                                {{ $fitnessBadge['label'] }}
                            </span>
                        </div>

                        {{-- Measurements --}}
                        <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-2">
                            {{-- Weight --}}
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-center relative">
                                @if($weightDiff !== null)
                                <span class="absolute -top-2 left-1/2 -translate-x-1/2 text-[9px] font-black px-1.5 py-0.5 rounded-full whitespace-nowrap
                                    {{ $weightDiff < 0 ? 'bg-green-500 text-white' : ($weightDiff > 0 ? 'bg-red-400 text-white' : 'bg-gray-200 text-gray-600') }}">
                                    {{ $weightDiff > 0 ? '+' : '' }}{{ $weightDiff }} {{ __('messages.user_dashboard.kg') }}
                                </span>
                                @endif
                                <p class="text-[10px] font-bold text-gray-400 font-arabic mb-1">{{ __('messages.user_dashboard.weight_label') }}</p>
                                <p class="font-display text-xl font-black text-textColor leading-none">{{ $eval->weight }}</p>
                                <p class="text-[10px] text-gray-400 font-arabic">{{ __('messages.user_dashboard.kg') }}</p>
                            </div>

                            @if($eval->height)
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-center">
                                <p class="text-[10px] font-bold text-gray-400 font-arabic mb-1">{{ __('messages.user_dashboard.height_label') }}</p>
                                <p class="font-display text-xl font-black text-textColor leading-none">{{ $eval->height }}</p>
                                <p class="text-[10px] text-gray-400 font-arabic">{{ __('messages.user_dashboard.cm') }}</p>
                            </div>
                            @endif

                            @if($eval->body_fat_percentage)
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-center">
                                <p class="text-[10px] font-bold text-gray-400 font-arabic mb-1">{{ __('messages.user_dashboard.fat_label') }}</p>
                                <p class="font-display text-xl font-black text-textColor leading-none">{{ $eval->body_fat_percentage }}</p>
                                <p class="text-[10px] text-gray-400 font-arabic">%</p>
                            </div>
                            @endif

                            @if($eval->muscle_mass)
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-center">
                                <p class="text-[10px] font-bold text-gray-400 font-arabic mb-1">{{ __('messages.user_dashboard.muscle_label') }}</p>
                                <p class="font-display text-xl font-black text-textColor leading-none">{{ $eval->muscle_mass }}</p>
                                <p class="text-[10px] text-gray-400 font-arabic">{{ __('messages.user_dashboard.kg') }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- Coach --}}
                        @if($eval->coach)
                        <div class="flex-shrink-0 flex flex-row sm:flex-col items-center gap-2 font-arabic text-center">
                            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center font-black text-white text-sm flex-shrink-0">
                                {{ mb_substr($eval->coach->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400">{{ __('messages.user_dashboard.coach_label') }}</p>
                                <p class="text-xs font-black text-textColor leading-tight">{{ explode(' ', $eval->coach->name)[0] }}</p>
                            </div>
                        </div>
                        @endif

                    </div>

                    @if($eval->notes)
                    <div class="mt-3 pt-3 border-t border-gray-100 font-arabic">
                        <p class="text-[10px] font-bold text-gray-400 mb-1">{{ __('messages.user_dashboard.coach_notes') }}</p>
                        <p class="text-sm text-textColor font-bold leading-relaxed">{{ $eval->notes }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

        </div>
        @endif

        @endif
        @endif

    </main>

</div>

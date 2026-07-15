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
                        style="stroke-dashoffset: {{ 408 - (408 * $pct / 100) }}"
                        data-dashoffset="{{ 408 - (408 * $pct / 100) }}"/>
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
                    <span class="text-sm"><span class="material-symbols-rounded" style="font-size:inherit;font-variation-settings:'FILL' 1">local_fire_department</span></span>
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
                @foreach($weekDays as $d)
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
        @if($todayDayStatus === null)
        <div class="card anim anim-2 flex items-center gap-4" style="direction:{{ $dir }}; {{ $bsRef }}:4px solid #94a3b8">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-slate-400" style="font-size:24px">pending</span>
            </div>
            <div class="font-arabic">
                <p class="font-black text-textColor text-sm">{{ __('messages.user_dashboard.no_program_yet') }}</p>
                <p class="text-slate-400 text-xs font-bold mt-0.5">{{ __('messages.user_dashboard.no_program_soon') }}</p>
            </div>
        </div>
        @elseif($todayDayStatus === 'done')
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
            <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0 text-3xl">
                <span class="material-symbols-rounded" style="font-size:inherit;font-variation-settings:'FILL' 1">local_fire_department</span>
            </div>
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

        @if($daysLeft <= 0)
            <span class="self-start text-[10px] font-black font-arabic text-red-500 bg-red-50 px-3 py-1 rounded-full border border-red-100">{{ __('messages.user_dashboard.expired') }}</span>
        @elseif($daysLeft <= 5)
            <span class="self-start text-[10px] font-black font-arabic text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-100 animate-pulse">{{ __('messages.user_dashboard.expiring_soon') }}</span>
        @else
            <span class="self-start text-[10px] font-black font-arabic text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">{{ __('messages.user_dashboard.active') }}</span>
        @endif

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
            $prevEval     = $evaluations->get($idx + 1);
            $weightDiff   = $prevEval ? round($eval->weight - $prevEval->weight, 1) : null;
            $fitnessBadge = match($eval->fitness_level) {
                'beginner'     => ['label' => __('messages.user_dashboard.beginner'),     'class' => 'bg-blue-50 text-blue-600 border-blue-100'],
                'intermediate' => ['label' => __('messages.user_dashboard.intermediate'), 'class' => 'bg-amber-50 text-amber-600 border-amber-100'],
                'advanced'     => ['label' => __('messages.user_dashboard.advanced'),     'class' => 'bg-green-50 text-green-600 border-green-100'],
                default        => ['label' => $eval->fitness_level,                       'class' => 'bg-gray-50 text-gray-600 border-gray-100'],
            };
        @endphp
        <div class="card" style="direction:{{ $dir }}">
            <div class="flex flex-col sm:flex-row sm:items-start gap-4">

                <div class="flex-shrink-0 font-arabic">
                    <p class="text-gray-400 text-xs font-bold mb-1.5">
                        {{ $eval->evaluated_at->locale($locale)->isoFormat('D MMMM YYYY') }}
                    </p>
                    <span class="text-[10px] font-black px-2.5 py-1 rounded-full border {{ $fitnessBadge['class'] }}">
                        {{ $fitnessBadge['label'] }}
                    </span>
                </div>

                <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-2">
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

@if($canSendInvitations)
    @include('components.web.dashboard.partials.family-reward')
@endif

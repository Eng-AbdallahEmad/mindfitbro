@php
    // Steps indicator config — changes based on assessment state
    $steps = [
        ['n'=>'1', 'label'=> __('messages.user_dashboard.step_assessment'),    'active'=> !$assessmentSubmitted, 'done'=> $assessmentSubmitted],
        ['n'=>'2', 'label'=> __('messages.user_dashboard.step_book'),          'active'=> $assessmentSubmitted && !$hasBooking, 'done'=> $hasBooking],
        ['n'=>'3', 'label'=> __('messages.user_dashboard.step_2_waiting'),     'active'=> $hasBooking && !$meetingDone, 'done'=> $meetingDone],
        ['n'=>'4', 'label'=> __('messages.user_dashboard.step_activate'),      'active'=> false, 'done'=> false],
    ];
@endphp

{{-- ══════════════════════════════════════
     CARD A: Fill Assessment (step 1)
══════════════════════════════════════ --}}
@if(! $assessmentSubmitted)
<div class="anim anim-1">
    <div class="card waiting-card-pulse border-2 border-dashed border-primary/25 bg-primary/[0.02]" style="direction:{{ $dir }}">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-primary" style="font-size:32px;font-variation-settings:'FILL' 1">assignment_ind</span>
            </div>
            <div class="flex-1 font-arabic">
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <h3 class="font-black text-textColor text-lg">{{ __('messages.user_dashboard.fill_assessment') }}</h3>
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200 animate-pulse">
                        {{ __('messages.user_dashboard.required_activation') }}
                    </span>
                </div>
                <p class="text-gray-400 text-sm font-bold leading-relaxed mb-3">
                    {{ __('messages.user_dashboard.assessment_desc') }}
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    @foreach($steps as $step)
                        @if(!$loop->first)<span class="text-gray-300">{{ $isRtl ? '←' : '→' }}</span>@endif
                        <div class="flex items-center gap-1.5 text-[11px] font-bold
                            {{ $step['done'] ? 'text-green-600' : ($step['active'] ? 'text-primary' : 'text-gray-400') }}">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0
                                {{ $step['done'] ? 'bg-green-500 text-white' : ($step['active'] ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500') }}">
                                @if($step['done'])
                                    <span class="material-symbols-rounded" style="font-size:12px;font-variation-settings:'FILL' 1">check</span>
                                @else
                                    {{ $step['n'] }}
                                @endif
                            </span>
                            {{ $step['label'] }}
                        </div>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('assessment.show', $subscription->id) }}"
               class="flex-shrink-0 flex items-center gap-2 bg-primary text-white font-black font-arabic text-sm px-5 py-3 rounded-xl hover:bg-primary/90 transition whitespace-nowrap self-stretch sm:self-auto justify-center">
                <span class="material-symbols-rounded" style="font-size:18px;color:#D4ED57;font-variation-settings:'FILL' 1">assignment_ind</span>
                {{ __('messages.user_dashboard.start_assessment_btn') }}
            </a>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     CARD B: Assessment done, book now (step 2)
══════════════════════════════════════ --}}
@elseif(! $hasBooking)
<div class="anim anim-1">
    {{-- Assessment done confirmation --}}
    <div class="card mb-4 border border-green-200 bg-green-50/50" style="direction:{{ $dir }}">
        <div class="flex items-center gap-3 font-arabic">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-green-600" style="font-size:20px;font-variation-settings:'FILL' 1">task_alt</span>
            </div>
            <div>
                <p class="font-black text-green-700 text-sm">{{ __('messages.user_dashboard.assessment_done') }}</p>
                <p class="text-green-600/70 text-xs font-bold">{{ __('messages.user_dashboard.assessment_done_sub') }}</p>
            </div>
        </div>
    </div>

    <div class="card waiting-card-pulse border-2 border-dashed border-primary/25 bg-primary/[0.02]" style="direction:{{ $dir }}">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-primary" style="font-size:32px;font-variation-settings:'FILL' 1">calendar_month</span>
            </div>
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
                    @foreach($steps as $step)
                        @if(!$loop->first)<span class="text-gray-300">{{ $isRtl ? '←' : '→' }}</span>@endif
                        <div class="flex items-center gap-1.5 text-[11px] font-bold
                            {{ $step['done'] ? 'text-green-600' : ($step['active'] ? 'text-primary' : 'text-gray-400') }}">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0
                                {{ $step['done'] ? 'bg-green-500 text-white' : ($step['active'] ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500') }}">
                                @if($step['done'])
                                    <span class="material-symbols-rounded" style="font-size:12px;font-variation-settings:'FILL' 1">check</span>
                                @else
                                    {{ $step['n'] }}
                                @endif
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
{{-- ══════════════════════════════════════
     CARD C: Session booked — confirmation (step 3)
══════════════════════════════════════ --}}
<div class="anim anim-1">
    <div class="card" style="direction:{{ $dir }}; {{ $bsRef }}: 4px solid #22c55e;">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-green-600" style="font-size:28px;font-variation-settings:'FILL' 1">event_available</span>
            </div>
            <div class="flex-1 font-arabic">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="text-base font-black text-textColor">{{ __('messages.user_dashboard.session_scheduled') }}</span>
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full border
                        {{ $booking->status === 'confirmed' ? 'bg-green-50 text-green-600 border-green-200' : 'bg-amber-50 text-amber-600 border-amber-200' }}">
                        {{ $booking->status === 'confirmed' ? __('messages.user_dashboard.confirmed_badge') : __('messages.user_dashboard.pending_badge') }}
                    </span>
                    @if($meetingDone)
                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                        {{ __('messages.user_dashboard.step_2_done') }}
                    </span>
                    @endif
                </div>
                <p class="text-gray-500 text-sm font-bold">
                    <span class="text-textColor">
                        {{ \Carbon\Carbon::parse($booking->meeting_date)->locale($locale)->isoFormat($isRtl ? 'dddd، D MMMM Y' : 'dddd, MMMM D, Y') }}
                    </span>
                    {{ __('messages.user_dashboard.at_time') }}
                    <span class="text-textColor">{{ \Carbon\Carbon::parse($booking->meeting_time)->format('g:i A') }}</span>
                </p>
                @if($booking->meet_link && !str_contains($booking->meet_link, 'xxx'))
                <a href="{{ $booking->meet_link }}" target="_blank"
                   class="inline-flex items-center gap-1.5 mt-2 text-[12px] font-black text-primary hover:underline">
                    <span class="material-symbols-rounded" style="font-size:15px">videocam</span>
                    {{ __('messages.user_dashboard.open_meeting_link') }}
                </a>
                @elseif($booking->status === 'confirmed')
                <p class="inline-flex items-center gap-1.5 mt-2 text-[12px] font-bold text-amber-600">
                    <span class="material-symbols-rounded" style="font-size:15px">hourglass_empty</span>
                    {{ __('messages.user_dashboard.meeting_link_pending') }}
                </p>
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
                <span class="text-[10px] font-black px-2 py-0.5 rounded-full border
                    {{ $subscription->status === 'approved' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-600 border-amber-200' }}">
                    {{ $subscription->status === 'approved' ? __('messages.user_dashboard.approved_badge') : __('messages.programs.waiting_activation') }}
                </span>
            </div>
            <p class="text-gray-400 text-xs font-bold">
                {{ (__('messages.plans_data.'.$plan->key.'.desc', [], null) ?: $plan->desc) ?? __('messages.programs.activation_note') }}
            </p>
        </div>
    </div>
    <div class="card flex items-center gap-4" style="direction:{{ $dir }}">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-rounded text-blue-400" style="font-size:30px">hourglass_empty</span>
        </div>
        <div class="font-arabic">
            <p class="text-gray-400 text-xs font-bold mb-0.5">{{ __('messages.user_dashboard.sub_status_label') }}</p>
            <p class="font-black text-textColor text-base leading-none mb-1">{{ __('messages.user_dashboard.waiting_status') }}</p>
            <p class="text-amber-500 text-[11px] font-bold">{{ __('messages.user_dashboard.book_activate_sub') }}</p>
        </div>
    </div>
</div>

@if($canSendInvitations)
    @include('components.web.dashboard.partials.family-reward')
@endif

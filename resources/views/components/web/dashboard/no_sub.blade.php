@if($rejectedRecent)
<div x-data="{ show: true }" x-show="show"
     class="relative flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3 font-arabic anim anim-1">
    <span class="material-symbols-rounded text-amber-500 flex-shrink-0" style="font-size:18px;font-variation-settings:'FILL' 1">info</span>
    <p class="text-amber-700 text-sm font-bold flex-1">{{ __('messages.user_dashboard.rejected_note') }}</p>
    <button @click="show = false" class="text-amber-400 hover:text-amber-600 transition flex-shrink-0">
        <span class="material-symbols-rounded" style="font-size:18px">close</span>
    </button>
</div>
@endif

<div class="flex items-center justify-center flex-1 py-16 anim {{ $rejectedRecent ? 'anim-2' : 'anim-1' }}">
    <div class="bg-white rounded-3xl border border-gray-100 p-10 max-w-sm w-full text-center flex flex-col items-center gap-6">
        <div class="w-[72px] h-[72px] rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center">
            <span class="material-symbols-rounded text-gray-400" style="font-size:32px">lock</span>
        </div>
        <div class="font-arabic">
            <h2 class="font-black text-textColor text-xl mb-2">{{ __('messages.user_dashboard.no_sub_title') }}</h2>
            <p class="text-gray-400 text-sm leading-relaxed">{{ __('messages.user_dashboard.no_sub_desc') }}</p>
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

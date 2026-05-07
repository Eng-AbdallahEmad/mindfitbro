@extends('layouts.web.app')

@section('title', __('messages.calculator.title'))

@section('style')
<style>

    /* ─── Step Indicator ─── */
    .step-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: #d1d5db;
        transition: all .4s cubic-bezier(.4,0,.2,1);
    }
    .step-dot.active {
        width: 28px;
        border-radius: 999px;
        background: #174DAD;
    }
    .step-dot.done {
        background: #D4ED57;
    }

    /* ─── Option Cards ─── */
    .option-card {
        cursor: pointer;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 1rem 1.2rem;
        transition: all .25s ease;
        display: flex;
        align-items: center;
        gap: .75rem;
        background: #fff;
    }
    .option-card:hover   { border-color: #174DAD; background: #eff5ff; }
    .option-card.selected{ border-color: #174DAD; background: #eff5ff; }
    .option-card.selected .opt-icon { background: #174DAD; color: #D4ED57; }
    .opt-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: #f3f4f6;
        display: flex; align-items: center; justify-content: center;
        color: #174DAD;
        flex-shrink: 0;
        transition: all .25s;
    }

    /* ─── Range Slider ─── */
    .slider-track {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 6px;
        border-radius: 999px;
        background: linear-gradient(to left, #174DAD var(--pct, 50%), #e5e7eb var(--pct, 50%));
        outline: none;
        cursor: pointer;
    }
    .slider-track::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 22px; height: 22px;
        border-radius: 50%;
        background: #174DAD;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(23,77,173,.35);
        cursor: grab;
        transition: transform .15s;
    }
    .slider-track::-webkit-slider-thumb:active { transform: scale(1.2); cursor: grabbing; }
    .slider-track::-moz-range-thumb {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: #174DAD;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(23,77,173,.35);
        cursor: grab;
    }

    /* ─── Number Input ─── */
    .num-input { -moz-appearance: textfield; }
    .num-input::-webkit-inner-spin-button,
    .num-input::-webkit-outer-spin-button { -webkit-appearance: none; }

    /* ─── Result Bars ─── */
    .macro-bar {
        height: 8px;
        border-radius: 999px;
        transition: width 1.2s cubic-bezier(.4,0,.2,1);
    }

    /* ─── Fade / Slide Transitions ─── */
    .calc-step { display: none; }
    .calc-step.active {
        display: block;
        animation: stepIn .4s cubic-bezier(.4,0,.2,1) both;
    }
    @keyframes stepIn {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ─── Result Ring ─── */
    .ring-circle {
        stroke-dasharray: 502;
        stroke-dashoffset: 502;
        transition: stroke-dashoffset 1.4s cubic-bezier(.4,0,.2,1);
    }

    /* ─── Goal Toggle ─── */
    .goal-pill {
        cursor: pointer;
        padding: .55rem 1.1rem;
        border-radius: 999px;
        border: 2px solid #e5e7eb;
        font-size: .82rem;
        font-weight: 800;
        color: #6b7280;
        background: #fff;
        transition: all .22s;
        font-family: 'Cairo', sans-serif;
        white-space: nowrap;
    }
    .goal-pill:hover   { border-color: #174DAD; color: #174DAD; }
    .goal-pill.selected{ border-color: #174DAD; background: #174DAD; color: #D4ED57; }

    /* ─── Hero BG (same as home) ─── */
    .hero-bg {
        background: radial-gradient(ellipse 70% 60% at 55% 55%,
            #6a8fd8 0%, #3a68c8 35%, #1e4db7 65%, #1a3fa0 100%);
    }
    .orb { border-radius: 50%; filter: blur(80px); opacity: .22; animation: drift 9s ease-in-out infinite alternate; }
    .orb-1 { width:450px; height:450px; background:#90b8f8; top:-80px; right:-80px; animation-duration:11s; }
    .orb-2 { width:320px; height:320px; background:#3a68c8; bottom:-60px; left:-60px; animation-duration:14s; animation-delay:-5s; }
    @keyframes drift {
        from { transform: translate(0,0) scale(1); }
        to   { transform: translate(28px,18px) scale(1.07); }
    }

</style>
@endsection

@section('content')

{{-- Nav Bar --}}
<x-web.navbar :transparent="true" />

{{-- ═══ Hero ═══ --}}
<section class="hero relative w-full flex items-center overflow-hidden px-4 lg:px-28 pt-[7.8rem] lg:pt-[10rem] pb-10 lg:pb-16">
    <div class="hero-bg absolute inset-0 z-0"></div>
    <div class="orb orb-1 absolute z-0 hidden lg:block"></div>
    <div class="orb orb-2 absolute z-0 hidden lg:block"></div>

    <div class="relative z-10 text-center w-full">
        <span class="inline-block bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic mb-5">
            {{ __('messages.calculator.free_badge') }}
        </span>
        <h1 class="text-white font-black font-display text-5xl lg:text-[7rem] leading-tight mb-4">
            {{ __('messages.calculator.hero_title') }}
        </h1>
        <p class="text-white/80 font-arabic text-base lg:text-xl max-w-xl mx-auto leading-relaxed">
            {{ __('messages.calculator.hero_sub') }}
        </p>
    </div>
</section>

{{-- ═══ Calculator Card ═══ --}}
<section class="w-full bg-lightBg py-12 lg:py-20 px-4 flex justify-center" id="calculator">

    <div class="w-full max-w-2xl" x-data="calorieCalc()" x-cloak>

        {{-- ─ Progress Steps ─ --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            <template x-for="i in [1,2,3,4]" :key="i">
                <div class="step-dot"
                    :class="{
                        'active' : currentStep === i,
                        'done'   : currentStep > i
                    }">
                </div>
            </template>
        </div>

        {{-- ─ Step Labels ─ --}}
        <div class="text-center mb-6 font-arabic">
            <p class="text-xs font-bold text-gray-400 tracking-widest uppercase mb-1"
                x-text="{{ json_encode(__('messages.calculator.step_label')) }}.replace(':step', currentStep)">
            </p>
            <h2 class="text-xl lg:text-3xl font-black text-textColor font-display"
                x-text="steps[currentStep - 1].title">
            </h2>
            <p class="text-gray-400 text-sm mt-1 font-arabic"
                x-text="steps[currentStep - 1].sub">
            </p>
        </div>

        {{-- ══════════ CARD ══════════ --}}
        <div class="bg-white rounded-[28px] shadow-xl border border-gray-100 overflow-hidden">

            {{-- ─── Step 1 : Gender & Age ─── --}}
            <div class="calc-step p-7 lg:p-10" :class="{ active: currentStep === 1 }">

                {{-- Gender --}}
                <p class="text-sm font-black text-textColor mb-3 font-arabic">{{ __('messages.calculator.gender_label') }}</p>
                <div class="grid grid-cols-2 gap-4 mb-7">
                    <div class="option-card" :class="{ selected: form.gender === 'male' }"
                        @click="form.gender = 'male'">
                        <div class="opt-icon">
                            <span class="material-symbols-rounded" style="font-size:20px">male</span>
                        </div>
                        <span class="font-black text-sm text-textColor font-arabic">{{ __('messages.calculator.male') }}</span>
                    </div>
                    <div class="option-card" :class="{ selected: form.gender === 'female' }"
                        @click="form.gender = 'female'">
                        <div class="opt-icon">
                            <span class="material-symbols-rounded" style="font-size:20px">female</span>
                        </div>
                        <span class="font-black text-sm text-textColor font-arabic">{{ __('messages.calculator.female') }}</span>
                    </div>
                </div>

                {{-- Age --}}
                <p class="text-sm font-black text-textColor mb-3 font-arabic">{{ __('messages.calculator.age_label') }}</p>
                <div class="flex items-center gap-4 mb-2">
                    <input
                        type="range" min="10" max="90"
                        class="slider-track flex-1"
                        x-model="form.age"
                        @input="updateSlider($event, 10, 90)"
                        :style="`--pct:${((form.age - 10) / 80) * 100}%`"
                    >
                    <div class="flex items-center gap-1 bg-[#EFF5FF] rounded-xl px-3 py-2 min-w-[72px] justify-center">
                        <input type="number" min="10" max="90"
                            class="num-input w-10 text-center font-black text-textColor text-base bg-transparent outline-none"
                            x-model="form.age">
                        <span class="text-xs text-gray-400 font-arabic">{{ __('messages.calculator.age_unit') }}</span>
                    </div>
                </div>

            </div>

            {{-- ─── Step 2 : Height & Weight ─── --}}
            <div class="calc-step p-7 lg:p-10" :class="{ active: currentStep === 2 }">

                {{-- Height --}}
                <p class="text-sm font-black text-textColor mb-3 font-arabic">{{ __('messages.calculator.height_label') }}</p>
                <div class="flex items-center gap-4 mb-6">
                    <input
                        type="range" min="140" max="220"
                        class="slider-track flex-1"
                        x-model="form.height"
                        :style="`--pct:${((form.height - 140) / 80) * 100}%`"
                    >
                    <div class="flex items-center gap-1 bg-[#EFF5FF] rounded-xl px-3 py-2 min-w-[84px] justify-center">
                        <input type="number" min="140" max="220"
                            class="num-input w-12 text-center font-black text-textColor text-base bg-transparent outline-none"
                            x-model="form.height">
                        <span class="text-xs text-gray-400 font-arabic">{{ __('messages.calculator.height_unit') }}</span>
                    </div>
                </div>

                {{-- Weight --}}
                <p class="text-sm font-black text-textColor mb-3 font-arabic">{{ __('messages.calculator.weight_label') }}</p>
                <div class="flex items-center gap-4 mb-6">
                    <input
                        type="range" min="40" max="200"
                        class="slider-track flex-1"
                        x-model="form.weight"
                        :style="`--pct:${((form.weight - 40) / 160) * 100}%`"
                    >
                    <div class="flex items-center gap-1 bg-[#EFF5FF] rounded-xl px-3 py-2 min-w-[84px] justify-center">
                        <input type="number" min="40" max="200"
                            class="num-input w-12 text-center font-black text-textColor text-base bg-transparent outline-none"
                            x-model="form.weight">
                        <span class="text-xs text-gray-400 font-arabic">{{ __('messages.calculator.weight_unit') }}</span>
                    </div>
                </div>

                {{-- BMI Preview --}}
                <div class="rounded-2xl bg-[#EFF5FF] p-4 flex items-center justify-between font-arabic mt-2">
                    <div>
                        <p class="text-xs font-bold text-gray-400 mb-0.5">{{ __('messages.calculator.bmi_index') }}</p>
                        <p class="text-2xl font-black text-textColor font-display" x-text="bmi()"></p>
                    </div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-black"
                        :class="bmiClass()" x-text="bmiLabel()">
                    </span>
                </div>

            </div>

            {{-- ─── Step 3 : Activity Level ─── --}}
            <div class="calc-step p-7 lg:p-10" :class="{ active: currentStep === 3 }">

                <div class="flex flex-col gap-3">
                    <template x-for="level in activityLevels" :key="level.key">
                        <div class="option-card"
                            :class="{ selected: form.activity === level.key }"
                            @click="form.activity = level.key">
                            <div class="opt-icon">
                                <span class="material-symbols-rounded" style="font-size:20px"
                                    x-text="level.icon"></span>
                            </div>
                            <div class="flex flex-col text-right flex-1">
                                <span class="font-black text-sm text-textColor font-arabic"
                                    x-text="level.label"></span>
                                <span class="text-xs text-gray-400 font-arabic"
                                    x-text="level.desc"></span>
                            </div>
                        </div>
                    </template>
                </div>

            </div>

            {{-- ─── Step 4 : Goal ─── --}}
            <div class="calc-step p-7 lg:p-10" :class="{ active: currentStep === 4 }">

                <div class="flex flex-wrap gap-3 justify-center mb-8">
                    <template x-for="g in goals" :key="g.key">
                        <button class="goal-pill"
                            :class="{ selected: form.goal === g.key }"
                            @click="form.goal = g.key"
                            x-text="g.label">
                        </button>
                    </template>
                </div>

                {{-- Goal Description --}}
                <div class="rounded-2xl bg-primary p-5 font-arabic text-center transition-all duration-300"
                    x-show="form.goal">
                    <span class="material-symbols-rounded text-accent text-3xl mb-2 block"
                        x-text="currentGoal()?.icon"></span>
                    <p class="text-white font-bold text-sm leading-relaxed"
                        x-text="currentGoal()?.desc"></p>
                </div>

            </div>

            {{-- ─── RESULT ─── --}}
            <div class="calc-step p-7 lg:p-10" :class="{ active: currentStep === 5 }">

                {{-- Ring + Calories --}}
                <div class="flex flex-col items-center mb-8">
                    <div class="relative w-44 h-44">
                        <svg viewBox="0 0 180 180" class="w-full h-full -rotate-90">
                            <circle cx="90" cy="90" r="80" fill="none" stroke="#EFF5FF" stroke-width="14"/>
                            <circle cx="90" cy="90" r="80" fill="none" stroke="#174DAD" stroke-width="14"
                                stroke-linecap="round"
                                class="ring-circle"
                                :style="`stroke-dashoffset: ${502 - (502 * ringPct)}`"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center font-arabic">
                            <span class="text-3xl font-black text-textColor font-display"
                                x-text="animatedCalories"></span>
                            <span class="text-xs text-gray-400 font-bold">{{ __('messages.calculator.cal_per_day') }}</span>
                        </div>
                    </div>
                    <p class="text-textColor font-black text-lg mt-3 font-arabic"
                        x-text="(currentGoal()?.label ?? '') + ' — {{ __('messages.calculator.daily_need') }}'">
                    </p>
                </div>

                {{-- Macros --}}
                <div class="flex flex-col gap-4 mb-6">

                    {{-- Protein --}}
                    <div>
                        <div class="flex justify-between mb-1 font-arabic">
                            <span class="text-sm font-black text-textColor">{{ __('messages.calculator.protein') }}</span>
                            <span class="text-sm font-bold text-gray-400">
                                <span x-text="macros().protein"></span> {{ __('messages.calculator.gram') }} &nbsp;·&nbsp;
                                <span x-text="macros().proteinCal"></span> {{ __('messages.calculator.cal') }}
                            </span>
                        </div>
                        <div class="h-2 rounded-full bg-[#EFF5FF] overflow-hidden">
                            <div class="macro-bar bg-primary" :style="`width:${macros().proteinPct}%`"></div>
                        </div>
                    </div>

                    {{-- Carbs --}}
                    <div>
                        <div class="flex justify-between mb-1 font-arabic">
                            <span class="text-sm font-black text-textColor">{{ __('messages.calculator.carbs') }}</span>
                            <span class="text-sm font-bold text-gray-400">
                                <span x-text="macros().carbs"></span> {{ __('messages.calculator.gram') }} &nbsp;·&nbsp;
                                <span x-text="macros().carbsCal"></span> {{ __('messages.calculator.cal') }}
                            </span>
                        </div>
                        <div class="h-2 rounded-full bg-[#EFF5FF] overflow-hidden">
                            <div class="macro-bar bg-accent" :style="`width:${macros().carbsPct}%`"></div>
                        </div>
                    </div>

                    {{-- Fat --}}
                    <div>
                        <div class="flex justify-between mb-1 font-arabic">
                            <span class="text-sm font-black text-textColor">{{ __('messages.calculator.fat') }}</span>
                            <span class="text-sm font-bold text-gray-400">
                                <span x-text="macros().fat"></span> {{ __('messages.calculator.gram') }} &nbsp;·&nbsp;
                                <span x-text="macros().fatCal"></span> {{ __('messages.calculator.cal') }}
                            </span>
                        </div>
                        <div class="h-2 rounded-full bg-[#EFF5FF] overflow-hidden">
                            <div class="macro-bar bg-amber-400" :style="`width:${macros().fatPct}%`"></div>
                        </div>
                    </div>

                </div>

                {{-- Info Chips --}}
                <div class="grid grid-cols-3 gap-3 mb-6 font-arabic">
                    <div class="rounded-2xl bg-[#EFF5FF] p-4 text-center">
                        <p class="text-xs text-gray-400 font-bold mb-1">BMR</p>
                        <p class="font-black text-textColor font-display text-lg" x-text="bmr()"></p>
                        <p class="text-[10px] text-gray-400">{{ __('messages.calculator.bmr_label') }}</p>
                    </div>
                    <div class="rounded-2xl bg-[#EFF5FF] p-4 text-center">
                        <p class="text-xs text-gray-400 font-bold mb-1">TDEE</p>
                        <p class="font-black text-textColor font-display text-lg" x-text="tdee()"></p>
                        <p class="text-[10px] text-gray-400">{{ __('messages.calculator.tdee_label') }}</p>
                    </div>
                    <div class="rounded-2xl bg-[#EFF5FF] p-4 text-center">
                        <p class="text-xs text-gray-400 font-bold mb-1">BMI</p>
                        <p class="font-black text-textColor font-display text-lg" x-text="bmi()"></p>
                        <p class="text-[10px] text-gray-400">{{ __('messages.calculator.bmi_body') }}</p>
                    </div>
                </div>

                {{-- CTA --}}
                <a href="{{ route('home') }}#programs"
                    class="group font-arabic text-textColor bg-accent px-5 py-3 rounded-full text-base font-black flex justify-center items-center gap-2 transition hover:bg-yellow-300 w-full">
                    {{ __('messages.calculator.cta_subscribe') }}
                    <svg class="transition-transform duration-300 group-hover:-translate-x-2"
                        width="22" height="12" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                    </svg>
                </a>

                {{-- Reset --}}
                <button @click="reset()"
                    class="w-full mt-3 text-sm font-bold text-gray-400 font-arabic hover:text-primary transition">
                    {{ __('messages.calculator.reset_btn') }}
                </button>

            </div>

            {{-- ══ Navigation Buttons ══ --}}
            <div x-show="currentStep <= 4"
                class="flex items-center justify-between px-7 lg:px-10 py-5 border-t border-gray-100">

                <button @click="prev()"
                    x-show="currentStep > 1"
                    class="font-arabic text-sm font-bold text-gray-400 hover:text-textColor transition flex items-center gap-1">
                    <span class="material-symbols-rounded" style="font-size:18px">arrow_forward</span>
                    {{ __('messages.calculator.back_btn') }}
                </button>
                <div x-show="currentStep === 1"></div>

                <button @click="next()"
                    :disabled="!canProceed()"
                    class="group font-arabic text-textColor bg-accent px-6 py-2.5 rounded-full text-sm font-black flex items-center gap-2 transition hover:bg-yellow-300 disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-text="currentStep === 4 ? calcTrans.calculate_btn : calcTrans.next_btn"></span>
                    <svg class="transition-transform duration-300 group-hover:-translate-x-1.5"
                        width="20" height="10" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                    </svg>
                </button>

            </div>

        </div>
        {{-- /CARD --}}

        {{-- Trust Bar --}}
        <p class="flex items-center justify-center gap-2 text-gray-400 text-xs font-arabic font-semibold mt-6">
            <span class="material-symbols-rounded text-green-500" style="font-size:16px">verified_user</span>
            {{ __('messages.calculator.trust_bar') }}
        </p>

    </div>

</section>

{{-- Footer --}}
<x-web.footer :hidden="false" />

@endsection

@section('script')
<script>
const calcTrans = {
    calculate_btn: @json(__('messages.calculator.calculate_btn')),
    next_btn:      @json(__('messages.calculator.next_btn')),
    bmi_underweight: @json(__('messages.calculator.bmi_underweight')),
    bmi_normal:      @json(__('messages.calculator.bmi_normal')),
    bmi_overweight:  @json(__('messages.calculator.bmi_overweight')),
    bmi_obese:       @json(__('messages.calculator.bmi_obese')),
};

function calorieCalc() {
    return {

        currentStep: 1,
        animatedCalories: 0,
        ringPct: 0,
        calcTrans: calcTrans,

        form: {
            gender  : '',
            age     : 25,
            height  : 170,
            weight  : 75,
            activity: '',
            goal    : '',
        },

        steps: @json(__('messages.calculator.steps')),

        activityLevels: @json(__('messages.calculator.activity_levels')),

        goals: @json(__('messages.calculator.goals')),

        canProceed() {
            if (this.currentStep === 1) return this.form.gender !== '';
            if (this.currentStep === 2) return true;
            if (this.currentStep === 3) return this.form.activity !== '';
            if (this.currentStep === 4) return this.form.goal !== '';
            return true;
        },

        bmi() {
            const h = this.form.height / 100;
            return (this.form.weight / (h * h)).toFixed(1);
        },

        bmiLabel() {
            const b = parseFloat(this.bmi());
            if (b < 18.5) return calcTrans.bmi_underweight;
            if (b < 25)   return calcTrans.bmi_normal;
            if (b < 30)   return calcTrans.bmi_overweight;
            return calcTrans.bmi_obese;
        },

        bmiClass() {
            const b = parseFloat(this.bmi());
            if (b < 18.5) return 'bg-blue-100 text-blue-600';
            if (b < 25)   return 'bg-green-100 text-green-600';
            if (b < 30)   return 'bg-yellow-100 text-yellow-600';
            return 'bg-red-100 text-red-600';
        },

        bmr() {
            const { weight, height, age, gender } = this.form;
            if (gender === 'male')
                return Math.round(10 * weight + 6.25 * height - 5 * age + 5);
            return Math.round(10 * weight + 6.25 * height - 5 * age - 161);
        },

        tdee() {
            const level = this.activityLevels.find(l => l.key === this.form.activity);
            return Math.round(this.bmr() * (level ? level.factor : 1.55));
        },

        targetCalories() {
            const goal = this.goals.find(g => g.key === this.form.goal);
            return Math.max(1200, this.tdee() + (goal ? goal.adj : 0));
        },

        currentGoal() {
            return this.goals.find(g => g.key === this.form.goal);
        },

        macros() {
            const cal  = this.targetCalories();
            const w    = this.form.weight;

            const proteinG   = Math.round(w * ((this.form.goal && this.form.goal.includes('gain')) ? 2.2 : 1.8));
            const proteinCal = proteinG * 4;

            const fatCal     = Math.round(cal * 0.25);
            const fatG       = Math.round(fatCal / 9);

            const carbsCal   = cal - proteinCal - fatCal;
            const carbsG     = Math.round(carbsCal / 4);

            return {
                protein    : proteinG,
                proteinCal : proteinCal,
                proteinPct : Math.round((proteinCal / cal) * 100),
                fat        : fatG,
                fatCal     : fatCal,
                fatPct     : 25,
                carbs      : Math.max(0, carbsG),
                carbsCal   : Math.max(0, carbsCal),
                carbsPct   : Math.max(0, Math.round((carbsCal / cal) * 100)),
            };
        },

        next() {
            if (!this.canProceed()) return;
            if (this.currentStep === 4) {
                this.currentStep = 5;
                this.$nextTick(() => this.animateResults());
            } else {
                this.currentStep++;
            }
        },

        prev() {
            if (this.currentStep > 1) {
                this.currentStep = this.currentStep === 5 ? 4 : this.currentStep - 1;
            }
        },

        reset() {
            this.currentStep    = 1;
            this.animatedCalories = 0;
            this.ringPct        = 0;
            this.form = { gender:'', age:25, height:170, weight:75, activity:'', goal:'' };
        },

        animateResults() {
            const target = this.targetCalories();
            const maxCal = 4000;
            let start    = null;
            const dur    = 1400;

            const step = (ts) => {
                if (!start) start = ts;
                const prog = Math.min((ts - start) / dur, 1);
                const ease = 1 - Math.pow(1 - prog, 3);
                this.animatedCalories = Math.round(ease * target);
                this.ringPct = ease * Math.min(target / maxCal, 1);
                if (prog < 1) requestAnimationFrame(step);
                else {
                    this.animatedCalories = target;
                    this.ringPct = Math.min(target / maxCal, 1);
                }
            };

            requestAnimationFrame(step);
        },

        updateSlider(e, min, max) {
            const pct = ((e.target.value - min) / (max - min)) * 100;
            e.target.style.setProperty('--pct', pct + '%');
        },
    }
}
</script>
@endsection
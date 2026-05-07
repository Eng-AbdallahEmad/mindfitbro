@extends('layouts.web.app')

@section('title', __('messages.reset.title'))

@section('style')
<style>
    .auth-left {
        background: radial-gradient(ellipse 80% 70% at 60% 50%,
            #6a8fd8 0%, #3a68c8 35%, #1e4db7 65%, #1a3fa0 100%);
        position: relative;
        overflow: hidden;
    }
    .orb {
        border-radius: 50%;
        filter: blur(70px);
        opacity: 0.22;
        animation: drift 9s ease-in-out infinite alternate;
        position: absolute;
        pointer-events: none;
    }
    .orb-1 { width:380px; height:380px; background:#90b8f8; top:-80px; right:-80px; animation-duration:11s; }
    .orb-2 { width:280px; height:280px; background:#D4ED57; bottom:-60px; left:-60px; animation-duration:14s; animation-delay:-5s; }
    @keyframes drift {
        from { transform: translate(0,0) scale(1); }
        to   { transform: translate(24px,16px) scale(1.07); }
    }
    .stat-float {
        background: rgba(255,255,255,0.12);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 16px;
        padding: .75rem 1.1rem;
        animation: floatCard 5s ease-in-out infinite alternate;
    }
    .stat-float:nth-child(2) { animation-delay: -2.5s; }
    @keyframes floatCard {
        from { transform: translateY(0px); }
        to   { transform: translateY(-10px); }
    }
    .auth-input {
        width: 100%;
        background: #F4F7FF;
        border: 2px solid #e0e8ff;
        border-radius: 14px;
        padding: .85rem 1.1rem;
        font-size: .9rem;
        color: #1c1c1c;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        font-family: 'Cairo', sans-serif;
        text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
    }
    .auth-input:focus {
        border-color: #174DAD;
        box-shadow: 0 0 0 4px rgba(23,77,173,.1);
    }
    .auth-input::placeholder { color: #b0bec5; }
    .auth-card {
        animation: cardIn .55s cubic-bezier(.4,0,.2,1) both;
    }
    @keyframes cardIn {
        from { opacity:0; transform:translateY(28px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .eye-btn {
        position: absolute;
        {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #b0bec5;
        display: flex;
        align-items: center;
        padding: 0;
        transition: color .2s;
    }
    .eye-btn:hover { color: #174DAD; }
    .input-wrap { position: relative; }
    .input-wrap .auth-input { {{ app()->getLocale() === 'ar' ? 'padding-left' : 'padding-right' }}: 2.8rem; }
</style>
@endsection

@section('content')

@php $isRtl = app()->getLocale() === 'ar'; @endphp

<div class="min-h-screen flex flex-col lg:flex-row">

    {{-- ══════════════ LEFT PANEL ══════════════ --}}
    <div class="auth-left hidden lg:flex flex-col justify-between items-center w-4/12 flex-shrink-0 p-12 xl:p-16">

        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="relative z-10 mb-5">
            <img src="{{ asset('assets/logo/mindfitbro.png') }}" alt="MindFitBro" class="w-[250px] object-contain">
        </a>

        {{-- Center Content --}}
        <div class="relative z-10 flex flex-col items-center gap-8">
            <div>
                <h2 class="font-display text-center text-3xl xl:text-4xl text-white font-black leading-tight mb-4">
                    {{ __('messages.reset.panel_heading') }}
                    <span class="text-accent">{{ __('messages.reset.panel_heading_highlight') }}</span>
                </h2>
                <p class="font-arabic text-center text-white/70 text-base leading-relaxed max-w-sm">
                    {{ __('messages.reset.panel_subtitle') }}
                </p>
            </div>

            {{-- Floating Stats --}}
            <div class="flex flex-col gap-4 w-full">
                <div class="stat-float flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-accent" style="font-size:20px;font-variation-settings:'FILL' 1">lock_reset</span>
                    </div>
                    <div class="font-arabic">
                        <p class="text-white font-black text-sm">{{ __('messages.reset.stat1_title') }}</p>
                        <p class="text-white/50 text-xs">{{ __('messages.reset.stat1_sub') }}</p>
                    </div>
                </div>
                <div class="stat-float flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-accent" style="font-size:20px;font-variation-settings:'FILL' 1">verified_user</span>
                    </div>
                    <div class="font-arabic">
                        <p class="text-white font-black text-sm">{{ __('messages.reset.stat2_title') }}</p>
                        <p class="text-white/50 text-xs">{{ __('messages.reset.stat2_sub') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom quote --}}
        <div class="relative z-10">
            <p class="font-arabic text-white/40 text-xs leading-relaxed">
                {{ __('messages.reset.quote') }}
            </p>
        </div>

    </div>

    {{-- ══════════════ RIGHT PANEL ══════════════ --}}
    <div class="flex-1 bg-lightBg flex items-center justify-center p-6 lg:p-12 min-h-screen lg:min-h-0">

        <div class="auth-card w-full max-w-md">

            {{-- Mobile Logo --}}
            <div class="flex justify-center mb-8 lg:hidden">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('assets/logo/mindfitbro-b.png') }}" alt="MindFitBro" class="w-[160px] object-contain">
                </a>
            </div>

            {{-- Back Button --}}
            <a href="{{ route('password.request') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 font-arabic font-semibold hover:text-primary transition mb-6">
                <span class="material-symbols-rounded" style="font-size:18px">{{ $isRtl ? 'arrow_forward' : 'arrow_back' }}</span>
                {{ __('messages.reset.back') }}
            </a>

            {{-- Heading --}}
            <div class="mb-8 {{ $isRtl ? 'text-right' : 'text-left' }}">
                <span class="inline-block bg-accent text-darkBg text-[11px] font-black tracking-widest px-4 py-1.5 rounded-full font-arabic mb-3">
                    {{ __('messages.reset.badge') }}
                </span>
                <h1 class="font-display text-3xl lg:text-4xl font-black text-textColor">
                    {{ __('messages.reset.heading') }}
                </h1>
                <p class="font-arabic text-gray-400 text-sm mt-1">
                    {{ __('messages.reset.subheading') }}
                </p>
            </div>

            {{-- Error Alert --}}
            @if ($errors->any())
            <div class="mb-5 rounded-2xl bg-red-50 border border-red-200 p-4 flex items-start gap-3 font-arabic">
                <span class="material-symbols-rounded text-red-500 flex-shrink-0" style="font-size:20px">error</span>
                <div class="text-sm text-red-600 font-semibold">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5" x-data="{ showPass: false, showConfirm: false }">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email --}}
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-black text-textColor font-arabic {{ $isRtl ? 'text-right' : 'text-left' }}">{{ __('messages.reset.email_label') }}</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $email) }}"
                        readonly
                        class="auth-input opacity-60 cursor-not-allowed"
                    >
                </div>

                {{-- New Password --}}
                <div class="flex flex-col gap-2">
                    <label for="password" class="text-sm font-black text-textColor font-arabic {{ $isRtl ? 'text-right' : 'text-left' }}">{{ __('messages.reset.new_password_label') }}</label>
                    <div class="input-wrap">
                        <input
                            :type="showPass ? 'text' : 'password'"
                            name="password"
                            id="password"
                            placeholder="{{ __('messages.reset.new_password_placeholder') }}"
                            autofocus
                            class="auth-input @error('password') border-red-400 @enderror"
                        >
                        <button type="button" class="eye-btn" @click="showPass = !showPass" tabindex="-1">
                            <span class="material-symbols-rounded" style="font-size:20px" x-text="showPass ? 'visibility_off' : 'visibility'">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-500 font-arabic font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="flex flex-col gap-2">
                    <label for="password_confirmation" class="text-sm font-black text-textColor font-arabic {{ $isRtl ? 'text-right' : 'text-left' }}">{{ __('messages.reset.confirm_password_label') }}</label>
                    <div class="input-wrap">
                        <input
                            :type="showConfirm ? 'text' : 'password'"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="{{ __('messages.reset.confirm_password_placeholder') }}"
                            class="auth-input"
                        >
                        <button type="button" class="eye-btn" @click="showConfirm = !showConfirm" tabindex="-1">
                            <span class="material-symbols-rounded" style="font-size:20px" x-text="showConfirm ? 'visibility_off' : 'visibility'">visibility</span>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="group font-arabic text-textColor bg-accent px-5 py-3.5 rounded-full text-base font-black flex justify-center items-center gap-2 transition hover:bg-yellow-300 w-full mt-1">
                    {{ __('messages.reset.submit') }}
                    <svg class="transition-transform duration-300 {{ $isRtl ? 'group-hover:-translate-x-2' : 'group-hover:translate-x-2 rotate-180' }}"
                        width="22" height="12" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                    </svg>
                </button>

            </form>

            {{-- Bottom Trust --}}
            <p class="flex items-center justify-center gap-2 text-gray-400 text-xs font-arabic font-semibold mt-8">
                <span class="material-symbols-rounded text-green-500" style="font-size:16px">lock</span>
                {{ __('messages.reset.ssl_note') }}
            </p>

        </div>
    </div>

</div>

@endsection

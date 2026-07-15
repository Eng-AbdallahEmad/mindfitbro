@extends('layouts.web.app')

@section('title', __('messages.login.title'))

@section('style')
<style>

    /* ─── Split Layout ─── */
    .auth-left {
        background: radial-gradient(ellipse 80% 70% at 60% 50%,
            #6a8fd8 0%, #3a68c8 35%, #1e4db7 65%, #1a3fa0 100%);
        position: relative;
        overflow: hidden;
    }

    /* ─── Orbs ─── */
    .orb {
        border-radius: 50%;
        filter: blur(70px);
        opacity: 0.22;
        animation: drift 9s ease-in-out infinite alternate;
        position: absolute;
        pointer-events: none;
    }
    .orb-1 { width:380px; height:380px; background:#90b8f8; top:-80px; right:-80px; animation-duration:11s; }
    .orb-2 { width:280px; height:280px; background:#D4ED57;  bottom:-60px; left:-60px; animation-duration:14s; animation-delay:-5s; }
    @keyframes drift {
        from { transform: translate(0,0) scale(1); }
        to   { transform: translate(24px,16px) scale(1.07); }
    }

    /* ─── Floating stat cards ─── */
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

    /* ─── Form Input ─── */
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

    /* ─── Password Toggle ─── */
    .pass-wrap { position: relative; }
    .pass-toggle {
        position: absolute;
        {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        display: flex;
        align-items: center;
        transition: color .2s;
    }
    .pass-toggle:hover { color: #174DAD; }

    /* ─── Page Enter Animation ─── */
    .auth-card {
        animation: cardIn .55s cubic-bezier(.4,0,.2,1) both;
    }
    @keyframes cardIn {
        from { opacity:0; transform:translateY(28px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* ─── Divider ─── */
    .or-line {
        display: flex;
        align-items: center;
        gap: .75rem;
        color: #d1d5db;
        font-size: .78rem;
        font-weight: 700;
    }
    .or-line::before,
    .or-line::after {
        content:'';
        flex:1;
        height:1px;
        background: #e5e7eb;
    }

    /* ─── Social Button ─── */
    .social-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        padding: .75rem;
        border-radius: 14px;
        border: 2px solid #e5e7eb;
        background: #fff;
        font-size: .85rem;
        font-weight: 800;
        color: #374151;
        cursor: pointer;
        transition: all .22s;
        font-family: 'Cairo', sans-serif;
    }
    .social-btn:hover { border-color: #174DAD; background: #eff5ff; color: #174DAD; }

    /* ─── Remember me checkbox ─── */
    .custom-check {
        width: 18px; height: 18px;
        border-radius: 6px;
        border: 2px solid #e0e8ff;
        background: #F4F7FF;
        cursor: pointer;
        accent-color: #174DAD;
    }

</style>
@endsection

@section('content')

@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

<div class="min-h-screen flex flex-col lg:flex-row">

    {{-- ══════════════ LEFT PANEL (decorative) ══════════════ --}}
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
                    {{ __('messages.login.journey_heading') }}
                    <span class="text-accent">{{ __('messages.login.strongest_version') }}</span>
                    {{ __('messages.login.of_you') }}
                </h2>
                <p class="font-arabic text-center text-white/70 text-base leading-relaxed max-w-sm">
                    {{ __('messages.login.panel_subtitle') }}
                </p>
            </div>

            {{-- Floating Stats --}}
            <div class="flex flex-col gap-4 w-full">

                <div class="stat-float flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-accent" style="font-size:20px;font-variation-settings:'FILL' 1">groups</span>
                    </div>
                    <div class="font-arabic">
                        <p class="text-white font-black text-sm">{{ __('messages.login.active_members') }}</p>
                        <p class="text-white/50 text-xs">{{ __('messages.login.join_daily') }}</p>
                    </div>
                </div>

                <div class="stat-float flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-accent" style="font-size:20px;font-variation-settings:'FILL' 1">star</span>
                    </div>
                    <div class="font-arabic">
                        <p class="text-white font-black text-sm">{{ __('messages.login.rating') }}</p>
                        <p class="text-white/50 text-xs">{{ __('messages.login.rating_count') }}</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Bottom quote --}}
        <div class="relative z-10">
            <p class="font-arabic text-white/40 text-xs leading-relaxed">
                {{ __('messages.login.quote') }}
            </p>
        </div>

    </div>

    {{-- ══════════════ RIGHT PANEL (form) ══════════════ --}}
    <div class="flex-1 bg-lightBg flex items-center justify-center p-6 lg:p-12 min-h-screen lg:min-h-0">

        <div class="auth-card w-full max-w-md">

            {{-- Mobile Logo --}}
            <div class="flex justify-center mb-8 lg:hidden">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('assets/logo/mindfitbro-b.png') }}" alt="MindFitBro" class="w-[160px] object-contain">
                </a>
            </div>

            {{-- Heading --}}
            <div class="mb-8 {{ $isRtl ? 'text-right' : 'text-left' }}">
                <span class="inline-block bg-accent text-darkBg text-[11px] font-black tracking-widest px-4 py-1.5 rounded-full font-arabic mb-3">
                    {{ __('messages.login.welcome_back') }}
                </span>
                <h1 class="font-display text-3xl lg:text-4xl font-black text-textColor">
                    {{ __('messages.login.title') }}
                </h1>
                <p class="font-arabic text-gray-400 text-sm mt-1">
                    {{ __('messages.login.no_account') }}
                    <a href="{{ route('register') }}" class="text-primary font-bold hover:underline">{{ __('messages.login.register_now') }}</a>
                </p>
            </div>

            {{-- Banned Alert --}}
            @if (session('banned') !== null)
            <div class="mb-5 rounded-2xl overflow-hidden border border-red-200 font-arabic">
                <div class="bg-red-500 px-4 py-3 flex items-center gap-2.5">
                    <span class="material-symbols-rounded text-white flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 1">block</span>
                    <p class="text-white font-black text-sm">تم تعليق هذا الحساب</p>
                </div>
                <div class="bg-red-50 px-4 py-3.5">
                    <p class="text-red-700 text-sm font-semibold leading-relaxed mb-3">
                        حسابك محظور حالياً ولا يمكنك الوصول إلى المنصة. إذا كنت تعتقد أن هذا خطأ أو تريد الاعتراض، يُرجى التواصل مع فريق الدعم.
                    </p>
                    @if(session('banned'))
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', session('banned')) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-black px-4 py-2 rounded-xl transition">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        تواصل عبر واتساب: {{ session('banned') }}
                    </a>
                    @else
                    <p class="text-red-500 text-sm font-bold">يُرجى التواصل مع الإدارة مباشرةً.</p>
                    @endif
                </div>
            </div>
            @endif

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

            {{-- Session Status --}}
            @if (session('status'))
            <div class="mb-5 rounded-2xl bg-green-50 border border-green-200 p-4 flex items-center gap-3 font-arabic">
                <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:20px">check_circle</span>
                <p class="text-sm text-green-600 font-semibold">{{ session('status') }}</p>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login.post') }}" class="flex flex-col gap-5">
                @csrf

                {{-- Username --}}
                <div class="flex flex-col gap-2">
                    <label for="username" class="text-sm font-black text-textColor font-arabic {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('messages.login.username_label') }}</label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        value="{{ old('username') }}"
                        placeholder="{{ __('messages.login.username_placeholder') }}"
                        autofocus
                        class="auth-input @error('username') border-red-400 @enderror"
                    >
                    @error('username')
                        <p class="text-xs text-red-500 font-arabic font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="flex flex-col gap-2">
                    <div class="flex flex-row-reverse items-center justify-between">
                        <a href="{{ route('password.request') }}"
                            class="text-xs text-primary font-bold font-arabic hover:underline">
                            {{ __('messages.login.forgot_password') }}
                        </a>
                        <label for="password" class="text-sm font-black text-textColor font-arabic">{{ __('messages.login.password_label') }}</label>
                    </div>
                    <div class="pass-wrap">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            class="auth-input @error('password') border-red-400 @enderror"
                        >
                        <button type="button" class="pass-toggle" onclick="togglePass('password', this)">
                            <span class="material-symbols-rounded" style="font-size:20px">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-500 font-arabic font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-start gap-2 font-arabic">
                    <input type="checkbox" name="remember" id="remember" class="custom-check">
                    <label class="text-sm font-semibold text-gray-500 cursor-pointer select-none" for="remember">
                        {{ __('messages.login.remember_me') }}
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="group font-arabic text-textColor bg-accent px-5 py-3.5 rounded-full text-base font-black flex justify-center items-center gap-2 transition hover:bg-yellow-300 w-full mt-1">
                    {{ __('messages.login.submit') }}
                    <svg class="transition-transform duration-300 {{ $isRtl ? 'group-hover:-translate-x-2' : 'group-hover:translate-x-2 rotate-180' }}"
                        width="22" height="12" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                    </svg>
                </button>

            </form>

            {{-- Bottom Trust --}}
            <p class="flex items-center justify-center gap-2 text-gray-400 text-xs font-arabic font-semibold mt-8">
                <span class="material-symbols-rounded text-green-500" style="font-size:16px">lock</span>
                {{ __('messages.login.ssl_note') }}
            </p>

        </div>
    </div>

</div>

@endsection

@section('script')
<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('.material-symbols-rounded');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endsection

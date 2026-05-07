@extends('layouts.web.app')

@section('title', __('messages.privacy.title'))

@section('content')

@php $isRtl = app()->getLocale() === 'ar'; @endphp

    {{-- Nav Bar --}}
    <x-web.navbar :transparent="true" />

    {{-- Hero Header --}}
    <section class="w-full bg-primary pt-36 pb-16 px-6 text-center relative overflow-hidden">

        {{-- Orbs --}}
        <div class="absolute top-[-60px] right-[-60px] w-72 h-72 rounded-full bg-blue-400/20 blur-[80px] pointer-events-none"></div>
        <div class="absolute bottom-[-40px] left-[-40px] w-56 h-56 rounded-full bg-blue-300/10 blur-[60px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col items-center gap-4">
            <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                {{ __('messages.privacy.badge') }}
            </span>
            <h1 class="font-display text-6xl md:text-7xl font-black text-white">
                {{ __('messages.privacy.title') }}
            </h1>
            <p class="font-arabic text-white/50 text-sm font-semibold">
                {{ __('messages.privacy.last_updated') }}
            </p>
        </div>

    </section>

    {{-- Main Content --}}
    <section class="w-full bg-lightBg py-20 px-6" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        <div class="max-w-[860px] mx-auto flex flex-col gap-5">

            {{-- Intro Card --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic">
                <p class="text-gray-600 text-base leading-[2] font-medium">
                    {{ __('messages.privacy.intro') }}
                </p>
            </div>

            {{-- 1. Information We Collect --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">database</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{{ __('messages.privacy.collect_title') }}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{{ __('messages.privacy.collect_intro') }}</p>
                <ul class="flex flex-col gap-3">
                    @foreach(__('messages.privacy.collect_items') as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        <span><span class="font-black text-textColor">{{ $item['bold'] }}</span> {{ $item['text'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 2. How We Use --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">settings</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{{ __('messages.privacy.how_use_title') }}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{{ __('messages.privacy.how_use_intro') }}</p>
                <ul class="flex flex-col gap-3">
                    @foreach(__('messages.privacy.how_use_items') as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-accent flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 3. Sharing --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">share</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{{ __('messages.privacy.sharing_title') }}</h2>
                </div>
                <div class="flex items-start gap-3 bg-green-50 border border-green-100 rounded-[12px] p-4">
                    <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 1">verified_user</span>
                    <p class="text-sm text-green-700 font-semibold leading-relaxed">
                        {{ __('messages.privacy.no_sell') }}
                    </p>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{{ __('messages.privacy.sharing_intro') }}</p>
                <ul class="flex flex-col gap-3">
                    @foreach(__('messages.privacy.sharing_items') as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 4. Data Security --}}
            <div class="rounded-[20px] bg-primary p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-accent" style="font-size:20px">lock</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-white">{{ __('messages.privacy.security_title') }}</h2>
                </div>
                <p class="text-white/70 text-sm leading-[2]">
                    {{ __('messages.privacy.security_body') }}
                </p>
            </div>

            {{-- 5 & 6: Cookies + Your Rights — Side by Side --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Cookies --}}
                <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-primary" style="font-size:20px">cookie</span>
                        </div>
                        <h2 class="font-display text-xl font-semibold text-textColor">{{ __('messages.privacy.cookies_title') }}</h2>
                    </div>
                    <p class="text-gray-500 text-sm leading-[2]">
                        {{ __('messages.privacy.cookies_body') }}
                    </p>
                </div>

                {{-- Your Rights --}}
                <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-primary" style="font-size:20px">gavel</span>
                        </div>
                        <h2 class="font-display text-xl font-semibold text-textColor">{{ __('messages.privacy.rights_title') }}</h2>
                    </div>
                    <ul class="flex flex-col gap-2.5">
                        @foreach(__('messages.privacy.rights_items') as $right)
                        <li class="flex items-start gap-2.5 text-sm text-gray-600 leading-relaxed">
                            <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                            {{ $right }}
                        </li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-gray-400 leading-relaxed mt-1">
                        {{ __('messages.privacy.rights_contact') }}
                        <a href="mailto:info@mindfitbro.com" class="text-primary font-bold hover:underline">
                            info@mindfitbro.com
                        </a>
                    </p>
                </div>

            </div>

            {{-- 7. Third-Party Links --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">link</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{{ __('messages.privacy.third_party_title') }}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {{ __('messages.privacy.third_party_body') }}
                </p>
            </div>

            {{-- 8. Children's Privacy --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">child_care</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{{ __('messages.privacy.children_title') }}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {{ __('messages.privacy.children_body') }}
                </p>
            </div>

            {{-- 9. Changes --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">update</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{{ __('messages.privacy.changes_title') }}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {{ __('messages.privacy.changes_body') }}
                </p>
            </div>

            {{-- 10. Contact Us --}}
            <div class="rounded-[20px] border-2 border-accent bg-gradient-to-l from-[#fffde8] to-[#f0f5ff] p-8 font-arabic flex flex-col gap-5">

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-accent/30 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-textColor" style="font-size:20px">contact_support</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{{ __('messages.privacy.contact_title') }}</h2>
                </div>

                <p class="text-gray-500 text-sm leading-relaxed">
                    {{ __('messages.privacy.contact_intro') }}
                </p>

                <div class="flex flex-col gap-3">
                    <a href="mailto:info@mindfitbro.com"
                        class="group flex items-center gap-3 text-sm text-gray-600 hover:text-primary transition-colors duration-300 w-fit">
                        <div class="w-8 h-8 rounded-[8px] bg-white border border-gray-100 flex items-center justify-center flex-shrink-0 group-hover:border-primary/30 transition-colors">
                            <span class="material-symbols-rounded text-primary" style="font-size:16px">mail</span>
                        </div>
                        <span class="font-semibold">info@mindfitbro.com</span>
                    </a>
                    <a href="tel:+966593035979"
                        class="group flex items-center gap-3 text-sm text-gray-600 hover:text-primary transition-colors duration-300 w-fit">
                        <div class="w-8 h-8 rounded-[8px] bg-white border border-gray-100 flex items-center justify-center flex-shrink-0 group-hover:border-primary/30 transition-colors">
                            <span class="material-symbols-rounded text-primary" style="font-size:16px">call</span>
                        </div>
                        <span class="font-semibold" dir="ltr">+966593035979</span>
                    </a>
                </div>

            </div>

        </div>
    </section>

    {{-- Footer --}}
    <x-web.footer :hidden="true" />

@endsection

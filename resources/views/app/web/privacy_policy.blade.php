@extends('layouts.web.app')

@section('title', \App\Models\PageContent::get('privacy_policy', 'title', app()->getLocale(), __('messages.privacy.title')))

@section('content')

@php
    $isRtl  = app()->getLocale() === 'ar';
    $locale = app()->getLocale();
    $pc     = fn ($key, $default) => nl2br(e(\App\Models\PageContent::get('privacy_policy', $key, $locale, $default)));
    $pcItems = fn ($key, $default) => \App\Models\PageContent::items('privacy_policy', $key, $locale, $default);

    $contact = \App\Services\Web\ContactInfo::current();
@endphp

    {{-- Nav Bar --}}
    <x-web.navbar :transparent="true" />

    {{-- Hero Header --}}
    <section class="w-full bg-primary pt-36 pb-16 px-6 text-center relative overflow-hidden">

        {{-- Orbs --}}
        <div class="absolute top-[-60px] right-[-60px] w-72 h-72 rounded-full bg-blue-400/20 blur-[80px] pointer-events-none"></div>
        <div class="absolute bottom-[-40px] left-[-40px] w-56 h-56 rounded-full bg-blue-300/10 blur-[60px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col items-center gap-4">
            <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                {!! $pc('badge', __('messages.privacy.badge')) !!}
            </span>
            <h1 class="font-display text-6xl md:text-7xl font-black text-white">
                {!! $pc('title', __('messages.privacy.title')) !!}
            </h1>
            <p class="font-arabic text-white/50 text-sm font-semibold">
                {!! $pc('last_updated', __('messages.privacy.last_updated')) !!}
            </p>
        </div>

    </section>

    {{-- Main Content --}}
    <section class="w-full bg-lightBg py-20 px-6" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        <div class="max-w-[860px] mx-auto flex flex-col gap-5">

            {{-- Intro Card --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic">
                <p class="text-gray-600 text-base leading-[2] font-medium">
                    {!! $pc('intro', __('messages.privacy.intro')) !!}
                </p>
            </div>

            {{-- 3.1 Information We Collect --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">database</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s31_title', __('messages.privacy.s31_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{!! $pc('s31_intro', __('messages.privacy.s31_intro')) !!}</p>
                <ul class="flex flex-col gap-3">
                    @php
                        $s31Bold = $pcItems('s31_items_bold', __('messages.privacy.s31_items_bold'));
                        $s31Text = $pcItems('s31_items_text', __('messages.privacy.s31_items_text'));
                    @endphp
                    @foreach($s31Bold as $i => $bold)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        <span><span class="font-black text-textColor">{{ $bold }}</span> {{ $s31Text[$i] ?? '' }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 3.2 How We Use Your Information --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">settings</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s32_title', __('messages.privacy.s32_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{!! $pc('s32_intro', __('messages.privacy.s32_intro')) !!}</p>
                <ul class="flex flex-col gap-3">
                    @foreach($pcItems('s32_items', __('messages.privacy.s32_items')) as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-accent flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 3.3 Legal Basis & Sensitive Data --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">health_and_safety</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s33_title', __('messages.privacy.s33_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {!! $pc('s33_body', __('messages.privacy.s33_body')) !!}
                </p>
            </div>

            {{-- 3.4 Sharing Your Data --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">share</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s34_title', __('messages.privacy.s34_title')) !!}</h2>
                </div>
                <div class="flex items-start gap-3 bg-green-50 border border-green-100 rounded-[12px] p-4">
                    <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 1">verified_user</span>
                    <p class="text-sm text-green-700 font-semibold leading-relaxed">
                        {!! $pc('s34_no_sell', __('messages.privacy.s34_no_sell')) !!}
                    </p>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{!! $pc('s34_intro', __('messages.privacy.s34_intro')) !!}</p>
                <ul class="flex flex-col gap-3">
                    @foreach($pcItems('s34_items', __('messages.privacy.s34_items')) as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 3.5 & 3.6: International Data Transfer + Data Retention — Side by Side --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- 3.5 International Data Transfer --}}
                <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-primary" style="font-size:20px">public</span>
                        </div>
                        <h2 class="font-display text-xl font-semibold text-textColor">{!! $pc('s35_title', __('messages.privacy.s35_title')) !!}</h2>
                    </div>
                    <p class="text-gray-500 text-sm leading-[2]">
                        {!! $pc('s35_body', __('messages.privacy.s35_body')) !!}
                    </p>
                </div>

                {{-- 3.6 Data Retention --}}
                <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-primary" style="font-size:20px">inventory_2</span>
                        </div>
                        <h2 class="font-display text-xl font-semibold text-textColor">{!! $pc('s36_title', __('messages.privacy.s36_title')) !!}</h2>
                    </div>
                    <p class="text-gray-500 text-sm leading-[2]">
                        {!! $pc('s36_body', __('messages.privacy.s36_body')) !!}
                    </p>
                </div>

            </div>

            {{-- 3.7 Your Rights --}}
            <div class="rounded-[20px] border-2 border-accent bg-gradient-to-l from-[#fffde8] to-[#f0f5ff] p-8 font-arabic flex flex-col gap-5">

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-accent/30 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-textColor" style="font-size:20px">gavel</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s37_title', __('messages.privacy.s37_title')) !!}</h2>
                </div>

                <p class="text-gray-500 text-sm leading-relaxed">{!! $pc('s37_intro', __('messages.privacy.s37_intro')) !!}</p>
                <ul class="flex flex-col gap-2.5">
                    @foreach($pcItems('s37_items', __('messages.privacy.s37_items')) as $item)
                    <li class="flex items-start gap-2.5 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>

                <p class="text-gray-500 text-sm leading-relaxed">
                    {!! $pc('s37_gdpr', __('messages.privacy.s37_gdpr')) !!}
                </p>

                <p class="text-sm text-gray-600 leading-relaxed">
                    {!! $pc('s37_contact_note', __('messages.privacy.s37_contact_note')) !!}
                    <a href="mailto:{{ $contact['email'] }}" class="text-primary font-bold hover:underline" dir="ltr">
                        {{ $contact['email'] }}
                    </a>
                </p>

            </div>

            {{-- 3.8 Data Security --}}
            <div class="rounded-[20px] bg-primary p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-accent" style="font-size:20px">lock</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-white">{!! $pc('s38_title', __('messages.privacy.s38_title')) !!}</h2>
                </div>
                <p class="text-white/70 text-sm leading-[2]">
                    {!! $pc('s38_body', __('messages.privacy.s38_body')) !!}
                </p>
            </div>

            {{-- 3.9 Cookies --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">cookie</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s39_title', __('messages.privacy.s39_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{!! $pc('s39_intro', __('messages.privacy.s39_intro')) !!}</p>
                <ul class="flex flex-col gap-3">
                    @foreach($pcItems('s39_items', __('messages.privacy.s39_items')) as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <p class="text-gray-500 text-sm leading-[2]">
                    {!! $pc('s39_footer', __('messages.privacy.s39_footer')) !!}
                </p>
            </div>

            {{-- 3.10 Changes to This Privacy Policy --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">update</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s310_title', __('messages.privacy.s310_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {!! $pc('s310_body', __('messages.privacy.s310_body')) !!}
                </p>
            </div>

        </div>
    </section>

    {{-- Footer --}}
    <x-web.footer :hidden="true" />

@endsection

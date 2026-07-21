@extends('layouts.web.app')

@section('title', \App\Models\PageContent::get('about_us', 'title', app()->getLocale(), __('messages.about_us.title')))

@section('meta_description', \App\Models\PageContent::get('about_us', 'meta_description', app()->getLocale(), __('messages.about_us.meta_description')))

@section('content')

@php
    $isRtl  = app()->getLocale() === 'ar';
    $locale = app()->getLocale();
    $pc     = fn ($key, $default) => nl2br(e(\App\Models\PageContent::get('about_us', $key, $locale, $default)));

    $contact      = \App\Services\Web\ContactInfo::current();
    $addressValue = nl2br(e($contact["address_{$locale}"]));
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
                {!! $pc('badge', __('messages.about_us.badge')) !!}
            </span>
            <h1 class="font-display text-6xl md:text-7xl font-black text-white">
                {!! $pc('title', __('messages.about_us.title')) !!}
            </h1>
        </div>

    </section>

    {{-- Main Content --}}
    <section class="w-full bg-lightBg py-20 px-6" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        <div class="max-w-[860px] mx-auto flex flex-col gap-5">

            {{-- Intro Card --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic">
                <p class="text-gray-600 text-base leading-[2] font-medium">
                    {!! $pc('intro', __('messages.about_us.intro')) !!}
                </p>
            </div>

            {{-- Our Approach --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">fitness_center</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('approach_title', __('messages.about_us.approach_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {!! $pc('approach_body', __('messages.about_us.approach_body')) !!}
                </p>
            </div>

            {{-- Our Founder --}}
            <div class="rounded-[20px] bg-primary p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-accent" style="font-size:20px">military_tech</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-white">{!! $pc('founder_title', __('messages.about_us.founder_title')) !!}</h2>
                </div>
                <p class="text-white/70 text-sm leading-[2]">
                    {!! $pc('founder_body', __('messages.about_us.founder_body')) !!}
                </p>
            </div>

            {{-- Legal Information --}}
            <div class="rounded-[20px] border-2 border-accent bg-gradient-to-l from-[#fffde8] to-[#f0f5ff] p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-accent/30 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-textColor" style="font-size:20px">gavel</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('legal_title', __('messages.about_us.legal_title')) !!}</h2>
                </div>

                <p class="text-gray-600 text-sm leading-relaxed font-semibold">
                    {!! $pc('legal_law', __('messages.about_us.legal_law')) !!}
                </p>

                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <div class="w-8 h-8 rounded-[8px] bg-white border border-gray-100 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:16px">location_on</span>
                    </div>
                    <span>
                        <span class="font-black text-textColor">{!! $pc('legal_address_label', __('messages.about_us.legal_address_label')) !!}</span>
                        {!! $addressValue !!}
                    </span>
                </div>
            </div>

        </div>
    </section>

    {{-- Footer --}}
    <x-web.footer :hidden="true" />

@endsection

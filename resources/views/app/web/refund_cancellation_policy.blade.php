@extends('layouts.web.app')

@section('title', \App\Models\PageContent::get('refund_policy', 'title', app()->getLocale(), __('messages.refund_policy.title')))

@section('meta_description', \App\Models\PageContent::get('refund_policy', 'meta_description', app()->getLocale(), __('messages.refund_policy.meta_description')))

@section('content')

@php
    $isRtl  = app()->getLocale() === 'ar';
    $locale = app()->getLocale();
    $pc     = fn ($key, $default) => nl2br(e(\App\Models\PageContent::get('refund_policy', $key, $locale, $default)));
    $pcItems = fn ($key, $default) => \App\Models\PageContent::items('refund_policy', $key, $locale, $default);

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
                {!! $pc('badge', __('messages.refund_policy.badge')) !!}
            </span>
            <h1 class="font-display text-6xl md:text-7xl font-black text-white">
                {!! $pc('title', __('messages.refund_policy.title')) !!}
            </h1>
            <p class="font-arabic text-white/50 text-sm font-semibold">
                {!! $pc('last_updated', __('messages.refund_policy.last_updated')) !!}
            </p>
        </div>

    </section>

    {{-- Main Content --}}
    <section class="w-full bg-lightBg py-20 px-6" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        <div class="max-w-[860px] mx-auto flex flex-col gap-5">

            {{-- Intro --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic">
                <p class="text-gray-600 text-base leading-[2] font-medium">
                    {!! $pc('intro', __('messages.refund_policy.intro')) !!}
                </p>
            </div>

            {{-- 5.1 Full Refund Window --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">verified</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s51_title', __('messages.refund_policy.s51_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{!! $pc('s51_intro', __('messages.refund_policy.s51_intro')) !!}</p>
                <ul class="flex flex-col gap-3">
                    @foreach($pcItems('s51_items', __('messages.refund_policy.s51_items')) as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 5.2 After Your Plan Has Been Delivered --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">task_alt</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s52_title', __('messages.refund_policy.s52_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{!! $pc('s52_intro', __('messages.refund_policy.s52_intro')) !!}</p>
                <ul class="flex flex-col gap-3">
                    @foreach($pcItems('s52_items', __('messages.refund_policy.s52_items')) as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 5.3 Service-Quality Issues --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">rate_review</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s53_title', __('messages.refund_policy.s53_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {!! $pc('s53_before', __('messages.refund_policy.s53_before')) !!}
                    <a href="mailto:{{ $contact['email'] }}" class="text-primary font-bold hover:underline" dir="ltr">{{ $contact['email'] }}</a>
                    {!! $pc('s53_after', __('messages.refund_policy.s53_after')) !!}
                </p>
            </div>

            {{-- 5.4 Freezing/Pausing a Subscription --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">pause_circle</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s54_title', __('messages.refund_policy.s54_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {!! $pc('s54_body', __('messages.refund_policy.s54_body')) !!}
                </p>
            </div>

            {{-- 5.5 Promotional Offers --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">sell</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s55_title', __('messages.refund_policy.s55_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {!! $pc('s55_body', __('messages.refund_policy.s55_body')) !!}
                </p>
            </div>

            {{-- 5.6 Refund Processing --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">payments</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s56_title', __('messages.refund_policy.s56_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {!! $pc('s56_body', __('messages.refund_policy.s56_body')) !!}
                </p>
            </div>

            {{-- 5.7 Governing Law & Disputes --}}
            <div class="rounded-[20px] bg-primary p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-accent" style="font-size:20px">gavel</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-white">{!! $pc('s57_title', __('messages.refund_policy.s57_title')) !!}</h2>
                </div>
                <p class="text-white/70 text-sm leading-[2]">
                    {!! $pc('s57_body', __('messages.refund_policy.s57_body')) !!}
                </p>
            </div>

            {{-- 6. Complaints & Suggestions --}}
            <div class="rounded-[20px] border-2 border-accent bg-gradient-to-l from-[#fffde8] to-[#f0f5ff] p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-accent/30 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-textColor" style="font-size:20px">contact_support</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s6_title', __('messages.refund_policy.s6_title')) !!}</h2>
                </div>
                <p class="text-gray-600 text-sm leading-[2]">
                    {!! $pc('s6_before', __('messages.refund_policy.s6_before')) !!}
                    <a href="mailto:{{ $contact['email'] }}" class="text-primary font-bold hover:underline" dir="ltr">{{ $contact['email'] }}</a>
                    {!! $pc('s6_or', __('messages.refund_policy.s6_or')) !!}
                    <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener" class="text-primary font-bold hover:underline">{{ __('messages.contact_us.whatsapp_btn') }}</a>
                    {!! $pc('s6_after', __('messages.refund_policy.s6_after')) !!}
                </p>
            </div>

        </div>
    </section>

    {{-- Footer --}}
    <x-web.footer :hidden="true" />

@endsection

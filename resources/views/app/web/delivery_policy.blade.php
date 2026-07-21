@extends('layouts.web.app')

@section('title', \App\Models\PageContent::get('delivery_policy', 'title', app()->getLocale(), __('messages.delivery_policy.title')))

@section('meta_description', \App\Models\PageContent::get('delivery_policy', 'meta_description', app()->getLocale(), __('messages.delivery_policy.meta_description')))

@section('content')

@php
    $isRtl  = app()->getLocale() === 'ar';
    $locale = app()->getLocale();
    $pc     = fn ($key, $default) => nl2br(e(\App\Models\PageContent::get('delivery_policy', $key, $locale, $default)));
    $pcItems = fn ($key, $default) => \App\Models\PageContent::items('delivery_policy', $key, $locale, $default);
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
                {!! $pc('badge', __('messages.delivery_policy.badge')) !!}
            </span>
            <h1 class="font-display text-6xl md:text-7xl font-black text-white">
                {!! $pc('title', __('messages.delivery_policy.title')) !!}
            </h1>
            <p class="font-arabic text-white/50 text-sm font-semibold">
                {!! $pc('last_updated', __('messages.delivery_policy.last_updated')) !!}
            </p>
        </div>

    </section>

    {{-- Main Content --}}
    <section class="w-full bg-lightBg py-20 px-6" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        <div class="max-w-[860px] mx-auto flex flex-col gap-5">

            {{-- 4.1 Digital Services --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">cloud_done</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s41_title', __('messages.delivery_policy.s41_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {!! $pc('s41_body', __('messages.delivery_policy.s41_body')) !!}
                </p>
            </div>

            {{-- 4.2 Service Delivery Timeline --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">schedule_send</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s42_title', __('messages.delivery_policy.s42_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">{!! $pc('s42_intro', __('messages.delivery_policy.s42_intro')) !!}</p>
                <ul class="flex flex-col gap-3">
                    @foreach($pcItems('s42_items', __('messages.delivery_policy.s42_items')) as $item)
                    <li class="flex items-start gap-3 text-sm text-gray-600 leading-relaxed">
                        <span class="material-symbols-rounded text-primary flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 4.3 What Happens If We're Late --}}
            <div class="rounded-[20px] bg-primary p-8 font-arabic flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-accent" style="font-size:20px">update</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-white">{!! $pc('s43_title', __('messages.delivery_policy.s43_title')) !!}</h2>
                </div>
                <p class="text-white/70 text-sm leading-[2]">
                    {!! $pc('s43_body', __('messages.delivery_policy.s43_body')) !!}
                </p>
            </div>

            {{-- 4.4 No Physical Shipping --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-primary" style="font-size:20px">local_shipping</span>
                    </div>
                    <h2 class="font-display text-2xl font-semibold text-textColor">{!! $pc('s44_title', __('messages.delivery_policy.s44_title')) !!}</h2>
                </div>
                <p class="text-gray-500 text-sm leading-[2]">
                    {!! $pc('s44_body', __('messages.delivery_policy.s44_body')) !!}
                </p>
            </div>

        </div>
    </section>

    {{-- Footer --}}
    <x-web.footer :hidden="true" />

@endsection

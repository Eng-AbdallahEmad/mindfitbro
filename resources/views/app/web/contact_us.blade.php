@extends('layouts.web.app')

@section('title', \App\Models\PageContent::get('contact_us', 'title', app()->getLocale(), __('messages.contact_us.title')))

@section('meta_description', \App\Models\PageContent::get('contact_us', 'meta_description', app()->getLocale(), __('messages.contact_us.meta_description')))

@section('content')

@php
    $isRtl  = app()->getLocale() === 'ar';
    $locale = app()->getLocale();
    $pc     = fn ($key, $default) => nl2br(e(\App\Models\PageContent::get('contact_us', $key, $locale, $default)));

    $contact         = \App\Services\Web\ContactInfo::current();
    $contactEmail    = $contact['email'];
    $contactPhone    = $contact['phone'];
    $contactWhatsapp = $contact['whatsapp'];
    $hoursValue      = nl2br(e($contact["hours_{$locale}"]));
    $addressValue    = nl2br(e($contact["address_{$locale}"]));
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
                {!! $pc('badge', __('messages.contact_us.badge')) !!}
            </span>
            <h1 class="font-display text-6xl md:text-7xl font-black text-white">
                {!! $pc('title', __('messages.contact_us.title')) !!}
            </h1>
        </div>

    </section>

    {{-- Main Content --}}
    <section class="w-full bg-lightBg py-20 px-6" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        <div class="max-w-[860px] mx-auto flex flex-col gap-5">

            {{-- Intro Card --}}
            <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic">
                <p class="text-gray-600 text-base leading-[2] font-medium">
                    {!! $pc('lead', __('messages.contact_us.lead')) !!}
                </p>
            </div>

            {{-- Contact Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Email --}}
                <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-primary" style="font-size:20px">mail</span>
                        </div>
                        <h2 class="font-display text-xl font-semibold text-textColor">{{ __('messages.contact_us.email_label') }}</h2>
                    </div>
                    <a href="mailto:{{ $contactEmail }}"
                        class="group flex items-center gap-3 text-sm text-gray-600 hover:text-primary transition-colors duration-300 w-fit">
                        <span class="font-bold" dir="ltr">{{ $contactEmail }}</span>
                    </a>
                </div>

                {{-- Phone / WhatsApp --}}
                <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-primary" style="font-size:20px">call</span>
                        </div>
                        <h2 class="font-display text-xl font-semibold text-textColor">{{ __('messages.contact_us.phone_label') }}</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="tel:{{ $contactPhone }}"
                            class="group flex items-center gap-2 text-sm text-gray-600 hover:text-primary transition-colors duration-300 w-fit">
                            <span class="material-symbols-rounded text-primary" style="font-size:16px">call</span>
                            <span class="font-bold">{{ __('messages.contact_us.call_btn') }}</span>
                        </a>
                        <a href="https://wa.me/{{ $contactWhatsapp }}" target="_blank" rel="noopener"
                            class="group flex items-center gap-2 text-sm text-gray-600 hover:text-primary transition-colors duration-300 w-fit">
                            <span class="material-symbols-rounded text-primary" style="font-size:16px">chat</span>
                            <span class="font-bold">{{ __('messages.contact_us.whatsapp_btn') }}</span>
                        </a>
                    </div>
                    <span class="text-xs text-gray-400" dir="ltr">{{ $contactPhone }}</span>
                </div>

                {{-- Business Hours --}}
                <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-primary" style="font-size:20px">schedule</span>
                        </div>
                        <h2 class="font-display text-xl font-semibold text-textColor">{{ __('messages.contact_us.hours_label') }}</h2>
                    </div>
                    <p class="text-sm text-gray-600 font-semibold">
                        {!! $hoursValue !!}
                    </p>
                </div>

                {{-- Address --}}
                <div class="rounded-[20px] bg-white border border-gray-100 p-8 font-arabic flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-[#EFF5FF] flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-primary" style="font-size:20px">location_on</span>
                        </div>
                        <h2 class="font-display text-xl font-semibold text-textColor">{{ __('messages.contact_us.address_label') }}</h2>
                    </div>
                    <p class="text-sm text-gray-600 font-semibold">
                        {!! $addressValue !!}
                    </p>
                </div>

            </div>

            {{-- Response Time Note --}}
            <div class="rounded-[20px] border-2 border-accent bg-gradient-to-l from-[#fffde8] to-[#f0f5ff] p-8 font-arabic flex items-center gap-4">
                <div class="w-10 h-10 rounded-[10px] bg-accent/30 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-rounded text-textColor" style="font-size:20px">bolt</span>
                </div>
                <p class="text-sm text-textColor font-bold leading-relaxed">
                    {!! $pc('response_note', __('messages.contact_us.response_note')) !!}
                </p>
            </div>

        </div>
    </section>

    {{-- Footer --}}
    <x-web.footer :hidden="true" />

@endsection

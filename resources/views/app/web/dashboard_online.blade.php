@extends('layouts.web.app')

@section('title', __('messages.online.page_title'))

@php
    $isRtl          = app()->getLocale() === 'ar';
    $dir            = $isRtl ? 'rtl' : 'ltr';
    $user           = auth()->user();
    $planName       = $plan?->name ?? __('messages.online.page_title');
    $hasCredentials = $crmEmail || $crmPassword;
    $googlePlay     = \App\Models\Setting::get('google_play_url', '');
    $appStore       = \App\Models\Setting::get('app_store_url', '');
    $hasAnyStore    = $googlePlay || $appStore;
@endphp

@section('style')
<style>
.online-hero {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 60%, #1e40af 100%);
    border-radius: 0 0 2.5rem 2.5rem;
    padding-top: 4rem;
    padding-bottom: 5.5rem;
}
.online-card-overlap {
    margin-top: -3.5rem;
}
.store-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #111827;
    border-radius: 14px;
    padding: 0.75rem 1.25rem;
    transition: background 0.2s, transform 0.15s;
    flex: 1;
    min-width: 0;
    text-decoration: none;
}
.store-badge:hover {
    background: #1f2937;
    transform: translateY(-1px);
}
.step-icon-wrap {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin: 0 auto 0.625rem;
}
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#F0F4FB]" dir="{{ $dir }}">

    {{-- ══════════════════════════════════════
         HERO
    ══════════════════════════════════════ --}}
    <div class="online-hero text-center px-5">
        <div class="max-w-sm mx-auto">

            {{-- Icon circle --}}
            <div class="w-16 h-16 rounded-2xl mx-auto mb-5 flex items-center justify-center text-3xl"
                 style="background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.2);">
                @if($hasCredentials) 🚀 @else ⏳ @endif
            </div>

            {{-- Greeting --}}
            <h1 class="text-2xl font-black text-white font-arabic leading-snug mb-2">
                {{ __('messages.online.greeting', ['name' => $user->name]) }}
            </h1>

            {{-- Sub --}}
            <p class="text-white/75 text-sm font-semibold font-arabic leading-relaxed">
                @if($hasCredentials)
                    {{ __('messages.online.hero_sub', ['plan' => $planName]) }}
                @else
                    {{ __('messages.online.pending_body') }}
                @endif
            </p>

        </div>
    </div>

    {{-- ══════════════════════════════════════
         BODY (overlaps hero)
    ══════════════════════════════════════ --}}
    <div class="max-w-md mx-auto px-4 pb-20 online-card-overlap space-y-4">

        {{-- ════ CREDENTIALS or PENDING card ════ --}}
        @if($hasCredentials)

        <div class="bg-white rounded-3xl shadow-xl shadow-blue-900/10 overflow-hidden border border-gray-100"
             x-data="{
                 showPass: false,
                 copiedEmail: false,
                 copiedPass: false,
                 email: {{ Js::from($crmEmail) }},
                 pass:  {{ Js::from($crmPassword) }},
                 copyEmail() {
                     navigator.clipboard.writeText(this.email).then(() => {
                         this.copiedEmail = true;
                         setTimeout(() => this.copiedEmail = false, 2000);
                     });
                 },
                 copyPass() {
                     navigator.clipboard.writeText(this.pass).then(() => {
                         this.copiedPass = true;
                         setTimeout(() => this.copiedPass = false, 2000);
                     });
                 },
             }">

            {{-- Card header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:#eff6ff">
                    <span class="material-symbols-rounded text-blue-600" style="font-size:18px;font-variation-settings:'FILL' 1">key</span>
                </div>
                <div>
                    <p class="text-sm font-black text-textColor font-arabic">{{ __('messages.online.login_data') }}</p>
                    <p class="text-[11px] text-gray-400 font-semibold font-arabic">{{ __('messages.online.login_data_hint') }}</p>
                </div>
            </div>

            {{-- Fields --}}
            <div class="p-6 space-y-4">

                {{-- Email --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-black text-gray-400 font-arabic tracking-widest uppercase">
                        {{ __('messages.online.email_label') }}
                    </label>
                    <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                        <span class="text-sm font-semibold text-textColor flex-1 break-all font-mono" dir="ltr" x-text="email">{{ $crmEmail }}</span>
                        <button type="button" @click="copyEmail()" title="{{ $isRtl ? 'نسخ' : 'Copy' }}"
                            class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-150"
                            :class="copiedEmail ? 'bg-green-100 text-green-600' : 'bg-gray-200/80 text-gray-500 hover:bg-blue-100 hover:text-blue-600'">
                            <span class="material-symbols-rounded" style="font-size:15px;font-variation-settings:'FILL' 1"
                                  x-text="copiedEmail ? 'check' : 'content_copy'">content_copy</span>
                        </button>
                    </div>
                </div>

                {{-- Password --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-black text-gray-400 font-arabic tracking-widest uppercase">
                        {{ __('messages.online.password_label') }}
                    </label>
                    <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                        <input :type="showPass ? 'text' : 'password'" :value="pass" readonly
                               class="text-sm font-semibold text-textColor flex-1 bg-transparent outline-none font-mono min-w-0" dir="ltr">
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <button type="button" @click="showPass = !showPass" :title="showPass ? '{{ $isRtl ? 'إخفاء' : 'Hide' }}' : '{{ $isRtl ? 'إظهار' : 'Show' }}'"
                                class="w-8 h-8 rounded-lg bg-gray-200/80 hover:bg-blue-100 hover:text-blue-600 text-gray-500 flex items-center justify-center transition-all duration-150">
                                <span class="material-symbols-rounded" style="font-size:15px"
                                      x-text="showPass ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                            <button type="button" @click="copyPass()" title="{{ $isRtl ? 'نسخ' : 'Copy' }}"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-150"
                                :class="copiedPass ? 'bg-green-100 text-green-600' : 'bg-gray-200/80 text-gray-500 hover:bg-blue-100 hover:text-blue-600'">
                                <span class="material-symbols-rounded" style="font-size:15px;font-variation-settings:'FILL' 1"
                                      x-text="copiedPass ? 'check' : 'content_copy'">content_copy</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Security micro-note --}}
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-rounded text-amber-400 flex-shrink-0" style="font-size:13px;font-variation-settings:'FILL' 1">lock</span>
                    <p class="text-[10px] font-semibold text-gray-400 font-arabic">{{ __('messages.online.security_note') }}</p>
                </div>

            </div>
        </div>

        @else

        {{-- ════ PENDING card ════ --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-blue-900/10 p-8 text-center border border-gray-100">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl"
                 style="background:#eff6ff;">
                ⏳
            </div>
            <h2 class="text-base font-black text-textColor font-arabic mb-2">{{ __('messages.online.pending_title') }}</h2>
            <p class="text-sm text-gray-400 font-semibold font-arabic leading-relaxed">{{ __('messages.online.pending_body') }}</p>
        </div>

        @endif

        {{-- ════ DOWNLOAD SECTION ════ --}}
        <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/40 p-6 border border-gray-100">

            <p class="text-xs font-black text-gray-500 font-arabic mb-4 flex items-center gap-2">
                <span class="material-symbols-rounded text-primary" style="font-size:16px;font-variation-settings:'FILL' 1">download</span>
                {{ __('messages.online.download_title') }}
            </p>

            @if($hasAnyStore)
            <div class="flex gap-3 flex-wrap">

                @if($googlePlay)
                <a href="{{ $googlePlay }}" target="_blank" rel="noopener" class="store-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="26" height="26" class="flex-shrink-0" aria-hidden="true">
                        <path fill="#4CAF50" d="M6.9,2.2C6.3,2.6,6,3.3,6,4.2v39.6c0,.9.3,1.6.9,2l.1.1L28,25v-.4L6.9,2.2z"/>
                        <path fill="#FFC107" d="M35,31.8l-7-7V25l7-6.8.2.1 8.3,4.7c2.4,1.3,2.4,3.5,0,4.9L35.2,31.7 35,31.8z"/>
                        <path fill="#F44336" d="M35.2,31.7 28,24.5 6.9,45.8c.8.8,2,.9,3.5.1L35.2,31.7"/>
                        <path fill="#2196F3" d="M35.2,17.3 10.4,3.1C8.9,2.3,7.7,2.4,6.9,3.2L28,24.5 35.2,17.3z"/>
                    </svg>
                    <div class="leading-none min-w-0">
                        <p class="text-white/50 text-[9px] font-semibold mb-0.5">{{ __('messages.footer.get_it_on') }}</p>
                        <p class="text-white text-[13px] font-bold">Google Play</p>
                    </div>
                </a>
                @endif

                @if($appStore)
                <a href="{{ $appStore }}" target="_blank" rel="noopener" class="store-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="26" height="26" fill="white" class="flex-shrink-0 opacity-90" aria-hidden="true">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                    <div class="leading-none min-w-0">
                        <p class="text-white/50 text-[9px] font-semibold mb-0.5">{{ __('messages.footer.download_on') }}</p>
                        <p class="text-white text-[13px] font-bold">App Store</p>
                    </div>
                </a>
                @endif

            </div>

            @else
            {{-- Fallback when both URLs are empty in settings --}}
            <div class="flex items-center gap-3 bg-amber-50 border border-amber-100 rounded-2xl px-4 py-3.5">
                <span class="material-symbols-rounded text-amber-400 flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 1">pending</span>
                <p class="text-xs font-bold text-amber-700 font-arabic">{{ __('messages.online.no_stores') }}</p>
            </div>
            @endif

        </div>

        {{-- ════ 3-STEP GUIDE ════ --}}
        <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/40 p-6 border border-gray-100">

            <div class="grid grid-cols-3 gap-2 text-center">

                {{-- Step 1 --}}
                <div class="flex flex-col items-center">
                    <div class="step-icon-wrap" style="background:#eff6ff;">
                        <span class="material-symbols-rounded text-blue-500" style="font-size:20px;font-variation-settings:'FILL' 1">download</span>
                    </div>
                    <div class="w-6 h-6 rounded-full bg-primary text-white text-[10px] font-black flex items-center justify-center mx-auto mb-1.5">1</div>
                    <p class="text-[11px] font-black text-textColor font-arabic leading-tight mb-0.5">{{ __('messages.online.step1_title') }}</p>
                    <p class="text-[9px] font-semibold text-gray-400 font-arabic leading-tight">{{ __('messages.online.step1_body') }}</p>
                </div>

                {{-- Arrow between 1 and 2 --}}
                {{-- Step connector lines --}}
                {{-- Step 2 --}}
                <div class="flex flex-col items-center relative">
                    {{-- Left connector --}}
                    <div class="absolute top-[1.375rem] {{ $isRtl ? 'right-0' : 'left-0' }} w-1/2 h-px bg-gray-200 -translate-y-1/2" style="top:1.375rem;"></div>
                    {{-- Right connector --}}
                    <div class="absolute top-[1.375rem] {{ $isRtl ? 'left-0' : 'right-0' }} w-1/2 h-px bg-gray-200" style="top:1.375rem;"></div>
                    <div class="step-icon-wrap" style="background:#f0fdf4; position:relative; z-index:1;">
                        <span class="material-symbols-rounded text-green-500" style="font-size:20px;font-variation-settings:'FILL' 1">login</span>
                    </div>
                    <div class="w-6 h-6 rounded-full bg-green-500 text-white text-[10px] font-black flex items-center justify-center mx-auto mb-1.5">2</div>
                    <p class="text-[11px] font-black text-textColor font-arabic leading-tight mb-0.5">{{ __('messages.online.step2_title') }}</p>
                    <p class="text-[9px] font-semibold text-gray-400 font-arabic leading-tight">{{ __('messages.online.step2_body') }}</p>
                </div>

                {{-- Step 3 --}}
                <div class="flex flex-col items-center relative">
                    <div class="absolute top-[1.375rem] {{ $isRtl ? 'right-0' : 'left-0' }} w-1/2 h-px bg-gray-200" style="top:1.375rem;"></div>
                    <div class="step-icon-wrap" style="background:#fdf4ff; position:relative; z-index:1;">
                        <span class="material-symbols-rounded text-purple-500" style="font-size:20px;font-variation-settings:'FILL' 1">rocket_launch</span>
                    </div>
                    <div class="w-6 h-6 rounded-full bg-purple-500 text-white text-[10px] font-black flex items-center justify-center mx-auto mb-1.5">3</div>
                    <p class="text-[11px] font-black text-textColor font-arabic leading-tight mb-0.5">{{ __('messages.online.step3_title') }}</p>
                    <p class="text-[9px] font-semibold text-gray-400 font-arabic leading-tight">{{ __('messages.online.step3_body') }}</p>
                </div>

            </div>
        </div>

        {{-- ════ HELP CARD ════ --}}
        <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/40 border border-gray-100 px-6 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:#eff6ff;">
                <span class="material-symbols-rounded text-blue-500" style="font-size:20px;font-variation-settings:'FILL' 1">support_agent</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-black text-textColor font-arabic">{{ __('messages.online.help_title') }}</p>
                <p class="text-[11px] text-gray-400 font-semibold font-arabic">{{ __('messages.online.help_body') }}</p>
            </div>
        </div>

    </div>{{-- /body --}}
</div>
@endsection

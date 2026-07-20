@extends('layouts.web.app')

@section('title', __('messages.online.page_title'))

@php
    $isRtl       = app()->getLocale() === 'ar';
    $dir         = $isRtl ? 'rtl' : 'ltr';
    $user        = auth()->user();
    $hasCredentials = $crmEmail || $crmPassword;
    $googlePlay  = \App\Models\Setting::get('google_play_url', '');
    $appStore    = \App\Models\Setting::get('app_store_url', '');
@endphp

@section('content')
<div class="min-h-screen bg-lightBg flex flex-col" dir="{{ $dir }}">

    {{-- ── Hero greeting ── --}}
    <div class="bg-gradient-to-br from-primary to-blue-700 pt-16 pb-24 px-4 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-rounded text-white" style="font-size:32px;font-variation-settings:'FILL' 1">smartphone</span>
            </div>
            <h1 class="text-2xl font-black text-white font-arabic leading-snug mb-2">
                {{ __('messages.online.greeting', ['name' => $user->name]) }}
            </h1>
            <p class="text-white/70 text-sm font-semibold font-arabic">
                {{ $plan?->name ?? __('messages.online.your_plan') }} &mdash; {{ __('messages.online.subtitle') }}
            </p>
        </div>
    </div>

    {{-- ── Main card ── --}}
    <div class="max-w-md mx-auto w-full px-4 -mt-12 pb-16 space-y-4">

        @if($hasCredentials)
        {{-- ════ Credentials card ════ --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-primary/10 overflow-hidden border border-gray-100"
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

            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-rounded text-primary" style="font-size:18px;font-variation-settings:'FILL' 1">key</span>
                </div>
                <div>
                    <p class="text-sm font-black text-textColor font-arabic">{{ __('messages.online.login_data') }}</p>
                    <p class="text-[11px] text-gray-400 font-semibold font-arabic">{{ __('messages.online.login_data_hint') }}</p>
                </div>
            </div>

            <div class="p-6 space-y-4">

                {{-- Email --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-black text-gray-400 font-arabic uppercase tracking-wide">
                        {{ __('messages.online.email_label') }}
                    </label>
                    <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                        <span class="text-sm font-bold text-textColor flex-1 break-all font-mono" x-text="email" dir="ltr">{{ $crmEmail }}</span>
                        <button type="button" @click="copyEmail()"
                            class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                            :class="copiedEmail ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-500 hover:bg-gray-300'">
                            <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1"
                                  x-text="copiedEmail ? 'check' : 'content_copy'">content_copy</span>
                        </button>
                    </div>
                </div>

                {{-- Password --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-black text-gray-400 font-arabic uppercase tracking-wide">
                        {{ __('messages.online.password_label') }}
                    </label>
                    <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                        <input :type="showPass ? 'text' : 'password'" :value="pass" readonly
                               class="text-sm font-bold text-textColor flex-1 bg-transparent outline-none font-mono min-w-0" dir="ltr">
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            {{-- Show/hide --}}
                            <button type="button" @click="showPass = !showPass"
                                class="w-8 h-8 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-500 flex items-center justify-center transition-all">
                                <span class="material-symbols-rounded" style="font-size:16px"
                                      x-text="showPass ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                            {{-- Copy --}}
                            <button type="button" @click="copyPass()"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                                :class="copiedPass ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-500 hover:bg-gray-300'">
                                <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1"
                                      x-text="copiedPass ? 'check' : 'content_copy'">content_copy</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Security note --}}
                <div class="flex items-start gap-2 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2.5">
                    <span class="material-symbols-rounded text-amber-500 flex-shrink-0 mt-0.5" style="font-size:14px;font-variation-settings:'FILL' 1">lock</span>
                    <p class="text-[10px] font-bold text-amber-700 font-arabic leading-relaxed">{{ __('messages.online.security_note') }}</p>
                </div>

            </div>
        </div>

        @else
        {{-- ════ Pending card ════ --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-primary/10 p-8 text-center border border-gray-100">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-5">
                <span class="material-symbols-rounded text-blue-400" style="font-size:32px;font-variation-settings:'FILL' 1">hourglass_top</span>
            </div>
            <h2 class="text-base font-black text-textColor font-arabic mb-2">{{ __('messages.online.pending_title') }}</h2>
            <p class="text-sm text-gray-400 font-semibold font-arabic leading-relaxed">{{ __('messages.online.pending_body') }}</p>
        </div>
        @endif

        {{-- ════ Download buttons ════ --}}
        @if($googlePlay || $appStore)
        <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/60 p-6 border border-gray-100">
            <p class="text-xs font-black text-gray-400 font-arabic mb-4 text-center">{{ __('messages.online.download_app') }}</p>
            <div class="flex flex-col gap-3">

                @if($googlePlay)
                <a href="{{ $googlePlay }}" target="_blank" rel="noopener"
                   class="flex items-center gap-4 bg-gray-900 hover:bg-gray-800 rounded-2xl px-5 py-3.5 transition-all w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="28" height="28" class="flex-shrink-0">
                        <path fill="#4CAF50" d="M6.9,2.2C6.3,2.6,6,3.3,6,4.2v39.6c0,0.9,0.3,1.6,0.9,2l0.1,0.1L28,25v-0.4L6.9,2.2z"/>
                        <path fill="#FFC107" d="M35,31.8l-7-7V25l7-6.8l0.2,0.1l8.3,4.7c2.4,1.3,2.4,3.5,0,4.9L35.2,31.7L35,31.8z"/>
                        <path fill="#F44336" d="M35.2,31.7L28,24.5L6.9,45.8c0.8,0.8,2,0.9,3.5,0.1L35.2,31.7"/>
                        <path fill="#2196F3" d="M35.2,17.3L10.4,3.1C8.9,2.3,7.7,2.4,6.9,3.2L28,24.5L35.2,17.3z"/>
                    </svg>
                    <div class="leading-none">
                        <p class="text-white/50 text-[10px] font-arabic mb-1">{{ __('messages.footer.get_it_on') }}</p>
                        <p class="text-white text-[13px] font-bold">Google Play</p>
                    </div>
                </a>
                @endif

                @if($appStore)
                <a href="{{ $appStore }}" target="_blank" rel="noopener"
                   class="flex items-center gap-4 bg-gray-900 hover:bg-gray-800 rounded-2xl px-5 py-3.5 transition-all w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="white" class="flex-shrink-0 opacity-90">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                    <div class="leading-none">
                        <p class="text-white/50 text-[10px] font-arabic mb-1">{{ __('messages.footer.download_on') }}</p>
                        <p class="text-white text-[13px] font-bold">App Store</p>
                    </div>
                </a>
                @endif

            </div>
        </div>
        @endif

        {{-- ════ Support note ════ --}}
        <p class="text-center text-[11px] text-gray-400 font-semibold font-arabic px-2">
            {{ __('messages.online.support_note') }}
        </p>

    </div>
</div>
@endsection

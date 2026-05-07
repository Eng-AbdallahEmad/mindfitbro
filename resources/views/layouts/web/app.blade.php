<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>

        @include('components.web.meta')

        <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>

        @include('components.web.link')

        @include('components.web.style')
    </head>
    <body class="bg-lightBg overflow-hidden">
        {{-- ─── Loader ─── --}}
        <div class="loader-container fixed inset-0 z-[99999] flex flex-col items-center justify-center gap-7"
            style="background: radial-gradient(ellipse 80% 70% at 50% 50%, #3a68c8 0%, #1a3fa0 60%, #0d1f4a 100%);">

            {{-- Logo --}}
            <img
                src="{{ asset('assets/logo/mindfitbro.png') }}"
                alt="MindFitBro"
                class="w-[180px] object-contain loader-item"
                style="animation: mfbFadeUp 0.6s ease both;"
            />

            {{-- Animated Bars --}}
            <div class="flex items-end gap-[5px] h-9 loader-item"
                style="animation: mfbFadeUp 0.6s 0.15s ease both; opacity:0;">
                <span class="mfb-bar" style="--d:0s;    --h:14px; opacity:.4;"></span>
                <span class="mfb-bar" style="--d:0.12s; --h:22px; opacity:.6;"></span>
                <span class="mfb-bar" style="--d:0.24s; --h:36px; opacity:1; "></span>
                <span class="mfb-bar" style="--d:0.36s; --h:22px; opacity:.6;"></span>
                <span class="mfb-bar" style="--d:0.48s; --h:14px; opacity:.4;"></span>
            </div>

            {{-- Tagline --}}
            <p class="font-arabic text-sm font-bold tracking-[3px] text-white/40 loader-item"
            style="animation: mfbFadeUp 0.6s 0.3s ease both; opacity:0;">
                {{ __('messages.loader.tagline') }}
            </p>

            {{-- Progress Bar --}}
            <div class="w-[120px] h-[2px] rounded-full overflow-hidden loader-item"
                style="background: rgba(255,255,255,0.12); animation: mfbFadeUp 0.6s 0.4s ease both; opacity:0;">
                <div class="h-full rounded-full bg-[#D4ED57]"
                    style="animation: mfbProgress 1.8s cubic-bezier(.4,0,.2,1) forwards;"></div>
            </div>

        </div>
        @yield('content')

        {{-- ══════════════ FLOATING ACTION BUTTONS ══════════════ --}}
        <div class="fixed bottom-6 left-6 z-[9999] flex flex-col-reverse gap-3">

            {{-- WhatsApp --}}
            <a href="https://wa.me/966593035979"
               target="_blank"
               rel="noopener noreferrer"
               title="{{ __('messages.fab.whatsapp_title') }}"
               class="group relative flex items-center justify-center w-14 h-14 rounded-2xl shadow-xl transition-all duration-300 hover:scale-110 hover:-translate-y-1"
               style="background:linear-gradient(135deg,#25d366 0%,#128c4a 100%);box-shadow:0 8px 24px rgba(37,211,102,0.35);">

                {{-- Ping animation --}}
                <span class="absolute inset-0 rounded-2xl bg-[#25d366] animate-ping opacity-20 group-hover:opacity-30"></span>

                {{-- WhatsApp SVG --}}
                <svg class="relative z-10 w-7 h-7" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>

                {{-- Tooltip --}}
                <span class="pointer-events-none absolute left-full mr-3 ml-3 whitespace-nowrap rounded-xl bg-gray-900/90 px-3 py-1.5 text-xs font-bold text-white font-arabic opacity-0 translate-x-2 transition-all duration-200 group-hover:opacity-100 group-hover:translate-x-0 backdrop-blur-sm"
                      style="right:auto;left:calc(100% + 12px);">
                    {{ __('messages.fab.whatsapp_tooltip') }}
                    <span class="absolute top-1/2 -translate-y-1/2 -left-1.5 border-4 border-transparent border-l-0"
                          style="border-right-color:rgba(17,24,39,0.9);"></span>
                </span>
            </a>

            {{-- Calorie Calculator --}}
            <a href="{{ route('calorie-calculator') }}"
               title="{{ __('messages.fab.calories_title') }}"
               class="group relative flex items-center justify-center w-14 h-14 rounded-2xl shadow-xl transition-all duration-300 hover:scale-110 hover:-translate-y-1"
               style="background:linear-gradient(135deg,#D4ED57 0%,#b8d400 100%);box-shadow:0 8px 24px rgba(212,237,87,0.4);">

                {{-- Icon --}}
                <span class="relative z-10 material-symbols-rounded text-[#1c1c1c]"
                      style="font-size:28px;font-variation-settings:'FILL' 1;">local_fire_department</span>

                {{-- Tooltip --}}
                <span class="pointer-events-none absolute whitespace-nowrap rounded-xl bg-gray-900/90 px-3 py-1.5 text-xs font-bold text-white font-arabic opacity-0 translate-x-2 transition-all duration-200 group-hover:opacity-100 group-hover:translate-x-0 backdrop-blur-sm"
                      style="left:calc(100% + 12px);">
                    {{ __('messages.fab.calories_tooltip') }}
                    <span class="absolute top-1/2 -translate-y-1/2 -left-1.5 border-4 border-transparent border-l-0"
                          style="border-right-color:rgba(17,24,39,0.9);"></span>
                </span>
            </a>

        </div>
        {{-- ══════════════ END FLOATING BUTTONS ══════════════ --}}

    </body>

    <script>
        window.addEventListener("load", function () {
            const loader = document.querySelector(".loader-container");
            if (!loader) return;

            setTimeout(() => {
                loader.style.transition = "opacity 0.6s ease, transform 0.6s ease";
                loader.style.opacity    = "0";
                loader.style.transform  = "scale(1.04)";

                setTimeout(() => {
                    document.body.classList.remove("overflow-hidden");
                    loader.remove();
                }, 620);
            }, 200);
        });
    </script>

    <!-- Swiper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js" integrity="sha512-Ysw1DcK1P+uYLqprEAzNQJP+J4hTx4t/3X2nbVwszao8wD+9afLjBQYjz7Uk4ADP+Er++mJoScI42ueGtQOzEA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- GSAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollToPlugin.min.js"></script>
    <!-- Lottie Player (deferred — custom element, doesn't block render) -->
    <script defer src="https://unpkg.com/@lottiefiles/lottie-player@2.0.8/dist/lottie-player.js"></script>

    @yield('script')
</html>

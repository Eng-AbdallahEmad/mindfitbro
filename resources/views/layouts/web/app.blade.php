<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
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
                جاهز تبقى أقوى؟
            </p>

            {{-- Progress Bar --}}
            <div class="w-[120px] h-[2px] rounded-full overflow-hidden loader-item"
                style="background: rgba(255,255,255,0.12); animation: mfbFadeUp 0.6s 0.4s ease both; opacity:0;">
                <div class="h-full rounded-full bg-[#D4ED57]"
                    style="animation: mfbProgress 1.8s cubic-bezier(.4,0,.2,1) forwards;"></div>
            </div>

        </div>
        @yield('content')
    </body>

    <script>
        window.addEventListener("load", function () {
            const loader = document.querySelector(".loader-container");
            if (!loader) return;

            // انتظر 200ms بعد الـ load لحسن التجربة
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

    @yield('script')
</html>

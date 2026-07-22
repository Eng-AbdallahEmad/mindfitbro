@extends('layouts.web.app')

@section('title', 'Home')

@php
    $isRtl      = app()->getLocale() === 'ar';
    $alignStart = $isRtl ? 'text-right' : 'text-left';
    $dur3Label = __('messages.programs.duration_3months');
    $dur6Label = __('messages.programs.duration_6months');
    $contact   = \App\Services\Web\ContactInfo::current();
@endphp

@section('style')
    <style>

        /* ─── Background: replicates Hero.png gradient ─── */
        .hero-bg {
            background:
                radial-gradient(ellipse 70% 60% at 55% 55%,
                    #6a8fd8 0%,
                    #3a68c8 35%,
                    #1e4db7 65%,
                    #1a3fa0 100%);
        }

        /* ─── Animated glow orbs ─── */
        .orb {
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            animation: drift 8s ease-in-out infinite alternate;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: #90b8f8;
            top: -100px; right: -100px;
            animation-duration: 10s;
        }
        .orb-2 {
            width: 350px; height: 350px;
            background: #3a68c8;
            bottom: -80px; left: -80px;
            animation-duration: 13s;
            animation-delay: -4s;
        }

        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, 20px) scale(1.08); }
        }

        .text-outline {
            color: transparent;
            -webkit-text-stroke: 2px rgba(255,255,255,1);
        }

        .mySwiper,
        .mySwiper2 {
            direction: ltr;
        }

        .swiper-slide {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .swiper-slide img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@endsection

@section('content')
    {{-- Nav Bar --}}
    <x-web.navbar :transparent="true" />

    {{-- Hero Section --}}
    <section class="hero relative w-full flex items-center overflow-hidden px-2 lg:px-[1rem] 2xl:px-28 pt-[10rem] lg:pt-[20rem] pb-[5rem] lg:pb-[10rem]">
        <div class="absolute top-0 left-[149px] w-[195px] h-[34rem] z-20 pointer-events-none bg-gradient-to-b from-white/30 to-transparent hidden 2xl:block"></div>

        {{-- Background Layer --}}
        <div class="hero-bg absolute inset-0 z-0"></div>

        {{-- Subtle animated glow orbs --}}
        <div class="orb orb-1 absolute z-0 hidden lg:block"></div>
        <div class="orb orb-2 absolute z-0 hidden lg:block"></div>

        <div class="absolute transform -translate-x-[66.8rem] translate-y-[70px] max-w-[195px] hidden 2xl:block">
            <p class="text-white text-xl font-black font-arabic">
                {!! __('messages.hero.success_count', ['count' => '<span class="text-[#D4ED57] font-bold">'.$settings->get('hero_success_count','500').'</span>']) !!}
            </p>
        </div>

        {{-- Content --}}
        <div class="relative z-10 {{ $alignStart }} px-4 lg:px-6 w-full">
            <h2 class="text-white font-semibold font-arabic text-xl lg:text-3xl">{{ __('messages.hero.subtitle') }}</h2>
            <div class="relative inline-block">

                <h1 class="text-white font-black font-display text-[4.2rem] md:text-[6rem] {{ $isRtl ? 'lg:text-[7.5rem] xl:text-[8rem] 2xl:text-[9rem]' : 'lg:text-[9rem] xl:text-[10.5rem] 2xl:text-[12rem]' }} text-center my-5 lg:my-0 md:whitespace-nowrap ipad-mini:text-[4.7rem] ipad-mini-land:text-[6.6rem]">
                    {{ __('messages.hero.title') }}
                </h1>

                <h2 class="absolute top-0 right-0 left-0 hidden lg:block text-outline font-black pointer-events-none font-display md:whitespace-nowrap {{ $isRtl ? 'lg:text-[7.5rem] xl:text-[8rem] 2xl:text-[9rem]' : 'lg:text-[9rem] xl:text-[10.5rem] 2xl:text-[12rem]' }} ipad-mini-land:text-[6.6rem] z-50">
                    {{ __('messages.hero.title') }}
                </h2>

            </div>

            <div class="absolute top-1/2 left-1/2 {{ $isRtl ? 'md:translate-x-[-70%] xl:translate-x-[-60%]' : 'md:translate-x-[-70%] xl:translate-x-[-30%]' }} {{ $isRtl ? 'md:translate-y-[-42%] xl:translate-y-[-46%]' : 'md:translate-y-[-42%] xl:translate-y-[-45%]' }} hidden lg:block {{ $isRtl ? 'md:w-[850px]' : 'md:w-[900px]' }}">

                <!-- Image -->
                <img
                class="w-full"
                src="{{ asset('assets/imgs/hero.png') }}"
                alt="{{ config('app.name', 'laravel') }}"
                fetchpriority="high"
                >

                <!-- Shadow -->
                <div class="absolute bottom-[65px] left-1/2 -translate-x-1/2 w-[640px] h-[30px] bg-black blur-xl rounded-full"></div>

            </div>

            <h3 class="text-white font-normal font-arabic md:mt-4 md:max-w-md xl:max-w-lg text-base lg:text-xl z-50 relative">
                {{ __('messages.hero.description') }}
            </h3>

            <div class=" max-w-[18.4rem] mt-8">
                <a href="#programs" class="group font-arabic text-textColor bg-accent px-5 py-3 rounded-full text-sm lg:text-base font-black flex justify-center items-center gap-2 transition">
                    {{ __('messages.hero.cta') }}
                    @if($isRtl)
                        <svg class="transition-transform duration-300 group-hover:-translate-x-2" width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                        </svg>
                    @else
                        <svg class="transition-transform duration-300 group-hover:translate-x-2" width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M28.5103 8.31712V5.68152H27.1423L27.1425 8.31712H28.5103ZM25.7035 11.2832C25.9091 11.2832 26.1024 11.2031 26.2478 11.0577C26.3932 10.9123 26.4732 10.719 26.4732 10.5134L26.473 8.65455L26.4729 8.65189L26.473 8.64924L26.4726 3.48591C26.4726 3.06162 26.1273 2.71639 25.703 2.71639H24.4084L24.409 11.2832L25.7035 11.2832ZM21.6916 13.7746C21.837 13.92 22.0302 14 22.2358 14L22.9701 13.9999C23.3944 13.9998 23.7396 13.6546 23.7396 13.2302L23.7388 2.38397L23.7387 2.38162L23.7388 2.37927L23.7387 0.76964C23.7387 0.564042 23.6586 0.370757 23.5132 0.225405C23.3679 0.0800539 23.1746 0 22.9691 0L22.2349 0.000129431C21.8106 0.000164676 21.4654 0.345474 21.4654 0.769816L21.4657 5.34526L21.4658 5.34718L21.4657 5.34912L21.4661 13.2304C21.4662 13.436 21.5462 13.6293 21.6916 13.7746ZM20.7963 8.31762L20.7962 5.68201L0.000188134 5.68283L0 8.31844L20.7963 8.31762Z" fill="#202020"/>
                        </svg>
                    @endif
                </a>
            </div>

            {{-- App Store Download Buttons --}}
            <div class="flex items-center gap-3 mt-5 flex-wrap">
                {{-- Google Play --}}
                <a href="{{ $settings->get('google_play_url', '#') }}"
                   class="group flex items-center gap-3 bg-white/10 hover:bg-white/18 border border-white/15 hover:border-white/30 rounded-[14px] px-4 py-2.5 transition-all duration-300 w-fit">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="30" height="30" class="flex-shrink-0">
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
                {{-- App Store --}}
                <a href="{{ $settings->get('app_store_url', '#') }}"
                   class="group flex items-center gap-3 bg-white/10 hover:bg-white/18 border border-white/15 hover:border-white/30 rounded-[14px] px-4 py-2.5 transition-all duration-300 w-fit">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30" fill="white" class="flex-shrink-0 opacity-85 group-hover:opacity-100 transition-opacity">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                    <div class="leading-none">
                        <p class="text-white/50 text-[10px] font-arabic mb-1">{{ __('messages.footer.download_on') }}</p>
                        <p class="text-white text-[13px] font-bold">App Store</p>
                    </div>
                </a>
            </div>
        </div>

    </section>

    {{-- Marquee / Ticker Section --}}
    <section id="tickerSection" class="bg-lightBg border-y border-gray-200 py-[14px] overflow-hidden w-full group">
            @php
                $lang        = app()->getLocale();
                $rawMarquee  = $settings->get("marquee_items_{$lang}", '');
                $items       = $rawMarquee
                    ? array_values(array_filter(array_map('trim', explode("\n", $rawMarquee))))
                    : __('messages.marquee.items');
            @endphp

        <div id="tickerTrack" class="flex w-max {{ $isRtl ? "animate-marquee" : "animate-marquee-ltr" }} group-hover:[animation-play-state:paused]">

            @foreach($items as $item)
                <span class="ticker-item flex items-center gap-[28px] px-[28px] whitespace-nowrap text-[14px] font-bold text-textColor tracking-[0.01em] font-arabic">

                    {{ $item }}

                    <span class="flex items-center text-blue-600">
                        <svg width="31" height="24" viewBox="0 0 41 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 14.2579V9.73976H2.34512L2.34475 14.2579H0ZM4.8116 19.3426C4.45914 19.3426 4.1278 19.2053 3.87859 18.956C3.62937 18.7068 3.49218 18.3754 3.4922 18.023L3.49246 14.8364C3.49246 14.8349 3.49268 14.8334 3.49268 14.8318C3.49268 14.8303 3.49246 14.8288 3.49246 14.8273L3.49316 5.97588C3.49326 5.24852 4.08516 4.6567 4.81256 4.6567H7.03182L7.03074 19.3426L4.8116 19.3426ZM11.6891 23.6137C11.4399 23.8628 11.1087 24 10.7563 24L9.49744 23.9998C8.77004 23.9997 8.17826 23.4078 8.17826 22.6804L8.17963 4.08683C8.17965 4.08546 8.17983 4.08418 8.17983 4.08281C8.17983 4.08144 8.17963 4.08015 8.17963 4.07878L8.17983 1.31942C8.17983 0.966968 8.31711 0.635622 8.56636 0.386448C8.81558 0.137275 9.14682 3.99508e-05 9.49923 3.99508e-05L10.7577 0.000261831C11.4852 0.000322252 12.077 0.59228 12.077 1.31972L12.0765 9.16333C12.0765 9.1644 12.0763 9.16549 12.0763 9.16662C12.0763 9.16774 12.0765 9.16881 12.0765 9.16994L12.0757 22.6807C12.0757 23.0331 11.9384 23.3645 11.6891 23.6137ZM13.2239 14.2588L13.2242 9.74061L27.0562 9.74202L27.0566 14.2602L13.2239 14.2588ZM30.7829 23.9998L29.5244 23.9999C28.7969 23.9999 28.205 23.4081 28.2049 22.6805L28.2034 1.3196C28.2034 0.5921 28.7952 0.00022131 29.5227 0.00014075L30.7812 0C31.1337 0 31.465 0.137154 31.7142 0.386348C31.9634 0.635602 32.1006 0.966867 32.1007 1.3194L32.102 19.9163L32.1019 19.9166L32.102 19.917L32.1022 22.6803C32.1022 23.4078 31.5104 23.9997 30.7829 23.9998ZM35.4689 19.3425L33.2497 19.3427L33.2486 4.6568L35.4678 4.65662C35.8203 4.65662 36.1516 4.79377 36.4008 5.04297C36.65 5.29214 36.7873 5.62349 36.7873 5.97594L36.7876 9.16803L36.7875 9.16897L36.7876 9.16992L36.7882 18.023C36.7882 18.7505 36.1964 19.3424 35.4689 19.3425ZM40.2802 14.261H37.9357L37.9354 9.74286H40.2802V14.261Z" fill="#174DAD"/>
                        </svg>
                    </span>

                </span>
            @endforeach

        </div>

    </section>

    {{-- Why Us Section --}}
    <section id="our-target" class="bg-white w-full py-16 lg:py-28 px-8 lg:px-20 flex flex-col justify-center">
        <div class="flex flex-col justify-center items-center mb-7">
            <h2 class="font-display text-3xl ipad-mini:text-4xl ipad-mini-land:text-5xl md:text-6xl xl:text-7xl cursor-default transition-all duration-300 text-textColor hover:text-primary font-semibold mb-4 lg:mb-7">
                {{ __('messages.why_us.title') }}
            </h2>
            <p class="font-arabic font-bold text-textColor text-sm lg:text-xl flex items-center justify-center gap-1 text-center leading-relaxed">
                {{ __('messages.why_us.subtitle') }}

                <lottie-player
                    src="{{ asset('assets/lotties/Muscle.json') }}"
                    background="transparent"
                    speed="1"
                    class="w-[40px] h-[40px] translate-y-[-9px] hidden lg:block {{ $isRtl ? '' : 'scale-x-[-1]' }}"
                    loop
                    autoplay>
                </lottie-player>
            </p>
        </div>

        <div class="swiper mySwiper w-full lg:w-[70%] h-[200px] lg:h-[500px]">
            <div class="swiper-wrapper">
                @forelse($videos as $video)
                <div class="swiper-slide">
                    <div class="video-card relative w-full h-full rounded-2xl overflow-hidden bg-black">
                        <div class="video-overlay absolute inset-0 z-10 transition-all duration-700 opacity-100">
                            <img
                                src="{{ $video->thumbnail_src }}"
                                alt="{{ $video->title }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-black/30"></div>
                            <a
                                href="{{ $video->video_url }}"
                                target="_blank"
                                class="play-btn absolute inset-0 m-auto w-20 h-20 rounded-full bg-white/90 flex items-center justify-center shadow-xl hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-black mr-[-3px]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5.14v14l11-7-11-7z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="swiper-slide">
                    <div class="video-card relative w-full h-full rounded-2xl overflow-hidden bg-black">
                        <div class="video-overlay absolute inset-0 z-10 transition-all duration-700 opacity-100">
                            <img
                                src="{{ asset('assets/imgs/video-thumb-1.png') }}"
                                alt="Video"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-black/30"></div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            <div class="swiper-pagination"></div>
        </div>

        <div class="my-9 lg:my-24 text-center">
            <h2 class="font-display text-3xl lg:text-7xl cursor-default transition-all duration-300 text-textColor hover:text-primary font-semibold mb-4 lg:mb-7 ipad-mini:text-4xl ipad-mini-land:text-5xl">{{ __('messages.why_us.note_title') }}</h2>
            <p class="font-arabic font-bold text-textColor text-xl lg:text-5xl ipad-mini-land:text-3xl">{{ __('messages.why_us.note_subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Card 1 -->
            <div class="relative rounded-2xl p-8 pt-12 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 bg-primary hover:bg-primaryDark font-arabic border-4 border-white hover:border-accent">
                <div class="absolute top-[-1.55rem] left-1/2 -translate-x-1/2 whitespace-nowrap px-6 py-2.5 rounded-full font-black text-lg text-textColor bg-accent">
                {{ $settings->get('whyus_card1_count', '+2,500') }} <span class="font-bold text-base">{{ __('messages.why_us.card1.badge') }}</span>
                </div>
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mt-4 bg-[rgba(255,255,255,0.12)]">
                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
                </div>
                <h3 class="text-xl font-black mb-3 text-white">{{ __('messages.why_us.card1.title') }}</h3>
                <p class="text-sm leading-relaxed text-[rgba(255,255,255,0.7)]">
                    {{ __('messages.why_us.card1.desc') }}
                </p>
            </div>

            <!-- Card 2 -->
            <div class="relative rounded-2xl p-8 pt-12 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 bg-primary hover:bg-primaryDark font-arabic border-4 border-white hover:border-accent">
                <div class="absolute top-[-1.55rem] left-1/2 -translate-x-1/2 whitespace-nowrap px-6 py-2.5 rounded-full font-black text-lg text-textColor bg-accent">
                {{ $settings->get('whyus_card2_count', '+20,000') }} <span class="font-bold text-base">{{ __('messages.why_us.card2.badge') }}</span>
                </div>
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mt-4 bg-[rgba(255,255,255,0.12)]">
                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                </div>
                <h3 class="text-xl font-black mb-3 text-white">{{ __('messages.why_us.card2.title') }}</h3>
                <p class="text-sm leading-relaxed text-[rgba(255,255,255,0.7)]">
                    {{ __('messages.why_us.card2.desc') }}
                </p>
            </div>

            <!-- Card 3 -->
            <div class="relative rounded-2xl p-8 pt-12 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 bg-primary hover:bg-primaryDark font-arabic border-4 border-white hover:border-accent">
                <div class="absolute top-[-1.55rem] left-1/2 -translate-x-1/2 whitespace-nowrap px-6 py-2.5 rounded-full font-black text-lg text-textColor bg-accent">
                {{ $settings->get('whyus_card3_count', '+10,000') }} <span class="font-bold text-base">{{ __('messages.why_us.card3.badge') }}</span>
                </div>
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mt-4 bg-[rgba(255,255,255,0.12)]">
                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                </div>
                <h3 class="text-xl font-black mb-3 text-white">{{ __('messages.why_us.card3.title') }}</h3>
                <p class="text-sm leading-relaxed text-[rgba(255,255,255,0.7)]">
                    {{ __('messages.why_us.card3.desc') }}
                </p>
            </div>

        </div>

        <div class="flex justify-center my-11">
              <div class="relative group">
                <a href="#programs"
                class="relative inline-block p-px font-semibold leading-6 text-white bg-textColor shadow-2xl cursor-pointer rounded-2xl shadow-emerald-900 transition-all duration-300 ease-in-out hover:scale-105 active:scale-95 hover:shadow-primary"
                >
                    <span
                        class="absolute inset-0 rounded-2xl bg-gradient-to-r from-emerald-500 via-cyan-500 to-sky-600 p-[2px] opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                    ></span>
                    <span class="relative z-10 block px-6 py-3 rounded-2xl bg-textColor">
                        <div class="relative z-10 flex items-center gap-2">
                            <span class="transition-all duration-500 {{ $isRtl ? 'group-hover:-translate-x-1.5' : 'group-hover:translate-x-1.5' }} group-hover:text-accent font-arabic text-xl">{{ __('messages.why_us.subscribe_now') }}</span>
                            @if ($isRtl)
                                <svg class="w-7 h-7 transition-all duration-500 group-hover:-translate-x-1.5 group-hover:text-accent" viewBox="0 0 29 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z"/>
                            </svg>
                            @else
                                <svg class="w-7 h-7 transition-all duration-500 group-hover:translate-x-1.5 group-hover:text-accent" viewBox="0 0 29 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M28.5103 8.31712V5.68152H27.1423L27.1425 8.31712H28.5103ZM25.7035 11.2832C25.9091 11.2832 26.1024 11.2031 26.2478 11.0577C26.3932 10.9123 26.4732 10.719 26.4732 10.5134L26.473 8.65455L26.4729 8.65189L26.473 8.64924L26.4726 3.48591C26.4726 3.06162 26.1273 2.71639 25.703 2.71639H24.4084L24.409 11.2832L25.7035 11.2832ZM21.6916 13.7746C21.837 13.92 22.0302 14 22.2358 14L22.9701 13.9999C23.3944 13.9998 23.7396 13.6546 23.7396 13.2302L23.7388 2.38397L23.7387 2.38162L23.7388 2.37927L23.7387 0.76964C23.7387 0.564042 23.6586 0.370757 23.5132 0.225405C23.3679 0.0800539 23.1746 0 22.9691 0L22.2349 0.000129431C21.8106 0.000164676 21.4654 0.345474 21.4654 0.769816L21.4657 5.34526L21.4658 5.34718L21.4657 5.34912L21.4661 13.2304C21.4662 13.436 21.5462 13.6293 21.6916 13.7746ZM20.7963 8.31762L20.7962 5.68201L0.000188134 5.68283L0 8.31844L20.7963 8.31762Z"/>
                                </svg>
                            @endif
                        </div>
                    </span>
                </a>
            </div>
        </div>
    </section>

    {{-- Our Services Section --}}
    <section class="w-full bg-[#EFF5FF] py-16 lg:py-28 px-8 lg:px-20">
        <div class="flex flex-col-reverse md:flex-row-reverse gap-8 items-start">

            <!-- RIGHT: Slider -->
            <div class="w-full md:w-[420px] xl:w-[750px] flex-shrink-0 mx-auto md:mx-0 ipad-mini:w-[320px]">
                <div class="swiper mySwiper2 w-full md:w-[100%] xl:w-[70%] h-[310px] lg:h-[550px] aspect-[9/16] rounded-2xl overflow-hidden shadow-2xl" dir="ltr">
                    <div class="swiper-wrapper">
                        @forelse($testimonials as $testimonial)
                        <div class="swiper-slide">
                            <div class="relative w-full h-full">
                                <img src="{{ $testimonial->image_src }}"
                                    class="w-full h-full object-cover"
                                    alt="{{ $testimonial->alt_text ?: 'MindFitBro' }}"
                                    loading="lazy">
                            </div>
                        </div>
                        @empty
                        <div class="swiper-slide">
                            <div class="relative w-full h-full">
                                <img src="{{ asset('assets/imgs/og-image.png') }}"
                                    class="w-full h-full object-cover"
                                    alt="MindFitBro"
                                    loading="lazy">
                            </div>
                        </div>
                        @endforelse
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>

            <!-- LEFT: Content -->
            <div class="flex flex-col gap-6">

                <!-- Heading -->
                <div>
                    <h2 class="font-display text-3xl lg:text-7xl cursor-default transition-all duration-300 text-textColor hover:text-primary font-semibold mb-4 lg:mb-7 ipad-mini-land:text-5xl">
                        {{ __('messages.services.title') }}
                    </h2>
                    <p class="text-base leading-relaxed text-[#1C1C1C] font-arabic">
                        {{ __('messages.services.desc') }}
                    </p>
                </div>

                <!-- Accordion -->
                <div class="flex flex-col gap-3">

                    <!-- Item 1 (Active) -->
                    <div class="accordion-item active cursor-pointer overflow-hidden rounded-[14px] bg-primary transition duration-300 hover:shadow-lg font-arabic"
                        onclick="toggleAccordion(this)">
                        <div class="flex items-center justify-between gap-4 p-4">
                            <span class="acc-title text-base lg:text-lg font-bold text-accent transition">
                                {{ __('messages.services.step1') }}
                            </span>
                            <div class="acc-btn flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-accent">
                                <svg class="h-[18px] w-[18px] -rotate-90 text-[#1C1C1C] transition"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="acc-desc block px-4 pb-4 text-sm leading-relaxed text-white">
                            {{ __('messages.services.step1_desc') }}
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="accordion-item cursor-pointer overflow-hidden rounded-[14px] bg-white transition duration-300 hover:shadow-lg font-arabic" onclick="toggleAccordion(this)">
                        <div class="flex items-center justify-between gap-4 p-4">
                            <span class="acc-title text-base lg:text-lg font-bold text-textColor transition">
                                {{ __('messages.services.step2') }}
                            </span>
                            <div class="acc-btn flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-accent">
                                <svg class="h-[18px] w-[18px] text-[#1C1C1C] transition"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="acc-desc hidden px-4 pb-4 text-sm leading-relaxed text-gray-600">
                           {{ __('messages.services.step2_desc') }}
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="accordion-item cursor-pointer overflow-hidden rounded-[14px] bg-white transition duration-300 hover:shadow-lg font-arabic" onclick="toggleAccordion(this)">
                        <div class="flex items-center justify-between gap-4 p-4">
                            <span class="acc-title text-base lg:text-lg font-bold text-textColor transition">
                                {{ __('messages.services.step3') }}
                            </span>
                            <div class="acc-btn flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-accent">
                                <svg class="h-[18px] w-[18px] text-[#1C1C1C] transition"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="acc-desc hidden px-4 pb-4 text-sm leading-relaxed text-gray-600">
                            {{ __('messages.services.step3_desc') }}
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="accordion-item cursor-pointer overflow-hidden rounded-[14px] bg-white transition duration-300 hover:shadow-lg font-arabic" onclick="toggleAccordion(this)">
                        <div class="flex items-center justify-between gap-4 p-4">
                            <span class="acc-title text-base lg:text-lg font-bold text-textColor transition">
                                {{ __('messages.services.step4') }}
                            </span>
                            <div class="acc-btn flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-accent">
                                <svg class="h-[18px] w-[18px] text-[#1C1C1C] transition"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="acc-desc hidden px-4 pb-4 text-sm leading-relaxed text-gray-600">
                           {{ __('messages.services.step4_desc') }}
                        </div>
                    </div>

                </div>

                <div class=" {{ $isRtl ? "max-w-[20.5rem]" : "max-w-[32rem]" }} mt-4 lg:mt-8">
                    <a href="#programs"
                    class="group font-arabic text-textColor bg-accent px-5 py-3 rounded-full text-sm lg:text-base font-black flex justify-center items-center gap-2 transition">
                        {{ __('messages.services.cta') }}
                        @if($isRtl)
                            <svg class="transition-transform duration-300 group-hover:-translate-x-2" width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                            </svg>
                        @else
                            <svg class="transition-transform duration-300 group-hover:translate-x-2" width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M28.5103 8.31712V5.68152H27.1423L27.1425 8.31712H28.5103ZM25.7035 11.2832C25.9091 11.2832 26.1024 11.2031 26.2478 11.0577C26.3932 10.9123 26.4732 10.719 26.4732 10.5134L26.473 8.65455L26.4729 8.65189L26.473 8.64924L26.4726 3.48591C26.4726 3.06162 26.1273 2.71639 25.703 2.71639H24.4084L24.409 11.2832L25.7035 11.2832ZM21.6916 13.7746C21.837 13.92 22.0302 14 22.2358 14L22.9701 13.9999C23.3944 13.9998 23.7396 13.6546 23.7396 13.2302L23.7388 2.38397L23.7387 2.38162L23.7388 2.37927L23.7387 0.76964C23.7387 0.564042 23.6586 0.370757 23.5132 0.225405C23.3679 0.0800539 23.1746 0 22.9691 0L22.2349 0.000129431C21.8106 0.000164676 21.4654 0.345474 21.4654 0.769816L21.4657 5.34526L21.4658 5.34718L21.4657 5.34912L21.4661 13.2304C21.4662 13.436 21.5462 13.6293 21.6916 13.7746ZM20.7963 8.31762L20.7962 5.68201L0.000188134 5.68283L0 8.31844L20.7963 8.31762Z" fill="#202020"/>
                            </svg>
                        @endif
                    </a>
                </div>
            </div>

        </div>
    </section>

    {{-- Before/After Section --}}
    <section id="before-after" class="w-full bg-white py-16 lg:py-28 flex flex-col justify-center items-center gap-14 overflow-hidden">

        {{-- Header --}}
        <div class="flex flex-col items-center gap-3 text-center px-6">
            <h2 class="font-display text-3xl lg:text-7xl cursor-default transition-all duration-300 text-textColor hover:text-primary font-semibold mb-4 lg:mb-7">
                {{ __('messages.before_after.title') }}
            </h2>
            <p class="font-arabic font-bold text-textColor text-base lg:text-xl flex items-center justify-center gap-1 text-center leading-relaxed">
                <lottie-player
                    src="{{ asset('assets/lotties/Muscle.json') }}"
                    background="transparent"
                    speed="1"
                    class="w-[40px] h-[40px] translate-y-[-9px] {{ $isRtl ? 'scale-x-[-1]' : '' }}"
                    loop
                    autoplay>
                </lottie-player>
                {{ __('messages.before_after.subtitle') }}
                <lottie-player
                    src="{{ asset('assets/lotties/Muscle.json') }}"
                    background="transparent"
                    speed="1"
                    class="w-[40px] h-[40px] translate-y-[-9px] {{ $isRtl ? '' : 'scale-x-[-1]' }}"
                    loop
                    autoplay>
                </lottie-player>
            </p>
        </div>

        {{-- Swiper --}}
        <div class="relative w-full max-w-[1200px] mx-auto px-3 lg:px-6">
            <div class="swiper beforeAfterSwiper w-full py-8">
                <div class="swiper-wrapper items-center">
                    @forelse($beforeAfters as $item)
                    <div class="swiper-slide">
                        <div class="rounded-[15px] overflow-hidden">
                            <img
                                src="{{ $item->image_src }}"
                                alt="{{ $item->alt_text ?: 'MindFitBro' }}"
                                class="w-full h-full object-cover object-top"
                                loading="lazy"
                            />
                        </div>
                    </div>
                    @empty
                    @for($i = 1; $i <= 5; $i++)
                    <div class="swiper-slide">
                        <div class="rounded-[15px] overflow-hidden">
                            <img src="{{ asset('assets/imgs/c' . $i . '.png') }}" alt="Client {{ $i }}"
                                class="w-full h-full object-cover object-top" loading="lazy"/>
                        </div>
                    </div>
                    @endfor
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Dots --}}
        <div class="flex justify-center items-center gap-2" id="baDots">
            @forelse($beforeAfters as $item)
            <button
                data-index="{{ $loop->index }}"
                class="ba-dot rounded-full border-0 cursor-pointer transition-all duration-300 p-0
                    {{ $loop->first ? 'w-2.5 h-2.5 bg-primary' : 'w-2.5 h-2.5 bg-gray-300' }}"
            ></button>
            @empty
            @for($i = 0; $i < 5; $i++)
            <button data-index="{{ $i }}"
                class="ba-dot rounded-full border-0 cursor-pointer transition-all duration-300 p-0
                    {{ $i === 0 ? 'w-2.5 h-2.5 bg-primary' : 'w-2.5 h-2.5 bg-gray-300' }}"
            ></button>
            @endfor
            @endforelse
        </div>

        {{-- Our Message --}}
        <div class="mt-8 lg:mt-14 px-3 lg:px-24 ipad-mini-land:px-3">
            <div class="container px-5 lg:px-52 py-10 lg:py-28 bg-primary shadow-xl rounded-[20px] relative overflow-hidden text-center">

                <img src="{{ asset('assets/icons/line1.svg') }}" alt="" class="absolute bottom-[-35px] right-[-20px]" loading="lazy">
                <img src="{{ asset('assets/icons/line2.svg') }}" alt="" class="absolute top-[-35px] left-[-20px]" loading="lazy">

                <h4 class="font-arabic mb-6 text-2xl lg:text-4xl font-black text-accent tracking-wide z-10">
                    {{ __('messages.before_after.mission_title') }}
                </h4>

                <h3 class="font-display mb-6 text-[1.72rem] lg:text-[2.6rem] leading-snug text-white z-10">
                    {!! __('messages.before_after.mission_heading') !!}
                </h3>

                <p class="font-arabic text-base lg:text-lg text-white/90 leading-relaxed max-w-3xl mx-auto z-10">
                    {!! __('messages.before_after.mission_desc') !!}

                    <span class="block mt-4 text-white font-bold">
                        {{ __('messages.before_after.mission_tagline') }}
                    </span>
                </p>

            </div>
        </div>

    </section>

    {{-- Subscription Section --}}
    <section id="programs" class="w-full bg-lightBg py-10 lg:py-28 flex flex-col justify-center items-center gap-14 overflow-hidden" x-data="{ months: 3 }">

        {{-- Header --}}
        <div class="flex flex-col items-center gap-3 text-center px-6">
            <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                {{ __('messages.programs.badge') }}
            </span>
            <h2 class="font-display text-4xl lg:text-7xl font-semibold text-textColor">
                {{ __('messages.programs.title') }} <span class="text-primary">{{ __('messages.programs.title_highlight') }}</span>
            </h2>
            <p class="text-[#1c1c1c] text-sm lg:text-base max-w-sm font-arabic font-medium leading-relaxed">
                {{ __('messages.programs.subtitle') }}
            </p>
        </div>

        @if($plans->isEmpty())

        {{-- ══ Coming Soon State ══ --}}
        <div class="w-full max-w-[980px] px-6 mx-auto">
            <div class="relative rounded-[28px] overflow-hidden border border-gray-100 bg-white shadow-[0_20px_60px_rgba(23,77,173,0.08)]">

                {{-- Top accent bar --}}
                <div class="h-1.5 w-full bg-gradient-to-r from-primary via-blue-400 to-accent"></div>

                <div class="flex flex-col lg:flex-row items-center gap-12 p-10 lg:p-16 font-arabic" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

                    {{-- Left / Text side --}}
                    <div class="flex-1 flex flex-col items-start gap-6 {{ $alignStart }}">

                        <span class="inline-flex items-center gap-2 bg-primary/10 text-primary text-[11px] font-black tracking-widest px-4 py-1.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                            {{ __('messages.programs.coming_soon_badge') }}
                        </span>

                        <div>
                            <h3 class="font-display text-3xl lg:text-5xl font-black text-textColor leading-tight mb-3">
                                {{ __('messages.programs.coming_soon_title') }}
                            </h3>
                            <p class="text-gray-500 text-sm lg:text-base leading-relaxed max-w-md">
                                {{ __('messages.programs.coming_soon_desc') }}
                            </p>
                        </div>

                        {{-- What's included preview --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full max-w-md">
                            @foreach([
                                ['icon' => 'fitness_center',    'text' => __('messages.programs.coming_feature_1')],
                                ['icon' => 'restaurant_menu',   'text' => __('messages.programs.coming_feature_2')],
                                ['icon' => 'monitor_heart',     'text' => __('messages.programs.coming_feature_3')],
                                ['icon' => 'military_tech',     'text' => __('messages.programs.coming_feature_4')],
                            ] as $feat)
                            <div class="flex items-center gap-3 bg-[#F4F7FF] rounded-2xl px-4 py-3">
                                <span class="material-symbols-rounded text-primary flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 1">{{ $feat['icon'] }}</span>
                                <span class="text-sm font-bold text-textColor">{{ $feat['text'] }}</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- WhatsApp CTA --}}
                        <a href="https://wa.me/{{ $contact['whatsapp'] }}?text={{ urlencode(__('messages.programs.coming_soon_wa_msg')) }}"
                           target="_blank" rel="noopener"
                           class="inline-flex items-center gap-3 bg-[#25D366] text-white font-black text-sm px-7 py-3.5 rounded-full hover:bg-[#1ebe5d] transition-all duration-200 hover:-translate-y-0.5 shadow-[0_4px_16px_rgba(37,211,102,0.3)]">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            {{ __('messages.programs.coming_soon_cta') }}
                        </a>

                    </div>

                    {{-- Right / Visual side --}}
                    <div class="hidden lg:flex flex-col items-center gap-5 min-w-[240px]">

                        {{-- Animated icon --}}
                        <div class="relative">
                            <div class="w-36 h-36 rounded-full bg-gradient-to-br from-primary to-blue-400 flex items-center justify-center shadow-[0_20px_48px_rgba(23,77,173,0.25)]">
                                <span class="material-symbols-rounded text-white" style="font-size:64px;font-variation-settings:'FILL' 1">workspace_premium</span>
                            </div>
                            <div class="absolute -top-2 -right-2 w-10 h-10 rounded-full bg-accent flex items-center justify-center shadow-md">
                                <span class="material-symbols-rounded text-darkBg" style="font-size:20px;font-variation-settings:'FILL' 1">bolt</span>
                            </div>
                        </div>

                        {{-- Mock price cards --}}
                        <div class="flex flex-col gap-3 w-full">
                            @foreach([['٣ شهور', 'from-primary/10 to-primary/5', 'text-primary'], ['٦ شهور', 'from-accent/30 to-accent/10', 'text-amber-700']] as [$label, $grad, $col])
                            <div class="flex items-center justify-between bg-gradient-to-l {{ $grad }} rounded-2xl px-5 py-3 border border-white/60 backdrop-blur-sm">
                                <span class="text-sm font-black {{ $col }}">{{ $label }}</span>
                                <div class="flex gap-1">
                                    @for($i = 0; $i < 4; $i++)
                                    <div class="h-2.5 rounded-full bg-current opacity-20 {{ $i === 0 ? 'w-10' : ($i === 1 ? 'w-6' : 'w-4') }} {{ $col }}"></div>
                                    @endfor
                                </div>
                            </div>
                            @endforeach
                            <p class="text-center text-[11px] text-gray-400 font-bold mt-1">{{ __('messages.programs.coming_soon_price_hint') }}</p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        @else

        {{-- Duration Selector --}}
        <div class="flex items-center gap-3">
            <div class="flex items-center bg-gray-100 rounded-full p-1.5">
                <button @click="months = 3"
                    class="font-arabic font-bold text-sm px-5 py-2 rounded-full transition-all duration-200"
                    :class="months === 3 ? 'bg-white text-primary shadow' : 'text-gray-400 hover:text-gray-600'">
                    {{ __('messages.programs.duration_3months') }}
                </button>
                <button @click="months = 6"
                    class="font-arabic font-bold text-sm px-5 py-2 rounded-full transition-all duration-200"
                    :class="months === 6 ? 'bg-white text-primary shadow' : 'text-gray-400 hover:text-gray-600'">
                    {{ __('messages.programs.duration_6months') }}
                </button>
            </div>
            <span class="bg-accent text-darkBg text-[11px] font-black px-3 py-1 rounded-full font-arabic">{{ __('messages.programs.save_with_6months') }}</span>
        </div>

        @php
            $rewardEnabled      = $settings->get('family_reward_enabled', '0') === '1';
            $rewardPlanId       = (int) ($settings->get('family_reward_plan_id') ?: 0);
            $isEliteSubscriber  = $rewardSubscription
                && $rewardSubscription->plan_id === $rewardPlanId
                && in_array($rewardSubscription->status, ['approved', 'active']);
            $isPendingRewardSub = $rewardSubscription
                && $rewardSubscription->plan_id === $rewardPlanId
                && $rewardSubscription->status === 'pending_review';
            $showRewardCard     = $rewardEnabled && $rewardPlanId;
            $isRewardLocked     = !$isEliteSubscriber;
            $showRewardBadge    = $rewardEnabled && $rewardPlanId;
            $familyDiscount     = $settings->get('family_reward_discount_mode', 'fixed') === 'range'
                ? ((int) $settings->get('family_reward_discount_min', 10) . '–' . (int) $settings->get('family_reward_discount_max', 30))
                : ((int) $settings->get('family_reward_discount_value', 20));
            $rewardPlan         = $plans->firstWhere('id', $rewardPlanId);
        @endphp

        {{-- Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-[980px] w-full px-6">
            @foreach($plans as $plan)
            <div class="relative rounded-[24px] p-8 border font-arabic {{ $alignStart }} transition-all duration-300 hover:-translate-y-2
                {{ $plan->id === $popularPlanId
                    ? 'border-[2.5px] border-primary bg-[#F0F5FF] shadow-[0_20px_48px_rgba(23,77,173,0.15)]'
                    : 'border-gray-200 bg-white hover:shadow-xl' }}">

                @if($plan->id === $popularPlanId)
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-white text-xs font-black px-5 py-1.5 rounded-full whitespace-nowrap font-arabic flex items-center gap-1">
                    <span class="material-symbols-rounded text-accent" style="font-size:14px">workspace_premium</span>
                    {{ __('messages.programs.popular') }}
                </div>
                @endif

                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 {{ $plan->iconBgClass }}">
                    <span class="material-symbols-rounded text-[26px] {{ $plan->iconColorClass }}" style="font-variation-settings:'FILL' 1">{{ $plan->iconName }}</span>
                </div>

                @if($showRewardBadge && $plan->id === $rewardPlanId)
                <div class="inline-flex items-center gap-1 bg-accent/15 text-yellow-700 text-[10px] font-black px-2.5 py-1 rounded-full mb-3">
                    <span class="material-symbols-rounded" style="font-size:11px;font-variation-settings:'FILL' 1">card_giftcard</span>
                    {{ __('messages.programs.reward_plan_badge') }}
                </div>
                @endif

                <h3 class="text-xl font-black text-textColor mb-2">{{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">{{ __('messages.plans_data.'.$plan->key.'.desc', [], null) ?: ($plan->desc ?: '') }}</p>

                @php
                    $price3m      = (float)($plan->priceFor($currency, 3)?->price ?? $plan->priceFor('SAR', 3)?->price ?? $plan->price);
                    $price6m      = (float)($plan->priceFor($currency, 6)?->price ?? $plan->priceFor('SAR', 6)?->price ?? $plan->price);
                    $showFallback = !$plan->priceFor($currency, 3) && $currency !== 'SAR';
                    $sPct         = $activeSeason ? (float) $activeSeason->discount_percentage : 0;
                    $sPrice3m     = $activeSeason ? (float) round($price3m * (1 - $sPct / 100)) : $price3m;
                    $sPrice6m     = $activeSeason ? (float) round($price6m * (1 - $sPct / 100)) : $price6m;
                    // Smart format: whole number → 0 dec, fractional → 2 dec
                    $fmtP   = fn(float $p): string => (($r = round($p, 2)) == floor($r))
                                  ? number_format((int)$r, 0) : number_format($r, 2);
                    $sFmt3m = $fmtP($sPrice3m);
                    $sFmt6m = $fmtP($sPrice6m);
                    $cDec   = \App\Services\Web\CurrencyService::META[$currency]['decimals'] ?? 0;
                    $symTxt = \App\Services\Web\CurrencyService::META[$currency]['symbol'] ?? $currency;
                    $fmt3m  = number_format($price3m, $cDec);
                    $fmt6m  = number_format($price6m, $cDec);
                    $pctStr = $sPct > 0 ? rtrim(rtrim(number_format($sPct, 2), '0'), '.') : '';
                @endphp

                @if($activeSeason)
                <div class="inline-flex items-center gap-1.5 bg-red-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full mb-3 font-arabic">
                    <span class="material-symbols-rounded" style="font-size:11px;font-variation-settings:'FILL' 1">local_offer</span>
                    {{ app()->getLocale() === 'ar' ? $activeSeason->name_ar : $activeSeason->name_en }} · {{ app()->getLocale() === 'ar' ? 'خصم' : '' }} {{ $pctStr }}%{{ app()->getLocale() !== 'ar' ? ' off' : '' }}
                </div>
                @endif

                @if($activeSeason)
                {{-- Strikethrough: original price above, aligned with main price --}}
                <div class="flex items-baseline gap-0.5 leading-none mb-1">
                    <span class="font-display font-bold text-gray-400/60 line-through" style="font-size:20px"
                        x-text="months === 3 ? '{{ $fmt3m }}' : '{{ $fmt6m }}'">{{ $fmt3m }}</span>
                    <span class="font-bold text-gray-400/60 line-through" style="font-size:13px">{{ $symTxt }}</span>
                </div>
                @endif

                {{-- Main price row: price + symbol tight together --}}
                <div class="flex items-baseline gap-0.5 leading-none mb-1">
                    <span class="text-5xl font-black font-display text-textColor leading-none"
                        x-text="months === 3 ? '{{ $sFmt3m }}' : '{{ $sFmt6m }}'">{{ $sFmt3m }}</span>
                    <x-web.currency-symbol :currency="$currency" />
                </div>

                {{-- Duration label below price --}}
                <p class="text-sm text-gray-400 font-arabic mb-1"
                    x-text="months === 3 ? '{{ $dur3Label }}' : '{{ $dur6Label }}'">{{ $dur3Label }}</p>

                @if($showFallback)
                <p class="text-[11px] text-amber-600 font-bold font-arabic mb-1">السعر بالريال السعودي</p>
                @endif

                <div class="h-5 mb-4"></div>

                <hr class="border-gray-100 mb-5">

                <ul class="flex flex-col gap-3 mb-8">
                    @foreach($plan->features as $feat)
                        <li class="flex items-center gap-3 text-sm font-semibold {{ $feat->pivot->is_included ? 'text-textColor' : 'text-gray-300' }}">
                            @if($feat->pivot->is_included)
                                <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:18px">check_circle</span>
                            @else
                                <span class="material-symbols-rounded text-gray-300 flex-shrink-0" style="font-size:18px">cancel</span>
                            @endif
                            {{ __('messages.features_data.'.$feat->key, [], null) ?: $feat->name }}
                        </li>
                    @endforeach
                </ul>

                @if($subscription)
                    <button type="button"
                            class="block w-full py-3 rounded-[14px] font-black text-sm text-center font-arabic {{ $plan->buttonClasses }} opacity-75 cursor-not-allowed">
                        {{ __('messages.programs.already_subscribed') }}
                    </button>
                @elseif (auth()->check() && auth()->user()->role === 'coach')
                        <button type="button"
                            class="block w-full py-3 rounded-[14px] font-black text-sm text-center font-arabic {{ $plan->buttonClasses }} opacity-75 cursor-not-allowed">
                            {{ __('messages.programs.coach_cant_subscribe') }}
                        </button>
                @else
                    <a
                        :href="'{{ route('purchase.form', $plan) }}?duration=' + months"
                        class="block w-full py-3 rounded-[14px] font-black text-sm text-center transition-all duration-300 font-arabic {{ $plan->buttonClasses }}"
                    >
                        {{ __('messages.programs.subscribe_now') }}
                    </a>
                @endif
            </div>
            @endforeach
        </div>

        @if($showRewardCard)
        <div class="w-full max-w-[980px] px-6 mx-auto">
            <div class="relative rounded-[28px] overflow-hidden font-arabic shadow-[0_24px_64px_rgba(18,0,46,0.15)]" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

                {{-- Lock / unlocked badge --}}
                @if($isRewardLocked)
                <div class="absolute top-4 {{ $isRtl ? 'left-4' : 'right-4' }} z-20 flex items-center gap-1.5 bg-slate-900/80 text-white text-[11px] font-black px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                    <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 1">lock</span>
                    مقفل
                </div>
                @else
                <div class="absolute top-4 {{ $isRtl ? 'left-4' : 'right-4' }} z-20 flex items-center gap-1.5 bg-accent/20 text-accent text-[11px] font-black px-3 py-1.5 rounded-full backdrop-blur-sm border border-accent/30">
                    <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 1">card_giftcard</span>
                    {{ __('messages.programs.reward_unlocked_badge') }}
                </div>
                @endif

                <div class="flex flex-col lg:flex-row items-stretch">

                    {{-- Content side (right in RTL) --}}
                    <div class="flex-1 bg-white p-8 lg:p-10 {{ $alignStart }}">

                        <div class="inline-flex items-center gap-1.5 bg-accent/15 text-yellow-700 text-[11px] font-black px-3 py-1.5 rounded-full mb-5">
                            <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 1">card_giftcard</span>
                            {{ __('messages.programs.reward_badge') }}
                        </div>

                        <h3 class="text-2xl font-black text-textColor mb-3 leading-snug">{{ __('messages.family_reward.title') }}</h3>

                        <p class="text-gray-400 text-sm leading-relaxed mb-7">
                            {{ __('messages.programs.reward_desc') }}
                        </p>

                        <div class="grid grid-cols-2 gap-x-8 gap-y-3.5">
                            @foreach(__('messages.programs.reward_features') as $feat)
                            <div class="flex items-center gap-2 text-sm font-semibold text-textColor">
                                <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:17px;font-variation-settings:'FILL' 1">check_circle</span>
                                {{ $feat }}
                            </div>
                            @endforeach
                        </div>

                    </div>

                    {{-- Dark panel: discount + CTA (left in RTL) --}}
                    <div class="lg:w-[260px] flex-shrink-0 relative flex flex-col items-center justify-center gap-6 p-8" style="background: linear-gradient(145deg, #12002E 0%, #1f0050 100%);">

                        {{-- Decorative glow spots --}}
                        <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-20 pointer-events-none" style="background:radial-gradient(circle, #EAB308 0%, transparent 70%); transform: translate(30%, -30%);"></div>
                        <div class="absolute bottom-0 left-0 w-36 h-36 rounded-full opacity-10 pointer-events-none" style="background:radial-gradient(circle, #174DAD 0%, transparent 70%); transform: translate(-30%, 30%);"></div>

                        {{-- Discount circle --}}
                        <div class="relative z-10">
                            <div class="w-32 h-32 rounded-full flex flex-col items-center justify-center" style="border: 2px solid rgba(234,179,8,0.35); background: rgba(234,179,8,0.08); box-shadow: 0 0 40px rgba(234,179,8,0.18);">
                                <div class="flex items-end leading-none gap-0.5">
                                    <span class="font-black font-display text-accent" style="font-size:52px; line-height:1">{{ $familyDiscount }}</span>
                                    <span class="font-black text-accent mb-1" style="font-size:22px">%</span>
                                </div>
                                <span class="text-white/40 text-[10px] font-arabic mt-1">{{ __('messages.programs.reward_discount_sub') }}</span>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-accent flex items-center justify-center shadow-lg">
                                <span class="material-symbols-rounded text-darkBg" style="font-size:17px;font-variation-settings:'FILL' 1">card_giftcard</span>
                            </div>
                        </div>

                        <p class="text-white/50 text-xs font-arabic text-center leading-relaxed z-10">
                            {{ __('messages.programs.reward_discount_label') }}
                        </p>

                        @if(!$isRewardLocked)
                        {{-- ✅ مفتوح: مشترك ومعتمد --}}
                        @php $rewardRemaining = max(0, $rewardMaxInvites - $rewardUsedInvites); @endphp
                        <div class="w-full z-10 flex flex-col items-center gap-4">
                            {{-- Remaining invites counter --}}
                            <div class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 flex items-center justify-center gap-3">
                                <span class="font-black text-accent" style="font-size:34px;line-height:1">{{ $rewardRemaining }}</span>
                                <div class="font-arabic {{ $isRtl ? 'text-right' : 'text-left' }}">
                                    <p class="text-white/65 text-xs font-black leading-snug">{{ __('messages.programs.reward_invites_available') }}</p>
                                    <p class="text-white/35 text-[10px] font-bold leading-snug">{{ __('messages.programs.reward_invites_of', ['max' => $rewardMaxInvites]) }}</p>
                                </div>
                            </div>
                            {{-- CTA --}}
                            <a href="{{ route('dashboard') }}#family-reward"
                                class="w-full py-3.5 rounded-2xl font-black text-sm text-center font-arabic text-darkBg flex items-center justify-center gap-2 transition-all duration-300 hover:scale-[1.03]"
                                style="background:linear-gradient(135deg,#EAB308 0%,#F59E0B 100%);box-shadow:0 6px 28px rgba(234,179,8,0.45);">
                                <span class="material-symbols-rounded" style="font-size:15px;font-variation-settings:'FILL' 1">send</span>
                                {{ __('messages.programs.reward_cta') }}
                            </a>
                        </div>

                        @elseif($isPendingRewardSub)
                        {{-- ⏳ طلب قيد المراجعة --}}
                        <div class="w-full z-10 flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(234,179,8,0.08);border:1.5px solid rgba(234,179,8,0.25);">
                                <span class="material-symbols-rounded text-yellow-400" style="font-size:26px;font-variation-settings:'FILL' 1">schedule</span>
                            </div>
                            <p class="text-white/60 text-[11px] font-arabic text-center leading-relaxed">
                                طلبك قيد المراجعة<br>
                                <span class="text-white/40">سيُفتح هذا القسم بعد<br>موافقة الأدمن</span>
                            </p>
                        </div>

                        @else
                        {{-- 🔒 مقفل: لم يشترك بعد --}}
                        <div class="w-full z-10 flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center" style="background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.10);">
                                <span class="material-symbols-rounded text-white/30" style="font-size:26px;font-variation-settings:'FILL' 1">lock</span>
                            </div>
                            @if($rewardPlan)
                            <p class="text-white/50 text-[11px] font-arabic text-center leading-relaxed">
                                اشترك في باقة<br>
                                <span class="text-accent font-black">{{ __('messages.plans_data.' . $rewardPlan->key . '.name') }}</span><br>
                                <span class="text-white/35">لفتح هذه الميزة</span>
                            </p>
                            <a href="{{ route('purchase.form', $rewardPlan->key) }}"
                                class="w-full py-3.5 rounded-2xl font-black text-sm text-center font-arabic text-darkBg flex items-center justify-center gap-1.5 transition-all duration-300 hover:scale-[1.03] whitespace-nowrap"
                                style="background:linear-gradient(135deg,#EAB308 0%,#F59E0B 100%);box-shadow:0 6px 28px rgba(234,179,8,0.35);">
                                <span class="material-symbols-rounded" style="font-size:15px;font-variation-settings:'FILL' 1">workspace_premium</span>
                                اشترك الآن
                            </a>
                            @endif
                        </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>
        @endif

        @endif {{-- end plans empty/full --}}

        {{-- Guarantee --}}
        <p class="flex items-center gap-2 text-gray-400 text-sm font-arabic font-semibold px-3 lg:px-0">
            <span class="material-symbols-rounded text-green-500" style="font-size:20px">verified_user</span>
            {{ __('messages.programs.guarantee') }}
        </p>

    </section>

    {{-- Testimonials Section --}}
    <section id="testimonials" class="w-full bg-[#EFF5FF] py-10 lg:py-28 flex flex-col justify-center items-center gap-14 overflow-hidden">

        @php
            $testimonials = [
                [
                    'name'   => 'Farouq Hashim',
                    'title'  => 'Jan 27, 2026',
                    'avatar' => 'https://ui-avatars.com/api/?name=Farouq+Hashim&background=174DAD&color=fff&size=44',
                    'rating' => 5,
                    'text'   => 'An excellent coach. Very professional and committed, always follows up on the traning and motivates you to improve',
                ],
                [
                    'name'   => 'Mohammad Alhamdan',
                    'title'  => 'Jan 15, 2026',
                    'avatar' => 'https://ui-avatars.com/api/?name=Muhammad+Alhamdan&background=E8FE61&color=000&size=44',
                    'rating' => 5,
                    'text'   => 'The best of the best very informed almost a doctor!!',
                ],
                [
                    'name'   => 'Omar Hassan',
                    'title'  => 'Mar 10, 2026',
                    'avatar' => 'https://ui-avatars.com/api/?name=Omar+Hassan&background=F5C842&color=fff&size=44',
                    'rating' => 5,
                    'text'   => 'out of the 5 years I\'ve been training, coach Ahmed is by far the best coach I\'ve had. he had shown me techniques and mistakes in my training that I never new I had. he is Very understanding and will make a schedule that suits your lifestyle perfectly.',
                ],
                [
                    'name'   => 'نايف ال بسام',
                    'title'  => 'Dec 29, 2025',
                    'avatar' => 'https://ui-avatars.com/api/?name=Naife+Al+Bassam&background=1D9E75&color=fff&size=44',
                    'rating' => 5,
                    'text'   => 'والله كويس المدرب عجبتني الحصة شكرا لكم',
                ],
                [
                    'name'   => 'سامي مؤمنة',
                    'title'  => 'Feb 23, 2026',
                    'avatar' => 'https://ui-avatars.com/api/?name=Sami+Moumena&background=D85A30&color=fff&size=44',
                    'rating' => 5,
                    'text'   => 'الله يعطيك العافية يا كوتش ما قصرت انبسطت جداً معاك في الجلسه وأتعلمت منك أشياء كثير',
                ],
                [
                    'name'   => 'Osama Damanhori',
                    'title'  => 'Feb 22, 2026',
                    'avatar' => 'https://ui-avatars.com/api/?name=Osama+Damanhori&background=7F77DD&color=fff&size=44',
                    'rating' => 5,
                    'text'   => 'مدرب فنان جداً مهتم بانه يطلع أفضل أداء عندك',
                ],
            ];
        @endphp

        {{-- Header --}}
        <div class="flex flex-col items-center gap-3 text-center px-6">
            <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                {{ __('messages.testimonials.badge') }}
            </span>
            <h2 class="font-display text-4xl lg:text-7xl font-semibold text-textColor">
                {{ __('messages.testimonials.title') }} <span class="text-primary">{{ __('messages.testimonials.title_highlight') }}</span>
            </h2>
            <p class="text-gray-500 text-sm lg:text-base max-w-sm font-arabic font-medium leading-relaxed">
                {{ __('messages.testimonials.subtitle') }}
            </p>
        </div>

        {{-- Stats Row --}}
        <div class="flex flex-wrap justify-center gap-4 md:gap-10 font-arabic">

            <div class="flex flex-col items-center gap-1">
                <span
                    class="font-display text-3xl lg:text-5xl font-semibold text-textColor"
                    data-suffix="+"
                    data-count="{{ $settings->get('testimonials_clients', '500') }}" dir="ltr">
                    {{ $settings->get('testimonials_clients', '500') }}+
                </span>
                <span class="text-gray-500 text-xs lg:text-sm font-medium">{{ __('messages.testimonials.happy_clients') }}</span>
            </div>

            <div class="w-px bg-gray-200 self-stretch hidden md:block"></div>

            <div class="flex flex-col items-center gap-1">
                <span
                    class="font-display text-3xl lg:text-5xl font-semibold text-textColor"
                    data-count="{{ $settings->get('testimonials_rating', '5.0') }}"
                    data-decimals="1">
                    {{ $settings->get('testimonials_rating', '5.0') }}
                </span>
                <div class="flex gap-0.5">
                    @for($i = 0; $i < 5; $i++)
                    <span class="material-symbols-rounded text-amber-300 text-sm lg:text-base" style="font-variation-settings:'FILL' 1">star</span>
                    @endfor
                </div>
            </div>

            <div class="w-px bg-gray-200 self-stretch hidden md:block"></div>

            <div class="flex flex-col items-center gap-1">
                <span
                    class="font-display text-3xl lg:text-5xl font-semibold text-textColor"
                    data-count="{{ $settings->get('testimonials_satisfaction', '100') }}"
                    data-suffix="%"
                    data-decimals="0">
                    {{ $settings->get('testimonials_satisfaction', '100') }}%
                </span>
                <span class="text-gray-500 text-xs lg:text-sm font-arabic font-medium">{{ __('messages.testimonials.satisfaction_rate') }}</span>
            </div>

        </div>

        {{-- Testimonials Grid --}}
        <div class="w-full max-w-[1200px] px-6 mx-auto">
            <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">

                @foreach($testimonials as $i => $t)
                <div class="break-inside-avoid rounded-[20px] p-6 border font-arabic {{ $alignStart }} transition-all duration-300 hover:-translate-y-1
                    {{ $i === 1
                        ? 'bg-primary border-primary shadow-[0_20px_48px_rgba(23,77,173,0.18)]'
                        : 'bg-white border-gray-100 hover:shadow-xl hover:border-gray-200' }}">

                    <div class="mb-4">
                        <span class="material-symbols-rounded {{ $i === 1 ? 'text-accent' : 'text-primary' }}" style="font-size:32px;font-variation-settings:'FILL' 1">format_quote</span>
                    </div>

                    <p class="text-sm leading-relaxed mb-6 {{ $i === 1 ? 'text-white/90' : 'text-gray-500' }}">
                        {{ $t['text'] }}
                    </p>

                    <hr class="{{ $i === 1 ? 'border-white/20' : 'border-gray-100' }} mb-5">

                    <div class="flex items-center gap-3">
                        <img
                            src="{{ $t['avatar'] }}"
                            alt="{{ $t['name'] }}"
                            class="w-11 h-11 rounded-full object-cover object-top flex-shrink-0 border-2 {{ $i === 1 ? 'border-accent/50' : 'border-gray-100' }}"
                            loading="lazy"
                        />
                        <div class="flex flex-col flex-1">
                            <span class="font-black text-sm {{ $i === 1 ? 'text-white' : 'text-textColor' }}">
                                {{ $t['name'] }}
                            </span>
                            <span class="text-xs font-medium {{ $i === 1 ? 'text-accent' : 'text-primary' }}">
                                {{ $t['title'] }}
                            </span>
                        </div>
                        <div class="flex gap-0.5 flex-shrink-0">
                            @for($s = 0; $s < $t['rating']; $s++)
                            <span class="material-symbols-rounded text-amber-400" style="font-size:14px;font-variation-settings:'FILL' 1">star</span>
                            @endfor
                            @for($s = $t['rating']; $s < 5; $s++)
                            <span class="material-symbols-rounded text-gray-300" style="font-size:14px;font-variation-settings:'FILL' 1">star</span>
                            @endfor
                        </div>
                    </div>

                </div>
                @endforeach

            </div>
        </div>

        {{-- CTA --}}
        <div class="flex flex-col items-center gap-4 text-center font-arabic">
            <p class="text-gray-500 text-sm font-medium">{{ __('messages.testimonials.ready_cta') }}</p>
            <a href="#programs"
                class="group font-arabic text-textColor bg-accent px-6 py-3 rounded-full text-base font-black flex justify-center items-center gap-2 transition hover:bg-yellow-300">
                {{ __('messages.testimonials.join_now') }}
                @if($isRtl)
                    <svg class="transition-transform duration-300 group-hover:-translate-x-2" width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                    </svg>
                @else
                    <svg class="transition-transform duration-300 group-hover:translate-x-2" width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M28.5103 8.31712V5.68152H27.1423L27.1425 8.31712H28.5103ZM25.7035 11.2832C25.9091 11.2832 26.1024 11.2031 26.2478 11.0577C26.3932 10.9123 26.4732 10.719 26.4732 10.5134L26.473 8.65455L26.4729 8.65189L26.473 8.64924L26.4726 3.48591C26.4726 3.06162 26.1273 2.71639 25.703 2.71639H24.4084L24.409 11.2832L25.7035 11.2832ZM21.6916 13.7746C21.837 13.92 22.0302 14 22.2358 14L22.9701 13.9999C23.3944 13.9998 23.7396 13.6546 23.7396 13.2302L23.7388 2.38397L23.7387 2.38162L23.7388 2.37927L23.7387 0.76964C23.7387 0.564042 23.6586 0.370757 23.5132 0.225405C23.3679 0.0800539 23.1746 0 22.9691 0L22.2349 0.000129431C21.8106 0.000164676 21.4654 0.345474 21.4654 0.769816L21.4657 5.34526L21.4658 5.34718L21.4657 5.34912L21.4661 13.2304C21.4662 13.436 21.5462 13.6293 21.6916 13.7746ZM20.7963 8.31762L20.7962 5.68201L0.000188134 5.68283L0 8.31844L20.7963 8.31762Z" fill="#202020"/>
                    </svg>
                @endif
            </a>
        </div>

    </section>

    {{-- Partners Section --}}
    @php
        $partners     = isset($partners) && $partners->isNotEmpty()
                            ? $partners
                            : \App\Models\Partner::where('is_active', true)->orderBy('sort_order')->get();
        $showPartners = $settings->get('section_partners_visible', '1') === '1';
    @endphp

    @if($showPartners && $partners->isNotEmpty())
        <section class="w-full bg-white py-10 lg:py-28 flex flex-col justify-center items-center gap-12 overflow-hidden">

            {{-- Header --}}
            <div class="flex flex-col items-center gap-3 text-center px-6">
                <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                    {{ __('messages.partners.badge') }}
                </span>
                <h2 class="font-display text-4xl lg:text-7xl font-semibold text-textColor">
                    {{ __('messages.partners.title') }} <span class="text-primary">{{ __('messages.partners.title_highlight') }}</span>
                </h2>
                <p class="text-gray-400 text-sm lg:text-base max-w-sm font-arabic font-medium leading-relaxed mt-6">
                    {{ __('messages.partners.subtitle') }}
                </p>
            </div>

            {{-- Marquee Row --}}
            <div class="relative w-full overflow-hidden" id="partnersMarquee">
                <div class="absolute top-0 right-0 h-full w-32 z-10 pointer-events-none bg-gradient-to-l from-white to-transparent"></div>
                <div class="absolute top-0 left-0 h-full w-32 z-10 pointer-events-none bg-gradient-to-r from-white to-transparent"></div>

                <div
                    id="partnersTrack"
                    class="flex w-max items-center gap-8 {{ $isRtl ? 'animate-marquee-partners' : 'animate-marquee-partners-ltr' }} will-change-transform [backface-visibility:hidden] [transform:translateZ(0)]"
                    dir="ltr"
                >
                    @foreach($partners as $partner)
                        <div
                            class="partner-item flex-shrink-0 flex flex-col items-center justify-center gap-3
                                    w-[160px] h-[90px] rounded-2xl border border-gray-100 bg-gray-50
                                    hover:border-primary/20 hover:bg-[#EFF5FF] hover:shadow-md
                                    transition-all duration-300 px-5 group cursor-default my-4"
                        >
                            <img
                                src="{{ $partner->logoSrc }}"
                                alt="{{ $partner->name }}"
                                class="h-8 w-auto object-contain grayscale group-hover:grayscale-0 opacity-50 group-hover:opacity-100 transition-all duration-300"
                                loading="lazy"
                            />
                            <span class="text-[11px] font-bold text-gray-400 group-hover:text-primary font-arabic transition-colors duration-300">
                                {{ $partner->name }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex flex-wrap justify-center gap-10 font-arabic">

                <div class="flex flex-col items-center gap-1">
                    <span class="font-display text-xl md:text-4xl font-semibold text-textColor"
                        data-count="{{ $settings->get('partners_certified', '20') }}" data-suffix="+">{{ $settings->get('partners_certified', '20') }}+</span>
                    <span class="text-gray-400 text-sm font-medium">{{ __('messages.partners.certified') }}</span>
                </div>

                <div class="w-px bg-gray-200 self-stretch hidden md:block"></div>

                <div class="flex flex-col items-center gap-1">
                    <span class="font-display text-xl md:text-4xl font-semibold text-textColor"
                        data-count="{{ $settings->get('partners_countries', '8') }}" data-suffix="{{ __('messages.partners.countries_suffix') }}">{{ $settings->get('partners_countries', '8') }}{{ __('messages.partners.countries_suffix') }}</span>
                    <span class="text-gray-400 text-sm font-medium">{{ __('messages.partners.coverage') }}</span>
                </div>

                <div class="w-px bg-gray-200 self-stretch hidden md:block"></div>

                <div class="flex flex-col items-center gap-1">
                    <span class="font-display text-xl md:text-4xl font-semibold text-textColor"
                        data-count="{{ $settings->get('partners_years', '3') }}" data-suffix="{{ __('messages.partners.years_suffix') }}">{{ $settings->get('partners_years', '3') }}{{ __('messages.partners.years_suffix') }}</span>
                    <span class="text-gray-400 text-sm font-medium">{{ __('messages.partners.partnership_years') }}</span>
                </div>

            </div>

        </section>
    @endif

    {{-- Contact Section --}}
    <section id="contact" class="w-full bg-lightBg py-16 lg:py-28 px-4 lg:px-20 flex flex-col justify-center items-center gap-14 overflow-hidden">

        {{-- Header --}}
        <div class="flex flex-col items-center gap-3 text-center px-6">
            <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                {{ __('messages.contact.badge') }}
            </span>
            <h2 class="font-display text-4xl lg:text-7xl font-semibold text-textColor">
                {{ __('messages.contact.title') }} <span class="text-primary">{{ __('messages.contact.title_highlight') }}</span>
            </h2>
            <p class="text-gray-500 text-sm lg:text-base max-w-sm font-arabic font-medium leading-relaxed">
                {{ __('messages.contact.subtitle') }}
            </p>
        </div>

        {{-- Quick Contact Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 w-full max-w-[980px]">

            {{-- Phone --}}
            <div class="group relative rounded-[20px] p-6 bg-white border-2 border-white hover:border-accent flex flex-col items-center text-center gap-3 transition-all duration-300 hover:-translate-y-1 font-arabic cursor-pointer">
                <div class="w-14 h-14 rounded-[14px] bg-[#EFF5FF] flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:28px">call</span>
                </div>
                <span class="text-lg font-black text-textColor">{{ __('messages.contact.phone_title') }}</span>
                <span class="text-xs text-gray-400 font-semibold leading-relaxed">{{ __('messages.contact.phone_hours') }}</span>
                <span class="text-sm font-bold text-primary" dir="ltr">{{ $contact['phone'] }}</span>
            </div>

            {{-- WhatsApp --}}
            <a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener noreferrer"
                class="group relative rounded-[20px] p-6 bg-white border-2 border-white hover:border-accent flex flex-col items-center text-center gap-3 transition-all duration-300 hover:-translate-y-1 font-arabic cursor-pointer">
                <div class="w-14 h-14 rounded-[14px] bg-[#EFF5FF] flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:28px">chat</span>
                </div>
                <span class="text-lg font-black text-textColor">{{ __('messages.contact.whatsapp_title') }}</span>
                <span class="text-xs text-gray-400 font-semibold leading-relaxed">{{ __('messages.contact.whatsapp_hours') }}</span>
                <span class="text-sm font-bold text-primary">{{ __('messages.contact.whatsapp_cta') }}</span>
            </a>

            {{-- Email --}}
            <div class="group relative rounded-[20px] p-6 bg-white border-2 border-white hover:border-accent flex flex-col items-center text-center gap-3 transition-all duration-300 hover:-translate-y-1 font-arabic cursor-pointer">
                <div class="w-14 h-14 rounded-[14px] bg-[#EFF5FF] flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:28px">mail</span>
                </div>
                <span class="text-lg font-black text-textColor">{{ __('messages.contact.email_title') }}</span>
                <span class="text-xs text-gray-400 font-semibold leading-relaxed">{{ __('messages.contact.email_hours') }}</span>
                <a href="mailto:{{ $contact['email'] }}" class="text-sm font-bold text-primary hover:underline">{{ $contact['email'] }}</a>
            </div>

        </div>

        {{-- Main Grid: Info + Form --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-[980px]">

            {{-- Info Card --}}
            <div class="rounded-[24px] bg-primary p-10 flex flex-col gap-8 font-arabic">

                <div>
                    <h3 class="text-2xl font-black text-white mb-2">{{ __('messages.contact.info_title') }}</h3>
                    <p class="text-sm leading-relaxed text-white/70">
                        {{ __('messages.contact.info_desc') }}
                    </p>
                </div>

                <div class="flex flex-col gap-5">

                    {{-- Hours --}}
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-[12px] bg-white/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-accent" style="font-size:22px">schedule</span>
                        </div>
                        <div>
                            <p class="text-[11px] text-white/50 font-semibold mb-0.5">{{ __('messages.contact.hours_label') }}</p>
                            <p class="text-sm font-bold text-white">{{ __('messages.contact.hours_value') }}</p>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-[12px] bg-white/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-accent" style="font-size:22px">location_on</span>
                        </div>
                        <div>
                            <p class="text-[11px] text-white/50 font-semibold mb-0.5">{{ __('messages.contact.location_label') }}</p>
                            <p class="text-sm font-bold text-white">{{ $contact['address_' . app()->getLocale()] }}</p>
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-[12px] bg-white/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-accent" style="font-size:22px">call</span>
                        </div>
                        <div>
                            <p class="text-[11px] text-white/50 font-semibold mb-0.5">{{ __('messages.contact.phone_label') }}</p>
                            <p class="text-sm font-bold text-white" dir="ltr">{{ $contact['phone'] }}</p>
                        </div>
                    </div>

                </div>

                {{-- Socials --}}
                <div class="mt-auto">
                    <p class="text-[11px] text-white/40 font-semibold mb-3">{{ __('messages.contact.follow_us') }}</p>
                    <div class="flex gap-2.5">
                        <a href="{{ $settings->get('instagram_url', '#') }}" class="w-10 h-10 rounded-[10px] bg-white/10 border border-white/20 flex items-center justify-center hover:bg-accent/20 transition-colors duration-200">
                            <svg viewBox="0 0 20 20" width="20" height="20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="currentColor" class="text-accent">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier"><g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="Dribbble-Light-Preview" transform="translate(-340.000000, -7439.000000)" fill="currentColor"><g id="icons" transform="translate(56.000000, 160.000000)"><path d="M289.869652,7279.12273 C288.241769,7279.19618 286.830805,7279.5942 285.691486,7280.72871 C284.548187,7281.86918 284.155147,7283.28558 284.081514,7284.89653 C284.035742,7285.90201 283.768077,7293.49818 284.544207,7295.49028 C285.067597,7296.83422 286.098457,7297.86749 287.454694,7298.39256 C288.087538,7298.63872 288.809936,7298.80547 289.869652,7298.85411 C298.730467,7299.25511 302.015089,7299.03674 303.400182,7295.49028 C303.645956,7294.859 303.815113,7294.1374 303.86188,7293.08031 C304.26686,7284.19677 303.796207,7282.27117 302.251908,7280.72871 C301.027016,7279.50685 299.5862,7278.67508 289.869652,7279.12273 M289.951245,7297.06748 C288.981083,7297.0238 288.454707,7296.86201 288.103459,7296.72603 C287.219865,7296.3826 286.556174,7295.72155 286.214876,7294.84312 C285.623823,7293.32944 285.819846,7286.14023 285.872583,7284.97693 C285.924325,7283.83745 286.155174,7282.79624 286.959165,7281.99226 C287.954203,7280.99968 289.239792,7280.51332 297.993144,7280.90837 C299.135448,7280.95998 300.179243,7281.19026 300.985224,7281.99226 C301.980262,7282.98483 302.473801,7284.28014 302.071806,7292.99991 C302.028024,7293.96767 301.865833,7294.49274 301.729513,7294.84312 C300.829003,7297.15085 298.757333,7297.47145 289.951245,7297.06748 M298.089663,7283.68956 C298.089663,7284.34665 298.623998,7284.88065 299.283709,7284.88065 C299.943419,7284.88065 300.47875,7284.34665 300.47875,7283.68956 C300.47875,7283.03248 299.943419,7282.49847 299.283709,7282.49847 C298.623998,7282.49847 298.089663,7283.03248 298.089663,7283.68956 M288.862673,7288.98792 C288.862673,7291.80286 291.150266,7294.08479 293.972194,7294.08479 C296.794123,7294.08479 299.081716,7291.80286 299.081716,7288.98792 C299.081716,7286.17298 296.794123,7283.89205 293.972194,7283.89205 C291.150266,7283.89205 288.862673,7286.17298 288.862673,7288.98792 M290.655732,7288.98792 C290.655732,7287.16159 292.140329,7285.67967 293.972194,7285.67967 C295.80406,7285.67967 297.288657,7287.16159 297.288657,7288.98792 C297.288657,7290.81525 295.80406,7292.29716 293.972194,7292.29716 C292.140329,7292.29716 290.655732,7290.81525 290.655732,7288.98792" id="instagram-[#167]"></path></g></g></g></g>
                            </svg>
                        </a>
                        <a href="{{ $settings->get('tiktok_url', '#') }}" class="w-10 h-10 rounded-[10px] bg-white/10 border border-white/20 flex items-center justify-center hover:bg-accent/20 transition-colors duration-200">
                            <svg class="text-accent" fill="currentColor" viewBox="0 0 32 32" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.656 1.029c1.637-0.025 3.262-0.012 4.886-0.025 0.054 2.031 0.878 3.859 2.189 5.213l-0.002-0.002c1.411 1.271 3.247 2.095 5.271 2.235l0.028 0.002v5.036c-1.912-0.048-3.71-0.489-5.331-1.247l0.082 0.034c-0.784-0.377-1.447-0.764-2.077-1.196l0.052 0.034c-0.012 3.649 0.012 7.298-0.025 10.934-0.103 1.853-0.719 3.543-1.707 4.954l0.020-0.031c-1.652 2.366-4.328 3.919-7.371 4.011l-0.014 0c-0.123 0.006-0.268 0.009-0.414 0.009-1.73 0-3.347-0.482-4.725-1.319l0.040 0.023c-2.508-1.509-4.238-4.091-4.558-7.094l-0.004-0.041c-0.025-0.625-0.037-1.25-0.012-1.862 0.49-4.779 4.494-8.476 9.361-8.476 0.547 0 1.083 0.047 1.604 0.136l-0.056-0.008c0.025 1.849-0.050 3.699-0.050 5.548-0.423-0.153-0.911-0.242-1.42-0.242-1.868 0-3.457 1.194-4.045 2.861l-0.009 0.030c-0.133 0.427-0.21 0.918-0.21 1.426 0 0.206 0.013 0.41 0.037 0.61l-0.002-0.024c0.332 2.046 2.086 3.59 4.201 3.59 0.061 0 0.121-0.001 0.181-0.004l-0.009 0c1.463-0.044 2.733-0.831 3.451-1.994l0.010-0.018c0.267-0.372 0.45-0.822 0.511-1.311l0.001-0.014c0.125-2.237 0.075-4.461 0.087-6.698 0.012-5.036-0.012-10.060 0.025-15.083z"/>
                            </svg>
                        </a>
                        <a href="{{ $settings->get('youtube_url', '#') }}" class="w-10 h-10 rounded-[10px] bg-white/10 border border-white/20 flex items-center justify-center hover:bg-accent/20 transition-colors duration-200">
                            <svg class="text-accent" width="20" height="20" fill="currentColor" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.932 20.459v-8.917l7.839 4.459zM30.368 8.735c-0.354-1.301-1.354-2.307-2.625-2.663l-0.027-0.006c-3.193-0.406-6.886-0.638-10.634-0.638-0.381 0-0.761 0.002-1.14 0.007l0.058-0.001c-0.322-0.004-0.701-0.007-1.082-0.007-3.748 0-7.443 0.232-11.070 0.681l0.434-0.044c-1.297 0.363-2.297 1.368-2.644 2.643l-0.006 0.026c-0.4 2.109-0.628 4.536-0.628 7.016 0 0.088 0 0.176 0.001 0.263l-0-0.014c-0 0.074-0.001 0.162-0.001 0.25 0 2.48 0.229 4.906 0.666 7.259l-0.038-0.244c0.354 1.301 1.354 2.307 2.625 2.663l0.027 0.006c3.193 0.406 6.886 0.638 10.634 0.638 0.38 0 0.76-0.002 1.14-0.007l-0.058 0.001c0.322 0.004 0.702 0.007 1.082 0.007 3.749 0 7.443-0.232 11.070-0.681l-0.434 0.044c1.298-0.362 2.298-1.368 2.646-2.643l0.006-0.026c0.399-2.109 0.627-4.536 0.627-7.015 0-0.088-0-0.176-0.001-0.263l0 0.013c0-0.074 0.001-0.162 0.001-0.25 0-2.48-0.229-4.906-0.666-7.259l0.038 0.244z"/>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

            {{-- Form Card --}}
            <div class="rounded-[24px] bg-white border-2 border-white p-10 flex flex-col gap-5 font-arabic">

                <h3 class="text-xl font-black text-textColor">{{ __('messages.contact.form_title') }}</h3>

                <form action="#" method="POST" class="flex flex-col gap-4">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-bold text-textColor">{{ __('messages.contact.name') }}</label>
                            <input type="text" name="name" placeholder="{{ __('messages.contact.name_placeholder') }}"
                                class="bg-[#F4F7FF] border border-[#e0e8ff] focus:border-primary rounded-xl px-4 py-3 text-sm text-textColor outline-none transition-colors duration-200 font-arabic w-full">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-bold text-textColor">{{ __('messages.contact.phone_num') }}</label>
                            <input type="tel" name="phone" placeholder="{{ $contact['phone_placeholder'] }}"
                                class="bg-[#F4F7FF] border border-[#e0e8ff] focus:border-primary rounded-xl px-4 py-3 text-sm text-textColor outline-none transition-colors duration-200 font-arabic w-full">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[13px] font-bold text-textColor">{{ __('messages.contact.email_label') }}</label>
                        <input type="email" name="email" placeholder="example@email.com"
                            class="bg-[#F4F7FF] border border-[#e0e8ff] focus:border-primary rounded-xl px-4 py-3 text-sm text-textColor outline-none transition-colors duration-200 font-arabic w-full">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[13px] font-bold text-textColor">{{ __('messages.contact.plan_label') }}</label>
                        <select name="plan"
                            class="bg-[#F4F7FF] border border-[#e0e8ff] focus:border-primary rounded-xl px-4 py-3 text-sm text-textColor outline-none transition-colors duration-200 font-arabic w-full">
                            <option value="">{{ __('messages.contact.select_plan') }}</option>
                            <option value="starter">{{ __('messages.contact.plan_options.starter') }}</option>
                            <option value="pro">{{ __('messages.contact.plan_options.pro') }}</option>
                            <option value="elite">{{ __('messages.contact.plan_options.elite') }}</option>
                            <option value="unknown">{{ __('messages.contact.plan_options.unknown') }}</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[13px] font-bold text-textColor">{{ __('messages.contact.message') }}</label>
                        <textarea name="message" rows="4" placeholder="{{ __('messages.contact.message_placeholder') }}"
                            class="bg-[#F4F7FF] border border-[#e0e8ff] focus:border-primary rounded-xl px-4 py-3 text-sm text-textColor outline-none transition-colors duration-200 font-arabic w-full resize-none"></textarea>
                    </div>

                    <button type="submit"
                        class="group font-arabic text-textColor bg-accent px-5 py-3 rounded-full text-sm lg:text-base font-black flex justify-center items-center gap-2 transition hover:bg-yellow-300 w-full mt-2">
                        {{ __('messages.contact.send') }}
                        @if($isRtl)
                            <svg class="transition-transform duration-300 group-hover:-translate-x-2" width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                            </svg>
                        @else
                            <svg class="transition-transform duration-300 group-hover:translate-x-2" width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M28.5103 8.31712V5.68152H27.1423L27.1425 8.31712H28.5103ZM25.7035 11.2832C25.9091 11.2832 26.1024 11.2031 26.2478 11.0577C26.3932 10.9123 26.4732 10.719 26.4732 10.5134L26.473 8.65455L26.4729 8.65189L26.473 8.64924L26.4726 3.48591C26.4726 3.06162 26.1273 2.71639 25.703 2.71639H24.4084L24.409 11.2832L25.7035 11.2832ZM21.6916 13.7746C21.837 13.92 22.0302 14 22.2358 14L22.9701 13.9999C23.3944 13.9998 23.7396 13.6546 23.7396 13.2302L23.7388 2.38397L23.7387 2.38162L23.7388 2.37927L23.7387 0.76964C23.7387 0.564042 23.6586 0.370757 23.5132 0.225405C23.3679 0.0800539 23.1746 0 22.9691 0L22.2349 0.000129431C21.8106 0.000164676 21.4654 0.345474 21.4654 0.769816L21.4657 5.34526L21.4658 5.34718L21.4657 5.34912L21.4661 13.2304C21.4662 13.436 21.5462 13.6293 21.6916 13.7746ZM20.7963 8.31762L20.7962 5.68201L0.000188134 5.68283L0 8.31844L20.7963 8.31762Z" fill="#202020"/>
                            </svg>
                        @endif
                    </button>

                </form>

            </div>

        </div>

    </section>

    <x-web.footer :hidden="false" />

@endsection

@section('script')
    <script>
        window.MFB_CURRENCY = @json($currencyMeta);

        // ─── Register GSAP plugins once ───
        gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

        // ─── Accordion (synchronous — used by onclick) ───
        function toggleAccordion(el) {
            if (el.classList.contains('active')) return;

            document.querySelectorAll('.accordion-item').forEach(item => {
                const isTarget = item === el;
                item.classList.toggle('active', isTarget);
                item.classList.toggle('bg-primary', isTarget);
                item.classList.toggle('bg-white', !isTarget);

                item.querySelector('.acc-title').classList.toggle('text-accent', isTarget);
                item.querySelector('.acc-title').classList.toggle('text-textColor', !isTarget);

                const desc = item.querySelector('.acc-desc');
                desc.classList.toggle('hidden', !isTarget);
                desc.classList.toggle('block', isTarget);
                desc.classList.toggle('text-white', isTarget);
                desc.classList.toggle('text-gray-600', !isTarget);

                item.querySelector('.acc-btn svg').classList.toggle('-rotate-90', isTarget);
            });
        }

        // ─── Critical init: Swipers + Smooth Scroll ───
        document.addEventListener('DOMContentLoaded', function () {

            // Swiper 1
            const swiper1 = new Swiper(".mySwiper", {
                loop: true,
                autoplay: { delay: 6000, disableOnInteraction: false },
                pagination: { el: ".swiper-pagination", dynamicBullets: true, clickable: true },
            });
            swiper1.on('slideChange', () => {
                swiper1.autoplay.start();
                swiper1.allowTouchMove = true;
            });

            // Swiper 2
            new Swiper(".mySwiper2", {
                loop: true,
                autoplay: { delay: 10000, disableOnInteraction: false },
                pagination: { el: ".swiper-pagination", dynamicBullets: true, clickable: true },
            });

            // Swiper 3 — Before/After
            new Swiper('.beforeAfterSwiper', {
                loop: true,
                spaceBetween: 30,
                speed: 700,
                grabCursor: true,
                autoplay: { delay: 7000, disableOnInteraction: false },
                breakpoints: {
                    0:    { slidesPerView: 1, spaceBetween: 16 },
                    768:  { slidesPerView: 2, spaceBetween: 20 },
                    1200: { slidesPerView: 2, spaceBetween: 30 },
                },
                on: {
                    slideChange() {
                        document.querySelectorAll('.ba-dot').forEach((d, i) => {
                            const active = i === this.realIndex;
                            d.classList.toggle('!w-7', active);
                            d.classList.toggle('!h-2.5', active);
                            d.classList.toggle('bg-primary', active);
                            d.classList.toggle('w-2.5', !active);
                            d.classList.toggle('bg-gray-300', !active);
                        });
                    }
                },
            });

            // Smooth Scroll
            document.querySelectorAll('a[href*="#"]').forEach(link => {
                link.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (!href || href === '#') return;
                    const url = new URL(href, window.location.origin);
                    if (url.pathname !== window.location.pathname || !url.hash || url.hash === '#') return;
                    const target = document.querySelector(url.hash);
                    if (!target) return;
                    e.preventDefault();
                    gsap.to(window, { scrollTo: { y: target, offsetY: 80 }, duration: 1.8, ease: 'power3.inOut' });
                });
            });
        });

        // ─── Non-critical: counters + marquee — deferred to idle time ───
        const initIdleWork = () => {

            // GSAP Counters
            document.querySelectorAll('[data-count]').forEach(el => {
                const target = parseFloat(el.dataset.count);
                const dec    = parseInt(el.dataset.decimals || 0);
                const suffix = el.dataset.suffix || '';
                const prefix = el.dataset.prefix || '';

                gsap.from(el, {
                    scrollTrigger: { trigger: el, start: 'top 85%', once: true },
                    y: 40, opacity: 0, duration: 0.8, ease: 'power3.out',
                });
                gsap.to({ val: 0 }, {
                    scrollTrigger: { trigger: el, start: 'top 85%', once: true },
                    val: target, duration: 2, ease: 'power2.out',
                    onUpdate() {
                        const v = this.targets()[0].val;
                        el.textContent = prefix + (dec ? v.toFixed(dec) : Math.floor(v)) + suffix;
                    },
                    onComplete() { el.textContent = prefix + (dec ? target.toFixed(dec) : target) + suffix; },
                });
            });

            // Text Ticker Marquee
            const tickerSection = document.getElementById("tickerSection");
            const tickerTrack   = document.getElementById("tickerTrack");
            if (tickerSection && tickerTrack) {
                const buildTicker = () => {
                    if (!tickerTrack.querySelector(".ticker-item[data-original]")) {
                        tickerTrack.querySelectorAll(".ticker-item").forEach(el => el.setAttribute("data-original", "true"));
                    }
                    tickerTrack.querySelectorAll(".ticker-item[data-clone]").forEach(c => c.remove());

                    const baseItems = Array.from(tickerTrack.querySelectorAll(".ticker-item[data-original]"));
                    if (!baseItems.length) return;

                    while (tickerTrack.scrollWidth < tickerSection.offsetWidth * 2) {
                        baseItems.forEach(item => {
                            const clone = item.cloneNode(true);
                            clone.setAttribute("data-clone", "true");
                            clone.setAttribute("aria-hidden", "true");
                            tickerTrack.appendChild(clone);
                        });
                    }

                    const allItems = tickerTrack.querySelectorAll(".ticker-item");
                    if (allItems.length % baseItems.length !== 0 || tickerTrack.scrollWidth < tickerSection.offsetWidth * 2.5) {
                        baseItems.forEach(item => {
                            const clone = item.cloneNode(true);
                            clone.setAttribute("data-clone", "true");
                            clone.setAttribute("aria-hidden", "true");
                            tickerTrack.appendChild(clone);
                        });
                    }
                };

                buildTicker();
                let tickerResizeTimeout;
                window.addEventListener("resize", () => {
                    clearTimeout(tickerResizeTimeout);
                    tickerResizeTimeout = setTimeout(buildTicker, 150);
                });
            }

            // Partners Marquee
            const marquee = document.getElementById("partnersMarquee");
            const track   = document.getElementById("partnersTrack");
            if (!marquee || !track) return;

            const buildMarquee = () => {
                if (!track.querySelector(".partner-item[data-original='true']")) {
                    Array.from(track.children).forEach(item => item.setAttribute("data-original", "true"));
                }
                Array.from(track.querySelectorAll(".partner-item[data-clone='true']")).forEach(c => c.remove());

                const baseItems = Array.from(track.querySelectorAll(".partner-item[data-original='true']"));
                if (!baseItems.length) return;

                while (track.scrollWidth < marquee.offsetWidth * 2) {
                    baseItems.forEach(item => {
                        const clone = item.cloneNode(true);
                        clone.setAttribute("data-clone", "true");
                        clone.setAttribute("aria-hidden", "true");
                        track.appendChild(clone);
                    });
                }

                const allItems = track.querySelectorAll(".partner-item");
                if (allItems.length % baseItems.length !== 0 || track.scrollWidth < marquee.offsetWidth * 2.5) {
                    baseItems.forEach(item => {
                        const clone = item.cloneNode(true);
                        clone.setAttribute("data-clone", "true");
                        clone.setAttribute("aria-hidden", "true");
                        track.appendChild(clone);
                    });
                }
            };

            buildMarquee();
            let resizeTimeout;
            window.addEventListener("resize", () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(buildMarquee, 150);
            });
        };

        if ('requestIdleCallback' in window) {
            requestIdleCallback(initIdleWork, { timeout: 2000 });
        } else {
            setTimeout(initIdleWork, 200);
        }
    </script>
@endsection

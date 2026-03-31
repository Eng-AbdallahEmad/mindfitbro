@extends('layouts.web.app')

@section('title', 'Home')

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
            /* text-shadow: 0 0 20px rgba(255,255,255,0.15); */
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
    <section class="hero relative w-full flex items-center overflow-hidden px-2 lg:px-28 pt-[7.8rem] lg:pt-[13rem] pb-[5rem] lg:pb-[10rem]">
        <div class="absolute top-0 left-[149px] w-[195px] h-[34rem] z-20 pointer-events-none
                    bg-gradient-to-b from-white/30 to-transparent hidden lg:block"></div>

        {{-- Background Layer --}}
        <div class="hero-bg absolute inset-0 z-0"></div>

        {{-- Subtle animated glow orbs --}}
        <div class="orb orb-1 absolute z-0 hidden lg:block"></div>
        <div class="orb orb-2 absolute z-0 hidden lg:block"></div>

        <div class="absolute transform -translate-x-[66.8rem] translate-y-[70px] max-w-[195px] hidden lg:block">
            <p class="text-white text-xl font-black font-arabic">
                أكثر من <span class="text-[#D4ED57] font-bold">5000</span> قصة نجاح
            </p>
        </div>

        {{-- Content --}}
        <div class="relative z-10 text-right px-4 lg:px-6 w-full">
            <h2 class="text-white font-semibold font-arabic text-xl lg:text-3xl">هـل أنــــت جــاهـز تبقـي أقـوي ....</h2>
            <h1 class="text-white font-black font-display text-[5.7rem] lg:text-[14rem] text-center my-5 lg:my-0">ذهنيـاً وجـسديــاً؟</h1>

            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 hidden lg:block">

                <!-- Image -->
                <img 
                class="w-[520px]"
                src="{{ asset('assets/imgs/hero.png') }}"
                alt="{{ config('app.name', 'laravel') }}"
                >

                <!-- Shadow -->
                <div class="absolute bottom-[65px] left-[43%] -translate-x-1/2 w-[280px] h-[30px] bg-black blur-xl rounded-full"></div>

                <!-- Outline Text -->
                <h2 class="absolute top-[18%] lg:top-[42%] left-1/2 -translate-x-1/2 -translate-y-1/2 text-outline font-black text-center pointer-events-none lg:whitespace-nowrap font-display text-[14rem]">
                    ذهنيـاً وجـسديــاً؟
                </h2>

            </div>

            <h3 class="text-white font-normal font-arabic max-w-lg text-base lg:text-xl z-50 relative">
                سواء كنت تسعى لضبط الهرمونات، تحسين صحة الجهاز الهضمي، أو الوصول لقوام مثالي…
                نقدّم لك تدريبًا احترافيًا قائمًا على العلم، يحقق نتائج حقيقية ومستدامة.
            </h3>

            <div class=" max-w-[18.4rem] mt-8">
                <a href="#programs" class="group font-arabic text-textColor bg-accent px-5 py-3 rounded-full text-sm lg:text-base font-black flex justify-center items-center gap-2 transition">
                    أستكشف برامجنا التدريبيه
                    <svg class="transition-transform duration-300 group-hover:-translate-x-2"
                        width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                    </svg>
                </a>
            </div>
        </div>

    </section>

    {{-- Marquee / Ticker Section --}}
    <section class="bg-lightBg border-y border-gray-200 py-[14px] overflow-hidden w-full group">
            @php
                $items = [
                    'خصومات إبريل 40%',
                    'عروض العيد لسه مخلصتش',
                    'جاهز للجاي...؟',
                    'يلا ننزل الكرش اللي عندك',
                    'مش ناوي تعمل فورمة العيد؟',
                    'العيد خلص بس عروضنا لسه مخلصتش',
                ];
            @endphp
        
        <div class="flex w-max animate-marquee group-hover:[animation-play-state:paused]">
            
            @foreach(array_merge($items, $items) as $item)
                <span class="flex items-center gap-[28px] px-[28px] whitespace-nowrap text-[14px] font-bold text-textColor tracking-[0.01em] font-arabic">
                    
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
            <h2 class="font-display text-4xl lg:text-7xl cursor-default transition-all duration-300 text-textColor hover:text-primary font-semibold mb-4 lg:mb-7">
                أنت مش لوحدك في الرحلة
            </h2>
            <p class="font-arabic font-bold text-textColor text-sm lg:text-xl flex items-center justify-center gap-1 text-center leading-relaxed">
                كل هدف له طريق… ومع مجتمع MindFitBro مش بس هتمشيه، أنت هتكسّره لحد ما تبقى أقوى نسخة من نفسك

                <lottie-player 
                    src="{{ asset('assets/lotties/Muscle.json') }}" 
                    background="transparent" 
                    speed="1" 
                    class="w-[40px] h-[40px] translate-y-[-9px] hidden lg:block"
                    loop 
                    autoplay>
                </lottie-player>
            </p>
        </div>

        <div class="swiper mySwiper w-full lg:w-[70%] h-[200px] lg:h-[500px]">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="video-card relative w-full h-full rounded-2xl overflow-hidden bg-black">

                        <!-- الفيديو -->
                        <video
                            class="video-slide absolute inset-0 w-full h-full object-cover opacity-0 pointer-events-none transition-all duration-700"
                            playsinline
                            preload="metadata">
                            <source src="{{ asset('assets/videos/v1.mp4') }}" type="video/mp4">
                        </video>

                        <!-- الثمبنيل + زر التشغيل -->
                        <div class="video-overlay absolute inset-0 z-10 transition-all duration-700 opacity-100">
                            <img
                                src="{{ asset('assets/imgs/video-thumb-1.jpg') }}"
                                alt="Video Thumbnail"
                                class="w-full h-full object-cover"
                            >

                            <!-- طبقة غامقة خفيفة -->
                            <div class="absolute inset-0 bg-black/30"></div>

                            <!-- زر التشغيل -->
                            <button
                                type="button"
                                class="play-btn absolute inset-0 m-auto w-20 h-20 rounded-full bg-white/90 flex items-center justify-center shadow-xl hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-black mr-[-3px]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5.14v14l11-7-11-7z"/>
                                </svg>
                            </button>
                        </div>

                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="video-card relative w-full h-full rounded-2xl overflow-hidden bg-black">

                        <!-- الفيديو -->
                        <video
                            class="video-slide absolute inset-0 w-full h-full object-cover opacity-0 pointer-events-none transition-all duration-700"
                            playsinline
                            preload="metadata">
                            <source src="{{ asset('assets/videos/v1.mp4') }}" type="video/mp4">
                        </video>

                        <!-- الثمبنيل + زر التشغيل -->
                        <div class="video-overlay absolute inset-0 z-10 transition-all duration-700 opacity-100">
                            <img
                                src="{{ asset('assets/imgs/video-thumb-1.jpg') }}"
                                alt="Video Thumbnail"
                                class="w-full h-full object-cover"
                            >

                            <!-- طبقة غامقة خفيفة -->
                            <div class="absolute inset-0 bg-black/30"></div>

                            <!-- زر التشغيل -->
                            <button
                                type="button"
                                class="play-btn absolute inset-0 m-auto w-20 h-20 rounded-full bg-white/90 flex items-center justify-center shadow-xl hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-black mr-[-3px]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5.14v14l11-7-11-7z"/>
                                </svg>
                            </button>
                        </div>

                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="video-card relative w-full h-full rounded-2xl overflow-hidden bg-black">

                        <!-- الفيديو -->
                        <video
                            class="video-slide absolute inset-0 w-full h-full object-cover opacity-0 pointer-events-none transition-all duration-700"
                            playsinline
                            preload="metadata">
                            <source src="{{ asset('assets/videos/v1.mp4') }}" type="video/mp4">
                        </video>

                        <!-- الثمبنيل + زر التشغيل -->
                        <div class="video-overlay absolute inset-0 z-10 transition-all duration-700 opacity-100">
                            <img
                                src="{{ asset('assets/imgs/video-thumb-1.jpg') }}"
                                alt="Video Thumbnail"
                                class="w-full h-full object-cover"
                            >

                            <!-- طبقة غامقة خفيفة -->
                            <div class="absolute inset-0 bg-black/30"></div>

                            <!-- زر التشغيل -->
                            <button
                                type="button"
                                class="play-btn absolute inset-0 m-auto w-20 h-20 rounded-full bg-white/90 flex items-center justify-center shadow-xl hover:scale-110 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-black mr-[-3px]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5.14v14l11-7-11-7z"/>
                                </svg>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>

        <div class="my-9 lg:my-24 text-center">
            <h2 class="font-display text-4xl lg:text-7xl cursor-default transition-all duration-300 text-textColor hover:text-primary font-semibold mb-4 lg:mb-7">خد بالك</h2>
            <p class="font-arabic font-bold text-textColor text-xl lg:text-5xl">مش كل البرامج شبه بعض… شوف الفرق</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Card 1 -->
            <div class="relative rounded-2xl p-8 pt-12 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 bg-primary hover:bg-primaryDark font-arabic border-4 border-white hover:border-accent">

                <!-- Badge -->
                <div class="absolute top-[-1.55rem] left-1/2 -translate-x-1/2 whitespace-nowrap px-6 py-2.5 rounded-full font-black text-lg text-textColor bg-accent">
                +2,500 <span class="font-bold text-base">عضو نشط</span>
                </div>

                <!-- Icon -->
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mt-4 bg-[rgba(255,255,255,0.12)]">
                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
                </div>

                <h3 class="text-xl font-black mb-3 text-white">مجتمع بيخليك تلتزم</h3>
                <p class="text-sm leading-relaxed text-[rgba(255,255,255,0.7)]">
                    لما تكون وسط ناس عندها نفس هدفك، الاستمرار بيبقى أسهل
                    والنتيجة بتكون أسرع.
                </p>
            </div>

            <!-- Card 2 -->
            <div class="relative rounded-2xl p-8 pt-12 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 bg-primary hover:bg-primaryDark font-arabic border-4 border-white hover:border-accent">

                <!-- Badge -->
                <div class="absolute top-[-1.55rem] left-1/2 -translate-x-1/2 whitespace-nowrap px-6 py-2.5 rounded-full font-black text-lg text-textColor bg-accent">
                +20,000 <span class="font-bold text-base">خطة تدريب</span>
                </div>

                <!-- Icon -->
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mt-4 bg-[rgba(255,255,255,0.12)]">
                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                </div>

                <h3 class="text-xl font-black mb-3 text-white">خطط ذكية مش عشوائية</h3>
                <p class="text-sm leading-relaxed text-[rgba(255,255,255,0.7)]">
                    كل برنامج معمول علشان يخدم هدفك تحديدًا مش مجرد جدول وخلاص.
                </p>
            </div>

            <!-- Card 3 -->
            <div class="relative rounded-2xl p-8 pt-12 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 bg-primary hover:bg-primaryDark font-arabic border-4 border-white hover:border-accent">

                <!-- Badge -->
                <div class="absolute top-[-1.55rem] left-1/2 -translate-x-1/2 whitespace-nowrap px-6 py-2.5 rounded-full font-black text-lg text-textColor bg-accent">
                +10,000 <span class="font-bold text-base">قصة نجاح</span>
                </div>

                <!-- Icon -->
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mt-4 bg-[rgba(255,255,255,0.12)]">
                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                </div>

                <h3 class="text-xl font-black mb-3 text-white">تغيير حقيقي مش مؤقت</h3>
                <p class="text-sm leading-relaxed text-[rgba(255,255,255,0.7)]">
                    هتبني أسلوب حياة يخليك محافظ على تقدمك مش ترجع لنقطة الصفر.
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
                            <span class="transition-all duration-500 group-hover:-translate-x-1.5 group-hover:text-accent font-arabic text-xl">أشتــرك الأن</span>
                            <svg class="w-7 h-7 transition-all duration-500 group-hover:-translate-x-1.5 group-hover:text-accent" viewBox="0 0 29 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z"/>
                            </svg>
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
            <div class="w-full md:w-[750px] flex-shrink-0 mx-auto md:mx-0">

                <div class="swiper mySwiper2 w-full lg:w-[70%] h-[310px] lg:h-[550px] aspect-[9/16] rounded-2xl overflow-hidden shadow-2xl">

                    <div class="swiper-wrapper">

                        <!-- Slide 1 -->
                        <div class="swiper-slide">
                            <div class="relative w-full h-full">

                                <img src="{{ asset('assets/imgs/t1.png') }}"
                                    class="w-full h-full object-cover"
                                    alt="كابتن 1">

                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div class="swiper-slide">
                            <div class="relative w-full h-full">

                                <img src="{{ asset('assets/imgs/t2.png') }}"
                                    class="w-full h-full object-cover"
                                    alt="كابتن 2">
                            </div>
                        </div>

                        <!-- Slide 3 -->
                        <div class="swiper-slide">
                            <div class="relative w-full h-full">

                                <img src="{{ asset('assets/imgs/t3.png') }}"
                                    class="w-full h-full object-cover"
                                    alt="كابتن 3">
                            </div>
                        </div>

                        <!-- Slide 4 -->
                        <div class="swiper-slide">
                            <div class="relative w-full h-full">

                                <img src="{{ asset('assets/imgs/t4.png') }}"
                                    class="w-full h-full object-cover"
                                    alt="كابتن 4">
                            </div>
                        </div>

                        <!-- Slide 5 -->
                        <div class="swiper-slide">
                            <div class="relative w-full h-full">

                                <img src="{{ asset('assets/imgs/t5.png') }}"
                                    class="w-full h-full object-cover"
                                    alt="كابتن 5">
                            </div>
                        </div>

                        <!-- Slide 6 -->
                        <div class="swiper-slide">
                            <div class="relative w-full h-full">

                                <img src="{{ asset('assets/imgs/t6.png') }}"
                                    class="w-full h-full object-cover"
                                    alt="كابتن 6">
                            </div>
                        </div>                        
                    </div>

                    <!-- Pagination -->
                    <div class="swiper-pagination"></div>

                </div>

            </div>
        
            <!-- LEFT: Content -->
            <div class="flex flex-col gap-6">

                <!-- Heading -->
                <div>
                    <h2 class="font-display text-4xl lg:text-7xl cursor-default transition-all duration-300 text-textColor hover:text-primary font-semibold mb-4 lg:mb-7">
                        مش بنقدّم تمارين وبس…
                    </h2>
                    <p class="text-base leading-relaxed text-[#1C1C1C] font-arabic">
                        إحنا بنبني نظام يخليك أقوى ذهنيًا وجسديًا وكل خطة مبنية على العلم ومصممة علشان توصلك لنتيجة حقيقية تدوم
                    </p>
                </div>

                <!-- Accordion -->
                <div class="flex flex-col gap-3">

                    <!-- Item 1 (Active) -->
                    <div class="accordion-item active cursor-pointer overflow-hidden rounded-[14px] bg-primary transition duration-300 hover:shadow-lg font-arabic"
                        onclick="toggleAccordion(this)">
                        <div class="flex items-center justify-between gap-4 p-4">
                            <span class="acc-title text-base lg:text-lg font-bold text-accent transition">
                                التقييم الشامل
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
                            ابدأ بتحليل صحي شامل لتحديد المجالات الرئيسية التي تحتاج إلى تحسين، ووضع خارطة طريق لتحولك الكامل.
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="accordion-item cursor-pointer overflow-hidden rounded-[14px] bg-white transition duration-300 hover:shadow-lg font-arabic" onclick="toggleAccordion(this)">
                        <div class="flex items-center justify-between gap-4 p-4">
                            <span class="acc-title text-base lg:text-lg font-bold text-textColor transition">
                                خطة مخصصة حسب أهدافك
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
                            نصمّم لك برنامجًا متكاملًا يتناسب تمامًا مع أهدافك وأسلوب حياتك وقدراتك الجسدية.
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="accordion-item cursor-pointer overflow-hidden rounded-[14px] bg-white transition duration-300 hover:shadow-lg font-arabic" onclick="toggleAccordion(this)">
                        <div class="flex items-center justify-between gap-4 p-4">
                            <span class="acc-title text-base lg:text-lg font-bold text-textColor transition">
                                تحليل المختبر والعلامات الحيوية
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
                            نحلّل جذور المشكلة عبر فحوصات معملية دقيقة لضمان تدخل صحيح وفعّال.
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="accordion-item cursor-pointer overflow-hidden rounded-[14px] bg-white transition duration-300 hover:shadow-lg font-arabic" onclick="toggleAccordion(this)">
                        <div class="flex items-center justify-between gap-4 p-4">
                            <span class="acc-title text-base lg:text-lg font-bold text-textColor transition">
                                تدريب متخصص مخصص لك
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
                            مدرب شخصي متفرغ لمتابعتك، يحفّزك ويعدّل خطتك باستمرار لضمان أفضل النتائج.
                        </div>
                    </div>

                </div>

                <div class=" max-w-[20.2rem] mt-4 lg:mt-8">
                    <a href="#programs"
                    class="group font-arabic text-textColor bg-accent px-5 py-3 rounded-full text-sm lg:text-base font-black flex justify-center items-center gap-2 transition">
                        احجز مكانك وابدأ التحول الآن
                        <svg class="transition-transform duration-300 group-hover:-translate-x-2"
                            width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </section>

    {{-- Before/After Section --}}
    <section id="before-after" class="w-full bg-white py-16 lg:py-28 flex flex-col justify-center items-center gap-14 overflow-hidden">

        @php
            $beforeAfterClients = [
                ['name'=>'أحمد سامي', 'image'=>asset('assets/imgs/t1.png')],
                ['name'=>'محمد علي', 'image'=>asset('assets/imgs/t2.png')],
                ['name'=>'خالد أمين', 'image'=>asset('assets/imgs/t3.png')],
                ['name'=>'يوسف حسن', 'image'=>asset('assets/imgs/t4.png')],
                ['name'=>'عمر فاروق', 'image'=>asset('assets/imgs/t5.png')],
                ['name'=>'كريم ناصر','image'=>asset('assets/imgs/t6.png')],
                ['name'=>'طارق محمود', 'image'=>asset('assets/imgs/t7.png')],
            ];
        @endphp

        {{-- Header --}}
        <div class="flex flex-col items-center gap-3 text-center px-6">
            <h2 class="font-display text-4xl lg:text-7xl cursor-default transition-all duration-300 text-textColor hover:text-primary font-semibold mb-4 lg:mb-7">
                نتائــج حـــقيقية
            </h2>
            <p class="font-arabic font-bold text-textColor text-base lg:text-xl flex items-center justify-center gap-1 text-center leading-relaxed">
                <lottie-player 
                    src="{{ asset('assets/lotties/Muscle.json') }}" 
                    background="transparent" 
                    speed="1" 
                    class="w-[40px] h-[40px] translate-y-[-9px] scale-x-[-1]"
                    loop 
                    autoplay>
                </lottie-player>
                أبطالنا… نتائجهم تتكلم — MindFitBro

                <lottie-player 
                    src="{{ asset('assets/lotties/Muscle.json') }}" 
                    background="transparent" 
                    speed="1" 
                    class="w-[40px] h-[40px] translate-y-[-9px]"
                    loop 
                    autoplay>
                </lottie-player>
            </p>
        </div>

        {{-- Swiper --}}
        <div class="relative w-full max-w-[1200px] mx-auto px-3 lg:px-6">
            <div class="swiper beforeAfterSwiper w-full py-8">
                <div class="swiper-wrapper items-center">
                    @foreach($beforeAfterClients as $client)
                    <div class="swiper-slide">
                        <div class="rounded-[15px] overflow-hidden">
                            <img
                                src="{{ $client['image'] }}"
                                alt="{{ $client['name'] }}"
                                class="w-full h-full object-cover object-top"
                            />
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Dots --}}
        <div class="flex justify-center items-center gap-2" id="baDots">
            @foreach($beforeAfterClients as $index => $client)
            <button
                data-index="{{ $index }}"
                class="ba-dot rounded-full border-0 cursor-pointer transition-all duration-300 p-0
                    {{ $index === 0 ? 'w-2.5 h-2.5 bg-primary' : 'w-2.5 h-2.5 bg-gray-300' }}"
            ></button>
            @endforeach
        </div>

        {{-- Our Message --}}
        <div class="mt-8 lg:mt-14 px-3 lg:px-0">
            <div class="container px-5 lg:px-52 py-10 lg:py-28 bg-primary shadow-xl rounded-[20px] relative overflow-hidden text-center">

                {{-- Decorative Lines --}}
                <img src="{{ asset('assets/icons/line1.svg') }}" 
                    alt="" 
                    class="absolute bottom-[-35px] right-[-20px]">

                <img src="{{ asset('assets/icons/line2.svg') }}" 
                    alt="" 
                    class="absolute top-[-35px] left-[-20px]">

                {{-- Title --}}
                <h4 class="font-arabic mb-6 text-2xl lg:text-4xl font-black text-accent tracking-wide z-10">
                    رســالــتنا
                </h4>

                {{-- Main Heading --}}
                <h3 class="font-display mb-6 text-[1.72rem] lg:text-[2.6rem] leading-snug text-white z-10">
                    في MindFitBro، مهمتنا مش بس إنك تتمرن… 
                    <span class="text-accent">مهمتنا إنك تتحول</span>
                </h3>

                {{-- Description --}}
                <p class="font-arabic text-base lg:text-lg text-white/90 leading-relaxed max-w-3xl mx-auto z-10">
                    بنبني عقلية منضبطة تقودك للاستمرار 
                    <span class="text-accent font-bold">(Mind)</span>،
                    ونحوّلها لقوة وأداء حقيقي على أرض الواقع 
                    <span class="text-accent font-bold">(Fit)</span>،
                    وسط مجتمع بيدعمك ويدفعك للأمام 
                    <span class="text-accent font-bold">(Bro)</span>.

                    من خلال خبرتنا مع آلاف المشتركين، فهمنا كل التحديات اللي بتواجهك،
                    علشان نقدملك نظام واضح يخليك تلتزم وتوصل لأقوى نسخة منك
                    جسديًا وذهنيًا وتستمر عليها…

                    <span class="block mt-4 text-white font-bold">
                        لأن MindFitBro مش برنامج، ده أسلوب حياة.
                    </span>
                </p>

            </div>
        </div>

    </section>

    {{-- Subscription Section --}}
    <section id="programs" class="w-full bg-lightBg py-10 lg:py-28 flex flex-col justify-center items-center gap-14 overflow-hidden" x-data="{ yearly: false }">

        {{-- Header --}}
        <div class="flex flex-col items-center gap-3 text-center px-6">
            <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                الأسعار والباقات
            </span>
            <h2 class="font-display text-4xl lg:text-7xl font-semibold text-textColor">
                اختار الباقة <span class="text-primary">المناسبة ليك</span>
            </h2>
            <p class="text-[#1c1c1c] text-sm lg:text-base max-w-sm font-arabic font-medium leading-relaxed">
                كل الباقات بتشمل وصول كامل للكوتش وخطة مخصصة
            </p>
        </div>

        {{-- Toggle --}}
        <div class="flex items-center gap-4">
            <span class="font-arabic font-bold text-sm transition-colors duration-300" :class="!yearly ? 'text-primary' : 'text-gray-400'">شهري</span>

            <button @click="yearly = !yearly"
                class="relative w-14 h-7 rounded-full transition-colors duration-300"
                :class="yearly ? 'bg-primary' : 'bg-gray-300'">
                <div class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full shadow transition-transform duration-300"
                    :class="yearly ? 'translate-x-7' : 'translate-x-0'"></div>
            </button>

            <span class="font-arabic font-bold text-sm transition-colors duration-300" :class="yearly ? 'text-primary' : 'text-gray-400'">سنوي</span>
            <span class="bg-accent text-darkBg text-[11px] font-black px-3 py-1 rounded-full font-arabic">وفّر 25%</span>
        </div>

        @php
            $familyOffer = true;

            $sarIcon = '<svg width="14" height="16" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="inline-block flex-shrink-0" style="vertical-align:middle"><path d="M9.36633 2.59339C10.0415 1.83554 10.4564 1.4953 11.2713 1.06514V13.6848L9.36633 14.0784V2.59339Z" fill="currentColor"/><path d="M15.4529 8.93793C15.8478 8.10434 15.8943 7.73386 16 6.87871L1.39805 10.0494C1.05179 10.8207 0.940326 11.2518 0.886964 12.0176L15.4529 8.93793Z" fill="currentColor"/><path d="M15.4529 12.8033C15.8478 11.9697 15.8943 11.5992 16 10.744L9.43602 12.1334C9.38956 12.8975 9.44292 13.2895 9.38956 14.0552L15.4529 12.8033Z" fill="currentColor"/><path d="M15.4529 16.668C15.8478 15.8345 15.8943 15.464 16 14.6088L10.0168 15.9077C9.7148 16.3245 9.52895 17.0191 9.38956 17.92L15.4529 16.668Z" fill="currentColor"/><path d="M5.95136 15.3519C6.53213 14.6341 7.13614 13.7311 7.5543 12.9901L0.51109 14.5167C0.164822 15.2881 0.0533618 15.7192 0 16.4849L5.95136 15.3519Z" fill="currentColor"/><path d="M5.64935 1.52825C6.32448 0.770398 6.73938 0.430158 7.5543 0V13.0364L5.64935 13.4301V1.52825Z" fill="currentColor"/></svg>';

            $plans = [
                [
                    'key'        => 'starter',
                    'name'       => 'ستارتر',
                    'icon'       => 'bolt',
                    'icon_bg'    => 'bg-blue-50',
                    'icon_color' => 'text-primary',
                    'desc'       => 'للمبتدئين اللي عايزين يبدأوا رحلتهم بثقة',
                    'price'      => 299,
                    'popular'    => false,
                    'btn_class'  => 'border-2 border-primary text-primary hover:bg-blue-50',
                    'features'   => [
                        ['text' => 'خطة تدريب أساسية',      'check' => true],
                        ['text' => 'برنامج غذائي مبدئي',     'check' => true],
                        ['text' => 'متابعة أسبوعية',         'check' => true],
                        ['text' => 'جلسات فيديو مع الكوتش', 'check' => false],
                        ['text' => 'تحليل مختبري',           'check' => false],
                    ],
                ],
                [
                    'key'        => 'pro',
                    'name'       => 'برو',
                    'icon'       => 'star',
                    'icon_bg'    => 'bg-primary',
                    'icon_color' => 'text-accent',
                    'desc'       => 'للجادين اللي عايزين نتيجة سريعة ومتابعة مكثفة',
                    'price'      => 599,
                    'popular'    => true,
                    'btn_class'  => 'bg-accent text-darkBg hover:bg-yellow-300',
                    'features'   => [
                        ['text' => 'خطة تدريب متقدمة',   'check' => true],
                        ['text' => 'برنامج غذائي مفصّل',  'check' => true],
                        ['text' => 'متابعة يومية',        'check' => true],
                        ['text' => 'جلستين فيديو شهرياً', 'check' => true],
                        ['text' => 'تحليل مختبري',        'check' => false],
                    ],
                ],
                [
                    'key'        => 'elite',
                    'name'       => 'إيليت',
                    'icon'       => 'emoji_events',
                    'icon_bg'    => 'bg-amber-50',
                    'icon_color' => 'text-amber-500',
                    'desc'       => 'تجربة VIP كاملة مع تحليلات متقدمة وكوتش خاص',
                    'price'      => 999,
                    'popular'    => false,
                    'btn_class'  => 'bg-primary text-white hover:bg-blue-800',
                    'features'   => [
                        ['text' => 'كل مميزات برو',             'check' => true],
                        ['text' => 'كوتش خاص على مدار الساعة', 'check' => true],
                        ['text' => 'جلسات فيديو أسبوعية',      'check' => true],
                        ['text' => 'تحليل مختبري شامل',         'check' => true],
                        ['text' => 'تقرير تقدم شهري مفصّل',    'check' => true],
                    ],
                ],
            ];
        @endphp

        {{-- Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-[980px] w-full px-6">
            @foreach($plans as $plan)
            <div class="relative rounded-[24px] p-8 border font-arabic text-right transition-all duration-300 hover:-translate-y-2
                {{ $plan['popular']
                    ? 'border-[2.5px] border-primary bg-[#F0F5FF] shadow-[0_20px_48px_rgba(23,77,173,0.15)]'
                    : 'border-gray-200 bg-white hover:shadow-xl' }}">

                @if($plan['popular'])
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-white text-xs font-black px-5 py-1.5 rounded-full whitespace-nowrap font-arabic flex items-center gap-1">
                    <span class="material-symbols-rounded text-accent" style="font-size:14px">workspace_premium</span>
                    الأكثر طلباً
                </div>
                @endif

                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5 {{ $plan['icon_bg'] }}">
                    <span class="material-symbols-rounded text-[26px] {{ $plan['icon_color'] }}">{{ $plan['icon'] }}</span>
                </div>

                <h3 class="text-xl font-black text-textColor mb-2">{{ $plan['name'] }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">{{ $plan['desc'] }}</p>

                <div class="flex items-baseline gap-1.5 mb-1 text-gray-400">
                    {!! $sarIcon !!}
                    <span class="text-5xl font-black font-display text-textColor leading-none"
                        x-text="yearly ? Math.round({{ $plan['price'] }} * 0.75) : {{ $plan['price'] }}">
                        {{ $plan['price'] }}
                    </span>
                    <span class="text-sm" x-text="yearly ? '/شهر (سنوي)' : '/شهر'">/شهر</span>
                </div>

                <div class="h-5 mb-4">
                    <div x-show="yearly" x-transition class="flex items-center gap-1 text-xs text-gray-300 font-arabic">
                        السعر الأصلي: {!! $sarIcon !!}
                        <span>{{ $plan['price'] }}/شهر</span>
                    </div>
                </div>

                <hr class="border-gray-100 mb-5">

                <ul class="flex flex-col gap-3 mb-8">
                    @foreach($plan['features'] as $feat)
                    <li class="flex items-center gap-3 text-sm font-semibold {{ $feat['check'] ? 'text-textColor' : 'text-gray-300' }}">
                        @if($feat['check'])
                        <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:18px">check_circle</span>
                        @else
                        <span class="material-symbols-rounded text-gray-300 flex-shrink-0" style="font-size:18px">cancel</span>
                        @endif
                        {{ $feat['text'] }}
                    </li>
                    @endforeach
                </ul>

                <a href="#" class="block w-full py-3 rounded-[14px] font-black text-sm text-center transition-all duration-300 font-arabic {{ $plan['btn_class'] }}">
                    اشترك دلوقتي
                </a>

            </div>
            @endforeach
        </div>

        @if($familyOffer)
            <div class="w-full max-w-[980px] px-6 mx-auto">
                <div class="relative rounded-[24px] border-2 border-dashed border-accent bg-gradient-to-l from-[#fffde8] to-[#f0f5ff] overflow-hidden font-arabic" dir="rtl">

                    {{-- شريط العرض المحدود --}}
                    <div class="w-full bg-accent flex items-center justify-center gap-2 py-2 px-4">
                        <span class="material-symbols-rounded text-darkBg" style="font-size:16px;font-variation-settings:'FILL' 1">timer</span>
                        <p class="text-darkBg text-xs font-black tracking-widest">⚡ عرض محدود — ينتهي قريباً</p>
                        <span class="material-symbols-rounded text-darkBg" style="font-size:16px;font-variation-settings:'FILL' 1">timer</span>
                    </div>

                    <div class="flex flex-col lg:flex-row items-center gap-8 p-8">

                        {{-- Right: Info --}}
                        <div class="flex-1 text-right">

                            {{-- Icon + Name --}}
                            <div class="flex items-center gap-3 mb-4 flex-row justify-start">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-accent/20">
                                    <span class="material-symbols-rounded text-[26px] text-amber-600" style="font-variation-settings:'FILL' 1">family_restroom</span>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-textColor leading-none">باقة العائلة</h3>
                                    <span class="text-xs font-bold text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">لـ 4 أفراد</span>
                                </div>
                            </div>

                            <p class="text-gray-500 text-sm leading-relaxed mb-6 max-w-md">
                                باقة مخصصة للعائلة كلها — كل فرد بياخد خطة تدريب وتغذية مخصصة ليه، مع متابعة يومية وجلسات فيديو شهرية لكل الأفراد.
                            </p>

                            {{-- Features Grid --}}
                            <div class="grid grid-cols-2 gap-x-6 gap-y-2.5 mb-6">
                                @php
                                $familyFeatures = [
                                    ['text' => 'خطط تدريب لـ 4 أفراد',       'check' => true],
                                    ['text' => 'برامج غذائية مخصصة',          'check' => true],
                                    ['text' => 'متابعة يومية للجميع',         'check' => true],
                                    ['text' => 'جلسات فيديو أسبوعية',         'check' => true],
                                    ['text' => 'تحليل مختبري لكل الأفراد',    'check' => true],
                                    ['text' => 'تقارير تقدم شهرية',           'check' => true],
                                ];
                                @endphp

                                @foreach($familyFeatures as $feat)
                                <div class="flex items-center gap-2 text-sm font-semibold text-textColor">
                                    <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                                    {{ $feat['text'] }}
                                </div>
                                @endforeach
                            </div>

                        </div>

                        {{-- Divider --}}
                        <div class="hidden lg:block w-px self-stretch bg-accent/30 mx-2"></div>

                        {{-- Left: Price + CTA --}}
                        <div class="flex flex-col items-center gap-4 min-w-[220px]">

                            {{-- Saving Badge --}}
                            <span class="bg-green-100 text-green-700 text-xs font-black px-4 py-1.5 rounded-full flex items-center gap-1">
                                <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1">savings</span>
                                وفّر 40% مقارنةً بالباقات الفردية
                            </span>

                            {{-- Price --}}
                            <div class="text-center">
                                <p class="text-xs text-gray-400 font-arabic mb-1">السعر بدل</p>
                                <p class="text-gray-300 text-lg font-bold line-through font-display mb-1">
                                    {!! $sarIcon !!} 2,396
                                </p>
                                <div class="flex items-baseline justify-center gap-1.5 text-textColor">
                                    {!! $sarIcon !!}
                                    <span class="text-6xl font-black font-display leading-none"
                                        x-text="yearly ? Math.round(1399 * 0.75) : 1399">
                                        1399
                                    </span>
                                </div>
                                <span class="text-sm text-gray-400 font-arabic"
                                    x-text="yearly ? '/شهر (سنوي)' : '/شهر'">/شهر</span>
                            </div>

                            {{-- Original yearly price --}}
                            <div class="h-4">
                                <p class="text-xs text-gray-300 font-arabic flex items-center gap-1"
                                    x-show="yearly" x-transition>
                                    السعر الأصلي: {!! $sarIcon !!} 1,399/شهر
                                </p>
                            </div>

                            {{-- CTA --}}
                            <a href="#"
                                class="w-full py-3.5 rounded-[14px] font-black text-sm text-center transition-all duration-300 font-arabic bg-accent text-darkBg hover:bg-yellow-300 flex items-center justify-center gap-2">
                                <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">family_restroom</span>
                                اشترك في باقة العائلة
                            </a>

                            <p class="text-gray-400 text-[11px] font-arabic text-center">
                                العرض ساري لفترة محدودة فقط
                            </p>

                        </div>

                    </div>

                </div>
            </div>
        @endif

        {{-- Guarantee --}}
        <p class="flex items-center gap-2 text-gray-400 text-sm font-arabic font-semibold px-3 lg:px-0">
            <span class="material-symbols-rounded text-green-500" style="font-size:20px">verified_user</span>
            ضمان استرداد كامل خلال 7 أيام — بدون أي شروط
        </p>

    </section>

    {{-- Testimonials Section --}}
    <section id="testimonials" class="w-full bg-[#EFF5FF] py-10 lg:py-28 flex flex-col justify-center items-center gap-14 overflow-hidden">

        @php
            $testimonials = [
                [
                    'name'   => 'أحمد سامي',
                    'title'  => 'خسر 18 كجم في 3 أشهر',
                    'avatar' => asset('assets/imgs/t1.png'),
                    'rating' => 5,
                    'text'   => 'والله ما توقعت النتيجة تكون بالشكل ده بسرعة، الكوتش متابعني يومياً والخطة كانت مضبوطة على جسمي تماماً. حياتي اتغيرت فعلاً.',
                ],
                [
                    'name'   => 'محمد علي',
                    'title'  => 'خسر 24 كجم في 6 أشهر',
                    'avatar' => asset('assets/imgs/t2.png'),
                    'rating' => 5,
                    'text'   => 'جربت برامج كتير قبل كده بس MindFitBro هو الأول اللي حسيت فيه بفرق حقيقي. النظام الغذائي والتدريب مع بعض عملوا معجزة.',
                ],
                [
                    'name'   => 'خالد أمين',
                    'title'  => 'خسر 15 كجم في 4 أشهر',
                    'avatar' => asset('assets/imgs/t3.png'),
                    'rating' => 5,
                    'text'   => 'أكتر حاجة عجبتني هي المتابعة اليومية، مش بس خطة وخلاص. الكوتش كان معايا في كل خطوة وده خلاني أكمل من غير ما أفقد الحماس.',
                ],
                [
                    'name'   => 'يوسف حسن',
                    'title'  => 'خسر 20 كجم في 5 أشهر',
                    'avatar' => asset('assets/imgs/t4.png'),
                    'rating' => 5,
                    'text'   => 'البرنامج مش بس غيّر جسمي، غيّر تفكيري كمان. بقيت أفهم جسمي أكتر وأعرف أتعامل مع الأكل والتمرين بشكل صح.',
                ],
                [
                    'name'   => 'عمر فاروق',
                    'title'  => 'خسر 12 كجم في 3 أشهر',
                    'avatar' => asset('assets/imgs/t5.png'),
                    'rating' => 4,
                    'text'   => 'نتائج ممتازة في وقت قصير، التحليل المختبري في أول البرنامج فرّق معايا كتير لأنهم عرفوا المشكلة الحقيقية ووضعوا الحل الصح.',
                ],
                [
                    'name'   => 'كريم ناصر',
                    'title'  => 'خسر 30 كجم في 8 أشهر',
                    'avatar' => asset('assets/imgs/t6.png'),
                    'rating' => 5,
                    'text'   => 'ثمانية أشهر غيّروا حياتي للأبد. الفريق كان محترف جداً والنتائج تتكلم عن نفسها. أنصح كل واحد عنده هدف يجرب MindFitBro.',
                ],
            ];
        @endphp

        {{-- Header --}}
        <div class="flex flex-col items-center gap-3 text-center px-6">
            <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                آراء عملاؤنا
            </span>
            <h2 class="font-display text-4xl lg:text-7xl font-semibold text-textColor">
                بيقولوا إيه <span class="text-primary">عننا؟</span>
            </h2>
            <p class="text-gray-500 text-sm lg:text-base max-w-sm font-arabic font-medium leading-relaxed">
                مش كلامنا — ده كلام ناس زيك غيّرت حياتها معانا
            </p>
        </div>

        {{-- Stats Row --}}
        <div class="flex flex-wrap justify-center gap-10 font-arabic">

            <div class="flex flex-col items-center gap-1">
                <span
                    class="font-display text-3xl lg:text-5xl font-semibold text-textColor"
                    data-count="5000">
                    +5000
                </span>
                <span class="text-gray-500 text-xs lg:text-sm font-medium">عميل راضي</span>
            </div>

            <div class="w-px bg-gray-200 self-stretch hidden md:block"></div>

            <div class="flex flex-col items-center gap-1">
                <span
                    class="font-display text-3xl lg:text-5xl font-semibold text-textColor"
                    data-count="4.9"
                    data-decimals="1">
                    4.9
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
                    data-count="98"
                    data-decimals="0">
                    98%
                </span>
                <span class="text-gray-500 text-xs lg:text-sm font-arabic font-medium">نسبة الرضا</span>
            </div>

        </div>

        {{-- Testimonials Grid --}}
        <div class="w-full max-w-[1200px] px-6 mx-auto">
            <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">

                @foreach($testimonials as $i => $t)
                <div class="break-inside-avoid rounded-[20px] p-6 border font-arabic text-right transition-all duration-300 hover:-translate-y-1
                    {{ $i === 1
                        ? 'bg-primary border-primary shadow-[0_20px_48px_rgba(23,77,173,0.18)]'
                        : 'bg-white border-gray-100 hover:shadow-xl hover:border-gray-200' }}">

                    {{-- Quote Icon --}}
                    <div class="mb-4">
                        <span class="material-symbols-rounded {{ $i === 1 ? 'text-accent' : 'text-primary' }}" style="font-size:32px;font-variation-settings:'FILL' 1">format_quote</span>
                    </div>

                    {{-- Text --}}
                    <p class="text-sm leading-relaxed mb-6 {{ $i === 1 ? 'text-white/90' : 'text-gray-500' }}">
                        {{ $t['text'] }}
                    </p>

                    {{-- Divider --}}
                    <hr class="{{ $i === 1 ? 'border-white/20' : 'border-gray-100' }} mb-5">

                    {{-- Author --}}
                    <div class="flex items-center gap-3">

                        {{-- Avatar --}}
                        <img
                            src="{{ $t['avatar'] }}"
                            alt="{{ $t['name'] }}"
                            class="w-11 h-11 rounded-full object-cover object-top flex-shrink-0 border-2 {{ $i === 1 ? 'border-accent/50' : 'border-gray-100' }}"
                        />

                        {{-- Info --}}
                        <div class="flex flex-col flex-1">
                            <span class="font-black text-sm {{ $i === 1 ? 'text-white' : 'text-textColor' }}">
                                {{ $t['name'] }}
                            </span>
                            <span class="text-xs font-medium {{ $i === 1 ? 'text-accent' : 'text-primary' }}">
                                {{ $t['title'] }}
                            </span>
                        </div>

                        {{-- Stars --}}
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
            <p class="text-gray-500 text-sm font-medium">جاهز تبدأ رحلتك؟</p>
            <a href="#programs"
                class="group font-arabic text-textColor bg-accent px-6 py-3 rounded-full text-base font-black flex justify-center items-center gap-2 transition hover:bg-yellow-300">
                انضم لآلاف المشتركين دلوقتي
                <svg class="transition-transform duration-300 group-hover:-translate-x-2"
                    width="22" height="12" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                </svg>
            </a>
        </div>

    </section>

    {{-- ═══════════════════════════════════════════
     Partners Section
     للتحكم: غيّر $showPartners = true / false
    ═══════════════════════════════════════════ --}}

    @php $showPartners = true; @endphp

    @if($showPartners)
        <section class="w-full bg-white py-10 lg:py-28 flex flex-col justify-center items-center gap-12 overflow-hidden">

            @php
                $partners = [
                    [
                        'name' => 'برو تيمز',
                        'logo' => asset('assets/logo/pro2.png'),
                    ],
                    [
                        'name' => 'برو تيمز',
                        'logo' => asset('assets/logo/pro3.png'),
                    ],
                ];
            @endphp

            {{-- Header --}}
            <div class="flex flex-col items-center gap-3 text-center px-6">
                <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                    شركاؤنا
                </span>
                <h2 class="font-display text-4xl lg:text-7xl font-semibold text-textColor">
                    بنتشارك مع <span class="text-primary">الأفضل</span>
                </h2>
                <p class="text-gray-400 text-sm lg:text-base max-w-sm font-arabic font-medium leading-relaxed mt-6">
                    شركاؤنا من أكبر العلامات التجارية في عالم اللياقة والصحة
                </p>
            </div>

            {{-- Marquee Row --}}
            <div class="relative w-full overflow-hidden" id="partnersMarquee">
                <div class="absolute top-0 right-0 h-full w-32 z-10 pointer-events-none bg-gradient-to-l from-white to-transparent"></div>
                <div class="absolute top-0 left-0 h-full w-32 z-10 pointer-events-none bg-gradient-to-r from-white to-transparent"></div>

                <div
                    id="partnersTrack"
                    class="flex w-max items-center gap-8 animate-marquee-partners will-change-transform [backface-visibility:hidden] [transform:translateZ(0)]"
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
                                src="{{ $partner['logo'] }}"
                                alt="{{ $partner['name'] }}"
                                class="h-8 w-auto object-contain grayscale group-hover:grayscale-0 opacity-50 group-hover:opacity-100 transition-all duration-300"
                            />
                            <span class="text-[11px] font-bold text-gray-400 group-hover:text-primary font-arabic transition-colors duration-300">
                                {{ $partner['name'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex flex-wrap justify-center gap-10 font-arabic">

                <div class="flex flex-col items-center gap-1">
                    <span class="font-display text-4xl font-semibold text-textColor"
                        data-count="20" data-suffix="+">20+</span>
                    <span class="text-gray-400 text-sm font-medium">شريك معتمد</span>
                </div>

                <div class="w-px bg-gray-200 self-stretch hidden md:block"></div>

                <div class="flex flex-col items-center gap-1">
                    <span class="font-display text-4xl font-semibold text-textColor"
                        data-count="8" data-suffix=" دول">8 دول</span>
                    <span class="text-gray-400 text-sm font-medium">نطاق التغطية</span>
                </div>

                <div class="w-px bg-gray-200 self-stretch hidden md:block"></div>

                <div class="flex flex-col items-center gap-1">
                    <span class="font-display text-4xl font-semibold text-textColor"
                        data-count="3" data-suffix=" سنوات">3 سنوات</span>
                    <span class="text-gray-400 text-sm font-medium">من الشراكة</span>
                </div>

            </div>

        </section>
    @endif

    {{-- Contact Section --}}
    <section id="contact" class="w-full bg-lightBg py-16 lg:py-28 px-4 lg:px-20 flex flex-col justify-center items-center gap-14 overflow-hidden">

        {{-- Header --}}
        <div class="flex flex-col items-center gap-3 text-center px-6">
            <span class="bg-accent text-darkBg text-[11px] font-black tracking-widest px-5 py-1.5 rounded-full font-arabic">
                تواصل معنا
            </span>
            <h2 class="font-display text-4xl lg:text-7xl font-semibold text-textColor">
                خليك على تواصل <span class="text-primary">معانا</span>
            </h2>
            <p class="text-gray-500 text-sm lg:text-base max-w-sm font-arabic font-medium leading-relaxed">
                فريقنا جاهز يرد عليك في أقرب وقت ويساعدك تبدأ رحلتك
            </p>
        </div>

        {{-- Quick Contact Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 w-full max-w-[980px]">

            {{-- Phone --}}
            <div class="group relative rounded-[20px] p-6 bg-white border-2 border-white hover:border-accent flex flex-col items-center text-center gap-3 transition-all duration-300 hover:-translate-y-1 font-arabic cursor-pointer">
                <div class="w-14 h-14 rounded-[14px] bg-[#EFF5FF] flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:28px">call</span>
                </div>
                <span class="text-lg font-black text-textColor">اتصل بينا</span>
                <span class="text-xs text-gray-400 font-semibold leading-relaxed">متاح من 9 صبح لـ 10 بالليل</span>
                <span class="text-sm font-bold text-primary">+966 5x xxx xxxx</span>
            </div>

            {{-- WhatsApp --}}
            <div class="group relative rounded-[20px] p-6 bg-white border-2 border-white hover:border-accent flex flex-col items-center text-center gap-3 transition-all duration-300 hover:-translate-y-1 font-arabic cursor-pointer">
                <div class="w-14 h-14 rounded-[14px] bg-[#EFF5FF] flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:28px">chat</span>
                </div>
                <span class="text-lg font-black text-textColor">واتساب</span>
                <span class="text-xs text-gray-400 font-semibold leading-relaxed">رد فوري خلال دقائق</span>
                <span class="text-sm font-bold text-primary">ابدأ محادثة دلوقتي</span>
            </div>

            {{-- Email --}}
            <div class="group relative rounded-[20px] p-6 bg-white border-2 border-white hover:border-accent flex flex-col items-center text-center gap-3 transition-all duration-300 hover:-translate-y-1 font-arabic cursor-pointer">
                <div class="w-14 h-14 rounded-[14px] bg-[#EFF5FF] flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:28px">mail</span>
                </div>
                <span class="text-lg font-black text-textColor">البريد الإلكتروني</span>
                <span class="text-xs text-gray-400 font-semibold leading-relaxed">بنرد خلال 24 ساعة</span>
                <span class="text-sm font-bold text-primary">hello@mindfitbro.com</span>
            </div>

        </div>

        {{-- Main Grid: Info + Form --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-[980px]">

            {{-- Info Card --}}
            <div class="rounded-[24px] bg-primary p-10 flex flex-col gap-8 font-arabic">

                <div>
                    <h3 class="text-2xl font-black text-white mb-2">إيه اللي بتستنى؟</h3>
                    <p class="text-sm leading-relaxed text-white/70">
                        فريق MindFitBro موجود علشان يجاوب على كل أسئلتك ويساعدك تختار الباقة المناسبة ليك
                    </p>
                </div>

                <div class="flex flex-col gap-5">

                    {{-- Hours --}}
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-[12px] bg-white/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-accent" style="font-size:22px">schedule</span>
                        </div>
                        <div>
                            <p class="text-[11px] text-white/50 font-semibold mb-0.5">ساعات العمل</p>
                            <p class="text-sm font-bold text-white">السبت – الخميس، 9ص – 10م</p>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-[12px] bg-white/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-accent" style="font-size:22px">location_on</span>
                        </div>
                        <div>
                            <p class="text-[11px] text-white/50 font-semibold mb-0.5">موقعنا</p>
                            <p class="text-sm font-bold text-white">الرياض، المملكة العربية السعودية</p>
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-[12px] bg-white/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded text-accent" style="font-size:22px">call</span>
                        </div>
                        <div>
                            <p class="text-[11px] text-white/50 font-semibold mb-0.5">رقم التواصل</p>
                            <p class="text-sm font-bold text-white">+966 5x xxx xxxx</p>
                        </div>
                    </div>

                </div>

                {{-- Socials --}}
                <div class="mt-auto">
                    <p class="text-[11px] text-white/40 font-semibold mb-3">تابعنا على</p>
                    <div class="flex gap-2.5">
                        <a href="#" class="w-10 h-10 rounded-[10px] bg-white/10 border border-white/20 flex items-center justify-center hover:bg-accent/20 transition-colors duration-200">
                            <svg viewBox="0 0 20 20" width="20" height="20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="currentColor" class="text-accent">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> 
                                        <g id="Dribbble-Light-Preview" transform="translate(-340.000000, -7439.000000)" fill="currentColor">
                                            <g id="icons" transform="translate(56.000000, 160.000000)">
                                                <path d="M289.869652,7279.12273 C288.241769,7279.19618 286.830805,7279.5942 285.691486,7280.72871 C284.548187,7281.86918 284.155147,7283.28558 284.081514,7284.89653 C284.035742,7285.90201 283.768077,7293.49818 284.544207,7295.49028 C285.067597,7296.83422 286.098457,7297.86749 287.454694,7298.39256 C288.087538,7298.63872 288.809936,7298.80547 289.869652,7298.85411 C298.730467,7299.25511 302.015089,7299.03674 303.400182,7295.49028 C303.645956,7294.859 303.815113,7294.1374 303.86188,7293.08031 C304.26686,7284.19677 303.796207,7282.27117 302.251908,7280.72871 C301.027016,7279.50685 299.5862,7278.67508 289.869652,7279.12273 M289.951245,7297.06748 C288.981083,7297.0238 288.454707,7296.86201 288.103459,7296.72603 C287.219865,7296.3826 286.556174,7295.72155 286.214876,7294.84312 C285.623823,7293.32944 285.819846,7286.14023 285.872583,7284.97693 C285.924325,7283.83745 286.155174,7282.79624 286.959165,7281.99226 C287.954203,7280.99968 289.239792,7280.51332 297.993144,7280.90837 C299.135448,7280.95998 300.179243,7281.19026 300.985224,7281.99226 C301.980262,7282.98483 302.473801,7284.28014 302.071806,7292.99991 C302.028024,7293.96767 301.865833,7294.49274 301.729513,7294.84312 C300.829003,7297.15085 298.757333,7297.47145 289.951245,7297.06748 M298.089663,7283.68956 C298.089663,7284.34665 298.623998,7284.88065 299.283709,7284.88065 C299.943419,7284.88065 300.47875,7284.34665 300.47875,7283.68956 C300.47875,7283.03248 299.943419,7282.49847 299.283709,7282.49847 C298.623998,7282.49847 298.089663,7283.03248 298.089663,7283.68956 M288.862673,7288.98792 C288.862673,7291.80286 291.150266,7294.08479 293.972194,7294.08479 C296.794123,7294.08479 299.081716,7291.80286 299.081716,7288.98792 C299.081716,7286.17298 296.794123,7283.89205 293.972194,7283.89205 C291.150266,7283.89205 288.862673,7286.17298 288.862673,7288.98792 M290.655732,7288.98792 C290.655732,7287.16159 292.140329,7285.67967 293.972194,7285.67967 C295.80406,7285.67967 297.288657,7287.16159 297.288657,7288.98792 C297.288657,7290.81525 295.80406,7292.29716 293.972194,7292.29716 C292.140329,7292.29716 290.655732,7290.81525 290.655732,7288.98792" id="instagram-[#167]">
                                                    </path>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-[10px] bg-white/10 border border-white/20 flex items-center justify-center hover:bg-accent/20 transition-colors duration-200">
                            <svg class="text-accent" fill="currentColor" viewBox="0 0 32 32" width="20" height="20" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path d="M16.656 1.029c1.637-0.025 3.262-0.012 4.886-0.025 0.054 2.031 0.878 3.859 2.189 5.213l-0.002-0.002c1.411 1.271 3.247 2.095 5.271 2.235l0.028 0.002v5.036c-1.912-0.048-3.71-0.489-5.331-1.247l0.082 0.034c-0.784-0.377-1.447-0.764-2.077-1.196l0.052 0.034c-0.012 3.649 0.012 7.298-0.025 10.934-0.103 1.853-0.719 3.543-1.707 4.954l0.020-0.031c-1.652 2.366-4.328 3.919-7.371 4.011l-0.014 0c-0.123 0.006-0.268 0.009-0.414 0.009-1.73 0-3.347-0.482-4.725-1.319l0.040 0.023c-2.508-1.509-4.238-4.091-4.558-7.094l-0.004-0.041c-0.025-0.625-0.037-1.25-0.012-1.862 0.49-4.779 4.494-8.476 9.361-8.476 0.547 0 1.083 0.047 1.604 0.136l-0.056-0.008c0.025 1.849-0.050 3.699-0.050 5.548-0.423-0.153-0.911-0.242-1.42-0.242-1.868 0-3.457 1.194-4.045 2.861l-0.009 0.030c-0.133 0.427-0.21 0.918-0.21 1.426 0 0.206 0.013 0.41 0.037 0.61l-0.002-0.024c0.332 2.046 2.086 3.59 4.201 3.59 0.061 0 0.121-0.001 0.181-0.004l-0.009 0c1.463-0.044 2.733-0.831 3.451-1.994l0.010-0.018c0.267-0.372 0.45-0.822 0.511-1.311l0.001-0.014c0.125-2.237 0.075-4.461 0.087-6.698 0.012-5.036-0.012-10.060 0.025-15.083z"></path>
                                </g>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-[10px] bg-white/10 border border-white/20 flex items-center justify-center hover:bg-accent/20 transition-colors duration-200">
                            <svg class="text-accent" width="20" height="20" fill="currentColor" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path d="M12.932 20.459v-8.917l7.839 4.459zM30.368 8.735c-0.354-1.301-1.354-2.307-2.625-2.663l-0.027-0.006c-3.193-0.406-6.886-0.638-10.634-0.638-0.381 0-0.761 0.002-1.14 0.007l0.058-0.001c-0.322-0.004-0.701-0.007-1.082-0.007-3.748 0-7.443 0.232-11.070 0.681l0.434-0.044c-1.297 0.363-2.297 1.368-2.644 2.643l-0.006 0.026c-0.4 2.109-0.628 4.536-0.628 7.016 0 0.088 0 0.176 0.001 0.263l-0-0.014c-0 0.074-0.001 0.162-0.001 0.25 0 2.48 0.229 4.906 0.666 7.259l-0.038-0.244c0.354 1.301 1.354 2.307 2.625 2.663l0.027 0.006c3.193 0.406 6.886 0.638 10.634 0.638 0.38 0 0.76-0.002 1.14-0.007l-0.058 0.001c0.322 0.004 0.702 0.007 1.082 0.007 3.749 0 7.443-0.232 11.070-0.681l-0.434 0.044c1.298-0.362 2.298-1.368 2.646-2.643l0.006-0.026c0.399-2.109 0.627-4.536 0.627-7.015 0-0.088-0-0.176-0.001-0.263l0 0.013c0-0.074 0.001-0.162 0.001-0.25 0-2.48-0.229-4.906-0.666-7.259l0.038 0.244z"></path>
                                </g>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

            {{-- Form Card --}}
            <div class="rounded-[24px] bg-white border-2 border-white p-10 flex flex-col gap-5 font-arabic">

                <h3 class="text-xl font-black text-textColor">ابعتلنا رسالة</h3>

                <form action="#" method="POST" class="flex flex-col gap-4">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-bold text-textColor">الاسم الكامل</label>
                            <input type="text" name="name" placeholder="مثال: أحمد محمد"
                                class="bg-[#F4F7FF] border border-[#e0e8ff] focus:border-primary rounded-xl px-4 py-3 text-sm text-textColor outline-none transition-colors duration-200 font-arabic w-full">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[13px] font-bold text-textColor">رقم الجوال</label>
                            <input type="tel" name="phone" placeholder="+966 5x xxx xxxx"
                                class="bg-[#F4F7FF] border border-[#e0e8ff] focus:border-primary rounded-xl px-4 py-3 text-sm text-textColor outline-none transition-colors duration-200 font-arabic w-full">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[13px] font-bold text-textColor">البريد الإلكتروني</label>
                        <input type="email" name="email" placeholder="example@email.com"
                            class="bg-[#F4F7FF] border border-[#e0e8ff] focus:border-primary rounded-xl px-4 py-3 text-sm text-textColor outline-none transition-colors duration-200 font-arabic w-full">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[13px] font-bold text-textColor">الباقة اللي بتفكر فيها</label>
                        <select name="plan"
                            class="bg-[#F4F7FF] border border-[#e0e8ff] focus:border-primary rounded-xl px-4 py-3 text-sm text-textColor outline-none transition-colors duration-200 font-arabic w-full">
                            <option value="">اختار باقة...</option>
                            <option value="starter">ستارتر — 299 ريال</option>
                            <option value="pro">برو — 599 ريال</option>
                            <option value="elite">إيليت — 999 ريال</option>
                            <option value="family">باقة العائلة — 1,399 ريال</option>
                            <option value="unknown">مش متأكد لسه</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[13px] font-bold text-textColor">رسالتك</label>
                        <textarea name="message" rows="4" placeholder="اكتب سؤالك أو اللي محتاج مساعدة فيه..."
                            class="bg-[#F4F7FF] border border-[#e0e8ff] focus:border-primary rounded-xl px-4 py-3 text-sm text-textColor outline-none transition-colors duration-200 font-arabic w-full resize-none"></textarea>
                    </div>

                    <button type="submit"
                        class="group font-arabic text-textColor bg-accent px-5 py-3 rounded-full text-sm lg:text-base font-black flex justify-center items-center gap-2 transition hover:bg-yellow-300 w-full mt-2">
                        ابعت رسالتك دلوقتي
                        <svg class="transition-transform duration-300 group-hover:-translate-x-2" width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                        </svg>
                    </button>

                </form>

            </div>

        </div>

    </section>

    <x-web.footer :hidden="false" />

@endsection

@section('script')
    <script>
        let swiper;

        document.addEventListener('DOMContentLoaded', function () {

            // ─── Init Swiper ───
            swiper = new Swiper(".mySwiper", {
                loop: true,
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    dynamicBullets: true,
                    clickable: true,
                },
            });

            // ─── Video Logic ───
            const videoCards   = document.querySelectorAll('.video-card');
            const allVideos    = document.querySelectorAll('.video-slide');
            const allOverlays  = document.querySelectorAll('.video-overlay');

            function resetCard(video, overlay) {
                video.pause();
                video.currentTime = 0;
                video.classList.remove('opacity-100');
                video.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                overlay.classList.add('opacity-100');
            }

            function resetAllExcept(currentVideo, currentOverlay) {
                videoCards.forEach(card => {
                    const v = card.querySelector('.video-slide');
                    const o = card.querySelector('.video-overlay');
                    if (v !== currentVideo) resetCard(v, o);
                });
            }

            videoCards.forEach(card => {
                const video   = card.querySelector('.video-slide');
                const overlay = card.querySelector('.video-overlay');
                const playBtn = card.querySelector('.play-btn');

                // ─── زر التشغيل ───
                playBtn.addEventListener('click', () => {
                    resetAllExcept(video, overlay);

                    swiper?.autoplay.stop();
                    swiper && (swiper.allowTouchMove = false);

                    overlay.classList.remove('opacity-100');
                    overlay.classList.add('opacity-0', 'pointer-events-none');

                    video.classList.remove('opacity-0', 'pointer-events-none');
                    video.classList.add('opacity-100');

                    video.play();
                });

                // ─── Pause / Play بعد التشغيل ───
                video.addEventListener('click', () => {
                    if (video.paused) {
                        video.play();
                    } else {
                        video.pause();
                        // رجّع الـ overlay لما يعمل pause
                        video.classList.remove('opacity-100');
                        video.classList.add('opacity-0', 'pointer-events-none');
                        overlay.classList.remove('opacity-0', 'pointer-events-none');
                        overlay.classList.add('opacity-100');

                        swiper?.autoplay.start();
                        swiper && (swiper.allowTouchMove = true);
                    }
                });

                // ─── لما الفيديو يخلص ───
                video.addEventListener('ended', () => {
                    resetCard(video, overlay);
                    swiper?.autoplay.start();
                    swiper && (swiper.allowTouchMove = true);
                });
            });

            // ─── لما السلايد يتغير ───
            swiper.on('slideChange', () => {
                videoCards.forEach(card => {
                    resetCard(card.querySelector('.video-slide'), card.querySelector('.video-overlay'));
                });
                swiper.autoplay.start();
                swiper.allowTouchMove = true;
            });

        });
    </script>

    <script>
        var swiper2 = new Swiper(".mySwiper2", {
            loop: true,
            autoplay: {
                delay: 10000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                dynamicBullets: true,
                clickable: true,
            },
        });
    </script>

    <script>
        const swiper3 = new Swiper('.beforeAfterSwiper', {
            loop: true,
            slidesPerView: 2,
            spaceBetween: 30,
            speed: 700,
            grabCursor: true,

            autoplay: {
                delay: 7000,
                disableOnInteraction: false
            },

            breakpoints: {
                0: {
                    slidesPerView: 1,
                    spaceBetween: 16
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },
                1200: {
                    slidesPerView: 2,
                    spaceBetween: 30
                }
            },

            on: {
                slideChange() {
                    document.querySelectorAll('.ba-dot').forEach((d, i) => {
                        const active = i === this.realIndex;
                        d.classList.toggle('!w-7', active);
                        d.classList.toggle('!h-2.5', active);
                        d.classList.toggle('bg-primary', active);
                        d.classList.toggle('w-2.5', !active);
                        d.classList.toggle('h-2.5', !active);
                        d.classList.toggle('bg-gray-300', !active);
                    });
                }
            }
        });
    </script>

    <script>
        function toggleAccordion(el) {
            const allItems = document.querySelectorAll('.accordion-item');

            allItems.forEach(item => {
                if (item !== el) {
                    item.classList.remove('active', 'bg-primary');
                    item.classList.add('bg-white');

                    const title = item.querySelector('.acc-title');
                    title.classList.remove('text-accent');
                    title.classList.add('text-textColor');

                    const desc = item.querySelector('.acc-desc');
                    desc.classList.add('hidden');
                    desc.classList.remove('block', 'text-white');
                    desc.classList.add('text-gray-600');

                    const icon = item.querySelector('.acc-btn svg');
                    icon.classList.remove('-rotate-90');
                }
            });

            if (el.classList.contains('active')) return;

            el.classList.add('active', 'bg-primary');
            el.classList.remove('bg-white');

            const title = el.querySelector('.acc-title');
            title.classList.remove('text-textColor');
            title.classList.add('text-accent');

            const desc = el.querySelector('.acc-desc');
            desc.classList.remove('hidden', 'text-gray-600');
            desc.classList.add('block', 'text-white');

            const icon = el.querySelector('.acc-btn svg');
            icon.classList.add('-rotate-90');
        }
    </script>

    <script>
        // ─── GSAP Counter + Slide Up ───
        document.addEventListener('DOMContentLoaded', function () {

            gsap.registerPlugin(ScrollTrigger);

            const counters = document.querySelectorAll('[data-count]');

            counters.forEach(el => {

                const target  = parseFloat(el.dataset.count);
                const dec     = parseInt(el.dataset.decimals || 0);
                const prefix  = el.dataset.prefix || '';
                const suffix  = el.dataset.suffix || '';

                // Slide Up
                gsap.from(el, {
                    scrollTrigger: {
                        trigger  : el,
                        start    : 'top 85%',
                        once     : true,
                    },
                    y        : 40,
                    opacity  : 0,
                    duration : 0.8,
                    ease     : 'power3.out',
                });

                // Count Up
                gsap.to({ val: 0 }, {
                    scrollTrigger: {
                        trigger  : el,
                        start    : 'top 85%',
                        once     : true,
                    },
                    val      : target,
                    duration : 2,
                    ease     : 'power2.out',
                    onUpdate() {
                        const v = this.targets()[0].val;
                        el.textContent = prefix + (dec ? v.toFixed(dec) : Math.floor(v)) + suffix;
                    },
                    onComplete() {
                        el.textContent = prefix + (dec ? target.toFixed(dec) : target) + suffix;
                    },
                });

            });

        });
    </script>

    <script>
        gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

        document.querySelectorAll('a[href*="#"]').forEach(link => {
            link.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (!href || href === '#') return;

                const url = new URL(href, window.location.origin);

                const isSamePage =
                    url.pathname === window.location.pathname &&
                    url.hash &&
                    url.hash !== '#';

                if (!isSamePage) return;

                const target = document.querySelector(url.hash);
                if (!target) return;

                e.preventDefault();

                gsap.to(window, {
                    scrollTo: {
                        y: target,
                        offsetY: 80,
                    },
                    duration: 1.8,
                    ease: 'power3.inOut',
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const marquee = document.getElementById("partnersMarquee");
            const track = document.getElementById("partnersTrack");

            if (!marquee || !track) return;

            const buildMarquee = () => {
                // رجّع التراك للحالة الأصلية أولاً
                const originalItems = Array.from(track.querySelectorAll(".partner-item[data-original='true']"));

                if (!originalItems.length) {
                    const currentItems = Array.from(track.children);
                    currentItems.forEach(item => item.setAttribute("data-original", "true"));
                }

                // احذف أي نسخ مضافة قبل كده
                Array.from(track.querySelectorAll(".partner-item[data-clone='true']")).forEach(clone => clone.remove());

                const baseItems = Array.from(track.querySelectorAll(".partner-item[data-original='true']"));

                if (!baseItems.length) return;

                // نكرر لحد ما عرض التراك يبقى أكبر من عرض الكونتينر بمرتين على الأقل
                // علشان -50% تشتغل بسلاسة
                while (track.scrollWidth < marquee.offsetWidth * 2) {
                    baseItems.forEach(item => {
                        const clone = item.cloneNode(true);
                        clone.setAttribute("data-clone", "true");
                        clone.setAttribute("aria-hidden", "true");
                        track.appendChild(clone);
                    });
                }

                // لو العدد النهائي فردي أو النسخ غير كافية للحركة السلسة
                // نضيف نسخة كمان من المجموعة الأصلية لضمان التطابق عند -50%
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
                resizeTimeout = setTimeout(() => {
                    buildMarquee();
                }, 150);
            });
        });
    </script>
@endsection
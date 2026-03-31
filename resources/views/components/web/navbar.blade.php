@props([
    'transparent' => false,
])

@php
    $navClasses = $transparent
        ? 'bg-transparent absolute top-0 left-0 w-full z-50'
        : 'bg-[#0F172A]/95 backdrop-blur-md shadow-md border-b border-white/10 w-full';

    $linkClasses = $transparent
        ? 'text-white hover:text-accent'
        : 'text-white hover:text-accent';

    $activeClasses = 'text-accent font-bold';
@endphp

{{-- Google Icons --}}
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />

<nav class="{{ $navClasses }} transition-all duration-300 font-arabic" id="navbar">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-[5.5rem]">

            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img class="w-[95px] logo-white transition duration-300"
                         src="{{ asset('assets/logo/mindfitbro.png') }}"
                         alt="{{ config('app.name', 'Laravel') }} logo">
                    <img class="w-[95px] logo-black opacity-0 transition duration-300 hidden"
                         src="{{ asset('assets/logo/mindfitbro-b.png') }}"
                         alt="{{ config('app.name', 'Laravel') }} logo">
                </a>
            </div>

            <!-- Right Side Links -->
            <div class="hidden lg:flex items-center gap-8">
                <a href="{{ route('home') }}"
                   class="text-white hover:text-accent text-lg font-semibold transition nav-link">
                    الرئيسية
                </a>

                <a href="{{ route('home') }}#our-target"
                   class="text-white hover:text-accent text-lg font-semibold transition nav-link">
                    هدفنا
                </a>

                <a href="{{ route('home') }}#before-after"
                   class="text-white hover:text-accent text-lg font-semibold transition nav-link">
                    قبل وبعد
                </a>

                <a href="{{ route('home') }}#programs"
                   class="text-white hover:text-accent text-lg font-semibold transition nav-link">
                    برامج التدريب
                </a>

                <a href="{{ route('home') }}#testimonials"
                   class="text-white hover:text-accent text-lg font-semibold transition nav-link">
                    آراء عملاؤنا
                </a>

                <a href="{{ route('home') }}#contact"
                   class="text-white hover:text-accent text-lg font-semibold transition nav-link">
                    اتصل بنا
                </a>
            </div>

            <!-- Left Side Links -->
            <div class="hidden lg:flex items-center gap-8">
                <a href="#cart"
                   class="{{ $linkClasses }} text-lg font-semibold transition flex items-center gap-2 nav-link">
                    <span class="material-symbols-rounded">shopping_cart</span>
                    السلة
                </a>

                <a href="{{ route('login') }}"
                   class="group text-textColor bg-accent px-5 py-3 rounded-full text-base font-bold flex justify-center items-center gap-2 transition">
                    تسجيل الدخول
                    <svg class="transition-transform duration-300 group-hover:-translate-x-2"
                         width="26" height="14" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z"
                              fill="#202020"/>
                    </svg>
                </a>
            </div>

            <!-- Mobile Button -->
            <button id="menuBtn"
                class="lg:hidden relative w-11 h-11 flex items-center justify-center rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg text-white transition-all duration-300 hover:scale-105 active:scale-95">
                <span class="material-symbols-rounded text-[30px]">menu</span>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu"
            class="fixed inset-0 z-[999] lg:hidden pointer-events-none opacity-0 transition-all duration-500">

            <!-- Overlay -->
            <div id="mobileOverlay"
                class="absolute inset-0 bg-[#0D1117]/0 backdrop-blur-0 transition-all duration-500">
            </div>

            <!-- Panel -->
            <div id="mobilePanel"
                class="absolute inset-y-0 right-0 w-full bg-[#0D1117]/92 backdrop-blur-xl translate-x-full transition-transform duration-500 ease-out touch-pan-y">

                <!-- Close Button -->
                <button id="closeMenuBtn"
                    class="absolute top-6 right-6 z-30 w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-white flex items-center justify-center transition-all duration-300 hover:scale-110 hover:bg-white/20 active:scale-95">
                    <span class="material-symbols-rounded text-[28px]">close</span>
                </button>

                <!-- Menu Content -->
                <div id="menuContent"
                    class="min-h-screen flex flex-col items-center justify-center gap-6 px-6 text-center">

                    <a href="{{ route('home') }}"
                       class="mobile-menu-link translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                        الرئيسية
                    </a>

                    <a href="#our-target"
                       class="mobile-menu-link translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                        هدفنا
                    </a>

                    <a href="#before-after"
                       class="mobile-menu-link translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                        قبل وبعد
                    </a>

                    <a href="#programs"
                       class="mobile-menu-link translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                        برامج التدريب
                    </a>

                    <a href="#testimonials"
                       class="mobile-menu-link translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                        آراء عملاؤنا
                    </a>

                    <a href="#contact"
                       class="mobile-menu-link translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                        اتصل بنا
                    </a>

                    <a href="#cart"
                       class="mobile-menu-link translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110 flex items-center gap-2">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        السلة
                    </a>

                    <a href="{{ route('login') }}"
                       class="mobile-menu-link translate-y-8 opacity-0 mt-4 bg-accent text-black px-7 py-3 rounded-full font-bold transition-all duration-500 ease-out hover:scale-105">
                        تسجيل الدخول
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    var navbar = document.getElementById('navbar');
    var menuBtnScroll = document.getElementById('menuBtn');
    var links = navbar.querySelectorAll(".nav-link");
    var logoWhite = document.querySelector(".logo-white");
    var logoBlack = document.querySelector(".logo-black");

    window.addEventListener("scroll", function() {
        if (window.scrollY >= 350) {
            navbar.classList.add("fixed", "bg-white");
            navbar.classList.remove("absolute", "bg-transparent");
            menuBtnScroll.classList.add("text-black");
            menuBtnScroll.classList.remove("text-white");

            links.forEach(link => {
                link.classList.remove("text-white", "hover:text-accent");
                link.classList.add("text-black", "hover:text-primary");
            });

            logoWhite.classList.add("hidden");
            logoBlack.classList.remove("hidden");
            logoBlack.classList.remove("opacity-0");

        } else {
            navbar.classList.remove("fixed", "bg-white");
            navbar.classList.add("absolute", "bg-transparent");
            menuBtnScroll.classList.remove("text-black");
            menuBtnScroll.classList.add("text-white");

            links.forEach(link => {
                link.classList.remove("text-black", "hover:text-primary");
                link.classList.add("text-white", "hover:text-accent");
            });

            logoWhite.classList.remove("hidden");
            logoBlack.classList.add("hidden");
            logoBlack.classList.add("opacity-0");
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuBtn = document.getElementById('menuBtn');
        const closeMenuBtn = document.getElementById('closeMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const mobilePanel = document.getElementById('mobilePanel');
        const menuContent = document.getElementById('menuContent');
        const mobileLinks = document.querySelectorAll('.mobile-menu-link');

        let touchStartX = 0;
        let touchCurrentX = 0;
        let isSwiping = false;

        function showLinksSequentially() {
            mobileLinks.forEach((link, index) => {
                setTimeout(() => {
                    link.classList.remove('translate-y-8', 'opacity-0');
                    link.classList.add('translate-y-0', 'opacity-100');
                }, 90 * index);
            });
        }

        function resetLinksAnimation() {
            mobileLinks.forEach(link => {
                link.classList.remove('translate-y-0', 'opacity-100');
                link.classList.add('translate-y-8', 'opacity-0');
            });
        }

        function openMenu() {
            mobileMenu.classList.remove('pointer-events-none', 'opacity-0');
            mobileMenu.classList.add('opacity-100');

            mobileOverlay.classList.remove('bg-[#0D1117]/0', 'backdrop-blur-0');
            mobileOverlay.classList.add('bg-[#0D1117]/60', 'backdrop-blur-sm');

            mobilePanel.classList.remove('translate-x-full');
            mobilePanel.classList.add('translate-x-0');

            document.body.classList.add('overflow-hidden');

            resetLinksAnimation();
            setTimeout(() => {
                showLinksSequentially();
            }, 120);
        }

        function closeMenu() {
            mobileMenu.classList.add('opacity-0');
            mobileMenu.classList.remove('opacity-100');

            mobileOverlay.classList.remove('bg-[#0D1117]/60', 'backdrop-blur-sm');
            mobileOverlay.classList.add('bg-[#0D1117]/0', 'backdrop-blur-0');

            mobilePanel.classList.add('translate-x-full');
            mobilePanel.classList.remove('translate-x-0');

            document.body.classList.remove('overflow-hidden');
            resetLinksAnimation();

            setTimeout(() => {
                mobileMenu.classList.add('pointer-events-none');
                mobilePanel.style.transform = '';
            }, 500);
        }

        function toggleMenu() {
            if (mobileMenu.classList.contains('opacity-0')) {
                openMenu();
            } else {
                closeMenu();
            }
        }

        menuBtn?.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleMenu();
        });

        closeMenuBtn?.addEventListener('click', function () {
            closeMenu();
        });

        mobileOverlay?.addEventListener('click', function () {
            closeMenu();
        });

        mobileLinks.forEach(link => {
            link.addEventListener('click', function () {
                closeMenu();
            });
        });

        menuContent?.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeMenu();
            }
        });

        // Swipe close
        mobilePanel?.addEventListener('touchstart', function (e) {
            if (mobileMenu.classList.contains('opacity-0')) return;
            touchStartX = e.touches[0].clientX;
            touchCurrentX = touchStartX;
            isSwiping = true;
            mobilePanel.classList.remove('duration-500');
            mobileOverlay.classList.remove('duration-500');
        }, { passive: true });

        mobilePanel?.addEventListener('touchmove', function (e) {
            if (!isSwiping) return;

            touchCurrentX = e.touches[0].clientX;
            const diff = touchCurrentX - touchStartX;

            if (diff > 0) {
                mobilePanel.style.transform = `translateX(${diff}px)`;

                const opacityRatio = Math.max(0, 0.6 - (diff / window.innerWidth));
                const blurRatio = Math.max(0, 4 - (diff / 80));

                mobileOverlay.style.backgroundColor = `rgba(13, 17, 23, ${opacityRatio})`;
                mobileOverlay.style.backdropFilter = `blur(${blurRatio}px)`;
            }
        }, { passive: true });

        mobilePanel?.addEventListener('touchend', function () {
            if (!isSwiping) return;

            const diff = touchCurrentX - touchStartX;
            isSwiping = false;

            mobilePanel.classList.add('duration-500');
            mobileOverlay.classList.add('duration-500');

            if (diff > 120) {
                mobilePanel.style.transform = '';
                mobileOverlay.style.backgroundColor = '';
                mobileOverlay.style.backdropFilter = '';
                closeMenu();
            } else {
                mobilePanel.style.transform = '';
                mobileOverlay.style.backgroundColor = '';
                mobileOverlay.style.backdropFilter = '';

                mobilePanel.classList.remove('translate-x-full');
                mobilePanel.classList.add('translate-x-0');

                mobileOverlay.classList.remove('bg-[#0D1117]/0', 'backdrop-blur-0');
                mobileOverlay.classList.add('bg-[#0D1117]/60', 'backdrop-blur-sm');
            }
        });
    });
</script>
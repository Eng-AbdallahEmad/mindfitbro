@props([
    'transparent' => false,
])

@php
    $navClasses = $transparent
        ? 'bg-transparent absolute top-0 left-0 w-full z-50'
        : 'bg-white backdrop-blur-md shadow-md border-b border-gray-100 w-full z-50 fixed top-0 left-0';

    $linkColor = $transparent
        ? 'text-white hover:text-accent'
        : 'text-textColor hover:text-primary';
@endphp

{{-- Google Icons --}}
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />

<nav class="{{ $navClasses }} transition-all duration-300 font-arabic" id="navbar">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-[5.5rem]">

            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    @if ($transparent)
                        <img class="w-[95px] logo-white transition duration-300"
                            src="{{ asset('assets/logo/mindfitbro.png') }}"
                            alt="{{ config('app.name') }} logo">
                        <img class="w-[95px] logo-black opacity-0 transition duration-300 hidden"
                            src="{{ asset('assets/logo/mindfitbro-b.png') }}"
                            alt="{{ config('app.name') }} logo">
                    @else
                        <img class="w-[95px]"
                            src="{{ asset('assets/logo/mindfitbro-b.png') }}"
                            alt="{{ config('app.name') }} logo">
                    @endif
                </a>
            </div>

            <!-- Right Side Links -->
            <div class="hidden lg:flex items-center gap-8">
                <a href="{{ route('home') }}"              class="{{ $linkColor }} text-lg font-semibold transition nav-link">الرئيسية</a>
                <a href="{{ route('home') }}#our-target"   class="{{ $linkColor }} text-lg font-semibold transition nav-link">هدفنا</a>
                <a href="{{ route('home') }}#before-after" class="{{ $linkColor }} text-lg font-semibold transition nav-link">قبل وبعد</a>
                <a href="{{ route('home') }}#programs"     class="{{ $linkColor }} text-lg font-semibold transition nav-link">برامج التدريب</a>
                <a href="{{ route('home') }}#testimonials" class="{{ $linkColor }} text-lg font-semibold transition nav-link">آراء عملاؤنا</a>
                <a href="{{ route('home') }}#contact"      class="{{ $linkColor }} text-lg font-semibold transition nav-link">اتصل بنا</a>
            </div>

            <!-- Left Side -->
            <div class="hidden lg:flex items-center gap-8">
                @if (auth()->check() && auth()->user()->role === 'user')
                    <a href="{{ route('cart.index') }}"
                       class="{{ $linkColor }} text-lg font-semibold transition flex items-center gap-1 nav-link">
                        <span class="material-symbols-rounded text-[20px]">shopping_cart</span>
                        السلة
                    </a>
                @endif

                @guest
                    <a href="{{ route('login') }}"
                        class="group text-textColor bg-accent p-3 rounded-full text-base font-bold flex items-center gap-2 transition hover:bg-yellow-300">
                        <span class="material-symbols-rounded text-[22px]" style="font-variation-settings:'FILL' 1">person</span>
                    </a>
                @endguest

                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button
                            @click="open = !open"
                            @click.away="open = false"
                            type="button"
                            class="relative w-11 h-11 flex items-center justify-center rounded-full bg-accent border border-accent/30 shadow-lg text-textColor transition-all duration-300 hover:scale-105 active:scale-95">
                            <span class="material-symbols-rounded text-[22px]" style="font-variation-settings:'FILL' 1">person</span>
                        </button>

                        <div
                            x-show="open"
                            x-transition
                            class="absolute left-0 mt-3 w-52 rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden z-[9999]"
                            style="display:none;">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold">مرحباً،</p>
                                @if (auth()->user()->role === 'coach')
                                    <p class="text-sm font-black text-textColor truncate">ك/ {{ auth()->user()->name }}</p>
                                @else
                                    <p class="text-sm font-black text-textColor truncate">{{ auth()->user()->name }}</p>
                                @endif
                            </div>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition">
                                <span class="material-symbols-rounded text-[18px]">dashboard</span> الداشبورد
                            </a>
                            <a href="" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition">
                                <span class="material-symbols-rounded text-[18px]">edit_square</span> تعديل الملف الشخصي
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50 transition text-right">
                                    <span class="material-symbols-rounded text-[18px]">logout</span> تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Mobile Buttons -->
            <div class="lg:hidden flex items-center gap-2">

                {{-- User Icon --}}
                @guest
                    <a href="{{ route('login') }}" id="authBtn"
                        class="relative w-11 h-11 flex items-center justify-center rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg {{ $transparent ? 'text-white' : 'text-textColor' }} transition-all duration-300 hover:scale-105 active:scale-95">
                        <span class="material-symbols-rounded text-[26px]">person</span>
                    </a>
                @endguest

                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button
                            @click="open = !open"
                            @click.away="open = false"
                            type="button"
                            class="relative w-11 h-11 flex items-center justify-center rounded-full bg-accent border border-accent/30 shadow-lg text-textColor transition-all duration-300 hover:scale-105 active:scale-95">
                            <span class="material-symbols-rounded text-[22px]" style="font-variation-settings:'FILL' 1">person</span>
                        </button>

                        <div
                            x-show="open"
                            x-transition
                            class="absolute left-0 mt-3 w-52 rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden z-[9999]"
                            style="display:none;">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold">مرحباً،</p>
                                <p class="text-sm font-black text-textColor truncate">{{ auth()->user()->name }}</p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition">
                                <span class="material-symbols-rounded text-[18px]">dashboard</span> الداشبورد
                            </a>
                            <a href="" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition">
                                <span class="material-symbols-rounded text-[18px]">edit_square</span> تعديل الملف الشخصي
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50 transition text-right">
                                    <span class="material-symbols-rounded text-[18px]">logout</span> تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth

                {{-- Menu Button --}}
                <button id="menuBtn"
                    class="relative w-11 h-11 flex items-center justify-center rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg {{ $transparent ? 'text-white' : 'text-textColor' }} transition-all duration-300 hover:scale-105 active:scale-95">
                    <span class="material-symbols-rounded text-[30px]">menu</span>
                </button>

            </div>
        </div>
    </div>
</nav>

{{-- ─── Mobile Menu: خارج الـ nav تماماً ─── --}}
<div id="mobileMenu"
    class="fixed inset-0 z-[9999] lg:hidden pointer-events-none opacity-0 transition-all duration-500">

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
               class="mobile-menu-link font-arabic translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                الرئيسية
            </a>
            <a href="{{ route('home') }}#our-target"
               class="mobile-menu-link font-arabic translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                هدفنا
            </a>
            <a href="{{ route('home') }}#before-after"
               class="mobile-menu-link font-arabic translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                قبل وبعد
            </a>
            <a href="{{ route('home') }}#programs"
               class="mobile-menu-link font-arabic translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                برامج التدريب
            </a>
            <a href="{{ route('home') }}#testimonials"
               class="mobile-menu-link font-arabic translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                آراء عملاؤنا
            </a>
            <a href="{{ route('home') }}#contact"
               class="mobile-menu-link font-arabic translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110">
                اتصل بنا
            </a>
            <a href="{{ route('cart.index') }}"
               class="mobile-menu-link font-arabic translate-y-8 opacity-0 text-2xl font-bold text-white transition-all duration-500 ease-out hover:text-[#D4ED57] hover:scale-110 flex items-center gap-2">
                <span class="material-symbols-rounded">shopping_cart</span>
                السلة
            </a>



        </div>
    </div>
</div>

{{-- ─── Scroll behavior ─── --}}
@if ($transparent)
<script>
    (function () {
        var navbar     = document.getElementById('navbar');
        var menuBtn    = document.getElementById('menuBtn');
        var authBtn    = document.getElementById('authBtn');
        var links      = navbar.querySelectorAll('.nav-link');
        var logoWhite  = document.querySelector('.logo-white');
        var logoBlack  = document.querySelector('.logo-black');

        window.addEventListener('scroll', function () {
            if (window.scrollY >= 350) {
                navbar.classList.add('fixed', 'bg-white', 'shadow-md');
                navbar.classList.remove('absolute', 'bg-transparent');
                menuBtn.classList.replace('text-white', 'text-textColor');
                authBtn.classList.replace('text-white', 'text-textColor');
                links.forEach(function (l) {
                    l.classList.remove('text-white', 'hover:text-accent');
                    l.classList.add('text-textColor', 'hover:text-primary');
                });
                if (logoWhite) logoWhite.classList.add('hidden');
                if (logoBlack) { logoBlack.classList.remove('hidden', 'opacity-0'); }
            } else {
                navbar.classList.remove('fixed', 'bg-white', 'shadow-md');
                navbar.classList.add('absolute', 'bg-transparent');
                menuBtn.classList.replace('text-textColor', 'text-white');
                links.forEach(function (l) {
                    l.classList.remove('text-textColor', 'hover:text-primary');
                    l.classList.add('text-white', 'hover:text-accent');
                });
                authBtn.classList.replace('text-textColor', 'text-white');
                if (logoWhite) logoWhite.classList.remove('hidden');
                if (logoBlack) { logoBlack.classList.add('hidden', 'opacity-0'); }
            }
        });
    })();
</script>
@endif

{{-- ─── Mobile Menu Logic ─── --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var menuBtn      = document.getElementById('menuBtn');
        var closeMenuBtn = document.getElementById('closeMenuBtn');
        var mobileMenu   = document.getElementById('mobileMenu');
        var mobileOverlay= document.getElementById('mobileOverlay');
        var mobilePanel  = document.getElementById('mobilePanel');
        var mobileLinks  = document.querySelectorAll('.mobile-menu-link');

        var touchStartX   = 0;
        var touchCurrentX = 0;
        var isSwiping     = false;

        function showLinks() {
            mobileLinks.forEach(function (link, i) {
                setTimeout(function () {
                    link.classList.remove('translate-y-8', 'opacity-0');
                    link.classList.add('translate-y-0', 'opacity-100');
                }, 90 * i);
            });
        }

        function resetLinks() {
            mobileLinks.forEach(function (link) {
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
            resetLinks();
            setTimeout(showLinks, 120);
        }

        function closeMenu() {
            mobileMenu.classList.add('opacity-0');
            mobileMenu.classList.remove('opacity-100');
            mobileOverlay.classList.remove('bg-[#0D1117]/60', 'backdrop-blur-sm');
            mobileOverlay.classList.add('bg-[#0D1117]/0', 'backdrop-blur-0');
            mobilePanel.classList.add('translate-x-full');
            mobilePanel.classList.remove('translate-x-0');
            document.body.classList.remove('overflow-hidden');
            resetLinks();
            setTimeout(function () {
                mobileMenu.classList.add('pointer-events-none');
                mobilePanel.style.transform = '';
            }, 500);
        }

        menuBtn      && menuBtn.addEventListener('click', function (e) { e.stopPropagation(); openMenu(); });
        closeMenuBtn && closeMenuBtn.addEventListener('click', closeMenu);
        mobileOverlay && mobileOverlay.addEventListener('click', closeMenu);
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeMenu(); });

        mobileLinks.forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });

        // ─── Swipe to close ───
        mobilePanel && mobilePanel.addEventListener('touchstart', function (e) {
            touchStartX = touchCurrentX = e.touches[0].clientX;
            isSwiping = true;
            mobilePanel.classList.remove('duration-500');
            mobileOverlay.classList.remove('duration-500');
        }, { passive: true });

        mobilePanel && mobilePanel.addEventListener('touchmove', function (e) {
            if (!isSwiping) return;
            touchCurrentX = e.touches[0].clientX;
            var diff = touchCurrentX - touchStartX;
            if (diff > 0) {
                mobilePanel.style.transform = 'translateX(' + diff + 'px)';
                var opacity = Math.max(0, 0.6 - diff / window.innerWidth);
                var blurVal = Math.max(0, 4  - diff / 80);
                mobileOverlay.style.backgroundColor  = 'rgba(13,17,23,' + opacity + ')';
                mobileOverlay.style.backdropFilter   = 'blur(' + blurVal + 'px)';
            }
        }, { passive: true });

        mobilePanel && mobilePanel.addEventListener('touchend', function () {
            if (!isSwiping) return;
            isSwiping = false;
            mobilePanel.classList.add('duration-500');
            mobileOverlay.classList.add('duration-500');
            var diff = touchCurrentX - touchStartX;
            mobilePanel.style.transform = '';
            mobileOverlay.style.backgroundColor = '';
            mobileOverlay.style.backdropFilter  = '';
            if (diff > 120) {
                closeMenu();
            } else {
                mobilePanel.classList.remove('translate-x-full');
                mobilePanel.classList.add('translate-x-0');
                mobileOverlay.classList.remove('bg-[#0D1117]/0', 'backdrop-blur-0');
                mobileOverlay.classList.add('bg-[#0D1117]/60', 'backdrop-blur-sm');
            }
        });
    });
</script>

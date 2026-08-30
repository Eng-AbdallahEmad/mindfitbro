<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') — MindFitBro Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Cairo', sans-serif; }

        /* ══════════════ SIDEBAR ══════════════ */
        .admin-sidebar {
            width: 260px;
            background: #0f172a;
            position: fixed;
            top: 0; right: 0; bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 60;
            border-left: 1px solid rgba(255,255,255,0.06);
            transition: transform .3s cubic-bezier(.4,0,.2,1);
        }

        /* Mobile: hidden off-screen to the right */
        @media (max-width: 1023px) {
            .admin-sidebar { transform: translateX(100%); }
            .admin-sidebar.is-open { transform: translateX(0); }
        }

        /* Desktop: always visible, no animation needed */
        @media (min-width: 1024px) {
            .admin-sidebar { transform: translateX(0) !important; }
        }

        /* ══════════════ BACKDROP ══════════════ */
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(2px);
            z-index: 55;
            opacity: 0;
            pointer-events: none;
            transition: opacity .35s ease, backdrop-filter .35s ease;
        }
        .sidebar-backdrop.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        /* ══════════════ MAIN ══════════════ */
        .admin-main {
            min-height: 100vh;
            background: #f1f5f9;
            transition: margin-right .3s cubic-bezier(.4,0,.2,1);
        }
        @media (min-width: 1024px) {
            .admin-main { margin-right: 260px; }
        }

        /* ══════════════ TOPBAR ══════════════ */
        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            position: sticky;
            top: 0;
            z-index: 40;
            gap: 1rem;
        }
        @media (min-width: 1024px) {
            .admin-topbar { height: 64px; padding: 0 1.75rem; }
        }

        /* ══════════════ HAMBURGER ══════════════ */
        .hamburger-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px; height: 38px;
            border-radius: 10px;
            border: none;
            background: #f1f5f9;
            color: #475569;
            cursor: pointer;
            transition: background .18s, color .18s;
            flex-shrink: 0;
        }
        .hamburger-btn:hover { background: #e2e8f0; color: #1e293b; }
        @media (min-width: 1024px) { .hamburger-btn { display: none; } }

        /* ══════════════ NAV ITEM ══════════════ */
        .nav-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem 1.1rem;
            border-radius: 12px;
            color: rgba(255,255,255,0.45);
            font-size: .875rem;
            font-weight: 700;
            transition: background .18s, color .18s;
            text-decoration: none;
            cursor: pointer;
            border: none;
            width: 100%;
            text-align: right;
            background: transparent;
        }
        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.85);
        }
        .nav-item.active {
            background: rgba(59,130,246,0.18);
            color: #60a5fa;
        }
        .nav-item.active .nav-icon { color: #3b82f6; }
        .nav-icon {
            font-size: 20px;
            font-variation-settings: 'FILL' 1;
            flex-shrink: 0;
        }

        /* ══════════════ SCROLLBAR ══════════════ */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 99px; }
    </style>

    @yield('style')
</head>
<body class="bg-slate-100 antialiased">

@php $admin = Auth::guard('admin')->user(); @endphp

{{-- ══════════ BACKDROP (mobile only) ══════════ --}}
<div id="sidebarBackdrop" class="sidebar-backdrop" onclick="closeSidebar()"></div>

{{-- ══════════ SIDEBAR ══════════ --}}
<aside id="adminSidebar" class="admin-sidebar">

    {{-- Logo --}}
    <div class="px-5 py-5 border-b border-white/5 flex-shrink-0">
        <img src="{{ asset('assets/logo/mindfitbro.png') }}" alt="MindFitBro" class="w-[140px] object-contain">
        <span class="inline-block mt-1.5 text-[10px] font-bold tracking-widest text-blue-400/60 uppercase">Admin Panel</span>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

        <p class="text-[10px] font-black tracking-widest text-white/20 uppercase px-3 pb-2 pt-1">الرئيسية</p>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           onclick="closeSidebar()">
            <span class="material-symbols-rounded nav-icon">dashboard</span>
            لوحة التحكم
        </a>

        <p class="text-[10px] font-black tracking-widest text-white/20 uppercase px-3 pb-2 pt-5">إدارة المستخدمين</p>

        <a href="{{ route('admin.members.index') }}" class="nav-item {{ request()->routeIs('admin.members*') ? 'active' : '' }}" onclick="closeSidebar()">
            <span class="material-symbols-rounded nav-icon">group</span>
            الأعضاء
        </a>

        <a href="{{ route('admin.coaches.index') }}" class="nav-item {{ request()->routeIs('admin.coaches*') ? 'active' : '' }}" onclick="closeSidebar()">
            <span class="material-symbols-rounded nav-icon">fitness_center</span>
            المدربون
        </a>

        <p class="text-[10px] font-black tracking-widest text-white/20 uppercase px-3 pb-2 pt-5">المالية</p>

        <a href="{{ route('admin.subscriptions.index') }}" class="nav-item {{ request()->routeIs('admin.subscriptions*') ? 'active' : '' }}" onclick="closeSidebar()">
            <span class="material-symbols-rounded nav-icon">subscriptions</span>
            الاشتراكات
        </a>

        <a href="{{ route('admin.plans.index') }}" class="nav-item {{ request()->routeIs('admin.plans*') ? 'active' : '' }}" onclick="closeSidebar()">
            <span class="material-symbols-rounded nav-icon">workspace_premium</span>
            الباقات
        </a>

        <a href="{{ route('admin.coupons.index') }}" class="nav-item {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}" onclick="closeSidebar()">
            <span class="material-symbols-rounded nav-icon">confirmation_number</span>
            أكواد الخصم
        </a>

        <a href="{{ route('admin.seasons.index') }}" class="nav-item {{ request()->routeIs('admin.seasons*') ? 'active' : '' }}" onclick="closeSidebar()">
            <span class="material-symbols-rounded nav-icon">local_offer</span>
            {{ __('messages.admin.nav_seasons') }}
        </a>

        <p class="text-[10px] font-black tracking-widest text-white/20 uppercase px-3 pb-2 pt-5">الإعدادات</p>

        <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" onclick="closeSidebar()">
            <span class="material-symbols-rounded nav-icon">settings</span>
            الإعدادات
        </a>

        <a href="{{ route('admin.fx-rates.index') }}" class="nav-item {{ request()->routeIs('admin.fx-rates*') ? 'active' : '' }}" onclick="closeSidebar()">
            <span class="material-symbols-rounded nav-icon">currency_exchange</span>
            أسعار الصرف
        </a>

        <a href="{{ route('admin.pages.index') }}" class="nav-item {{ request()->routeIs('admin.pages*') ? 'active' : '' }}" onclick="closeSidebar()">
            <span class="material-symbols-rounded nav-icon">article</span>
            صفحات الموقع
        </a>

    </nav>

    {{-- Admin Info + Logout --}}
    @if($admin)
    <div class="px-4 py-4 border-t border-white/5 flex-shrink-0">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-xl bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-blue-400"
                      style="font-size:18px;font-variation-settings:'FILL' 1">shield_person</span>
            </div>
            <div class="overflow-hidden">
                <p class="text-white text-sm font-bold truncate">{{ $admin->name }}</p>
                <p class="text-white/30 text-xs truncate">{{ $admin->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
                class="nav-item text-red-400/60 hover:text-red-400 hover:bg-red-500/10">
                <span class="material-symbols-rounded nav-icon" style="font-size:18px">logout</span>
                تسجيل الخروج
            </button>
        </form>
    </div>
    @endif

</aside>

{{-- ══════════ MAIN ══════════ --}}
<div class="admin-main">

    {{-- Topbar --}}
    <header class="admin-topbar">

        {{-- Right: hamburger (mobile) + page info --}}
        <div class="flex items-center gap-3 min-w-0">
            <button id="sidebarToggle" class="hamburger-btn" onclick="toggleSidebar()">
                <span class="material-symbols-rounded" style="font-size:22px">menu</span>
            </button>
            <div class="min-w-0">
                <h1 class="text-sm font-black text-slate-800 leading-tight truncate">
                    @yield('page-title', 'لوحة التحكم')
                </h1>
                <p class="text-[11px] text-slate-400 font-semibold hidden sm:block">
                    @yield('page-subtitle', '')
                </p>
            </div>
        </div>

        {{-- Left: date --}}
        <div class="flex items-center gap-2 bg-slate-100 rounded-xl px-3 py-2 flex-shrink-0">
            <span class="material-symbols-rounded text-slate-400"
                  style="font-size:15px;font-variation-settings:'FILL' 1">calendar_today</span>
            <span class="text-[11px] font-bold text-slate-500 hidden sm:block">{{ now()->format('d / m / Y') }}</span>
            <span class="text-[11px] font-bold text-slate-500 sm:hidden">{{ now()->format('d/m') }}</span>
        </div>

    </header>

    {{-- Page Content --}}
    <main class="p-4 sm:p-6 lg:p-8">
        @yield('content')
    </main>

</div>

<script>
    const sidebar  = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    function openSidebar() {
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function toggleSidebar() {
        sidebar.classList.contains('is-open') ? closeSidebar() : openSidebar();
    }

    // Close on Escape key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeSidebar();
    });

    // Auto-close sidebar when resizing to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) closeSidebar();
    });
</script>

@yield('script')
</body>
</html>

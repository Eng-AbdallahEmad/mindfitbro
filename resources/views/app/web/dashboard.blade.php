@extends('layouts.web.app')

@section('title', 'Dashboard — ' . auth()->user()->name)

@section('style')
    <style>

        /* ══════════════════════════════════════
        LAYOUT
        ══════════════════════════════════════ */
        .dash-wrap {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
            background: #F0F4FB;
        }

        @media (max-width: 1024px) {
            .dash-wrap { grid-template-columns: 1fr; }
            .dash-sidebar { display: none; }
            .dash-sidebar.open { display: flex; position: fixed; z-index: 50; inset: 0; }
        }

        /* ══════════════════════════════════════
        SIDEBAR
        ══════════════════════════════════════ */
        .dash-sidebar {
            background: #0f2d6b;
            display: flex;
            flex-direction: column;
            padding: 2rem 1.2rem;
            gap: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .7rem 1rem;
            border-radius: 14px;
            font-family: 'Cairo', sans-serif;
            font-size: .85rem;
            font-weight: 700;
            color: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all .22s;
            text-decoration: none;
            direction: rtl;
        }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
        .nav-item.active {
            background: rgba(212,237,87,0.12);
            color: #D4ED57;
            border: 1px solid rgba(212,237,87,0.2);
        }
        .nav-item.active .nav-icon { color: #D4ED57; }
        .nav-icon { color: rgba(255,255,255,0.35); transition: color .22s; font-size: 20px; }

        /* ══════════════════════════════════════
        CARDS
        ══════════════════════════════════════ */
        .card {
            background: #fff;
            border-radius: 22px;
            padding: 1.5rem;
            border: 1px solid rgba(23,77,173,.06);
        }
        .card-dark {
            background: #174DAD;
            border-radius: 22px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .card-dark::before {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            top: -60px; left: -60px;
        }
        .card-dark::after {
            content: '';
            position: absolute;
            width: 140px; height: 140px;
            border-radius: 50%;
            background: rgba(212,237,87,0.08);
            bottom: -40px; right: 30px;
        }

        /* ══════════════════════════════════════
        PROGRESS RING
        ══════════════════════════════════════ */
        .ring-bg   { fill: none; stroke: rgba(255,255,255,0.1); stroke-width: 10; }
        .ring-fill { fill: none; stroke: #D4ED57; stroke-width: 10; stroke-linecap: round;
                    stroke-dasharray: 408; stroke-dashoffset: 408;
                    transition: stroke-dashoffset 1.4s cubic-bezier(.4,0,.2,1);
                    transform-origin: center; transform: rotate(-90deg); }

        /* ══════════════════════════════════════
        WEEK DAYS
        ══════════════════════════════════════ */
        .day-dot {
            width: 34px; height: 34px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem; font-weight: 800;
            font-family: 'Cairo', sans-serif;
            border: 2px solid transparent;
            transition: all .2s;
        }
        .day-done    { background: #D4ED57; color: #1c1c1c; }
        .day-today   { background: #174DAD; color: #fff; border-color: #D4ED57; }
        .day-rest    { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.3); }
        .day-upcoming{ background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.5); border-color: rgba(255,255,255,0.12); }

        /* ══════════════════════════════════════
        MACRO BARS
        ══════════════════════════════════════ */
        .macro-bar-wrap {
            height: 7px;
            border-radius: 999px;
            background: #F0F4FB;
            overflow: hidden;
        }
        .macro-bar-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 1.2s cubic-bezier(.4,0,.2,1);
        }

        /* ══════════════════════════════════════
        ACHIEVEMENT BADGE
        ══════════════════════════════════════ */
        .badge {
            display: flex; flex-direction: column; align-items: center; gap: .35rem;
            padding: .85rem .6rem;
            border-radius: 16px;
            background: #F0F4FB;
            border: 1.5px solid transparent;
            transition: all .22s;
            text-align: center;
        }
        .badge.earned { background: #fffbe6; border-color: #D4ED57; }
        .badge:not(.earned) { filter: grayscale(1); opacity: .45; }

        /* ══════════════════════════════════════
        WAITING CARD
        ══════════════════════════════════════ */
        .waiting-card-pulse {
            animation: waitingPulse 2.5s ease-in-out infinite;
        }

        @keyframes waitingPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(23,77,173,0); }
            50%       { box-shadow: 0 0 0 6px rgba(23,77,173,0.06); }
        }

        /* ══════════════════════════════════════
        ANIMATIONS
        ══════════════════════════════════════ */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .anim { animation: fadeUp .5s ease both; }
        .anim-1 { animation-delay:.05s; }
        .anim-2 { animation-delay:.12s; }
        .anim-3 { animation-delay:.19s; }
        .anim-4 { animation-delay:.26s; }
        .anim-5 { animation-delay:.33s; }
        .anim-6 { animation-delay:.40s; }

    </style>
@endsection

@section('content')


@if(auth()->user()->role === 'coach')
    <x-web.coach
        :total-users="$totalUsers"
        :total-clients="$totalClients"
        :new-clients-this-month="$newClientsThisMonth"
        :today-sessions="$todaySessions"
        :first-session-time="$firstSessionTime"
        :pending-bookings="$pendingBookings"
        :pending-bookings-list="$pendingBookingsList"
        :monthly-revenue="$monthlyRevenue"
        :active-clients="$activeClients"
    />
@else
   <x-web.user :subscription="$subscription" :progress="$progress"/>
@endif


@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const ring = document.getElementById('journeyRing');
        if (ring) {
            const target = parseFloat(ring.style.strokeDashoffset);
            ring.style.strokeDashoffset = 408;
            setTimeout(() => { ring.style.strokeDashoffset = target; }, 200);
        }

        document.querySelectorAll('.macro-bar-fill').forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => { bar.style.width = w; }, 300);
        });

        const sections = document.querySelectorAll('main [id]');
        const navItems = document.querySelectorAll('.nav-item');
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    navItems.forEach(n => {
                        n.classList.toggle('active', n.getAttribute('href') === '#' + e.target.id);
                    });
                }
            });
        }, { threshold: 0.4 });
        sections.forEach(s => obs.observe(s));

    });
</script>
@endsection
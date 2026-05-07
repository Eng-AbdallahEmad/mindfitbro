@extends('layouts.web.app')

@section('title', __('messages.schedule_meeting.title'))

@php $isRtl = app()->getLocale() === 'ar'; @endphp

@section('style')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@700;800;900&family=Cairo:wght@400;500;600;700;800;900&display=swap');

    :root {
        --primary:      #174DAD;
        --primary-dark: #0f3a87;
        --accent:       #D4ED57;
        --text:         #1C1C1C;
        --bg:           #EEEEEE;
        --white:        #ffffff;
        --muted:        #6b7280;
        --border:       rgba(23,77,173,0.13);
        --green:        #22c55e;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ══ PAGE ══════════════════════════════════════════ */
    .sched-page {
        min-height: 100vh;
        background: var(--bg);
        font-family: 'Cairo', sans-serif;
        direction: {{ $isRtl ? 'rtl' : 'ltr' }};
        position: relative;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 120px 16px 72px;
    }

    /* subtle dot-grid */
    .sched-page::before {
        content: '';
        position: fixed; inset: 0;
        background-image: radial-gradient(circle, rgba(23,77,173,.09) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none; z-index: 0;
    }

    /* blobs */
    .blob { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; }
    .b1 { width:520px;height:520px; background:var(--primary); opacity:.08; top:-140px; right:-120px; animation:bf 10s ease-in-out infinite; }
    .b2 { width:380px;height:380px; background:var(--accent);  opacity:.13; bottom:-90px; left:-90px;  animation:bf 13s ease-in-out infinite reverse; }

    @keyframes bf {
        0%,100%{transform:translateY(0) scale(1);}
        50%{transform:translateY(-22px) scale(1.05);}
    }

    /* ══ CARD ══════════════════════════════════════════ */
    .sched-card {
        position: relative; z-index: 1;
        width: 100%; max-width: 680px;
        background: var(--white);
        border-radius: 28px;
        border: 1.5px solid var(--border);
        box-shadow: 0 24px 72px rgba(23,77,173,.14);
        overflow: hidden;
        animation: cardIn .65s cubic-bezier(.34,1.56,.64,1) both;
    }

    @keyframes cardIn {
        from { opacity:0; transform: translateY(36px) scale(.96); }
        to   { opacity:1; transform: translateY(0)    scale(1); }
    }

    /* ── header strip ── */
    .sched-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 32px 36px 28px;
        position: relative;
        overflow: hidden;
    }

    /* cross-hatch pattern */
    .sched-header::before {
        content: '';
        position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    /* curved bottom edge */
    .sched-header::after {
        content: '';
        position: absolute; bottom: -1px; left: 0; right: 0;
        height: 20px;
        background: var(--white);
        border-radius: 50% 50% 0 0 / 100% 100% 0 0;
    }

    .header-inner {
        position: relative; z-index: 1;
        display: flex; align-items: center; gap: 18px;
    }

    .header-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        background: var(--accent);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 20px rgba(212,237,87,.4);
        animation: iconPop .5s cubic-bezier(.34,1.56,.64,1) .3s both;
    }

    @keyframes iconPop {
        from { transform: scale(0); opacity:0; }
        to   { transform: scale(1); opacity:1; }
    }

    .header-text {}
    .header-title {
        font-family: 'Sora', sans-serif;
        font-size: 22px; font-weight: 900;
        color: white;
        animation: fadeUp .4s ease .2s both;
    }

    .header-sub {
        font-size: 13px; color: rgba(255,255,255,.7);
        font-weight: 500; margin-top: 4px;
        animation: fadeUp .4s ease .3s both;
    }

    .header-badges {
        display: flex; gap: 8px; margin-top: 10px;
        animation: fadeUp .4s ease .4s both;
    }

    .hbadge {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 20px; padding: 4px 12px;
        font-size: 11px; font-weight: 700; color: white;
    }

    .hbadge.accent { background: var(--accent); border-color: transparent; color: var(--text); }

    /* ── body ── */
    .sched-body {
        padding: 32px 36px 36px;
        animation: fadeUp .45s ease .35s both;
    }

    @keyframes fadeUp {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* ── info row ── */
    .info-strip {
        display: flex;
        gap: 12px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .info-chip {
        display: flex; align-items: center; gap: 7px;
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        padding: 9px 14px;
        font-size: 12px; font-weight: 700; color: var(--text);
        transition: border-color .2s, transform .15s;
    }

    .info-chip:hover { border-color: var(--primary); transform: translateY(-1px); }
    .info-chip svg { color: var(--primary); flex-shrink: 0; }

    /* ── Meet link box ── */
    .meet-box {
        display: flex; align-items: center; justify-content: space-between;
        gap: 14px;
        background: #f0fdf4;
        border: 1.5px solid #bbf7d0;
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 28px;
    }

    .meet-box-left {
        display: flex; align-items: center; gap: 12px;
    }

    .meet-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: white;
        border: 1.5px solid #bbf7d0;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 20px;
    }

    .meet-label { font-size: 12px; color: #166534; font-weight: 700; }
    .meet-url   { font-size: 11px; color: #15803d; font-weight: 500; margin-top: 2px; word-break: break-all; }

    .meet-copy-btn {
        background: white;
        border: 1.5px solid #bbf7d0;
        border-radius: 10px;
        padding: 7px 14px;
        font-size: 12px; font-weight: 700;
        color: #166534;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        transition: background .15s;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .meet-copy-btn:hover { background: #dcfce7; }
    .meet-copy-btn.copied { color: var(--green); }

    /* ── calendar label ── */
    .cal-label {
        font-size: 13px; font-weight: 800; color: var(--text);
        margin-bottom: 14px;
        display: flex; align-items: center; gap: 8px;
    }

    .cal-label svg { color: var(--primary); }

    /* ══ CALENDAR UI ═══════════════════════════════════ */
    .cal-wrap {
        border: 1.5px solid var(--border);
        border-radius: 18px;
        overflow: hidden;
        background: var(--bg);
    }

    /* ── month header ── */
    .cal-nav {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px;
        background: var(--white);
        border-bottom: 1.5px solid var(--border);
    }

    .cal-nav-btn {
        width: 34px; height: 34px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        background: transparent; color: var(--primary);
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: background .15s, border-color .15s;
    }

    .cal-nav-btn:hover { background: var(--primary); color: white; border-color: var(--primary); }

    .cal-month-title {
        font-family: 'Sora', sans-serif;
        font-size: 15px; font-weight: 800; color: var(--text);
    }

    /* ── day names ── */
    .cal-days-header {
        display: grid; grid-template-columns: repeat(7, 1fr);
        background: var(--white);
        border-bottom: 1.5px solid var(--border);
    }

    .cal-day-name {
        text-align: center; padding: 10px 4px;
        font-size: 11px; font-weight: 800; color: var(--muted);
    }

    /* ── days grid ── */
    .cal-grid {
        display: grid; grid-template-columns: repeat(7, 1fr);
        gap: 4px; padding: 12px;
        background: var(--bg);
    }

    .cal-cell {
        aspect-ratio: 1;
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px;
        font-size: 13px; font-weight: 700; color: var(--text);
        cursor: pointer;
        transition: background .15s, color .15s, transform .1s;
        position: relative;
        border: 1.5px solid transparent;
        background: var(--white);
    }

    .cal-cell:hover:not(.empty):not(.past):not(.unavail) {
        background: var(--primary);
        color: white;
        transform: scale(1.05);
        border-color: var(--primary);
    }

    .cal-cell.empty  { background: transparent; cursor: default; }
    .cal-cell.past   { color: #d1d5db; background: transparent; cursor: not-allowed; }
    .cal-cell.unavail{ color: #d1d5db; background: transparent; cursor: not-allowed; text-decoration: line-through; }

    .cal-cell.today {
        border-color: var(--primary);
        color: var(--primary);
    }

    .cal-cell.selected {
        background: var(--accent) !important;
        color: var(--text) !important;
        border-color: var(--accent-dark, #b8d43a) !important;
        box-shadow: 0 4px 12px rgba(212,237,87,.45);
    }

    .cal-cell.has-slots::after {
        content: '';
        position: absolute; bottom: 4px; left: 50%; transform: translateX(-50%);
        width: 5px; height: 5px; border-radius: 50%;
        background: var(--green);
    }

    /* ── time slots ── */
    .slots-wrap {
        padding: 16px 20px;
        background: var(--white);
        border-top: 1.5px solid var(--border);
        display: none;
    }

    .slots-wrap.show { display: block; }

    .slots-title {
        font-size: 12px; font-weight: 800; color: var(--muted);
        margin-bottom: 12px; letter-spacing: .5px;
    }

    .slots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
        gap: 8px;
    }

    .slot-btn {
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 9px 6px;
        text-align: center;
        font-size: 13px; font-weight: 700;
        font-family: 'Cairo', sans-serif;
        color: var(--text);
        cursor: pointer;
        transition: all .15s;
    }

    .slot-btn:hover    { background: var(--primary); color: white; border-color: var(--primary); }
    .slot-btn.selected { background: var(--accent); color: var(--text); border-color: #b8d43a; }

    /* ── confirm section ── */
    .confirm-wrap {
        padding: 20px 20px 24px;
        background: var(--white);
        border-top: 1.5px solid var(--border);
        display: none;
    }

    .confirm-wrap.show { display: block; }

    .selected-summary {
        display: flex; align-items: center; gap: 12px;
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 16px;
    }

    .sum-icon {
        width: 38px; height: 38px;
        border-radius: 11px;
        background: var(--primary);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .sum-date { font-size: 14px; font-weight: 800; color: var(--text); }
    .sum-time { font-size: 12px; color: var(--muted); font-weight: 500; margin-top: 2px; }

    .confirm-btn {
        display: flex; align-items: center; justify-content: center; gap: 10px;
        width: 100%;
        background: var(--accent);
        color: var(--text);
        border: none; border-radius: 14px;
        padding: 15px 20px;
        font-size: 15px; font-weight: 900;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
        transition: background .15s, transform .1s;
        margin-bottom: 10px;
    }

    .confirm-btn:hover  { background: #c8e040; transform: translateY(-1px); }
    .confirm-btn:active { transform: scale(.98); }

    .skip-btn {
        display: block; width: 100%; text-align: center;
        background: none; border: none;
        font-family: 'Cairo', sans-serif;
        font-size: 12px; font-weight: 600;
        color: var(--muted); cursor: pointer;
        text-decoration: underline; text-underline-offset: 3px;
        transition: color .15s;
        padding: 6px;
    }

    .skip-btn:hover { color: var(--primary); }

    .booked-edit-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: transparent; color: var(--muted);
        border: 1.5px solid var(--border); border-radius: 14px;
        padding: 10px 24px;
        font-size: 13px; font-weight: 700;
        font-family: 'Cairo', sans-serif;
        cursor: pointer; text-decoration: none;
        transition: all .15s;
        width: 100%; max-width: 320px;
        justify-content: center;
    }

    .booked-edit-btn:hover {
        color: var(--primary);
        border-color: var(--primary);
        background: #EFF5FF;
    }

    /* ══ BOOKED STATE ══════════════════════════════════ */
    .booked-state {
        display: none;
        flex-direction: column; align-items: center; text-align: center;
        padding: 48px 32px; gap: 16px;
    }

    .booked-state.show { display: flex; }

    .booked-icon {
        width: 72px; height: 72px; border-radius: 50%;
        background: var(--accent);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 24px rgba(212,237,87,.4);
        animation: iconPop .5s cubic-bezier(.34,1.56,.64,1) both;
    }

    .booked-title {
        font-family: 'Sora', sans-serif;
        font-size: 20px; font-weight: 900; color: var(--text);
    }

    .booked-detail {
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: 14px;
        padding: 14px 24px;
        font-size: 14px; font-weight: 700; color: var(--primary);
        display: flex; align-items: center; gap: 8px;
    }

    .booked-sub {
        font-size: 13px; color: var(--muted); font-weight: 500; line-height: 1.7;
        max-width: 360px;
    }

    .booked-meet-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: var(--primary); color: white;
        border: none; border-radius: 14px;
        padding: 14px 28px;
        font-size: 14px; font-weight: 800;
        font-family: 'Cairo', sans-serif;
        cursor: pointer; text-decoration: none;
        transition: background .15s, transform .1s;
        width: 100%; max-width: 320px;
        justify-content: center;
        margin-top: 4px;
    }

    .booked-meet-btn:hover { background: var(--primary-dark, #0f3a87); transform: translateY(-1px); }

    .dash-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: transparent; color: var(--primary);
        border: 1.5px solid var(--primary); border-radius: 14px;
        padding: 12px 28px;
        font-size: 13px; font-weight: 700;
        font-family: 'Cairo', sans-serif;
        cursor: pointer; text-decoration: none;
        transition: background .15s;
        width: 100%; max-width: 320px;
        justify-content: center;
    }

    .dash-btn:hover { background: #EFF5FF; }

    /* ── toast ── */
    .toast {
        position: fixed; bottom: 28px; left: 50%;
        transform: translateX(-50%) translateY(16px);
        background: var(--text); color: white;
        font-size: 13px; font-weight: 700;
        padding: 10px 22px; border-radius: 20px;
        box-shadow: 0 8px 24px rgba(0,0,0,.15);
        opacity: 0; pointer-events: none; z-index: 50;
        transition: opacity .2s, transform .2s;
        white-space: nowrap;
    }

    .toast.show { opacity:1; transform: translateX(-50%) translateY(0); }

    @media (max-width: 540px) {
        .sched-body { padding: 24px 20px 28px; }
        .sched-header { padding: 24px 22px 22px; }
        .info-strip { gap: 8px; }
        .info-chip  { padding: 7px 11px; font-size: 11px; }
        .header-badges { flex-wrap: wrap; }
    }
</style>
@endsection


@section('content')

@php
    $meetLink = config('app.meet_link', 'https://meet.google.com/xxx-xxxx-xxx');
    $meetShort = parse_url($meetLink, PHP_URL_HOST) . parse_url($meetLink, PHP_URL_PATH);

    // Available time slots per day (can be moved to config/db later)
    $timeSlots = ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

    // Days off (0=Sunday, 5=Friday, 6=Saturday)
    $daysOff = [5, 6];
@endphp

@php
    $hasBooking = isset($booking) && $booking;
    $bookingId = $hasBooking ? $booking->id : null;
    $locale = app()->getLocale();
@endphp

<x-web.navbar :transparent="false" />

<div class="blob b1"></div>
<div class="blob b2"></div>

<div class="sched-page">
<div class="sched-card" id="schedCard">

    {{-- ── MAIN VIEW ────────────────────────────── --}}
    <div id="mainView" style="{{ $hasBooking ? 'display: none;' : '' }}">

        {{-- Header --}}
        <div class="sched-header">
            <div class="header-inner">
                <div class="header-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                         stroke="#1C1C1C" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div class="header-text">
                    <div class="header-title font-arabic">{{ __('messages.schedule_meeting.header_title') }}</div>
                    <div class="header-sub">{{ __('messages.schedule_meeting.header_sub') }}</div>
                    <div class="header-badges">
                        <span class="hbadge accent">{{ __('messages.schedule_meeting.badge_free') }}</span>
                        <span class="hbadge">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ __('messages.schedule_meeting.badge_timezone') }}
                        </span>
                        <span class="hbadge">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.07 8.67 19.79 19.79 0 0 1 .01 2.11 2 2 0 0 1 2 0h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L6.91 7.91"/></svg>
                            {{ __('messages.schedule_meeting.badge_online') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="sched-body">

            {{-- Meet link (commented out) --}}
            {{-- <div class="meet-box"> ... </div> --}}

            {{-- Calendar label --}}
            <div class="cal-label">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                {{ __('messages.schedule_meeting.cal_label') }}
            </div>

            {{-- Calendar --}}
            <div class="cal-wrap">

                {{-- Month nav --}}
                <div class="cal-nav">
                    <button class="cal-nav-btn" onclick="prevMonth()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                    <span class="cal-month-title font-arabic" id="calMonthTitle"></span>
                    <button class="cal-nav-btn" onclick="nextMonth()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>
                </div>

                {{-- Day names --}}
                <div class="cal-days-header">
                    @foreach(__('messages.schedule_meeting.day_names') as $d)
                        <div class="cal-day-name">{{ $d }}</div>
                    @endforeach
                </div>

                {{-- Days grid --}}
                <div class="cal-grid" id="calGrid"></div>

                {{-- Time slots --}}
                <div class="slots-wrap" id="slotsWrap">
                    <div class="slots-title" id="slotsTitle">{{ __('messages.schedule_meeting.slots_available') }}</div>
                    <div class="slots-grid" id="slotsGrid"></div>
                </div>

                {{-- Confirm --}}
                <div class="confirm-wrap" id="confirmWrap">
                    <div class="selected-summary">
                        <div class="sum-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sum-date" id="sumDate">—</div>
                            <div class="sum-time" id="sumTime">—</div>
                        </div>
                    </div>

                    <button class="confirm-btn" onclick="confirmBooking()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        {{ __('messages.schedule_meeting.confirm_btn') }}
                    </button>
                </div>

            </div>{{-- /cal-wrap --}}
        </div>{{-- /sched-body --}}
    </div>{{-- /mainView --}}

    {{-- ── BOOKED STATE ──────────────────────────── --}}
    <div class="booked-state {{ $hasBooking ? 'show' : '' }}" id="bookedState">
        <div class="booked-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1C1C1C" stroke-width="3"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
        <div class="booked-title font-arabic">{{ __('messages.schedule_meeting.booked_title') }}</div>
        <div class="booked-detail" id="bookedDetail">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary)">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="bookedDetailText">
                @if($hasBooking)
                    {{ \Carbon\Carbon::parse($booking->meeting_date)->locale($locale)->translatedFormat(__('messages.schedule_meeting.booked_date_format')) }}
                    — {{ __('messages.schedule_meeting.time_at') }} {{ \Carbon\Carbon::parse($booking->meeting_time)->format('H:i') }}
                @else
                    —
                @endif
            </span>
        </div>
        <div class="booked-sub">
            {{ __('messages.schedule_meeting.booked_sub') }}
        </div>

        <button onclick="editBooking()" class="booked-edit-btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            {{ __('messages.schedule_meeting.edit_btn') }}
        </button>

        <a href="{{ url('/dashboard') }}" class="dash-btn">
            {{ __('messages.schedule_meeting.go_dashboard') }}
        </a>
    </div>

</div>{{-- /sched-card --}}
</div>{{-- /sched-page --}}

<div class="toast" id="toast"></div>

<x-web.footer :hidden="false" />
@endsection


@section('script')
<script>
// ── Config ─────────────────────────────────────────────────────
const DAYS_OFF   = @json($daysOff);
const TIME_SLOTS = @json($timeSlots);
const MEET_LINK  = @json($meetLink);
const CSRF       = '{{ csrf_token() }}';

const SCHED_TRANS = {
    months:           @json(__('messages.schedule_meeting.months')),
    days:             @json(__('messages.schedule_meeting.day_names')),
    slots_for:        @json(__('messages.schedule_meeting.slots_for')),
    sum_date_format:  @json(__('messages.schedule_meeting.sum_date_format')),
    sum_time_format:  @json(__('messages.schedule_meeting.sum_time_format')),
    detail_format:    @json(__('messages.schedule_meeting.detail_format')),
    error_booking:    @json(__('messages.schedule_meeting.error_booking')),
    error_server:     @json(__('messages.schedule_meeting.error_server')),
    toast_booked:     @json(__('messages.schedule_meeting.toast_booked')),
    toast_edited:     @json(__('messages.schedule_meeting.toast_edited')),
    toast_link_copied:@json(__('messages.schedule_meeting.toast_link_copied')),
    copy_link_btn:    @json(__('messages.schedule_meeting.copy_link_btn')),
    copied_btn:       @json(__('messages.schedule_meeting.copied_btn')),
};

function formatStr(template, vars) {
    return template.replace(/:(\w+)/g, (_, k) => vars[k] ?? '');
}

let state = {
    today: new Date(),
    current: new Date(),
    selectedDate: null,
    selectedTime: null,
};

const MIN_MONTH = new Date(state.today.getFullYear(), state.today.getMonth(), 1);
const MAX_MONTH = new Date(state.today.getFullYear(), state.today.getMonth() + 1, 1);

// ── Helpers ────────────────────────────────────────────────────
function isSameMonth(date1, date2) {
    return date1.getFullYear() === date2.getFullYear()
        && date1.getMonth() === date2.getMonth();
}

// ── Render Calendar ────────────────────────────────────────────
function renderCalendar() {
    const { current, today, selectedDate } = state;
    const year  = current.getFullYear();
    const month = current.getMonth();

    document.getElementById('calMonthTitle').textContent =
        SCHED_TRANS.months[month] + ' ' + year;

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const grid = document.getElementById('calGrid');
    grid.innerHTML = '';

    const todayOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());

    // empty cells before first day
    for (let i = 0; i < firstDay; i++) {
        const cell = document.createElement('div');
        cell.className = 'cal-cell empty';
        grid.appendChild(cell);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const date = new Date(year, month, d);
        const dayOfWeek = date.getDay();
        const cellDateOnly = new Date(year, month, d);

        const isPast = cellDateOnly < todayOnly;
        const isToday = cellDateOnly.toDateString() === todayOnly.toDateString();
        const isUnavail = DAYS_OFF.includes(dayOfWeek);
        const isSel = selectedDate && cellDateOnly.toDateString() === selectedDate.toDateString();
        const hasSlots = !isPast && !isUnavail;

        let cls = 'cal-cell';
        if (isPast) cls += ' past';
        else if (isUnavail) cls += ' unavail';
        else if (hasSlots) cls += ' has-slots';
        if (isToday) cls += ' today';
        if (isSel) cls += ' selected';

        const cell = document.createElement('div');
        cell.className = cls;
        cell.textContent = d;

        if (!isPast && !isUnavail) {
            cell.onclick = () => selectDate(cellDateOnly);
        }

        grid.appendChild(cell);
    }

    const prevBtn = document.querySelector('.cal-nav button:first-child');
    const nextBtn = document.querySelector('.cal-nav button:last-child');

    const isAtMinMonth = isSameMonth(state.current, MIN_MONTH);
    const isAtMaxMonth = isSameMonth(state.current, MAX_MONTH);

    prevBtn.disabled = isAtMinMonth;
    nextBtn.disabled = isAtMaxMonth;

    prevBtn.style.opacity = isAtMinMonth ? '0.45' : '1';
    nextBtn.style.opacity = isAtMaxMonth ? '0.45' : '1';

    prevBtn.style.cursor = isAtMinMonth ? 'not-allowed' : 'pointer';
    nextBtn.style.cursor = isAtMaxMonth ? 'not-allowed' : 'pointer';
}

// ── Select Date ────────────────────────────────────────────────
function selectDate(date) {
    state.selectedDate = date;
    state.selectedTime = null;

    renderCalendar();
    renderSlots(date);

    document.getElementById('confirmWrap').classList.remove('show');
}

// ── Render Time Slots ──────────────────────────────────────────
function renderSlots(date) {
    const wrap  = document.getElementById('slotsWrap');
    const grid  = document.getElementById('slotsGrid');
    const title = document.getElementById('slotsTitle');

    title.textContent = formatStr(SCHED_TRANS.slots_for, {
        day:   SCHED_TRANS.days[date.getDay()],
        date:  date.getDate(),
        month: SCHED_TRANS.months[date.getMonth()],
    });

    grid.innerHTML = '';

    TIME_SLOTS.forEach(slot => {
        const btn = document.createElement('button');
        btn.className = 'slot-btn';
        btn.textContent = slot;
        btn.onclick = () => selectSlot(slot, btn);
        grid.appendChild(btn);
    });

    wrap.classList.add('show');
    wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ── Select Slot ────────────────────────────────────────────────
function selectSlot(time, btn) {
    state.selectedTime = time;

    document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    const date = state.selectedDate;
    document.getElementById('sumDate').textContent = formatStr(SCHED_TRANS.sum_date_format, {
        day:   SCHED_TRANS.days[date.getDay()],
        date:  date.getDate(),
        month: SCHED_TRANS.months[date.getMonth()],
        year:  date.getFullYear(),
    });
    document.getElementById('sumTime').textContent = formatStr(SCHED_TRANS.sum_time_format, { time });

    document.getElementById('confirmWrap').classList.add('show');
    document.getElementById('confirmWrap').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ── mode: 'create' | 'edit' ────────────────────────────────
let bookingMode = '{{ $hasBooking ? "edit" : "create" }}';
let currentBookingId = {{ $bookingId ?? 'null' }};

// ── Confirm Booking (create or update) ────────────────────
async function confirmBooking() {
    if (!state.selectedDate || !state.selectedTime) return;

    const date   = state.selectedDate;
    const detail = formatStr(SCHED_TRANS.detail_format, {
        day:   SCHED_TRANS.days[date.getDay()],
        date:  date.getDate(),
        month: SCHED_TRANS.months[date.getMonth()],
        year:  date.getFullYear(),
        time:  state.selectedTime,
    });

    const isEdit = bookingMode === 'edit' && currentBookingId;
    const url    = isEdit
        ? `/booking/${currentBookingId}`
        : '{{ route("booking.store") }}';
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                subscription_id: {{ $subscription->id }},
                date: `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`,
                time: state.selectedTime,
                meet_link: MEET_LINK,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            showToast(data.message || SCHED_TRANS.error_booking);
            return;
        }

        if (data.booking) {
            currentBookingId = data.booking.id;
            bookingMode = 'edit';
        }

    } catch (e) {
        showToast(SCHED_TRANS.error_server);
        return;
    }

    document.getElementById('bookedDetailText').textContent = detail;
    document.getElementById('mainView').style.display = 'none';
    document.getElementById('bookedState').classList.add('show');

    showToast(bookingMode === 'edit' ? SCHED_TRANS.toast_edited : SCHED_TRANS.toast_booked);
}

// ── Edit Booking — go back to calendar ─────────────────────
function editBooking() {
    bookingMode = 'edit';

    document.getElementById('bookedState').classList.remove('show');
    document.getElementById('mainView').style.display = '';

    state.selectedDate = null;
    state.selectedTime = null;
    document.getElementById('slotsWrap').classList.remove('show');
    document.getElementById('confirmWrap').classList.remove('show');

    renderCalendar();

    document.getElementById('schedCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Month nav ──────────────────────────────────────────────────
function prevMonth() {
    if (isSameMonth(state.current, MIN_MONTH)) return;

    const d = state.current;
    const prev = new Date(d.getFullYear(), d.getMonth() - 1, 1);

    state.current = prev < MIN_MONTH ? new Date(MIN_MONTH) : prev;
    state.selectedDate = null;
    state.selectedTime = null;

    document.getElementById('slotsWrap').classList.remove('show');
    document.getElementById('confirmWrap').classList.remove('show');

    renderCalendar();
}

function nextMonth() {
    if (isSameMonth(state.current, MAX_MONTH)) return;

    const d = state.current;
    const next = new Date(d.getFullYear(), d.getMonth() + 1, 1);

    state.current = next > MAX_MONTH ? new Date(MAX_MONTH) : next;
    state.selectedDate = null;
    state.selectedTime = null;

    document.getElementById('slotsWrap').classList.remove('show');
    document.getElementById('confirmWrap').classList.remove('show');

    renderCalendar();
}

// ── Copy Meet Link ─────────────────────────────────────────────
function copyMeet() {
    navigator.clipboard.writeText(MEET_LINK).then(() => {
        const btn = document.getElementById('copyMeetBtn');
        if (!btn) return;

        btn.textContent = SCHED_TRANS.copied_btn;
        btn.classList.add('copied');
        showToast(SCHED_TRANS.toast_link_copied);

        setTimeout(() => {
            btn.textContent = SCHED_TRANS.copy_link_btn;
            btn.classList.remove('copied');
        }, 2500);
    });
}

// ── Skip ───────────────────────────────────────────────────────
function skipBooking() {
    window.location.href = '{{ url('/dashboard') }}';
}

// ── Toast ──────────────────────────────────────────────────────
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2400);
}

// ── Init ───────────────────────────────────────────────────────
renderCalendar();
</script>
@endsection

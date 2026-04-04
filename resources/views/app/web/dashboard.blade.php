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
        COACH FEEDBACK
        ══════════════════════════════════════ */
        .feedback-bubble {
            background: #EFF5FF;
            border-radius: 0 18px 18px 18px;
            padding: 1rem 1.2rem;
            position: relative;
            font-family: 'Cairo', sans-serif;
        }
        .feedback-bubble::before {
            content: '';
            position: absolute;
            top: 0; right: -10px;
            border-width: 0 0 12px 12px;
            border-style: solid;
            border-color: transparent transparent #EFF5FF transparent;
        }

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
        WEIGHT CHART (sparkline)
        ══════════════════════════════════════ */
        .spark-line {
            fill: none;
            stroke: #174DAD;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .spark-area {
            fill: url(#sparkGrad);
            opacity: .18;
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

{{-- ══════════════ DATA (mock — replace with real Eloquent) ══════════════ --}}
@php
    $user = auth()->user();

    $plan = [
        'name'        => 'برو',
        'icon'        => 'star',
        'color'       => 'text-primary',
        'bg'          => 'bg-[#EFF5FF]',
        'expires'     => '15 مايو 2025',
        'daysLeft'    => 14,
    ];

    $progress = [
        'startWeight'   => 98,
        'currentWeight' => 83,
        'goalWeight'    => 75,
        'startDate'     => '1 يناير 2025',
        'weeksDone'     => 12,
        'totalWeeks'    => 16,
        'pct'           => 75,     // percentage of journey
        'streak'        => 9,      // days streak
    ];

    $weekDays = [
        ['label'=>'أح', 'status'=>'done'],
        ['label'=>'إث', 'status'=>'done'],
        ['label'=>'ث',  'status'=>'done'],
        ['label'=>'أر', 'status'=>'done'],
        ['label'=>'خ',  'status'=>'today'],
        ['label'=>'ج',  'status'=>'upcoming'],
        ['label'=>'س',  'status'=>'rest'],
    ];

    $todayWorkout = [
        'title'     => 'تدريب الأيزو + الكارديو',
        'duration'  => '55 دقيقة',
        'exercises' => 8,
        'done'      => false,
    ];

    $macros = [
        'calories' => ['target'=>2100, 'done'=>1640],
        'protein'  => ['target'=>180,  'done'=>132, 'color'=>'bg-primary'],
        'carbs'    => ['target'=>220,  'done'=>180, 'color'=>'bg-accent'],
        'fat'      => ['target'=>65,   'done'=>48,  'color'=>'bg-amber-400'],
    ];

    $achievements = [
        ['icon'=>'🔥', 'label'=>'أول أسبوع',    'earned'=>true ],
        ['icon'=>'💪', 'label'=>'أول شهر',      'earned'=>true ],
        ['icon'=>'⚡', 'label'=>'9 أيام متتالية','earned'=>true ],
        ['icon'=>'🏆', 'label'=>'10 كيلو',      'earned'=>false],
        ['icon'=>'🎯', 'label'=>'الهدف النهائي','earned'=>false],
        ['icon'=>'👑', 'label'=>'3 أشهر',        'earned'=>false],
    ];

    $coachNote = [
        'coach'   => 'كابتن محمود',
        'avatar'  => asset('assets/imgs/t1.png'),
        'time'    => 'اليوم، 9:30 ص',
        'rating'  => 4,
        'message' => 'أداء ممتاز اليوم أحمد! 💪 لاحظت إنك رفعت الأوزان في تمرين الضغط — ده تقدم حقيقي. بس خلي بالك من التنفس في تمرين الديدليفت، وحاول تنام 7 ساعات دلوقتي في الفترة دي.',
    ];

    $weightHistory = [98, 96, 94, 91, 89, 87, 86, 85, 84, 83.5, 83];
@endphp

<div class="dash-wrap" x-data="{ sideOpen: false }">

    {{-- ══════════════ SIDEBAR ══════════════ --}}
    <aside class="dash-sidebar" :class="{ open: sideOpen }">

        {{-- Logo --}}
        <div class="flex items-center justify-between mb-8 px-1">
            <span class="font-display text-white text-xl font-black">
                MindFit<span class="text-accent">Bro</span>
            </span>
            <button class="lg:hidden text-white/50 hover:text-white"
                @click="sideOpen = false">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        {{-- Avatar --}}
        <div class="flex items-center gap-3 px-1 mb-8 pb-6 border-b border-white/10">
            <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center font-black text-textColor text-sm flex-shrink-0">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <div>
                <p class="text-white font-black text-sm leading-none">{{ $user->name }}</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-accent/20 text-accent mt-1 inline-block">
                    {{ $plan['name'] }}
                </span>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex flex-col gap-1 flex-1">
            <a href="#overview" class="nav-item active">
                <span class="material-symbols-rounded nav-icon" style="font-size:20px;font-variation-settings:'FILL' 1">dashboard</span>
                نظرة عامة
            </a>
            <a href="#workout" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-size:20px">fitness_center</span>
                تمريناتي
            </a>
            <a href="#nutrition" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-size:20px">nutrition</span>
                التغذية
            </a>
            <a href="#coach" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-size:20px">chat</span>
                الكوتش
            </a>
            <a href="#progress" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-size:20px">trending_up</span>
                التقدم
            </a>

            <div class="mt-4 mb-2 px-3">
                <p class="text-[10px] font-bold text-white/25 tracking-widest uppercase">الحساب</p>
            </div>

            <a href="#" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-size:20px">settings</span>
                الإعدادات
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item w-full text-right">
                    <span class="material-symbols-rounded nav-icon" style="font-size:20px">logout</span>
                    تسجيل الخروج
                </button>
            </form>
        </nav>

        {{-- Plan badge --}}
        <div class="mt-auto pt-4 border-t border-white/10">
            <div class="rounded-2xl bg-accent/10 border border-accent/20 p-3 font-arabic text-right">
                <p class="text-accent text-xs font-black mb-0.5">باقة {{ $plan['name'] }}</p>
                <p class="text-white/50 text-[10px]">تنتهي {{ $plan['expires'] }}</p>
                <a href="#" class="mt-2 text-[10px] font-black text-accent hover:underline block">ترقية الباقة ←</a>
            </div>
        </div>

    </aside>

    {{-- ══════════════ MAIN CONTENT ══════════════ --}}
    <main class="flex flex-col gap-5 p-5 lg:p-8 overflow-y-auto">

        {{-- ─ Topbar ─ --}}
        <div class="flex items-center justify-between">
            <div class="font-arabic">
                <p class="text-gray-400 text-sm mb-3 font-bold">
                    {{ now()->isoFormat('dddd، D MMMM Y') }}
                </p>
                <h1 class="font-display text-2xl lg:text-3xl text-textColor font-black">
                    أهلاً، {{ explode(' ', $user->name)[0] }} 👋
                </h1>
            </div>

            <div class="flex items-center gap-3">
                {{-- Streak chip --}}
                <div class="hidden md:flex items-center gap-2 bg-white rounded-full px-4 py-2 border border-gray-100 shadow-sm font-arabic">
                    <span class="text-lg">🔥</span>
                    <span class="font-black text-sm text-textColor">{{ $progress['streak'] }} أيام</span>
                    <span class="text-xs text-gray-400 font-bold">متتالية</span>
                </div>

                {{-- Mobile menu --}}
                <button class="lg:hidden w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center"
                    @click="sideOpen = true">
                    <span class="material-symbols-rounded text-textColor" style="font-size:20px">menu</span>
                </button>
            </div>
        </div>

        {{-- ══ ROW 1: Hero Progress Card + Plan + Streak ══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ── Journey Progress (dark card) ── --}}
            <div class="card-dark lg:col-span-2 anim anim-1" id="overview">
                <div class="relative z-10 flex flex-col lg:flex-row items-center gap-6">

                    {{-- Ring --}}
                    <div class="relative flex-shrink-0">
                        <svg width="130" height="130" viewBox="0 0 130 130">
                            <circle cx="65" cy="65" r="58" class="ring-bg"/>
                            <circle cx="65" cy="65" r="58" class="ring-fill" id="journeyRing"
                                style="stroke-dashoffset: {{ 408 - (408 * $progress['pct'] / 100) }}"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center font-arabic">
                            <span class="font-display text-3xl font-black text-white leading-none">{{ $progress['pct'] }}%</span>
                            <span class="text-white/50 text-[10px] font-bold">اكتمل</span>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 font-arabic text-center lg:text-right">
                        <span class="inline-block bg-accent/20 text-accent text-[10px] font-black px-3 py-1 rounded-full mb-2">
                            رحلتك جارية 🚀
                        </span>
                        <h2 class="text-white font-black text-xl mb-1">
                            أسبوع {{ $progress['weeksDone'] }} من {{ $progress['totalWeeks'] }}
                        </h2>
                        <p class="text-white/60 text-sm mb-4">
                            بدأت بـ {{ $progress['startWeight'] }} كجم — هدفك {{ $progress['goalWeight'] }} كجم
                        </p>

                        {{-- Weight delta --}}
                        <div class="flex items-center justify-center lg:justify-start gap-4">
                            <div class="text-center">
                                <p class="text-white/40 text-[10px] font-bold mb-0.5">وزن البداية</p>
                                <p class="text-white font-black font-display text-xl">{{ $progress['startWeight'] }}<span class="text-xs text-white/40 font-bold"> كجم</span></p>
                            </div>
                            <div class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-accent/20 border border-accent/30">
                                <span class="material-symbols-rounded text-accent" style="font-size:14px">trending_down</span>
                                <span class="text-accent font-black text-sm">{{ $progress['startWeight'] - $progress['currentWeight'] }} كجم</span>
                            </div>
                            <div class="text-center">
                                <p class="text-white/40 text-[10px] font-bold mb-0.5">الوزن الحالي</p>
                                <p class="text-accent font-black font-display text-xl">{{ $progress['currentWeight'] }}<span class="text-xs text-accent/60 font-bold"> كجم</span></p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Week strip --}}
                <div class="relative z-10 mt-6 pt-5 border-t border-white/10">
                    <p class="text-white/40 text-[10px] font-bold font-arabic mb-3">أيام الأسبوع</p>
                    <div class="flex items-center justify-between gap-2">
                        @foreach($weekDays as $d)
                        <div class="day-dot {{ $d['status'] === 'done' ? 'day-done' : ($d['status'] === 'today' ? 'day-today' : ($d['status'] === 'rest' ? 'day-rest' : 'day-upcoming')) }}"
                            title="{{ $d['status'] === 'rest' ? 'راحة' : '' }}">
                            @if($d['status'] === 'done')
                                <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1">check</span>
                            @elseif($d['status'] === 'rest')
                                <span class="material-symbols-rounded" style="font-size:13px">hotel</span>
                            @else
                                {{ $d['label'] }}
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── Plan & Quick Stats ── --}}
            <div class="flex flex-col gap-5">

                {{-- Plan card --}}
                <div class="card anim anim-2 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black text-gray-400 font-arabic">باقتك الحالية</span>
                        <a href="#" class="text-[11px] font-black text-primary font-arabic hover:underline">ترقية</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl {{ $plan['bg'] }} flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-rounded {{ $plan['color'] }}" style="font-size:24px;font-variation-settings:'FILL' 1">{{ $plan['icon'] }}</span>
                        </div>
                        <div class="font-arabic">
                            <p class="font-black text-textColor text-lg leading-none">باقة {{ $plan['name'] }}</p>
                            <p class="text-gray-400 text-xs mt-0.5">تنتهي {{ $plan['expires'] }}</p>
                        </div>
                    </div>
                    {{-- Days left bar --}}
                    <div>
                        <div class="flex justify-between text-[11px] font-bold font-arabic text-gray-400 mb-1.5">
                            <span>{{ $plan['daysLeft'] }} يوم متبقي</span>
                            <span>30 يوم</span>
                        </div>
                        <div class="macro-bar-wrap">
                            <div class="macro-bar-fill bg-primary" style="width:{{ ($plan['daysLeft'] / 30) * 100 }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Streak card --}}
                <div class="card anim anim-3 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0 text-3xl">🔥</div>
                    <div class="font-arabic">
                        <p class="text-gray-400 text-xs font-bold mb-0.5">أيام متتالية</p>
                        <p class="font-display text-3xl font-black text-textColor leading-none">{{ $progress['streak'] }}</p>
                        <p class="text-green-500 text-[11px] font-bold mt-0.5">استمر! أنت رائع 💪</p>
                    </div>
                </div>

            </div>

        </div>

        {{-- ══ ROW 2: Coach Feedback + Achievements ══ --}}
        <div class="flex flex-col gap-5">
            {{-- Coach Feedback --}}
            <div class="card anim anim-5">
                <div class="flex items-center justify-between mb-5 font-arabic">
                    <span class="text-xs font-black text-gray-400">{{ $coachNote['time'] }}</span>
                    <p class="font-black text-textColor text-base">ملاحظات الكوتش</p>
                </div>

                {{-- Coach info --}}
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ $coachNote['avatar'] }}"
                        alt="{{ $coachNote['coach'] }}"
                        class="w-11 h-11 rounded-full object-cover border-2 border-primary/20 flex-shrink-0">
                    <div class="font-arabic text-right flex-1">
                        <p class="font-black text-textColor text-sm">{{ $coachNote['coach'] }}</p>
                        <div class="flex gap-0.5 justify-end mt-0.5">
                            @for($i = 0; $i < $coachNote['rating']; $i++)
                                <span class="material-symbols-rounded text-amber-400" style="font-size:13px;font-variation-settings:'FILL' 1">star</span>
                            @endfor
                            @for($i = $coachNote['rating']; $i < 5; $i++)
                                <span class="material-symbols-rounded text-gray-200" style="font-size:13px;font-variation-settings:'FILL' 1">star</span>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Bubble --}}
                <div class="feedback-bubble font-arabic text-right mb-5">
                    <p class="text-sm text-textColor leading-relaxed">{{ $coachNote['message'] }}</p>
                </div>

                {{-- Today Rating --}}
                <div class="flex items-center justify-between p-3 rounded-2xl bg-[#F0F4FB] font-arabic">
                    <div class="flex gap-1.5">
                        @foreach(['😴','😐','😊','💪','🔥'] as $emoji)
                        <button class="w-9 h-9 rounded-xl text-lg hover:scale-110 transition bg-white shadow-sm">{{ $emoji }}</button>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 font-bold">إزاي حسيت النهارده؟</p>
                </div>
            </div>
        </div>

    </main>

</div>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // ─ Animate journey ring on load ─
        const ring = document.getElementById('journeyRing');
        if (ring) {
            const target = parseFloat(ring.style.strokeDashoffset);
            ring.style.strokeDashoffset = 408;
            setTimeout(() => { ring.style.strokeDashoffset = target; }, 200);
        }

        // ─ Animate macro bars ─
        document.querySelectorAll('.macro-bar-fill').forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => { bar.style.width = w; }, 300);
        });

        // ─ Active nav on scroll ─
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
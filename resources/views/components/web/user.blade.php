@props(['subscription' => null, 'progress' => []])

@php
    $user = auth()->user();

    $hasSub     = $subscription !== null && in_array($subscription->status, ['active', 'waiting']);
    $isWaiting  = $hasSub && $subscription->status === 'waiting';
    $booking    = $hasSub ? $subscription->meetingBookings->first() : null;
    $hasBooking = $booking !== null;
    $plan       = $hasSub ? $subscription->plan : null;

    $daysLeft = ($hasSub && $subscription->end_date)
                ? (int) now()->diffInDays($subscription->end_date, false)
                : 0;

    $totalDays = ($hasSub && $subscription->start_date && $subscription->end_date)
                 ? (int) $subscription->start_date->diffInDays($subscription->end_date)
                 : 1;

    $daysUsed = ($hasSub && $subscription->start_date)
                ? (int) $subscription->start_date->diffInDays(now())
                : 0;

    $subPct = $totalDays > 0 ? min(100, (int) round(($daysUsed / $totalDays) * 100)) : 0;

    $startsInFuture = $hasSub && !$isWaiting
                  && $subscription->start_date
                  && $subscription->start_date->isFuture();

    // ── Progress من الداتا بيز ──
    $startWeight   = $progress['startWeight']   ?? 0;
    $currentWeight = $progress['currentWeight'] ?? 0;
    $goalWeight    = $progress['goalWeight']    ?? 0;
    $weeksDone     = $progress['weeksDone']     ?? 0;
    $totalWeeks    = $progress['totalWeeks']    ?? 0;
    $pct           = $progress['pct']           ?? 0;
    $streak        = $progress['streak']        ?? 0;
    $weekDays      = $progress['weekDays']      ?? [];
@endphp

<div class="dash-wrap" x-data="{ sideOpen: false }">

    {{-- ══════════════ SIDEBAR ══════════════ --}}
    <aside class="dash-sidebar" :class="{ open: sideOpen }">

        {{-- Logo --}}
        <div class="flex items-center justify-between mb-8 px-1">
            <img src="{{ asset('assets/logo/mindfitbro.png') }}" class="w-32" alt="MindFitBro Logo">
            <button class="lg:hidden text-white/50 hover:text-white z-50" @click="sideOpen = false">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        {{-- Avatar --}}
        <div class="flex items-center gap-3 px-1 mb-8 pb-6 border-b border-white/10">
            <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center font-black text-textColor font-arabic text-sm flex-shrink-0">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <div>
                <p class="text-white font-black text-lg font-display leading-none">{{ $user->name }}</p>
                @if($hasSub)
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full mt-1 inline-block font-arabic
                        {{ $isWaiting ? 'bg-amber-400/20 text-amber-300' : 'bg-accent/20 text-accent' }}">
                        {{ $isWaiting ? '⏳ ' : '' }}{{ $plan->name }}
                    </span>
                @else
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/10 text-white/40 mt-1 inline-block font-arabic">
                        بدون باقة
                    </span>
                @endif
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex flex-col gap-1 flex-1">
            <a href="{{ route('home') }}" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">home</span>
                الصفحة الرئيسية
            </a>
            <a href="#overview" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">dashboard</span>
                نظرة عامة
            </a>
            <a href="#" class="nav-item">
                <span class="material-symbols-rounded nav-icon" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">settings</span>
                الإعدادات
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item w-full text-right">
                    <span class="material-symbols-rounded nav-icon" style="font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">logout</span>
                    تسجيل الخروج
                </button>
            </form>
        </nav>

        {{-- Plan badge in sidebar --}}
        <div class="mt-auto pt-4 border-t border-white/10">
            @if($hasSub)
                <div class="rounded-2xl p-3 font-arabic text-right
                    {{ $isWaiting ? 'bg-amber-400/10 border border-amber-400/20' : 'bg-accent/10 border border-accent/20' }}">
                    <p class="{{ $isWaiting ? 'text-amber-300' : 'text-accent' }} text-xs font-black mb-0.5">
                        {{ $isWaiting ? '⏳ في انتظار التفعيل' : 'باقة ' . $plan->name }}
                    </p>
                    <p class="text-white/50 text-[10px]">
                        @if($isWaiting)
                            احجز موعدك لتفعيل الباقة
                        @else
                            تنتهي {{ $subscription->end_date ? $subscription->end_date->locale('ar')->isoFormat('D MMMM YYYY') : '—' }}
                        @endif
                    </p>
                    @if($isWaiting)
                        <a href="{{ route('booking.show', $subscription->id) }}"
                           class="mt-2 text-[10px] font-black text-amber-300 hover:underline block">
                            احجز الجلسة الآن ←
                        </a>
                    @else
                        <a href="#" class="mt-2 text-[10px] font-black text-accent hover:underline block">ترقية الباقة ←</a>
                    @endif
                </div>
            @else
                <div class="rounded-2xl bg-white/5 border border-white/10 p-3 font-arabic text-right">
                    <p class="text-white/50 text-xs font-black mb-0.5">مش مشترك</p>
                    <a href="{{ route('home') }}#programs"
                       class="mt-2 text-[10px] font-black text-accent hover:underline block">
                        اشترك دلوقتي ←
                    </a>
                </div>
            @endif
        </div>

    </aside>

    {{-- ══════════════ MAIN CONTENT ══════════════ --}}
    <main class="flex flex-col gap-5 p-5 lg:p-8 overflow-y-auto">

        {{-- ─ Topbar ─ --}}
        <div class="flex items-center justify-between">
            <div class="font-arabic">
                <p class="text-gray-400 text-sm mb-1 font-bold">
                    {{ now()->isoFormat('dddd، D MMMM Y') }}
                </p>
                <h1 class="font-display text-2xl lg:text-3xl text-textColor font-black">
                    أهلاً، {{ explode(' ', $user->name)[0] }} 👋
                </h1>
            </div>
            <div class="flex items-center gap-3">
                @if($hasSub && !$isWaiting)
                    <div class="hidden md:flex items-center gap-2 bg-white rounded-full px-4 py-2 border border-gray-100 shadow-sm font-arabic">
                        <span class="text-lg">🔥</span>
                        <span class="font-black text-sm text-textColor">{{ $streak }} أيام</span>
                        <span class="text-xs text-gray-400 font-bold">متتالية</span>
                    </div>
                @endif
                <button class="lg:hidden w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center"
                    @click="sideOpen = true">
                    <span class="material-symbols-rounded text-textColor" style="font-size:20px">menu</span>
                </button>
            </div>
        </div>

        {{-- ══ NO SUBSCRIPTION ══ --}}
        @if(! $hasSub)
            <div class="flex items-center justify-center flex-1 py-16 anim anim-1">
                <div class="bg-white rounded-3xl border border-gray-100 p-10 max-w-sm w-full text-center flex flex-col items-center gap-6">

                    <div class="w-18 h-18 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center"
                        style="width:72px;height:72px;">
                        <span class="material-symbols-rounded text-gray-400" style="font-size:32px;">lock</span>
                    </div>

                    <div class="font-arabic">
                        <h2 class="font-black text-textColor text-xl mb-2">مفيش باقة فعّالة</h2>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            أنت مش مشترك في أي باقة دلوقتي.<br>
                            اشترك عشان توصل للداشبورد وتبدأ رحلتك.
                        </p>
                    </div>

                    <div class="w-full flex flex-col gap-3">
                        <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-3 border border-gray-100 text-right">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-primary" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">fitness_center</span>
                            </div>
                            <div class="font-arabic">
                                <p class="text-sm font-black text-textColor leading-none mb-0.5">برامج تدريب مخصصة</p>
                                <p class="text-xs text-gray-400 font-bold">تدريب يومي حسب هدفك</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-3 border border-gray-100 text-right">
                            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-green-600" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">monitoring</span>
                            </div>
                            <div class="font-arabic">
                                <p class="text-sm font-black text-textColor leading-none mb-0.5">تتبع التقدم والوزن</p>
                                <p class="text-xs text-gray-400 font-bold">شوف رحلتك خطوة بخطوة</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-3 border border-gray-100 text-right">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-amber-500" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">sports_score</span>
                            </div>
                            <div class="font-arabic">
                                <p class="text-sm font-black text-textColor leading-none mb-0.5">متابعة مع الكوتش</p>
                                <p class="text-xs text-gray-400 font-bold">ملاحظات ونصايح يومية</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full flex flex-col gap-2">
                        <a href="{{ route('home') }}#programs"
                            class="w-full flex items-center justify-center gap-2 bg-primary text-white font-black font-arabic text-sm px-6 py-3.5 rounded-2xl hover:bg-primary/90 transition">
                            <span class="material-symbols-rounded" style="font-size:18px;color:#D4ED57;">rocket_launch</span>
                            اشترك دلوقتي
                        </a>
                        <a href="{{ route('home') }}"
                            class="w-full text-center text-xs text-gray-400 font-bold font-arabic py-2 hover:text-primary transition">
                            العودة للصفحة الرئيسية
                        </a>
                    </div>

                </div>
            </div>

        @else

        {{-- ══ WAITING STATE ══ --}}
        @if($isWaiting)

            @if(! $hasBooking)
            <div class="anim anim-1">
                <div class="card waiting-card-pulse border-2 border-dashed border-primary/25 bg-primary/[0.02]" style="direction:rtl">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center flex-shrink-0 text-3xl">🗓️</div>
                        <div class="flex-1 font-arabic">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <h3 class="font-black text-textColor text-lg">حدد موعد جلستك الأولى</h3>
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200 animate-pulse">
                                    مطلوب لتفعيل الباقة
                                </span>
                            </div>
                            <p class="text-gray-400 text-sm font-bold leading-relaxed mb-3">
                                اشتراكك في انتظار تأكيد الموعد — احجز جلسة أونلاين مجانية مع الكوتش عشان نبدأ معاك ونفعّل باقتك.
                            </p>
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-gray-400">
                                    <span class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center text-[10px] font-black flex-shrink-0">1</span>
                                    اختار الموعد
                                </div>
                                <span class="text-gray-300">←</span>
                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-gray-400">
                                    <span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px] font-black flex-shrink-0">2</span>
                                    جلسة مع الكوتش
                                </div>
                                <span class="text-gray-300">←</span>
                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-gray-400">
                                    <span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px] font-black flex-shrink-0">3</span>
                                    تفعيل الباقة 🚀
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('booking.show', $subscription->id) }}"
                           class="flex-shrink-0 flex items-center gap-2 bg-primary text-white font-black font-arabic text-sm px-5 py-3 rounded-xl hover:bg-primary/90 transition whitespace-nowrap self-stretch sm:self-auto justify-center">
                            <span class="material-symbols-rounded" style="font-size:18px;color:#D4ED57">calendar_add_on</span>
                            احجز دلوقتي
                        </a>
                    </div>
                </div>
            </div>

            @else
            <div class="anim anim-1">
                <div class="card" style="direction:rtl; border-right: 4px solid #22c55e;">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center flex-shrink-0 text-2xl">📅</div>
                        <div class="flex-1 font-arabic">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-base font-black text-textColor">تم تحديد موعد الجلسة</span>
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-full border
                                    {{ $booking->status === 'confirmed' ? 'bg-green-50 text-green-600 border-green-200' : 'bg-amber-50 text-amber-600 border-amber-200' }}">
                                    {{ $booking->status === 'confirmed' ? '✓ مؤكد' : '⏳ في الانتظار' }}
                                </span>
                            </div>
                            <p class="text-gray-500 text-sm font-bold">
                                <span class="text-textColor">
                                    {{ \Carbon\Carbon::parse($booking->meeting_date)->locale('ar')->isoFormat('dddd، D MMMM Y') }}
                                </span>
                                — الساعة
                                <span class="text-textColor">
                                    {{ \Carbon\Carbon::parse($booking->meeting_time)->format('H:i') }}
                                </span>
                            </p>
                            @if($booking->meet_link && !str_contains($booking->meet_link, 'xxx'))
                            <a href="{{ $booking->meet_link }}" target="_blank"
                               class="inline-flex items-center gap-1.5 mt-2 text-[12px] font-black text-primary hover:underline">
                                <span class="material-symbols-rounded" style="font-size:15px">videocam</span>
                                افتح لينك الاجتماع
                            </a>
                            @endif
                        </div>
                        <a href="{{ route('booking.show', $subscription->id) }}"
                           class="flex-shrink-0 flex items-center gap-1.5 text-[12px] font-black text-gray-400 hover:text-primary font-arabic transition border border-gray-200 hover:border-primary rounded-xl px-3 py-2 whitespace-nowrap">
                            <span class="material-symbols-rounded" style="font-size:15px">edit_calendar</span>
                            تغيير الموعد
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim anim-2">
                <div class="card lg:col-span-2 flex items-center gap-4" style="direction:rtl">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background: {{ $plan->icon_bg ?? '#EFF5FF' }}">
                        <span class="material-symbols-rounded"
                              style="font-size:28px;font-variation-settings:'FILL' 1; color: {{ $plan->icon_color ?? '#174DAD' }}">
                            {{ $plan->icon ?? 'star' }}
                        </span>
                    </div>
                    <div class="font-arabic flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <p class="font-black text-textColor text-xl leading-none">{{ $plan->name }}</p>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200">
                                في انتظار التفعيل
                            </span>
                        </div>
                        <p class="text-gray-400 text-xs font-bold">
                            {{ $plan->desc ?? 'سيتم تفعيل الباقة بعد جلسة التعارف مع الكوتش' }}
                        </p>
                    </div>
                </div>
                <div class="card flex items-center gap-4" style="direction:rtl">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0 text-3xl">⏳</div>
                    <div class="font-arabic">
                        <p class="text-gray-400 text-xs font-bold mb-0.5">حالة الاشتراك</p>
                        <p class="font-black text-textColor text-base leading-none mb-1">في الانتظار</p>
                        <p class="text-amber-500 text-[11px] font-bold">احجز جلستك لتفعيل الباقة</p>
                    </div>
                </div>
            </div>

            @elseif($startsInFuture)

                {{-- ══ UPCOMING STATE ══ --}}
                <div class="flex items-center justify-center flex-1 py-16 anim anim-1">
                    <div class="bg-white rounded-3xl border border-gray-100 p-10 max-w-sm w-full text-center flex flex-col items-center gap-6">

                        <div class="w-18 h-18 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center"
                            style="width:72px;height:72px;">
                            <span class="material-symbols-rounded text-primary" style="font-size:32px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">
                                event_upcoming
                            </span>
                        </div>

                        <div class="font-arabic">
                            <h2 class="font-black text-textColor text-xl mb-2">باقتك جاهزة وبتستناك! 🎉</h2>
                            <p class="text-gray-400 text-sm leading-relaxed">
                                رحلتك هتبدأ رسمياً في
                                <span class="font-black text-primary">
                                    {{ $subscription->start_date->locale('ar')->isoFormat('D MMMM YYYY') }}
                                </span>
                            </p>
                        </div>

                        {{-- Countdown --}}
                        <div class="w-full bg-blue-50 border border-blue-100 rounded-2xl p-4 font-arabic">
                            <p class="text-primary text-xs font-bold mb-2">متبقي على البداية</p>
                            <p class="font-display text-4xl font-black text-primary leading-none">
                                {{ (int) now()->startOfDay()->diffInDays($subscription->start_date->startOfDay()) }}
                                <span class="text-sm font-bold text-gray-400">يوم</span>
                            </p>
                        </div>

                        {{-- Plan info --}}
                        <div class="w-full flex items-center gap-3 bg-gray-50 rounded-2xl p-3 border border-gray-100 text-right">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                style="background: {{ $plan->icon_bg ?? '#EFF5FF' }}">
                                <span class="material-symbols-rounded"
                                    style="font-size:20px;font-variation-settings:'FILL' 1; color: {{ $plan->icon_color ?? '#174DAD' }}">
                                    {{ $plan->icon ?? 'star' }}
                                </span>
                            </div>
                            <div class="font-arabic">
                                <p class="text-sm font-black text-textColor leading-none mb-0.5">{{ $plan->name }}</p>
                                <p class="text-xs text-gray-400 font-bold">
                                    تنتهي {{ $subscription->end_date?->locale('ar')->isoFormat('D MMMM YYYY') ?? '—' }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            @else
            {{-- ══ FULL ACTIVE DASHBOARD ══ --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Journey Progress Card --}}
                <div class="card-dark lg:col-span-2 anim anim-1" id="overview">
                    <div class="relative z-10 flex flex-col lg:flex-row items-center gap-6">

                        {{-- Ring --}}
                        <div class="relative flex-shrink-0">
                            <svg width="130" height="130" viewBox="0 0 130 130">
                                <circle cx="65" cy="65" r="58" class="ring-bg"/>
                                <circle cx="65" cy="65" r="58" class="ring-fill" id="journeyRing"
                                    style="stroke-dashoffset: {{ 408 - (408 * $pct / 100) }}"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center font-arabic">
                                <span class="font-display text-3xl font-black text-white leading-none">{{ $pct }}%</span>
                                <span class="text-white/50 text-[10px] font-bold">اكتمل</span>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 font-arabic text-center lg:text-right">
                            <span class="inline-block bg-accent/20 text-accent text-[10px] font-black px-3 py-1 rounded-full mb-2">
                                رحلتك جارية 🚀
                            </span>
                            <h2 class="text-white font-black text-xl mb-1">
                                أسبوع {{ $weeksDone }} من {{ $totalWeeks }}
                            </h2>
                            <p class="text-white/60 text-sm mb-4">
                                بدأت بـ {{ $startWeight }} كجم — هدفك {{ $goalWeight }} كجم
                            </p>
                            <div class="flex items-center justify-center lg:justify-start gap-4">
                                <div class="text-center">
                                    <p class="text-white/40 text-[10px] font-bold mb-0.5">وزن البداية</p>
                                    <p class="text-white font-black font-display text-xl">
                                        {{ $startWeight }}<span class="text-xs text-white/40 font-bold"> كجم</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-accent/20 border border-accent/30">
                                    <span class="material-symbols-rounded text-accent" style="font-size:14px">
                                        {{ $currentWeight <= $startWeight ? 'trending_down' : 'trending_up' }}
                                    </span>
                                    <span class="text-accent font-black text-sm">
                                        {{ abs($startWeight - $currentWeight) }} كجم
                                    </span>
                                </div>
                                <div class="text-center">
                                    <p class="text-white/40 text-[10px] font-bold mb-0.5">الوزن الحالي</p>
                                    <p class="text-accent font-black font-display text-xl">
                                        {{ $currentWeight }}<span class="text-xs text-accent/60 font-bold"> كجم</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Week strip --}}
                    <div class="relative z-10 mt-6 pt-5 border-t border-white/10">
                        <p class="text-white/40 text-[10px] font-bold font-arabic mb-3">أيام الأسبوع</p>
                        <div class="flex items-center justify-between gap-2">
                            @foreach($weekDays as $d)
                                <div class="day-dot
                                    {{ $d['status'] === 'done'     ? 'day-done'     : '' }}
                                    {{ $d['status'] === 'today'    ? 'day-today'    : '' }}
                                    {{ $d['status'] === 'rest'     ? 'day-rest'     : '' }}
                                    {{ $d['status'] === 'upcoming' ? 'day-upcoming' : '' }}">
                                    @if($d['status'] === 'done')
                                        <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check</span>
                                    @elseif($d['status'] === 'rest')
                                        <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">hotel</span>
                                    @else
                                        {{ $d['label'] }}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Plan & Streak --}}
                <div class="flex flex-col gap-5">

                    <div class="card anim anim-2 flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-black text-gray-400 font-arabic">باقتك الحالية</span>
                            <a href="{{ route('home') }}#programs" class="text-[11px] font-black text-primary font-arabic hover:underline">ترقية</a>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                                    style="background: {{ $plan->icon_bg ?? '#EFF5FF' }}">
                                <span class="material-symbols-rounded"
                                        style="font-size:24px;font-variation-settings:'FILL' 1; color: {{ $plan->icon_color ?? '#174DAD' }}">
                                    {{ $plan->icon ?? 'star' }}
                                </span>
                            </div>
                            <div class="font-arabic">
                                <p class="font-black text-textColor text-lg leading-none">{{ $plan->name }}</p>
                                <p class="text-gray-400 text-xs mt-0.5">
                                    تنتهي {{ $subscription->end_date ? $subscription->end_date->locale('ar')->isoFormat('D MMMM YYYY') : '—' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-[11px] font-bold font-arabic text-gray-400 mb-1.5">
                                <span>{{ max(0, $daysLeft) }} يوم متبقي</span>
                                <span>{{ $totalDays }} يوم</span>
                            </div>
                            <div class="macro-bar-wrap">
                                <div class="macro-bar-fill bg-primary" style="width: {{ 100 - $subPct }}%"></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($daysLeft <= 5 && $daysLeft > 0)
                                <span class="text-[10px] font-black font-arabic text-amber-500 bg-amber-50 px-2 py-0.5 rounded-full">⚠️ تنتهي قريباً</span>
                            @elseif($daysLeft <= 0)
                                <span class="text-[10px] font-black font-arabic text-red-500 bg-red-50 px-2 py-0.5 rounded-full">منتهية</span>
                            @else
                                <span class="text-[10px] font-black font-arabic text-green-600 bg-green-50 px-2 py-0.5 rounded-full">✓ نشطة</span>
                            @endif
                            <span class="text-[10px] text-gray-400 font-arabic font-bold">
                                بدأت {{ $subscription->start_date ? $subscription->start_date->locale('ar')->isoFormat('D MMMM YYYY') : '—' }}
                            </span>
                        </div>
                    </div>

                    <div class="card anim anim-3 flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0 text-3xl">🔥</div>
                        <div class="font-arabic">
                            <p class="text-gray-400 text-xs font-bold mb-0.5">أيام متتالية</p>
                            <p class="font-display text-3xl font-black text-textColor leading-none">{{ $streak }}</p>
                            <p class="text-green-500 text-[11px] font-bold mt-0.5">استمر! أنت رائع 💪</p>
                        </div>
                    </div>

                </div>

            </div>

        @endif
        @endif

    </main>

</div>
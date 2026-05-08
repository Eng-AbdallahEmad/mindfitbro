{{--
    ╔══════════════════════════════════════════════════╗
    ║  صفحة المشتركين والحضور والتقييم               ║
    ║  MindFitBro Coach Dashboard                     ║
    ╚══════════════════════════════════════════════════╝
--}}

@extends('layouts.web.app')

@section('title', 'المشتركون والحضور')

@section('style')
<style>
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

    .card {
        background: #fff;
        border-radius: 22px;
        padding: 1.5rem;
        border: 1px solid rgba(23,77,173,.06);
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .anim { animation: fadeUp .5s ease both; }
    .anim-1 { animation-delay: .05s; }
    .anim-2 { animation-delay: .12s; }
    .anim-3 { animation-delay: .19s; }
    .anim-4 { animation-delay: .26s; }

    .att-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        border: 1.5px solid transparent;
    }
    .att-btn:disabled { opacity: .5; cursor: not-allowed; }

    .att-present        { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .att-present.active { background: #22c55e; color: #fff;    border-color: #22c55e; }
    .att-present:not(.active):not(:disabled):hover { background: #dcfce7; border-color: #86efac; }

    .att-late        { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .att-late.active { background: #f59e0b; color: #fff;    border-color: #f59e0b; }
    .att-late:not(.active):not(:disabled):hover { background: #fef3c7; border-color: #fcd34d; }

    .att-absent        { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
    .att-absent.active { background: #ef4444; color: #fff;    border-color: #ef4444; }
    .att-absent:not(.active):not(:disabled):hover { background: #fee2e2; border-color: #fca5a5; }

    .macro-bar-wrap { height: 4px; background: #f3f4f6; border-radius: 99px; overflow: hidden; }
    .macro-bar-fill { height: 100%; border-radius: 99px; transition: width .6s ease; }

    .toast-fixed {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(80px);
        padding: .65rem 1.5rem;
        border-radius: 14px;
        font-family: 'Cairo', sans-serif;
        font-size: .85rem;
        font-weight: 700;
        z-index: 9999;
        transition: transform .4s cubic-bezier(.34,1.56,.64,1);
        box-shadow: 0 10px 40px rgba(0,0,0,.15);
        white-space: nowrap;
    }
    .toast-fixed.show { transform: translateX(-50%) translateY(0); }
</style>
@endsection

@section('content')

{{-- ══════════════════════════════════════
     EVALUATION MODAL
══════════════════════════════════════ --}}
<div x-data="evalModal()" x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="direction:rtl">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
         @click="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    {{-- Modal card --}}
    <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-[500px] max-h-[90vh] overflow-hidden flex flex-col"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between px-7 pt-6 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-violet-100 to-primary/10 flex items-center justify-center">
                    <span class="material-symbols-rounded text-violet-600" style="font-size:22px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">assignment</span>
                </div>
                <div class="font-arabic">
                    <h3 class="font-black text-textColor text-[15px] leading-none">تقييم جديد</h3>
                    <p class="text-gray-400 text-[11px] font-bold mt-1 flex items-center gap-1">
                        <span class="material-symbols-rounded" style="font-size:12px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">person</span>
                        <span x-text="userName"></span>
                    </p>
                </div>
            </div>
            <button @click="open = false" class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-gray-100 transition group">
                <span class="material-symbols-rounded text-gray-400 group-hover:text-gray-600 transition" style="font-size:16px">close</span>
            </button>
        </div>

        {{-- Scrollable body --}}
        <div class="overflow-y-auto flex-1 px-7 py-5 flex flex-col gap-5">

            {{-- Weight + Height --}}
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-blue-100 flex items-center justify-center">
                        <span class="material-symbols-rounded text-primary" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">monitor_weight</span>
                    </div>
                    <p class="text-xs font-black text-textColor font-arabic">قياسات الجسم</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1 font-arabic">
                        <label class="text-[11px] font-bold text-gray-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span> الوزن الحالي (كجم) *
                        </label>
                        <div class="relative">
                            <input type="number" x-model="form.weight" min="20" max="400" step="0.1" placeholder="75.5" required
                                class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full pl-10">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300">كجم</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 font-arabic">
                        <label class="text-[11px] font-bold text-gray-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> الطول (سم)
                        </label>
                        <div class="relative">
                            <input type="number" x-model="form.height" min="50" max="280" placeholder="175"
                                class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full pl-10">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300">سم</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1 font-arabic">
                        <label class="text-[11px] font-bold text-gray-400">نسبة الدهون (%)</label>
                        <div class="relative">
                            <input type="number" x-model="form.bodyFat" min="1" max="70" step="0.1" placeholder="18"
                                class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full pl-8">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300">%</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 font-arabic">
                        <label class="text-[11px] font-bold text-gray-400">الكتلة العضلية (كجم)</label>
                        <div class="relative">
                            <input type="number" x-model="form.muscleMass" min="10" max="200" step="0.1" placeholder="35"
                                class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full pl-10">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300">كجم</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fitness Level --}}
            <div class="flex flex-col gap-3 border-t border-gray-100 pt-5">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-rounded text-green-600" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">fitness_center</span>
                    </div>
                    <p class="text-xs font-black text-textColor font-arabic">مستوى اللياقة</p>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="form.fitnessLevel = 'beginner'"
                        :class="form.fitnessLevel === 'beginner' ? 'bg-primary text-white border-primary' : 'bg-gray-50 text-gray-500 border-gray-200 hover:border-primary/40'"
                        class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-2xl border-2 transition-all font-arabic cursor-pointer">
                        <span class="material-symbols-rounded" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">sprint</span>
                        <span class="text-[11px] font-black">مبتدئ</span>
                    </button>
                    <button type="button" @click="form.fitnessLevel = 'intermediate'"
                        :class="form.fitnessLevel === 'intermediate' ? 'bg-amber-500 text-white border-amber-500' : 'bg-gray-50 text-gray-500 border-gray-200 hover:border-amber-300'"
                        class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-2xl border-2 transition-all font-arabic cursor-pointer">
                        <span class="material-symbols-rounded" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">fitness_center</span>
                        <span class="text-[11px] font-black">متوسط</span>
                    </button>
                    <button type="button" @click="form.fitnessLevel = 'advanced'"
                        :class="form.fitnessLevel === 'advanced' ? 'bg-green-600 text-white border-green-600' : 'bg-gray-50 text-gray-500 border-gray-200 hover:border-green-300'"
                        class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-2xl border-2 transition-all font-arabic cursor-pointer">
                        <span class="material-symbols-rounded" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">emoji_events</span>
                        <span class="text-[11px] font-black">متقدم</span>
                    </button>
                </div>
            </div>

            {{-- Date + Notes --}}
            <div class="flex flex-col gap-3 border-t border-gray-100 pt-5">
                <div class="flex flex-col gap-1 font-arabic">
                    <label class="text-[11px] font-bold text-gray-400">تاريخ التقييم</label>
                    <input type="date" x-model="form.date"
                        class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full">
                </div>
                <div class="flex flex-col gap-1 font-arabic">
                    <label class="text-[11px] font-bold text-gray-400">ملاحظات (اختياري)</label>
                    <textarea x-model="form.notes" rows="3" placeholder="أداء الاشتراك، ملاحظات التدريب..."
                        class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all resize-none w-full"></textarea>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-7 py-4 border-t border-gray-100 bg-white flex gap-3">
            <button type="button" @click="open = false"
                class="flex-1 py-3 rounded-2xl font-black font-arabic text-sm text-gray-400 bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-all">إلغاء</button>
            <button type="button" @click="submit()" :disabled="saving || !form.weight"
                class="flex-1 py-3 rounded-2xl font-black font-arabic text-sm text-white bg-gradient-to-l from-violet-600 to-primary hover:shadow-lg hover:shadow-primary/25 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                <template x-if="!saving">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">save</span>
                        حفظ التقييم
                    </span>
                </template>
                <template x-if="saving">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        جاري الحفظ...
                    </span>
                </template>
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     MAIN DASHBOARD
══════════════════════════════════════ --}}
<div class="dash-wrap" x-data="subscribersPage()">

    {{-- SIDEBAR --}}
    <x-web.sidebar :coach="$coach" active="subscribers"/>

    {{-- MAIN CONTENT --}}
    <main class="flex flex-col gap-5 p-5 lg:p-8 overflow-y-auto">

        {{-- Toast --}}
        <div class="toast-fixed" style="direction:rtl"
             :class="[toast.show ? 'show' : '', toast.ok ? 'bg-green-800 text-white' : 'bg-red-800 text-white']"
             x-text="toast.msg"></div>

        {{-- ─ Topbar ─ --}}
        <div class="flex items-center justify-between">
            <div class="font-arabic" style="direction:rtl">
                <p class="text-gray-400 text-sm mb-1 font-bold">{{ now()->isoFormat('dddd، D MMMM Y') }}</p>
                <h1 class="font-display text-2xl lg:text-3xl text-textColor font-black">المشتركون والحضور</h1>
            </div>
            <button class="lg:hidden w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center"
                @click="sideOpen = true">
                <span class="material-symbols-rounded text-textColor" style="font-size:20px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">menu</span>
            </button>
        </div>

        {{-- ══ Stats ══ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 anim anim-1">

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">group</span>
                </div>
                <div class="font-arabic" style="direction:rtl">
                    <p class="text-gray-400 text-xs font-bold mb-1">إجمالي المشتركين</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none">{{ $subscribers->count() }}</p>
                </div>
                <span class="text-[10px] font-black font-arabic text-blue-700 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full self-start">اشتراك نشط</span>
            </div>

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-green-600" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">how_to_reg</span>
                </div>
                <div class="font-arabic" style="direction:rtl">
                    <p class="text-gray-400 text-xs font-bold mb-1">حضروا اليوم</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none">{{ $presentToday }}</p>
                </div>
                <span class="text-[10px] font-black font-arabic text-green-700 bg-green-50 border border-green-100 px-2 py-0.5 rounded-full self-start">حاضر / متأخر</span>
            </div>

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-amber-500" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">pending_actions</span>
                </div>
                <div class="font-arabic" style="direction:rtl">
                    <p class="text-gray-400 text-xs font-bold mb-1">لم يُسجَّل بعد</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none">{{ $notMarked }}</p>
                </div>
                @if($notMarked > 0)
                    <span class="text-[10px] font-black font-arabic text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full self-start animate-pulse">يحتاج متابعة</span>
                @else
                    <span class="text-[10px] font-black font-arabic text-green-700 bg-green-50 border border-green-100 px-2 py-0.5 rounded-full self-start">اكتمل التسجيل ✓</span>
                @endif
            </div>

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-violet-600" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">assignment</span>
                </div>
                <div class="font-arabic" style="direction:rtl">
                    <p class="text-gray-400 text-xs font-bold mb-1">تقييمات هذا الأسبوع</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none">{{ $evalsThisWeek }}</p>
                </div>
                <span class="text-[10px] font-black font-arabic text-violet-700 bg-violet-50 border border-violet-100 px-2 py-0.5 rounded-full self-start">تقييم مكتمل</span>
            </div>

        </div>

        {{-- ══ Search ══ --}}
        <div class="card anim anim-2 flex items-center gap-3" style="direction:rtl; padding: 1rem 1.25rem;">
            <span class="material-symbols-rounded text-gray-400 flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">search</span>
            <input type="text" x-model="search" placeholder="ابحث باسم المشترك..."
                class="flex-1 bg-transparent outline-none font-arabic text-sm text-textColor placeholder-gray-300 font-bold">
            <button x-show="search" @click="search = ''" class="text-gray-300 hover:text-gray-500 transition flex-shrink-0">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>

        {{-- ══ Subscriber List ══ --}}
        <div class="card anim anim-3" style="padding: 0; overflow: hidden;">

            {{-- Header row --}}
            <div class="flex items-center gap-3 px-6 py-3.5 border-b border-gray-100 bg-gray-50/60" style="direction:rtl">
                <span class="material-symbols-rounded text-primary" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">group</span>
                <h2 class="font-black text-textColor text-sm font-arabic flex-1">قائمة المشتركين</h2>
                <span class="text-[11px] font-bold font-arabic text-gray-400">{{ now()->isoFormat('D MMMM Y') }}</span>
            </div>

            @forelse($subscribers as $sub)
            @php
                $client    = $sub->user;
                $plan      = $sub->plan;
                $profile   = $sub->user->profile;
                $totalD    = ($sub->start_date && $sub->end_date)
                             ? (int) $sub->start_date->startOfDay()->diffInDays($sub->end_date->startOfDay()) : 1;
                $usedD     = $sub->start_date
                             ? (int) $sub->start_date->startOfDay()->diffInDays(now()->startOfDay()) : 0;
                $pct       = $totalD > 0 ? min(100, (int) round(($usedD / $totalD) * 100)) : 0;
                $daysLeft  = $sub->end_date
                             ? (int) now()->startOfDay()->diffInDays($sub->end_date->startOfDay(), false) : 0;
                $isRestDay = $restDayUserIds->contains($sub->user_id);
            @endphp

            <div class="px-6 py-4 border-b border-gray-100 last:border-0 transition hover:bg-gray-50/50"
                 style="direction:rtl"
                 data-name="{{ mb_strtolower($client->name ?? '') }}"
                 x-show="!search || $el.dataset.name.includes(search.toLowerCase())">

                <div class="flex items-center gap-4 flex-wrap lg:flex-nowrap">

                    {{-- Avatar + info --}}
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center font-black text-primary font-arabic text-sm flex-shrink-0">
                            {{ mb_substr($client->name ?? '?', 0, 1) }}
                        </div>
                        <div class="font-arabic min-w-0">
                            <p class="font-black text-textColor text-sm leading-none mb-1">{{ $client->name ?? '—' }}</p>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] font-black text-primary bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full">
                                    {{ $plan?->name ?? '—' }}
                                </span>
                                @if($daysLeft <= 5 && $daysLeft > 0)
                                    <span class="text-[10px] font-black text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full">{{ $daysLeft }} يوم متبقي</span>
                                @elseif($daysLeft <= 0)
                                    <span class="text-[10px] font-black text-red-600 bg-red-50 border border-red-100 px-2 py-0.5 rounded-full">منتهية</span>
                                @else
                                    <span class="text-[10px] font-bold text-gray-400 font-arabic">{{ $daysLeft }} يوم متبقي</span>
                                @endif
                            </div>
                            <div class="macro-bar-wrap mt-2" style="width:80px">
                                <div class="macro-bar-fill bg-primary" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Last eval --}}
                    <div class="font-arabic text-center flex-shrink-0 hidden lg:block" style="min-width:100px">
                        <template x-if="lastEvals[{{ $sub->user_id }}]">
                            <div>
                                <p class="text-[11px] font-bold text-gray-400 mb-0.5">آخر تقييم</p>
                                <p class="font-black text-textColor text-sm" x-text="lastEvals[{{ $sub->user_id }}].weight + ' كجم'"></p>
                                <p class="text-[10px] text-gray-400 font-bold" x-text="lastEvals[{{ $sub->user_id }}].evaluated_at"></p>
                            </div>
                        </template>
                        <template x-if="!lastEvals[{{ $sub->user_id }}]">
                            <p class="text-[10px] font-bold text-gray-300">لا يوجد تقييم</p>
                        </template>
                    </div>

                    @if($isRestDay)
                    {{-- Rest day badge --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="flex items-center gap-1.5 text-[11px] font-black font-arabic text-slate-500 bg-slate-100 border border-slate-200 px-3 py-2 rounded-xl">
                            <span class="material-symbols-rounded" style="font-size:15px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">bedtime</span>
                            يوم راحة
                        </span>
                    </div>
                    @else
                    {{-- Attendance buttons --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button @click="markAttendance({{ $sub->user_id }}, 'present')"
                            :disabled="marking[{{ $sub->user_id }}]"
                            :class="attendance[{{ $sub->user_id }}] === 'present' ? 'active' : ''"
                            class="att-btn att-present">
                            <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check_circle</span>
                            حاضر
                        </button>
                        <button @click="markAttendance({{ $sub->user_id }}, 'late')"
                            :disabled="marking[{{ $sub->user_id }}]"
                            :class="attendance[{{ $sub->user_id }}] === 'late' ? 'active' : ''"
                            class="att-btn att-late">
                            <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">schedule</span>
                            متأخر
                        </button>
                        <button @click="markAttendance({{ $sub->user_id }}, 'absent')"
                            :disabled="marking[{{ $sub->user_id }}]"
                            :class="attendance[{{ $sub->user_id }}] === 'absent' ? 'active' : ''"
                            class="att-btn att-absent">
                            <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">cancel</span>
                            غائب
                        </button>
                    </div>

                    {{-- Eval button --}}
                    <button type="button"
                        @click="openEval({{ $sub->user_id }})"
                        class="flex items-center gap-1.5 text-[11px] font-black font-arabic text-violet-700 bg-violet-50 border border-violet-200 px-3 py-2 rounded-xl hover:bg-violet-100 transition flex-shrink-0">
                        <span class="material-symbols-rounded" style="font-size:15px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">assignment</span>
                        تقييم
                    </button>
                    @endif

                    {{-- Profile link --}}
                    <a href="{{ route('coach.subscribers.show', $sub->user_id) }}"
                       class="flex items-center justify-center w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 border border-gray-200 transition flex-shrink-0"
                       title="عرض السجل الكامل">
                        <span class="material-symbols-rounded text-gray-500" style="font-size:16px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">open_in_new</span>
                    </a>

                </div>
            </div>

            @empty
            <div class="flex flex-col items-center justify-center py-16 gap-3 font-arabic text-center">
                <span class="material-symbols-rounded text-gray-300" style="font-size:48px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 48">group</span>
                <p class="text-gray-400 font-bold text-sm">لا يوجد مشتركين نشطين حالياً</p>
            </div>
            @endforelse

        </div>

    </main>
</div>

@endsection

@section('script')
<script>
    // ── PHP-injected initial data ─────────────────────────────
    window.__subData = {
        attendance: {
            @foreach($subscribers as $sub)
            {{ $sub->user_id }}: @js($todayAttendances[$sub->user_id]->status ?? ''),
            @endforeach
        },
        lastEvals: {
            @foreach($subscribers as $sub)
            @php
                $lv = $latestEvals[$sub->user_id] ?? null;
                $evalArr = $lv ? ['weight' => $lv->weight, 'fitness_level' => $lv->fitness_level, 'evaluated_at' => $lv->evaluated_at?->format('Y-m-d')] : null;
            @endphp
            {{ $sub->user_id }}: @js($evalArr),
            @endforeach
        },
        profiles: {
            @foreach($subscribers as $sub)
            @php $cl2 = $sub->user; $pr2 = $sub->user->profile; @endphp
            {{ $sub->user_id }}: { name: @js($cl2->name ?? ''), height: @js((float)($pr2?->height ?? 0)), weight: @js((float)($pr2?->current_weight ?? 0)), restDay: {{ $restDayUserIds->contains($sub->user_id) ? 'true' : 'false' }} },
            @endforeach
        },
        urls: {
            attendance: @js(route('coach.subscribers.attendance')),
            evaluation: @js(route('coach.subscribers.evaluation')),
        },
        csrf: @js(csrf_token()),
    };

    // ── Alpine Components ─────────────────────────────────────
    document.addEventListener('alpine:init', () => {

        // ── Evaluation Modal ──────────────────────────────────
        Alpine.data('evalModal', () => ({
            open:     false,
            saving:   false,
            userId:   null,
            userName: '',
            form:     { weight: '', height: '', bodyFat: '', muscleMass: '', fitnessLevel: 'beginner', notes: '', date: '' },

            openModal(userId, userName, height, weight, fitnessLevel) {
                this.userId   = userId;
                this.userName = userName;
                this.form     = {
                    weight:       weight       || '',
                    height:       height       || '',
                    bodyFat:      '',
                    muscleMass:   '',
                    fitnessLevel: fitnessLevel || 'beginner',
                    notes:        '',
                    date:         new Date().toISOString().split('T')[0],
                };
                this.saving = false;
                this.open   = true;
            },

            async submit() {
                if (this.saving || !this.form.weight) return;
                this.saving = true;
                try {
                    const res  = await fetch(window.__subData.urls.evaluation, {
                        method:  'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.__subData.csrf,
                        },
                        body: JSON.stringify({
                            user_id:             this.userId,
                            weight:              this.form.weight,
                            height:              this.form.height      || null,
                            body_fat_percentage: this.form.bodyFat     || null,
                            muscle_mass:         this.form.muscleMass  || null,
                            fitness_level:       this.form.fitnessLevel,
                            notes:               this.form.notes       || null,
                            evaluated_at:        this.form.date,
                        }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('eval-saved', {
                            detail: { userId: this.userId, eval: data.eval },
                        }));
                        this.open = false;
                    }
                } catch (e) {
                    console.error(e);
                }
                this.saving = false;
            },

            init() {
                window.addEventListener('open-eval-modal', (e) => {
                    const d = e.detail;
                    this.openModal(d.userId, d.userName, d.height, d.weight, d.fitnessLevel);
                });
            },
        }));

        // ── Subscribers Page ──────────────────────────────────
        Alpine.data('subscribersPage', () => ({
            sideOpen:   false,
            search:     '',
            attendance: Object.assign({}, window.__subData.attendance),
            lastEvals:  Object.assign({}, window.__subData.lastEvals),
            marking:    {},
            toast:      { show: false, msg: '', ok: true },

            showToast(msg, ok = true) {
                this.toast = { show: true, msg, ok };
                setTimeout(() => this.toast.show = false, 3000);
            },

            async markAttendance(userId, status) {
                if (this.marking[userId]) return;
                this.marking[userId] = true;
                try {
                    const res  = await fetch(window.__subData.urls.attendance, {
                        method:  'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.__subData.csrf,
                        },
                        body: JSON.stringify({ user_id: userId, status }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.attendance[userId] = data.status;
                        const labels = {
                            present: 'تم تسجيل الحضور ✓',
                            late:    'تم تسجيل التأخر ✓',
                            absent:  'تم تسجيل الغياب ✓',
                        };
                        this.showToast(labels[data.status] || 'تم الحفظ ✓');
                    } else {
                        this.showToast('حدث خطأ، حاول مرة أخرى', false);
                    }
                } catch {
                    this.showToast('خطأ في الاتصال بالخادم', false);
                }
                this.marking[userId] = false;
            },

            openEval(userId) {
                const p  = (window.__subData.profiles || {})[userId] || {};
                const lv = this.lastEvals[userId] || {};
                window.dispatchEvent(new CustomEvent('open-eval-modal', { detail: {
                    userId,
                    userName:     p.name    || '',
                    height:       p.height  || '',
                    weight:       p.weight  || '',
                    fitnessLevel: lv.fitness_level || 'beginner',
                }}));
            },

            init() {
                window.addEventListener('eval-saved', (e) => {
                    const { userId, eval: ev } = e.detail;
                    this.lastEvals[userId] = ev;
                    this.showToast('تم حفظ التقييم بنجاح ✓');
                });
            },
        }));

    });
</script>
@endsection

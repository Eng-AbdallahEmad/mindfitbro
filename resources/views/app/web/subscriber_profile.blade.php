@extends('layouts.web.app')

@section('title', 'سجل ' . $member->name)

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
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
    }
    .nav-item {
        display: flex; align-items: center; gap: .75rem;
        padding: .7rem 1rem; border-radius: 14px;
        font-family: 'Cairo', sans-serif; font-size: .85rem; font-weight: 700;
        color: rgba(255,255,255,0.5); cursor: pointer; transition: all .22s;
        text-decoration: none; direction: rtl;
    }
    .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.9); }
    .nav-item.active { background: rgba(212,237,87,0.12); color: #D4ED57; border: 1px solid rgba(212,237,87,0.2); }
    .nav-item.active .nav-icon { color: #D4ED57; }
    .nav-icon { color: rgba(255,255,255,0.35); transition: color .22s; font-size: 20px; }

    .card {
        background: #fff;
        border-radius: 22px;
        padding: 1.5rem;
        border: 1px solid rgba(23,77,173,.06);
    }

    /* ── Heatmap ── */
    .hm-grid {
        display: grid;
        grid-template-rows: repeat(7, 13px);
        grid-auto-flow: column;
        gap: 3px;
    }
    .hm-cell { width: 13px; height: 13px; border-radius: 3px; }
    .hm-empty   { background: transparent; }
    .hm-none    { background: #e5e7eb; }
    .hm-present { background: #22c55e; }
    .hm-late    { background: #f59e0b; }
    .hm-absent  { background: #ef4444; }
    .hm-rest    { background: #cbd5e1; }
    .hm-today   { background: #60a5fa; outline: 1.5px solid #2563eb; outline-offset: 1px; }

    /* ── Status badges ── */
    .status-present { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .status-late    { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .status-absent  { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    /* ── Att buttons (same as subscribers page) ── */
    .att-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 12px; border-radius: 10px;
        font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 700;
        cursor: pointer; transition: all .2s; border: 1.5px solid transparent;
    }
    .att-btn:disabled { opacity: .5; cursor: not-allowed; }
    .att-present        { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .att-present.active { background: #22c55e; color: #fff; border-color: #22c55e; }
    .att-present:not(.active):not(:disabled):hover { background: #dcfce7; border-color: #86efac; }
    .att-late           { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .att-late.active    { background: #f59e0b; color: #fff; border-color: #f59e0b; }
    .att-late:not(.active):not(:disabled):hover { background: #fef3c7; border-color: #fcd34d; }
    .att-absent         { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
    .att-absent.active  { background: #ef4444; color: #fff; border-color: #ef4444; }
    .att-absent:not(.active):not(:disabled):hover { background: #fee2e2; border-color: #fca5a5; }

    .toast-fixed {
        position: fixed; bottom: 2rem; left: 50%;
        transform: translateX(-50%) translateY(80px);
        padding: .65rem 1.5rem; border-radius: 14px;
        font-family: 'Cairo', sans-serif; font-size: .85rem; font-weight: 700;
        z-index: 9999; transition: transform .4s cubic-bezier(.34,1.56,.64,1);
        box-shadow: 0 10px 40px rgba(0,0,0,.15); white-space: nowrap;
    }
    .toast-fixed.show { transform: translateX(-50%) translateY(0); }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim   { animation: fadeUp .45s ease both; }
    .anim-1 { animation-delay: .04s; }
    .anim-2 { animation-delay: .10s; }
    .anim-3 { animation-delay: .16s; }
    .anim-4 { animation-delay: .22s; }
    .anim-5 { animation-delay: .28s; }
    .anim-6 { animation-delay: .34s; }
</style>
@endsection

@section('content')

{{-- ══════════════════════════════════════
     EVALUATION MODAL
══════════════════════════════════════ --}}
<div x-data="evalModalProfile()" x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="direction:rtl">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
         @click="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-[500px] max-h-[90vh] overflow-hidden flex flex-col"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         @click.stop>
        <div class="flex items-center justify-between px-7 pt-6 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-violet-100 to-primary/10 flex items-center justify-center">
                    <span class="material-symbols-rounded text-violet-600" style="font-size:22px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">assignment</span>
                </div>
                <div class="font-arabic">
                    <h3 class="font-black text-textColor text-[15px] leading-none">تقييم جديد</h3>
                    <p class="text-gray-400 text-[11px] font-bold mt-1">{{ $member->name }}</p>
                </div>
            </div>
            <button @click="open = false" class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-gray-100 transition">
                <span class="material-symbols-rounded text-gray-400" style="font-size:16px">close</span>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 px-7 py-5 flex flex-col gap-5">
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1 font-arabic">
                    <label class="text-[11px] font-bold text-gray-400">الوزن (كجم) *</label>
                    <input type="number" x-model="form.weight" min="20" max="400" step="0.1" placeholder="75.5"
                        class="bg-gray-50 border border-gray-200 focus:border-primary rounded-xl px-3.5 py-2.5 text-sm outline-none w-full">
                </div>
                <div class="flex flex-col gap-1 font-arabic">
                    <label class="text-[11px] font-bold text-gray-400">الطول (سم)</label>
                    <input type="number" x-model="form.height" min="50" max="280" placeholder="175"
                        class="bg-gray-50 border border-gray-200 focus:border-primary rounded-xl px-3.5 py-2.5 text-sm outline-none w-full">
                </div>
                <div class="flex flex-col gap-1 font-arabic">
                    <label class="text-[11px] font-bold text-gray-400">نسبة الدهون (%)</label>
                    <input type="number" x-model="form.bodyFat" min="1" max="70" step="0.1" placeholder="18"
                        class="bg-gray-50 border border-gray-200 focus:border-primary rounded-xl px-3.5 py-2.5 text-sm outline-none w-full">
                </div>
                <div class="flex flex-col gap-1 font-arabic">
                    <label class="text-[11px] font-bold text-gray-400">الكتلة العضلية (كجم)</label>
                    <input type="number" x-model="form.muscleMass" min="10" max="200" step="0.1" placeholder="35"
                        class="bg-gray-50 border border-gray-200 focus:border-primary rounded-xl px-3.5 py-2.5 text-sm outline-none w-full">
                </div>
            </div>
            <div class="flex flex-col gap-2 font-arabic border-t border-gray-100 pt-4">
                <p class="text-xs font-black text-textColor">مستوى اللياقة</p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="form.fitnessLevel = 'beginner'"
                        :class="form.fitnessLevel==='beginner' ? 'bg-primary text-white border-primary' : 'bg-gray-50 text-gray-500 border-gray-200'"
                        class="flex flex-col items-center gap-1 py-2.5 px-2 rounded-2xl border-2 transition-all cursor-pointer">
                        <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">sprint</span>
                        <span class="text-[11px] font-black">مبتدئ</span>
                    </button>
                    <button type="button" @click="form.fitnessLevel = 'intermediate'"
                        :class="form.fitnessLevel==='intermediate' ? 'bg-amber-500 text-white border-amber-500' : 'bg-gray-50 text-gray-500 border-gray-200'"
                        class="flex flex-col items-center gap-1 py-2.5 px-2 rounded-2xl border-2 transition-all cursor-pointer">
                        <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">fitness_center</span>
                        <span class="text-[11px] font-black">متوسط</span>
                    </button>
                    <button type="button" @click="form.fitnessLevel = 'advanced'"
                        :class="form.fitnessLevel==='advanced' ? 'bg-green-600 text-white border-green-600' : 'bg-gray-50 text-gray-500 border-gray-200'"
                        class="flex flex-col items-center gap-1 py-2.5 px-2 rounded-2xl border-2 transition-all cursor-pointer">
                        <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">emoji_events</span>
                        <span class="text-[11px] font-black">متقدم</span>
                    </button>
                </div>
            </div>
            <div class="flex flex-col gap-3 border-t border-gray-100 pt-4">
                <div class="flex flex-col gap-1 font-arabic">
                    <label class="text-[11px] font-bold text-gray-400">تاريخ التقييم</label>
                    <input type="date" x-model="form.date"
                        class="bg-gray-50 border border-gray-200 focus:border-primary rounded-xl px-3.5 py-2.5 text-sm outline-none w-full">
                </div>
                <div class="flex flex-col gap-1 font-arabic">
                    <label class="text-[11px] font-bold text-gray-400">ملاحظات</label>
                    <textarea x-model="form.notes" rows="3" placeholder="ملاحظات التدريب..."
                        class="bg-gray-50 border border-gray-200 focus:border-primary rounded-xl px-3.5 py-2.5 text-sm outline-none resize-none w-full"></textarea>
                </div>
            </div>
        </div>

        <div class="px-7 py-4 border-t border-gray-100 bg-white flex gap-3">
            <button @click="open = false"
                class="flex-1 py-3 rounded-2xl font-black font-arabic text-sm text-gray-400 bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-all">إلغاء</button>
            <button @click="submit()" :disabled="saving || !form.weight"
                class="flex-1 py-3 rounded-2xl font-black font-arabic text-sm text-white bg-gradient-to-l from-violet-600 to-primary hover:shadow-lg transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                <template x-if="!saving">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">save</span>
                        حفظ التقييم
                    </span>
                </template>
                <template x-if="saving">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
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
     MAIN LAYOUT
══════════════════════════════════════ --}}
<div class="dash-wrap" x-data="profilePage()">

    <x-web.sidebar :coach="$coach" active="subscribers"/>

    <main class="flex flex-col gap-5 p-5 lg:p-8 overflow-y-auto">

        {{-- Toast --}}
        <div class="toast-fixed" style="direction:rtl"
             :class="[toast.show ? 'show' : '', toast.ok ? 'bg-green-800 text-white' : 'bg-red-800 text-white']"
             x-text="toast.msg"></div>

        {{-- ── Topbar ── --}}
        <div class="flex items-center justify-between anim anim-1">
            <div class="flex items-center gap-3" style="direction:rtl">
                <a href="{{ route('coach.subscribers.index') }}"
                   class="w-9 h-9 rounded-xl bg-white border border-gray-100 flex items-center justify-center hover:bg-gray-50 transition flex-shrink-0">
                    <span class="material-symbols-rounded text-gray-500" style="font-size:18px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">arrow_forward</span>
                </a>
                <div class="font-arabic">
                    <p class="text-gray-400 text-xs font-bold">المشتركون ← سجل شخصي</p>
                    <h1 class="font-display text-xl lg:text-2xl text-textColor font-black leading-tight">{{ $member->name }}</h1>
                </div>
            </div>
            <button class="lg:hidden w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center"
                @click="sideOpen = true">
                <span class="material-symbols-rounded text-textColor" style="font-size:20px">menu</span>
            </button>
        </div>

        {{-- ── Profile overview card ── --}}
        <div class="card anim anim-2" style="direction:rtl">
            <div class="flex items-start gap-5 flex-wrap">

                {{-- Avatar --}}
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/10 to-primary/20 flex items-center justify-center font-black text-primary text-2xl font-arabic flex-shrink-0">
                    {{ mb_substr($member->name, 0, 1) }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0 font-arabic">
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <h2 class="font-black text-textColor text-lg leading-none">{{ $member->name }}</h2>
                        @if($subscription)
                            @php
                                $subDaysLeft = $subscription->end_date
                                    ? (int) now()->startOfDay()->diffInDays($subscription->end_date->startOfDay(), false)
                                    : null;
                            @endphp
                            @if($subDaysLeft !== null && $subDaysLeft > 0)
                                <span class="text-[10px] font-black text-green-700 bg-green-50 border border-green-100 px-2 py-0.5 rounded-full">
                                    نشط · {{ $subDaysLeft }} يوم متبقي
                                </span>
                            @elseif($subDaysLeft !== null && $subDaysLeft <= 0)
                                <span class="text-[10px] font-black text-red-600 bg-red-50 border border-red-100 px-2 py-0.5 rounded-full">منتهي الاشتراك</span>
                            @endif
                        @else
                            <span class="text-[10px] font-black text-gray-400 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded-full">لا يوجد اشتراك</span>
                        @endif
                    </div>
                    <p class="text-gray-400 text-xs font-bold mb-3">{{ $member->email }}</p>

                    <div class="flex items-center gap-4 flex-wrap text-xs font-bold">
                        @if($member->profile?->date_of_birth)
                            <span class="flex items-center gap-1 text-gray-500">
                                <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">cake</span>
                                {{ $member->profile->age }} سنة
                            </span>
                        @endif
                        @if($member->profile?->height)
                            <span class="flex items-center gap-1 text-gray-500">
                                <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">height</span>
                                {{ $member->profile->height }} سم
                            </span>
                        @endif
                        @if($member->profile?->current_weight)
                            <span class="flex items-center gap-1 text-gray-500">
                                <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">monitor_weight</span>
                                {{ $member->profile->current_weight }} كجم
                            </span>
                        @endif
                        @if($member->profile?->goal_weight)
                            <span class="flex items-center gap-1 text-primary">
                                <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">flag</span>
                                الهدف: {{ $member->profile->goal_weight }} كجم
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Subscription mini card --}}
                @if($subscription)
                <div class="bg-blue-50 rounded-2xl p-4 font-arabic border border-blue-100 flex-shrink-0" style="min-width:160px">
                    <p class="text-[10px] font-bold text-gray-400 mb-1">الخطة الحالية</p>
                    <p class="font-black text-primary text-sm leading-tight">{{ $subscription->plan?->name ?? '—' }}</p>
                    @if($subscription->start_date)
                        <p class="text-[10px] text-gray-400 font-bold mt-2">
                            بدأ: {{ $subscription->start_date->isoFormat('D MMM Y') }}
                        </p>
                    @endif
                    @if($subscription->end_date)
                        <p class="text-[10px] text-gray-400 font-bold">
                            ينتهي: {{ $subscription->end_date->isoFormat('D MMM Y') }}
                        </p>
                    @endif
                </div>
                @endif

            </div>
        </div>

        {{-- ── Attendance stats (4 cards) ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 anim anim-3">
            <div class="card flex flex-col gap-2" style="direction:rtl">
                <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-green-600" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check_circle</span>
                </div>
                <p class="text-gray-400 text-xs font-bold font-arabic">مرات الحضور</p>
                <p class="font-display text-3xl font-black text-textColor leading-none">{{ $presentCount }}</p>
            </div>
            <div class="card flex flex-col gap-2" style="direction:rtl">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-amber-500" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">schedule</span>
                </div>
                <p class="text-gray-400 text-xs font-bold font-arabic">مرات التأخر</p>
                <p class="font-display text-3xl font-black text-textColor leading-none">{{ $lateCount }}</p>
            </div>
            <div class="card flex flex-col gap-2" style="direction:rtl">
                <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-red-500" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">cancel</span>
                </div>
                <p class="text-gray-400 text-xs font-bold font-arabic">مرات الغياب</p>
                <p class="font-display text-3xl font-black text-textColor leading-none">{{ $absentCount }}</p>
            </div>
            <div class="card flex flex-col gap-2" style="direction:rtl">
                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">bar_chart</span>
                </div>
                <p class="text-gray-400 text-xs font-bold font-arabic">معدل الحضور</p>
                <p class="font-display text-3xl font-black text-textColor leading-none">
                    {{ $attendanceRate }}<span class="text-base font-bold text-gray-400">%</span>
                </p>
            </div>
        </div>

        {{-- ── Today attendance ── --}}
        @if(! $todayIsRest)
        <div class="card anim anim-4" style="direction:rtl">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="font-arabic">
                    <p class="text-gray-400 text-xs font-bold mb-0.5">{{ now()->isoFormat('dddd، D MMMM Y') }}</p>
                    <h3 class="font-black text-textColor text-sm">تسجيل حضور اليوم</h3>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="markToday('present')"
                        :disabled="marking"
                        :class="todayStatus === 'present' ? 'active' : ''"
                        class="att-btn att-present">
                        <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check_circle</span>
                        حاضر
                    </button>
                    <button @click="markToday('late')"
                        :disabled="marking"
                        :class="todayStatus === 'late' ? 'active' : ''"
                        class="att-btn att-late">
                        <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">schedule</span>
                        متأخر
                    </button>
                    <button @click="markToday('absent')"
                        :disabled="marking"
                        :class="todayStatus === 'absent' ? 'active' : ''"
                        class="att-btn att-absent">
                        <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">cancel</span>
                        غائب
                    </button>
                </div>
            </div>
        </div>
        @else
        <div class="card anim anim-4 flex items-center gap-3" style="direction:rtl">
            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-slate-400" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">bedtime</span>
            </div>
            <div class="font-arabic">
                <p class="font-black text-textColor text-sm">يوم راحة</p>
                <p class="text-gray-400 text-xs font-bold">{{ now()->isoFormat('dddd، D MMMM Y') }}</p>
            </div>
        </div>
        @endif

        {{-- ── Attendance heatmap (last 90 days) ── --}}
        <div class="card anim anim-5" style="direction:rtl">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div class="font-arabic">
                    <h3 class="font-black text-textColor text-sm">سجل الحضور</h3>
                    <p class="text-gray-400 text-xs font-bold">آخر 90 يوم</p>
                </div>
                {{-- Legend --}}
                <div class="flex items-center gap-3 flex-wrap font-arabic">
                    <span class="flex items-center gap-1 text-[10px] font-bold text-gray-500">
                        <span class="w-3 h-3 rounded-sm bg-green-500 inline-block"></span> حاضر
                    </span>
                    <span class="flex items-center gap-1 text-[10px] font-bold text-gray-500">
                        <span class="w-3 h-3 rounded-sm bg-amber-400 inline-block"></span> متأخر
                    </span>
                    <span class="flex items-center gap-1 text-[10px] font-bold text-gray-500">
                        <span class="w-3 h-3 rounded-sm bg-red-400 inline-block"></span> غائب
                    </span>
                    <span class="flex items-center gap-1 text-[10px] font-bold text-gray-500">
                        <span class="w-3 h-3 rounded-sm bg-slate-300 inline-block"></span> راحة
                    </span>
                    <span class="flex items-center gap-1 text-[10px] font-bold text-gray-500">
                        <span class="w-3 h-3 rounded-sm bg-gray-200 inline-block"></span> بدون تسجيل
                    </span>
                </div>
            </div>

            {{-- Day-of-week labels --}}
            <div class="flex gap-1 mb-1 font-arabic" style="padding-right:0">
                <div style="display:grid;grid-template-rows:repeat(7,13px);gap:3px;margin-left:6px;margin-top:2px">
                    @foreach(['أح','إث','ث','أر','خ','ج','س'] as $lbl)
                        <div style="height:13px;line-height:13px;font-size:9px;font-weight:800;color:#9ca3af;white-space:nowrap">{{ $lbl }}</div>
                    @endforeach
                </div>
                <div class="hm-grid overflow-x-auto">
                    @foreach($grid90 as $cell)
                        <div class="hm-cell hm-{{ $cell['status'] }}"
                             @if($cell['status'] !== 'empty') title="{{ $cell['day'] }} {{ $cell['date'] }}" @endif></div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Recent attendance list ── --}}
        <div class="card anim anim-6" style="padding:0; overflow:hidden; direction:rtl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-black text-textColor text-sm font-arabic">آخر السجلات</h3>
                <span class="text-[11px] font-bold text-gray-400 font-arabic">{{ $markedCount }} سجل إجمالي</span>
            </div>

            @forelse($recentAttendance as $att)
            @php
                $statusLabel = ['present' => 'حاضر', 'late' => 'متأخر', 'absent' => 'غائب'][$att->status] ?? $att->status;
                $statusClass = ['present' => 'status-present', 'late' => 'status-late', 'absent' => 'status-absent'][$att->status] ?? '';
            @endphp
            <div class="flex items-center gap-4 px-6 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition">
                <div class="font-arabic flex-1">
                    <p class="font-bold text-textColor text-sm">{{ $att->attended_at->isoFormat('dddd') }}</p>
                    <p class="text-xs text-gray-400 font-bold">{{ $att->attended_at->isoFormat('D MMMM Y') }}</p>
                </div>
                <span class="text-[11px] font-black font-arabic px-3 py-1 rounded-full {{ $statusClass }}">{{ $statusLabel }}</span>
                @if($att->notes)
                    <p class="text-xs text-gray-400 font-arabic hidden lg:block max-w-[200px] truncate">{{ $att->notes }}</p>
                @endif
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-10 gap-2 font-arabic">
                <span class="material-symbols-rounded text-gray-300" style="font-size:36px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 36">event_busy</span>
                <p class="text-gray-400 font-bold text-sm">لا توجد سجلات حضور بعد</p>
            </div>
            @endforelse
        </div>

        {{-- ── Evaluations ── --}}
        <div class="card" style="padding:0; overflow:hidden; direction:rtl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-black text-textColor text-sm font-arabic">سجل التقييمات</h3>
                <button @click="openEval()"
                    class="flex items-center gap-1.5 text-[11px] font-black font-arabic text-violet-700 bg-violet-50 border border-violet-200 px-3 py-2 rounded-xl hover:bg-violet-100 transition">
                    <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">add</span>
                    تقييم جديد
                </button>
            </div>

            @forelse($evaluations as $idx => $eval)
            @php
                $prevEval   = $evaluations[$idx + 1] ?? null;
                $weightDiff = $prevEval ? round($eval->weight - $prevEval->weight, 1) : null;
                $levelLabels = ['beginner' => 'مبتدئ', 'intermediate' => 'متوسط', 'advanced' => 'متقدم'];
                $levelColors = ['beginner' => 'text-primary bg-blue-50 border-blue-100', 'intermediate' => 'text-amber-700 bg-amber-50 border-amber-100', 'advanced' => 'text-green-700 bg-green-50 border-green-100'];
            @endphp
            <div class="flex items-start gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50/40 transition flex-wrap lg:flex-nowrap">
                {{-- Date --}}
                <div class="font-arabic flex-shrink-0 text-center" style="min-width:80px">
                    <p class="font-black text-textColor text-lg leading-none">{{ $eval->evaluated_at->isoFormat('D') }}</p>
                    <p class="text-[11px] font-bold text-gray-400">{{ $eval->evaluated_at->isoFormat('MMM Y') }}</p>
                </div>

                {{-- Stats --}}
                <div class="flex-1 grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="font-arabic">
                        <p class="text-[10px] font-bold text-gray-400 mb-0.5">الوزن</p>
                        <div class="flex items-center gap-1.5">
                            <span class="font-black text-textColor text-sm">{{ $eval->weight }} كجم</span>
                            @if($weightDiff !== null)
                                <span class="text-[10px] font-black {{ $weightDiff < 0 ? 'text-green-600' : ($weightDiff > 0 ? 'text-red-500' : 'text-gray-400') }}">
                                    {{ $weightDiff > 0 ? '+' : '' }}{{ $weightDiff }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($eval->height)
                    <div class="font-arabic">
                        <p class="text-[10px] font-bold text-gray-400 mb-0.5">الطول</p>
                        <span class="font-black text-textColor text-sm">{{ $eval->height }} سم</span>
                    </div>
                    @endif
                    @if($eval->body_fat_percentage)
                    <div class="font-arabic">
                        <p class="text-[10px] font-bold text-gray-400 mb-0.5">نسبة الدهون</p>
                        <span class="font-black text-textColor text-sm">{{ $eval->body_fat_percentage }}%</span>
                    </div>
                    @endif
                    @if($eval->muscle_mass)
                    <div class="font-arabic">
                        <p class="text-[10px] font-bold text-gray-400 mb-0.5">الكتلة العضلية</p>
                        <span class="font-black text-textColor text-sm">{{ $eval->muscle_mass }} كجم</span>
                    </div>
                    @endif
                </div>

                {{-- Fitness level + notes --}}
                <div class="flex flex-col gap-1.5 flex-shrink-0">
                    <span class="text-[10px] font-black font-arabic px-2 py-1 rounded-full border self-start {{ $levelColors[$eval->fitness_level] ?? 'text-gray-500 bg-gray-50 border-gray-200' }}">
                        {{ $levelLabels[$eval->fitness_level] ?? $eval->fitness_level }}
                    </span>
                    @if($eval->notes)
                        <p class="text-[10px] text-gray-400 font-arabic max-w-[180px] line-clamp-2">{{ $eval->notes }}</p>
                    @endif
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-10 gap-2 font-arabic">
                <span class="material-symbols-rounded text-gray-300" style="font-size:36px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 36">assignment</span>
                <p class="text-gray-400 font-bold text-sm">لا توجد تقييمات بعد</p>
            </div>
            @endforelse
        </div>

        {{-- ── Weight chart ── --}}
        @if($weightLogs->count() >= 2)
        <div class="card" style="direction:rtl">
            <div class="mb-4 font-arabic">
                <h3 class="font-black text-textColor text-sm">تطور الوزن</h3>
                <p class="text-gray-400 text-xs font-bold">{{ $weightLogs->count() }} قياس مسجّل</p>
            </div>
            <div style="position:relative; height:200px">
                <canvas id="weightChart"></canvas>
            </div>
        </div>
        @endif

    </main>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
    window.__profileData = {
        userId:      {{ $member->id }},
        todayStatus: @js($todayAttendance?->status ?? ''),
        urls: {
            attendance: @js(route('coach.subscribers.attendance')),
            evaluation: @js(route('coach.subscribers.evaluation')),
        },
        csrf: @js(csrf_token()),
        member: {
            height: @js((float)($member->profile?->height ?? 0)),
            weight: @js((float)($member->profile?->current_weight ?? 0)),
        },
        weightChart: {
            labels: [@foreach($weightLogs as $log)@js($log->logged_at->format('d/m/y')),@endforeach],
            data:   [@foreach($weightLogs as $log){{ $log->weight }},@endforeach],
        },
    };

    document.addEventListener('alpine:init', () => {

        Alpine.data('evalModalProfile', () => ({
            open:   false,
            saving: false,
            form:   { weight: '', height: '', bodyFat: '', muscleMass: '', fitnessLevel: 'beginner', notes: '', date: '' },

            openModal() {
                this.form = {
                    weight:       window.__profileData.member.weight || '',
                    height:       window.__profileData.member.height || '',
                    bodyFat:      '',
                    muscleMass:   '',
                    fitnessLevel: 'beginner',
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
                    const res  = await fetch(window.__profileData.urls.evaluation, {
                        method:  'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.__profileData.csrf },
                        body: JSON.stringify({
                            user_id:             window.__profileData.userId,
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
                        this.open = false;
                        window.location.reload();
                    }
                } catch (e) { console.error(e); }
                this.saving = false;
            },

            init() {
                window.addEventListener('open-eval-modal-profile', () => this.openModal());
            },
        }));

        Alpine.data('profilePage', () => ({
            sideOpen:    false,
            todayStatus: window.__profileData.todayStatus,
            marking:     false,
            toast:       { show: false, msg: '', ok: true },

            showToast(msg, ok = true) {
                this.toast = { show: true, msg, ok };
                setTimeout(() => this.toast.show = false, 3000);
            },

            async markToday(status) {
                if (this.marking) return;
                this.marking = true;
                try {
                    const res  = await fetch(window.__profileData.urls.attendance, {
                        method:  'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.__profileData.csrf },
                        body: JSON.stringify({ user_id: window.__profileData.userId, status }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.todayStatus = data.status;
                        const labels = { present: 'تم تسجيل الحضور ✓', late: 'تم تسجيل التأخر ✓', absent: 'تم تسجيل الغياب ✓' };
                        this.showToast(labels[data.status] || 'تم الحفظ ✓');
                    } else {
                        this.showToast('حدث خطأ، حاول مرة أخرى', false);
                    }
                } catch { this.showToast('خطأ في الاتصال بالخادم', false); }
                this.marking = false;
            },

            openEval() {
                window.dispatchEvent(new CustomEvent('open-eval-modal-profile'));
            },
        }));

    });

    // Weight chart
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('weightChart');
        if (!ctx) return;
        const d = window.__profileData.weightChart;
        if (!d.data.length) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: d.labels,
                datasets: [{
                    data: d.data,
                    borderColor: '#174DAD',
                    backgroundColor: 'rgba(23,77,173,0.06)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#174DAD',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.y + ' كجم',
                        },
                        bodyFont: { family: 'Cairo' },
                        titleFont: { family: 'Cairo' },
                    },
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { family: 'Cairo', size: 11 }, callback: v => v + ' كجم' },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Cairo', size: 10 }, maxTicksLimit: 10 },
                    },
                },
            },
        });
    });
</script>
@endsection

@props([
    'totalUsers'          => 0,
    'totalClients'        => 0,
    'newClientsThisMonth' => 0,
    'firstSessionTime'    => null,
    'pendingBookings'     => 0,
    'monthlyRevenue'      => 0,
    'pendingBookingsList' => collect(),
    'activeClients'       => collect(),
])

@php
    $coach = auth()->user();
    $sarIcon = '<svg width="14" height="16" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="inline-block flex-shrink-0" style="vertical-align:middle"><path d="M9.36633 2.59339C10.0415 1.83554 10.4564 1.4953 11.2713 1.06514V13.6848L9.36633 14.0784V2.59339Z" fill="currentColor"/><path d="M15.4529 8.93793C15.8478 8.10434 15.8943 7.73386 16 6.87871L1.39805 10.0494C1.05179 10.8207 0.940326 11.2518 0.886964 12.0176L15.4529 8.93793Z" fill="currentColor"/><path d="M15.4529 12.8033C15.8478 11.9697 15.8943 11.5992 16 10.744L9.43602 12.1334C9.38956 12.8975 9.44292 13.2895 9.38956 14.0552L15.4529 12.8033Z" fill="currentColor"/><path d="M15.4529 16.668C15.8478 15.8345 15.8943 15.464 16 14.6088L10.0168 15.9077C9.7148 16.3245 9.52895 17.0191 9.38956 17.92L15.4529 16.668Z" fill="currentColor"/><path d="M5.95136 15.3519C6.53213 14.6341 7.13614 13.7311 7.5543 12.9901L0.51109 14.5167C0.164822 15.2881 0.0533618 15.7192 0 16.4849L5.95136 15.3519Z" fill="currentColor"/><path d="M5.64935 1.52825C6.32448 0.770398 6.73938 0.430158 7.5543 0V13.0364L5.64935 13.4301V1.52825Z" fill="currentColor"/></svg>';
@endphp

{{-- ══════════════════════════════════════
     CONFIRM BOOKING MODAL
══════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        booking: null,
        clientName: '',
        formAction: '',
        autoCalc: true,
        planDuration: 30,
        saving: false,

        days: [
            { name: 'الأحد',    short: 'أحد',    order: 1, isRest: false },
            { name: 'الاثنين',  short: 'اثنين',  order: 2, isRest: false },
            { name: 'الثلاثاء', short: 'ثلاثاء', order: 3, isRest: false },
            { name: 'الأربعاء', short: 'أربعاء', order: 4, isRest: true  },
            { name: 'الخميس',   short: 'خميس',   order: 5, isRest: false },
            { name: 'الجمعة',   short: 'جمعة',   order: 6, isRest: false },
            { name: 'السبت',    short: 'سبت',    order: 7, isRest: true  },
        ],

        init() {
            window.addEventListener('open-confirm-booking', (e) => {
                const d         = e.detail;
                this.booking    = d;
                this.clientName = d.clientName ?? '';
                this.formAction = d.formAction ?? '';
                this.planDuration = d.planDuration || 30;
                this.autoCalc   = true;
                this.saving     = false;
                this.open       = true;

                // Default rest days
                this.days.forEach(day => {
                    day.isRest = (day.order === 4 || day.order === 7);
                });

                this.$nextTick(() => {
                    const today = new Date();
                    const formatted = today.getFullYear() + '-' +
                        String(today.getMonth() + 1).padStart(2, '0') + '-' +
                        String(today.getDate()).padStart(2, '0');

                    document.getElementById('cb_start_date').value     = d.startDate ?? formatted;
                    document.getElementById('cb_height').value         = d.height ?? '';
                    document.getElementById('cb_birth_date').value     = d.birthDate ?? '';
                    document.getElementById('cb_start_weight').value   = d.startWeight ?? '';
                    document.getElementById('cb_current_weight').value = d.currentWeight ?? '';
                    document.getElementById('cb_goal_weight').value    = d.goalWeight ?? '';

                    if (this.autoCalc) {
                        this.calcEnd(document.getElementById('cb_start_date').value);
                    }
                });
            });
        },

        calcEnd(startDate) {
            if (!startDate) return;
            const parts = startDate.split('-');
            const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            d.setDate(d.getDate() + parseInt(this.planDuration));
            const y   = d.getFullYear();
            const m   = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            document.getElementById('cb_end_date').value = `${y}-${m}-${day}`;
        },

        toggleDay(index) { this.days[index].isRest = !this.days[index].isRest; },

        get workoutCount() { return this.days.filter(d => !d.isRest).length; },
        get restCount()    { return this.days.filter(d => d.isRest).length; },

        close() { this.open = false; }
    }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="direction:rtl"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="close()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    {{-- Modal --}}
    <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-[520px] max-h-[92vh] overflow-hidden flex flex-col"
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
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:22px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">how_to_reg</span>
                </div>
                <div class="font-arabic">
                    <h3 class="font-black text-textColor text-[15px] leading-none">تأكيد وتفعيل الباقة</h3>
                    <p class="text-gray-400 text-[11px] font-bold mt-1 flex items-center gap-1">
                        <span class="material-symbols-rounded" style="font-size:12px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">person</span>
                        <span x-text="clientName"></span>
                    </p>
                </div>
            </div>
            <button @click="close()" class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-gray-100 transition group">
                <span class="material-symbols-rounded text-gray-400 group-hover:text-gray-600 transition" style="font-size:16px">close</span>
            </button>
        </div>

        {{-- Scrollable Content --}}
        <div class="overflow-y-auto flex-1 px-7 py-5">
            <form :action="formAction" method="POST" class="flex flex-col gap-6" @submit="saving = true" id="confirmBookingForm">
                @csrf
                @method('PATCH')

                {{-- ── قسم 1: تواريخ الباقة ── --}}
                <div class="flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-primary/10 flex items-center justify-center">
                                <span class="material-symbols-rounded text-primary" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">date_range</span>
                            </div>
                            <p class="text-xs font-black text-textColor font-arabic">تواريخ الباقة</p>
                        </div>
                        <label class="flex items-center gap-1.5 cursor-pointer select-none">
                            <span class="text-[10px] font-bold font-arabic" :class="autoCalc ? 'text-primary' : 'text-gray-300'">تلقائي</span>
                            <button type="button"
                                @click="autoCalc = !autoCalc; if(autoCalc) { let s = document.getElementById('cb_start_date').value; if(s) calcEnd(s); }"
                                class="relative w-8 h-[18px] rounded-full transition-colors duration-200"
                                :class="autoCalc ? 'bg-primary' : 'bg-gray-200'">
                                <div class="absolute top-[2px] w-[14px] h-[14px] rounded-full bg-white shadow-sm transition-all duration-200"
                                     :class="autoCalc ? 'right-[2px]' : 'right-[14px]'"></div>
                            </button>
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> تاريخ البداية
                            </label>
                            <input id="cb_start_date" type="date" name="start_date" required
                                @change="if(autoCalc) calcEnd($event.target.value)"
                                class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full">
                        </div>
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> تاريخ النهاية
                            </label>
                            <input id="cb_end_date" type="date" name="end_date" required
                                :readonly="autoCalc"
                                class="border rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full"
                                :class="autoCalc ? 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-50/80 border-gray-200 focus:border-primary focus:bg-white'">
                        </div>
                    </div>
                    <p class="text-[10px] font-bold font-arabic text-gray-300 flex items-center gap-1" x-show="autoCalc">
                        <span class="material-symbols-rounded" style="font-size:11px">info</span>
                        مدة الباقة الافتراضية: <span class="text-primary font-black" x-text="planDuration + ' يوم'"></span>
                    </p>
                </div>

                {{-- ── قسم 2: جدول الأسبوع ── --}}
                <div class="flex flex-col gap-3 border-t border-gray-100 pt-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-orange-100 flex items-center justify-center">
                                <span class="material-symbols-rounded text-orange-500" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">calendar_month</span>
                            </div>
                            <p class="text-xs font-black text-textColor font-arabic">جدول الأسبوع</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] font-black font-arabic text-green-600 bg-green-50 px-2 py-0.5 rounded-full" x-text="workoutCount + ' تمرين'"></span>
                            <span class="text-[9px] font-black font-arabic text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full" x-text="restCount + ' راحة'"></span>
                        </div>
                    </div>
                    <p class="text-[10px] font-bold font-arabic text-gray-300">
                        اضغط على اليوم لتحويله بين تمرين
                        <span class="inline-flex items-center text-green-500"><span class="material-symbols-rounded" style="font-size:10px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">fitness_center</span></span>
                        وراحة
                        <span class="inline-flex items-center text-amber-500"><span class="material-symbols-rounded" style="font-size:10px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">bedtime</span></span>
                        (يمكن اختيار أكثر من يوم راحة)
                    </p>
                    <div class="grid grid-cols-7 gap-1.5">
                        <template x-for="(day, index) in days" :key="index">
                            <button type="button" @click="toggleDay(index)"
                                class="relative flex flex-col items-center gap-1 py-2.5 px-0.5 rounded-2xl border-2 transition-all duration-300 cursor-pointer select-none group overflow-hidden"
                                :class="day.isRest
                                    ? 'bg-gradient-to-b from-amber-50 to-orange-50/50 border-amber-200/80 hover:border-amber-300 shadow-sm shadow-amber-100/50'
                                    : 'bg-gradient-to-b from-green-50 to-emerald-50/50 border-green-200/80 hover:border-green-300 shadow-sm shadow-green-100/50'">
                                <div class="absolute inset-0 rounded-2xl opacity-0 group-active:opacity-100 transition-opacity duration-150"
                                     :class="day.isRest ? 'bg-amber-200/30' : 'bg-green-200/30'"></div>
                                <span class="material-symbols-rounded transition-all duration-300 relative z-10"
                                      :class="day.isRest ? 'text-amber-500' : 'text-green-500'"
                                      style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20"
                                      x-text="day.isRest ? 'bedtime' : 'fitness_center'"></span>
                                <span class="text-[9px] font-black font-arabic leading-none relative z-10"
                                      :class="day.isRest ? 'text-amber-700' : 'text-green-700'"
                                      x-text="day.short"></span>
                                <span class="text-[7px] font-bold font-arabic relative z-10 mt-0.5"
                                      :class="day.isRest ? 'text-amber-400' : 'text-green-400'"
                                      x-text="day.isRest ? 'راحة' : 'تمرين'"></span>
                            </button>
                        </template>
                    </div>
                    <template x-for="(day, index) in days" :key="'cb-input-' + index">
                        <input type="hidden" :name="'day_types[' + day.order + ']'" :value="day.isRest ? 'rest' : 'workout'">
                    </template>
                </div>

                {{-- ── قسم 3: بيانات العميل ── --}}
                <div class="flex flex-col gap-3 border-t border-gray-100 pt-5">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-blue-100 flex items-center justify-center">
                            <span class="material-symbols-rounded text-blue-500" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">person</span>
                        </div>
                        <p class="text-xs font-black text-textColor font-arabic">بيانات العميل</p>
                    </div>

                    <div class="flex flex-col gap-1 font-arabic">
                        <label class="text-[11px] font-bold text-gray-400">تاريخ الميلاد</label>
                        <input id="cb_birth_date" type="date" name="birth_date"
                            class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400">الطول (سم)</label>
                            <div class="relative">
                                <input id="cb_height" type="number" name="height" min="100" max="250" placeholder="170" required
                                    class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full pl-10">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300">سم</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400">وزن البداية (كجم)</label>
                            <div class="relative">
                                <input id="cb_start_weight" type="number" name="start_weight" min="30" max="300" step="0.1" placeholder="80" required
                                    class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full pl-10">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300">كجم</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400">الوزن الحالي (كجم)</label>
                            <div class="relative">
                                <input id="cb_current_weight" type="number" name="current_weight" min="30" max="300" step="0.1" placeholder="80"
                                    class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full pl-10">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300">كجم</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400">الوزن المستهدف (كجم)</label>
                            <div class="relative">
                                <input id="cb_goal_weight" type="number" name="goal_weight" min="30" max="300" step="0.1" placeholder="70" required
                                    class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full pl-10">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300">كجم</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── قسم 4: ماذا سيحدث؟ ── --}}
                <div class="bg-gradient-to-br from-primary/[0.04] to-blue-50/50 rounded-2xl p-4 border border-primary/10">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-rounded text-primary" style="font-size:16px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">info</span>
                        <p class="text-xs font-black text-primary font-arabic">ماذا سيحدث بعد التأكيد؟</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        @foreach(['تفعيل الاشتراك وحفظ تواريخ الباقة', 'إنشاء برنامج تدريبي مع أيام الراحة المحددة', 'تسجيل وزن البداية في سجل الوزن', 'إنشاء ملف تعريف افتراضي للعميل'] as $item)
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-rounded text-green-500" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check_circle</span>
                            <span class="text-[11px] font-bold text-gray-500 font-arabic">{{ $item }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="px-7 py-4 border-t border-gray-100 bg-white flex gap-3">
            <button type="button" @click="close()"
                class="flex-1 py-3 rounded-2xl font-black font-arabic text-sm text-gray-400 bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-all">إلغاء</button>
            <button type="submit" form="confirmBookingForm" :disabled="saving"
                class="flex-1 py-3 rounded-2xl font-black font-arabic text-sm text-white bg-gradient-to-l from-primary to-blue-600 hover:shadow-lg hover:shadow-primary/25 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                <template x-if="!saving">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check_circle</span>
                        تأكيد وتفعيل
                    </span>
                </template>
                <template x-if="saving">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        جاري التفعيل...
                    </span>
                </template>
            </button>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════
     EDIT CLIENT MODAL
══════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        subscriptionId: null,
        clientName: '',
        formAction: '',
        autoCalc: true,
        planDuration: 30,
        saving: false,

        days: [
            { name: 'الأحد',    short: 'أحد',    order: 1, isRest: false },
            { name: 'الاثنين',  short: 'اثنين',  order: 2, isRest: false },
            { name: 'الثلاثاء', short: 'ثلاثاء', order: 3, isRest: false },
            { name: 'الأربعاء', short: 'أربعاء', order: 4, isRest: true  },
            { name: 'الخميس',   short: 'خميس',   order: 5, isRest: false },
            { name: 'الجمعة',   short: 'جمعة',   order: 6, isRest: false },
            { name: 'السبت',    short: 'سبت',    order: 7, isRest: true  },
        ],

        init() {
            window.addEventListener('open-edit-client', (e) => {
                const d           = e.detail;
                this.subscriptionId = d.subscriptionId;
                this.clientName     = d.clientName;
                this.formAction     = `/coach/subscriptions/${d.subscriptionId}/update-client`;
                this.planDuration   = d.planDuration || 30;
                this.autoCalc       = true;
                this.saving         = false;
                this.open           = true;

                // تحميل أيام الراحة لو موجودة
                if (d.restDays && Array.isArray(d.restDays)) {
                    const restDays = d.restDays.map(Number);

                    this.days.forEach(day => {
                        day.isRest = restDays.includes(Number(day.order));
                    });
                } else {
                    this.days.forEach(day => {
                        day.isRest = (day.order === 6 || day.order === 7);
                    });
                }

                this.$nextTick(() => {
                    document.getElementById('ec_height').value         = d.height        ?? '';
                    document.getElementById('ec_current_weight').value = d.currentWeight ?? '';
                    document.getElementById('ec_start_weight').value   = d.startWeight   ?? '';
                    document.getElementById('ec_goal_weight').value    = d.goalWeight    ?? '';
                    document.getElementById('ec_start_date').value     = d.startDate     ?? '';
                    document.getElementById('ec_plan_id').value        = d.planId        ?? '';

                    if (this.autoCalc && d.startDate) {
                        this.calcEnd(d.startDate);
                    } else {
                        document.getElementById('ec_end_date').value = d.endDate ?? '';
                    }
                });
            });
        },

        calcEnd(startDate) {
            if (!startDate) return;
            const parts = startDate.split('-');
            const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            d.setDate(d.getDate() + parseInt(this.planDuration));
            const y   = d.getFullYear();
            const m   = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            document.getElementById('ec_end_date').value = `${y}-${m}-${day}`;
        },

        toggleDay(index) { this.days[index].isRest = !this.days[index].isRest; },

        get workoutCount() { return this.days.filter(d => !d.isRest).length; },
        get restCount()    { return this.days.filter(d => d.isRest).length; },

        close() { this.open = false; }
    }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="direction:rtl"
>
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="close()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-[520px] max-h-[92vh] overflow-hidden flex flex-col"
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
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-100 to-primary/10 flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:22px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">manage_accounts</span>
                </div>
                <div class="font-arabic">
                    <h3 class="font-black text-textColor text-[15px] leading-none">تعديل بيانات العميل</h3>
                    <p class="text-gray-400 text-[11px] font-bold mt-1 flex items-center gap-1">
                        <span class="material-symbols-rounded" style="font-size:12px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">person</span>
                        <span x-text="clientName"></span>
                    </p>
                </div>
            </div>
            <button @click="close()" class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-gray-100 transition group">
                <span class="material-symbols-rounded text-gray-400 group-hover:text-gray-600 transition" style="font-size:16px">close</span>
            </button>
        </div>

        {{-- Scrollable Content --}}
        <div class="overflow-y-auto flex-1 px-7 py-5">
            <form :action="formAction" method="POST" class="flex flex-col gap-6" @submit="saving = true" id="editClientForm">
                @csrf
                @method('PATCH')

                {{-- ── قسم 1: بيانات الجسم ── --}}
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-primary/10 flex items-center justify-center">
                            <span class="material-symbols-rounded text-primary" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">monitor_weight</span>
                        </div>
                        <p class="text-xs font-black text-textColor font-arabic">بيانات الجسم</p>
                    </div>

                    <div class="flex flex-col gap-1 font-arabic">
                        <label class="text-[11px] font-bold text-gray-400">الطول (سم)</label>
                        <div class="relative">
                            <input id="ec_height" type="number" name="height" min="100" max="250" placeholder="170"
                                class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full pl-10">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-300">سم</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2.5">
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400">وزن البداية</label>
                            <div class="relative">
                                <input id="ec_start_weight" type="number" name="start_weight" min="30" max="300" step="0.1" placeholder="80"
                                    class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3 py-2.5 text-sm outline-none transition-all w-full pl-9">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[9px] font-bold text-gray-300">كجم</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400">الحالي</label>
                            <div class="relative">
                                <input id="ec_current_weight" type="number" name="current_weight" min="30" max="300" step="0.1" placeholder="75"
                                    class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3 py-2.5 text-sm outline-none transition-all w-full pl-9">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[9px] font-bold text-gray-300">كجم</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400">المستهدف</label>
                            <div class="relative">
                                <input id="ec_goal_weight" type="number" name="goal_weight" min="30" max="300" step="0.1" placeholder="70"
                                    class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3 py-2.5 text-sm outline-none transition-all w-full pl-9">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[9px] font-bold text-gray-300">كجم</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── قسم 2: جدول الأسبوع ── --}}
                <div class="flex flex-col gap-3 border-t border-gray-100 pt-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-orange-100 flex items-center justify-center">
                                <span class="material-symbols-rounded text-orange-500" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">calendar_month</span>
                            </div>
                            <p class="text-xs font-black text-textColor font-arabic">جدول الأسبوع</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] font-black font-arabic text-green-600 bg-green-50 px-2 py-0.5 rounded-full" x-text="workoutCount + ' تمرين'"></span>
                            <span class="text-[9px] font-black font-arabic text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full" x-text="restCount + ' راحة'"></span>
                        </div>
                    </div>
                    <p class="text-[10px] font-bold font-arabic text-gray-300">
                        اضغط على اليوم لتحويله بين تمرين
                        <span class="inline-flex items-center text-green-500"><span class="material-symbols-rounded" style="font-size:10px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">fitness_center</span></span>
                        وراحة
                        <span class="inline-flex items-center text-amber-500"><span class="material-symbols-rounded" style="font-size:10px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">bedtime</span></span>
                    </p>
                    <div class="grid grid-cols-7 gap-1.5">
                        <template x-for="(day, index) in days" :key="index">
                            <button type="button" @click="toggleDay(index)"
                                class="relative flex flex-col items-center gap-1 py-2.5 px-0.5 rounded-2xl border-2 transition-all duration-300 cursor-pointer select-none group overflow-hidden"
                                :class="day.isRest
                                    ? 'bg-gradient-to-b from-amber-50 to-orange-50/50 border-amber-200/80 hover:border-amber-300 shadow-sm shadow-amber-100/50'
                                    : 'bg-gradient-to-b from-green-50 to-emerald-50/50 border-green-200/80 hover:border-green-300 shadow-sm shadow-green-100/50'">
                                <div class="absolute inset-0 rounded-2xl opacity-0 group-active:opacity-100 transition-opacity duration-150"
                                     :class="day.isRest ? 'bg-amber-200/30' : 'bg-green-200/30'"></div>
                                <span class="material-symbols-rounded transition-all duration-300 relative z-10"
                                      :class="day.isRest ? 'text-amber-500' : 'text-green-500'"
                                      style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20"
                                      x-text="day.isRest ? 'bedtime' : 'fitness_center'"></span>
                                <span class="text-[9px] font-black font-arabic leading-none relative z-10"
                                      :class="day.isRest ? 'text-amber-700' : 'text-green-700'"
                                      x-text="day.short"></span>
                                <span class="text-[7px] font-bold font-arabic relative z-10 mt-0.5"
                                      :class="day.isRest ? 'text-amber-400' : 'text-green-400'"
                                      x-text="day.isRest ? 'راحة' : 'تمرين'"></span>
                            </button>
                        </template>
                    </div>
                    <template x-for="(day, index) in days" :key="'ec-input-' + index">
                        <input type="hidden" :name="'day_types[' + day.order + ']'" :value="day.isRest ? 'rest' : 'workout'">
                    </template>
                </div>

                {{-- ── قسم 3: تواريخ الباقة ── --}}
                <div class="flex flex-col gap-3 border-t border-gray-100 pt-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-violet-100 flex items-center justify-center">
                                <span class="material-symbols-rounded text-violet-500" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">date_range</span>
                            </div>
                            <p class="text-xs font-black text-textColor font-arabic">تواريخ الباقة</p>
                        </div>
                        <label class="flex items-center gap-1.5 cursor-pointer select-none">
                            <span class="text-[10px] font-bold font-arabic" :class="autoCalc ? 'text-primary' : 'text-gray-300'">تلقائي</span>
                            <button type="button"
                                @click="autoCalc = !autoCalc; if(autoCalc) { let s = document.getElementById('ec_start_date').value; if(s) calcEnd(s); }"
                                class="relative w-8 h-[18px] rounded-full transition-colors duration-200"
                                :class="autoCalc ? 'bg-primary' : 'bg-gray-200'">
                                <div class="absolute top-[2px] w-[14px] h-[14px] rounded-full bg-white shadow-sm transition-all duration-200"
                                     :class="autoCalc ? 'right-[2px]' : 'right-[14px]'"></div>
                            </button>
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> تاريخ البداية
                            </label>
                            <input id="ec_start_date" type="date" name="start_date"
                                @change="if(autoCalc) calcEnd($event.target.value)"
                                class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full">
                        </div>
                        <div class="flex flex-col gap-1 font-arabic">
                            <label class="text-[11px] font-bold text-gray-400 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> تاريخ النهاية
                            </label>
                            <input id="ec_end_date" type="date" name="end_date"
                                :readonly="autoCalc"
                                class="border rounded-xl px-3.5 py-2.5 text-sm outline-none transition-all w-full"
                                :class="autoCalc ? 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-50/80 border-gray-200 focus:border-primary focus:bg-white'">
                        </div>
                    </div>
                    <p class="text-[10px] font-bold font-arabic text-gray-300 flex items-center gap-1" x-show="autoCalc">
                        <span class="material-symbols-rounded" style="font-size:11px">info</span>
                        مدة الباقة: <span class="text-primary font-black" x-text="planDuration + ' يوم'"></span>
                    </p>
                </div>

                {{-- ── قسم 4: الخطة ── --}}
                <div class="flex flex-col gap-3 border-t border-gray-100 pt-5">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-green-100 flex items-center justify-center">
                            <span class="material-symbols-rounded text-green-600" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">card_membership</span>
                        </div>
                        <p class="text-xs font-black text-textColor font-arabic">الخطة</p>
                    </div>
                    <select id="ec_plan_id" name="plan_id"
                        class="bg-gray-50/80 border border-gray-200 focus:border-primary focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-arabic outline-none transition-all w-full">
                        @foreach(\App\Models\Plan::orderBy('name')->get() as $plan)
                            <option value="{{ $plan->id }}">{{ __('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="px-7 py-4 border-t border-gray-100 bg-white flex gap-3">
            <button type="button" @click="close()"
                class="flex-1 py-3 rounded-2xl font-black font-arabic text-sm text-gray-400 bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-all">إلغاء</button>
            <button type="submit" form="editClientForm" :disabled="saving"
                class="flex-1 py-3 rounded-2xl font-black font-arabic text-sm text-white bg-gradient-to-l from-primary to-blue-600 hover:shadow-lg hover:shadow-primary/25 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                <template x-if="!saving">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">save</span>
                        حفظ التعديلات
                    </span>
                </template>
                <template x-if="saving">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
     DASHBOARD WRAP
══════════════════════════════════════ --}}
<div class="dash-wrap" x-data="{ sideOpen: false }">

    {{-- ══════════════ SIDEBAR ══════════════ --}}
    <x-web.sidebar :coach="$coach"/>

    {{-- ══════════════ MAIN CONTENT ══════════════ --}}
    <main class="flex flex-col gap-5 p-5 lg:p-8 overflow-y-auto">

        {{-- ─ Topbar ─ --}}
        <div class="flex items-center justify-between">
            <div class="font-arabic">
                <p class="text-gray-400 text-sm mb-1 font-bold">
                    {{ now()->isoFormat('dddd، D MMMM Y') }}
                </p>
                <h1 class="font-display text-2xl lg:text-3xl text-textColor font-black">
                    أهلاً كابتن، {{ explode(' ', $coach->name)[0] }} 👋
                </h1>
            </div>
            <button class="lg:hidden w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center"
                @click="sideOpen = true">
                <span class="material-symbols-rounded text-textColor" style="font-size:20px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">menu</span>
            </button>
        </div>

        {{-- ══ ROW 1: Stats ══ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 anim anim-1" id="overview">

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-green-600" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">people</span>
                </div>
                <div class="font-arabic">
                    <p class="text-gray-400 text-xs font-bold mb-1">إجمالي المستخدمين</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none">{{ $totalUsers }}</p>
                </div>
                <span class="text-[10px] font-black font-arabic text-green-700 bg-green-50 border border-green-100 px-2 py-0.5 rounded-full self-start">{{ $totalClients }} جدد هذا الشهر</span>
            </div>

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">group</span>
                </div>
                <div class="font-arabic">
                    <p class="text-gray-400 text-xs font-bold mb-1">إجمالي المشتركين</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none">{{ $totalClients }}</p>
                </div>
                <span class="text-[10px] font-black font-arabic text-blue-700 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full self-start">{{ $newClientsThisMonth }} جدد هذا الشهر</span>
            </div>

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-amber-500" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">pending_actions</span>
                </div>
                <div class="font-arabic">
                    <p class="text-gray-400 text-xs font-bold mb-1">في انتظار التأكيد</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none">{{ $pendingBookings }}</p>
                </div>
                @if($pendingBookings > 0)
                    <span class="text-[10px] font-black font-arabic text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full self-start animate-pulse">يحتاج مراجعة</span>
                @else
                    <span class="text-[10px] font-black font-arabic text-green-700 bg-green-50 border border-green-100 px-2 py-0.5 rounded-full self-start">كل شيء تمام ✓</span>
                @endif
            </div>

            <div class="card flex flex-col gap-3">
                <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center">
                    <span class="material-symbols-rounded text-primary" style="font-size:20px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">payments</span>
                </div>
                <div class="font-arabic">
                    <p class="text-gray-400 text-xs font-bold mb-1">إيرادات الشهر</p>
                    <p class="font-display text-3xl font-black text-textColor leading-none">{{ number_format($monthlyRevenue) }}</p>
                </div>
                <span class="text-[10px] font-black font-arabic text-green-700 bg-green-50 border border-green-100 px-2 py-0.5 rounded-full self-start flex items-center gap-1">
                    {!! $sarIcon !!} ريال سعودي
                </span>
            </div>
        </div>

        {{-- ══ ROW 2: Bookings + Clients ══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- ── طلبات الحجز ── --}}
            <div class="card anim anim-2" id="bookings">
                <div class="flex items-center justify-between mb-4" style="direction:rtl">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-amber-500" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">pending_actions</span>
                        <h2 class="font-black text-textColor text-base font-arabic">طلبات الحجز</h2>
                        @if($pendingBookings > 0)
                            <span class="text-[10px] font-black text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full font-arabic">{{ $pendingBookings }}</span>
                        @endif
                    </div>
                    <a href="{{ route('coach.bookings') }}" class="text-[11px] font-black text-primary font-arabic hover:underline">عرض الكل</a>
                </div>

                @forelse($pendingBookingsList as $booking)
                <div class="flex flex-col gap-2 py-3 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="flex items-center gap-3" style="direction:rtl">
                        <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center font-black text-primary font-arabic text-sm flex-shrink-0">
                            {{ mb_substr($booking->subscription->user->name ?? '?', 0, 1) }}
                        </div>
                        <div class="flex-1 font-arabic">
                            <p class="font-black text-textColor text-sm leading-none mb-0.5">{{ $booking->subscription->user->name ?? '—' }}</p>
                            <p class="text-gray-400 text-xs font-bold flex items-center gap-1">
                                <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">calendar_today</span>
                                {{ \Carbon\Carbon::parse($booking->meeting_date)->locale('ar')->isoFormat('D MMMM') }}
                                <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">schedule</span>
                                {{ \Carbon\Carbon::parse($booking->meeting_time)->format('H:i') }}
                            </p>
                        </div>
                        <div class="flex gap-2 flex-shrink-0">
                            {{-- ✅ زر التأكيد — يفتح الـ modal بالـ event الصحيح --}}
                            <button type="button"
                                @click="window.dispatchEvent(new CustomEvent('open-confirm-booking', {
                                    detail: {
                                        bookingId:    {{ $booking->id }},
                                        clientName:   '{{ addslashes($booking->subscription->user->name ?? '') }}',
                                        formAction:   '{{ route('coach.bookings.confirm', $booking->id) }}',
                                        planDuration: {{ $booking->subscription->plan->duration_days ?? 30 }}
                                    }
                                }))"
                                class="flex items-center gap-1 text-[11px] font-black font-arabic text-green-700 bg-green-50 border border-green-200 px-3 py-1.5 rounded-lg hover:bg-green-100 transition">
                                <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check_circle</span>
                                تأكيد
                            </button>

                            <form method="POST" action="{{ route('coach.bookings.reject', $booking->id) }}"
                                  onsubmit="return confirm('هل أنت متأكد من رفض هذا الحجز؟')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="flex items-center gap-1 text-[11px] font-black font-arabic text-red-600 bg-red-50 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-100 transition">
                                    <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">cancel</span>
                                    رفض
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Meet Link --}}
                    <form method="POST" action="{{ route('coach.bookings.meet-link', $booking->id) }}"
                          class="flex items-center gap-2 pt-1" style="direction:rtl">
                        @csrf @method('PATCH')
                        <span class="material-symbols-rounded text-blue-500 flex-shrink-0" style="font-size:16px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">videocam</span>
                        <input type="url" name="meet_link" value="{{ $booking->meet_link ?? '' }}"
                            placeholder="https://meet.google.com/xxx-xxxx-xxx"
                            class="flex-1 text-xs font-arabic bg-gray-50 border border-gray-200 focus:border-primary rounded-lg px-3 py-1.5 outline-none transition-colors">
                        <button type="submit"
                            class="flex-shrink-0 text-[11px] font-black font-arabic text-primary bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition">حفظ</button>
                        @if($booking->meet_link)
                            <a href="{{ $booking->meet_link }}" target="_blank" rel="noopener noreferrer"
                               class="flex-shrink-0 flex items-center gap-1 text-[11px] font-black font-arabic text-white bg-blue-500 border border-blue-500 px-3 py-1.5 rounded-lg hover:bg-blue-600 transition">
                                <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">open_in_new</span>
                                فتح
                            </a>
                        @endif
                    </form>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 gap-3 font-arabic text-center">
                    <span class="material-symbols-rounded text-green-400" style="font-size:40px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 48">check_circle</span>
                    <p class="text-gray-400 text-sm font-bold">مفيش حجوزات في الانتظار</p>
                </div>
                @endforelse
            </div>

            {{-- ── المشتركين النشطين ── --}}
            <div class="card anim anim-3" id="clients">
                <div class="flex items-center justify-between mb-4" style="direction:rtl">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-primary" style="font-size:18px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">group</span>
                        <h2 class="font-black text-textColor text-base font-arabic">المشتركين النشطين</h2>
                    </div>
                    <a href="#" class="text-[11px] font-black text-primary font-arabic hover:underline">عرض الكل</a>
                </div>

                @forelse($activeClients as $sub)
                @php
                    $client   = $sub->user;
                    $plan     = $sub->plan;
                    $profile  = \App\Models\UserProfile::where('user_id', $sub->user_id)->first();
                    $program  = \App\Models\Program::where('user_id', $sub->user_id)->first();
                    $restDays = $program
                        ? $program->days()->where('type', 'rest')->pluck('day_order')->toArray()
                        : [4, 7];
                    $totalD   = ($sub->start_date && $sub->end_date)
                                ? (int) $sub->start_date->startOfDay()->diffInDays($sub->end_date->startOfDay()) : 1;
                    $usedD    = $sub->start_date
                                ? (int) $sub->start_date->startOfDay()->diffInDays(now()->startOfDay()) : 0;
                    $pct      = $totalD > 0 ? min(100, (int) round(($usedD / $totalD) * 100)) : 0;
                    $daysLeft = $sub->end_date
                                ? (int) now()->startOfDay()->diffInDays($sub->end_date->startOfDay(), false) : 0;
                @endphp
                <div class="flex items-center gap-3 py-3 border-b border-gray-100 last:border-0 last:pb-0" style="direction:rtl">

                    <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center font-black text-primary font-arabic text-sm flex-shrink-0">
                        {{ mb_substr($client->name ?? '?', 0, 1) }}
                    </div>

                    <div class="flex-1 font-arabic">
                        <p class="font-black text-textColor text-sm leading-none mb-0.5">{{ $client->name ?? '—' }}</p>
                        <p class="text-gray-400 text-xs font-bold mb-1.5">{{ $plan ? (__('messages.plans_data.'.$plan->key.'.name', [], null) ?: $plan->name) : '—' }}</p>
                        <div class="macro-bar-wrap" style="width:90px">
                            <div class="macro-bar-fill bg-primary" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>

                    @if($daysLeft <= 5 && $daysLeft > 0)
                        <span class="flex items-center gap-1 text-[10px] font-black font-arabic text-amber-700 bg-amber-50 border border-amber-100 px-2 py-1 rounded-full">
                            <span class="material-symbols-rounded" style="font-size:12px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">warning</span>
                            تنتهي قريباً
                        </span>
                    @elseif($daysLeft <= 0)
                        <span class="flex items-center gap-1 text-[10px] font-black font-arabic text-red-600 bg-red-50 border border-red-100 px-2 py-1 rounded-full">
                            <span class="material-symbols-rounded" style="font-size:12px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">block</span>
                            منتهية
                        </span>
                    @else
                        <span class="flex items-center gap-1 text-[10px] font-black font-arabic text-green-700 bg-green-50 border border-green-100 px-2 py-1 rounded-full">
                            <span class="material-symbols-rounded" style="font-size:12px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 20">check_circle</span>
                            نشط
                        </span>
                    @endif

                    {{-- ✏️ زر التعديل — بيبعت restDays صح --}}
                    <button type="button"
                        @click="window.dispatchEvent(new CustomEvent('open-edit-client', {
                            detail: {
                                subscriptionId: {{ $sub->id }},
                                clientName:     '{{ addslashes($client->name ?? '') }}',
                                height:         '{{ $profile->height ?? '' }}',
                                startWeight:    '{{ $profile->start_weight ?? '' }}',
                                currentWeight:  '{{ $profile->current_weight ?? '' }}',
                                goalWeight:     '{{ $profile->goal_weight ?? '' }}',
                                startDate:      '{{ $sub->start_date?->format('Y-m-d') ?? '' }}',
                                endDate:        '{{ $sub->end_date?->format('Y-m-d') ?? '' }}',
                                planId:         '{{ $sub->plan_id ?? '' }}',
                                planDuration:   {{ $sub->plan->duration_days ?? 30 }},
                                restDays:       {{ json_encode(array_map('intval', $restDays)) }}
                            }
                        }))"
                        class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-blue-50 hover:text-primary transition flex-shrink-0">
                        <span class="material-symbols-rounded text-gray-400" style="font-size:16px;font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 20">edit</span>
                    </button>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 gap-3 font-arabic text-center">
                    <span class="material-symbols-rounded text-gray-300" style="font-size:40px;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 48">group</span>
                    <p class="text-gray-400 text-sm font-bold">مفيش مشتركين نشطين دلوقتي</p>
                </div>
                @endforelse
            </div>

        </div>
    </main>
</div>
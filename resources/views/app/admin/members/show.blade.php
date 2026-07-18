@extends('layouts.admin.app')

@section('title', 'ملف ' . $member->name)
@section('page-title', 'ملف العضو')
@section('page-subtitle', $member->name)

@section('style')
<style>
    /* ─── Section Card ─── */
    .s-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .s-card-header {
        display: flex; align-items: center; gap: .75rem;
        padding: 1rem 1.4rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .s-card-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .s-card-body { padding: 1.25rem 1.4rem; }

    /* ─── Info Row ─── */
    .info-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: .65rem 0;
        border-bottom: 1px solid #f8fafc;
        gap: 1rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-size: .75rem; font-weight: 800; color: #94a3b8; white-space: nowrap; }
    .info-value { font-size: .85rem; font-weight: 700; color: #1e293b; text-align: left; }

    /* ─── Badge ─── */
    .badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .28rem .8rem; border-radius: 999px;
        font-size: .71rem; font-weight: 800; white-space: nowrap;
    }
    .badge .dot { width:5px; height:5px; border-radius:50%; background:currentColor; opacity:.8; flex-shrink:0; }
    .badge-green  { background:#dcfce7; color:#16a34a; }
    .badge-blue   { background:#dbeafe; color:#1d4ed8; }
    .badge-purple { background:#ede9fe; color:#7c3aed; }
    .badge-yellow { background:#fef9c3; color:#a16207; }
    .badge-red    { background:#fee2e2; color:#dc2626; }
    .badge-gray   { background:#f1f5f9; color:#64748b; }
    .badge-orange { background:#ffedd5; color:#c2410c; }

    /* ─── Table ─── */
    .admin-table { width:100%; border-collapse:collapse; }
    .admin-table th {
        padding:.6rem 1.1rem; text-align:right;
        font-size:.69rem; font-weight:900; color:#94a3b8;
        letter-spacing:.07em; background:#fafbfc;
        border-bottom:1px solid #f1f5f9; white-space:nowrap;
    }
    .admin-table td {
        padding:.8rem 1.1rem; font-size:.83rem;
        font-weight:600; color:#374151;
        border-bottom:1px solid #f8fafc; vertical-align:middle;
    }
    .admin-table tbody tr:last-child td { border-bottom:none; }
    .admin-table tbody tr:hover td { background:#f8fafc; }

    /* ─── Stat Pill ─── */
    .stat-pill {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        background: #f8fafc; border-radius: 14px; padding: .85rem .5rem;
        border: 1px solid #e8edf5; flex: 1; min-width: 0;
    }

    /* ─── Attendance dot ─── */
    .att-dot {
        width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
        display: inline-block;
    }

    /* ─── Hero ─── */
    .hero-avatar {
        width: 72px; height: 72px; border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; font-weight: 900; flex-shrink: 0;
    }
</style>
@endsection

@section('content')

@php
    $isMale    = $member->gender === 'male';
    $profile   = $member->profile;

    $statusMap = [
        'active'   => ['label' => 'نشط',     'class' => 'badge-green'],
        'inactive' => ['label' => 'غير نشط', 'class' => 'badge-gray'],
        'banned'   => ['label' => 'محظور',   'class' => 'badge-red'],
    ];
    $subStatusMap = [
        'active'    => ['label' => 'نشط',    'class' => 'badge-green'],
        'expired'   => ['label' => 'منتهي',  'class' => 'badge-red'],
        'cancelled' => ['label' => 'ملغي',   'class' => 'badge-gray'],
        'waiting'   => ['label' => 'انتظار', 'class' => 'badge-yellow'],
    ];
    $bookingStatusMap = [
        'pending'   => ['label' => 'قيد الانتظار', 'class' => 'badge-yellow'],
        'confirmed' => ['label' => 'مؤكد',          'class' => 'badge-green'],
        'rejected'  => ['label' => 'مرفوض',         'class' => 'badge-red'],
        'completed' => ['label' => 'مكتمل',         'class' => 'badge-blue'],
    ];
    $planColors = [
        'النخبة'  => 'badge-blue',
        'إيليت'   => 'badge-purple',
        'الأساسي' => 'badge-gray',
    ];
    $st = $statusMap[$member->status] ?? ['label' => $member->status, 'class' => 'badge-gray'];
@endphp

{{-- ══════════ BACK + HERO ══════════ --}}
<div class="mb-6">

    {{-- Back --}}
    <a href="{{ route('admin.members.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-500 hover:text-slate-800 transition mb-4">
        <span class="material-symbols-rounded" style="font-size:18px">arrow_forward</span>
        العودة إلى قائمة الأعضاء
    </a>

    {{-- Hero Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

        {{-- Top section: Avatar + Name + Chips --}}
        <div class="flex items-start gap-4 p-5 sm:p-6">

            {{-- Avatar --}}
            <div class="hero-avatar {{ $isMale ? 'bg-blue-50 text-blue-500' : 'bg-pink-50 text-pink-500' }}">
                {{ mb_substr($member->name, 0, 1) }}
            </div>

            {{-- Name + Info --}}
            <div class="flex-1 min-w-0 pt-1">

                {{-- Name + Badge --}}
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <h2 class="text-xl font-black text-slate-800 leading-tight">{{ $member->name }}</h2>
                    <span class="badge {{ $st['class'] }}">
                        <span class="dot"></span>{{ $st['label'] }}
                    </span>
                </div>

                {{-- Info Chips --}}
                <div class="flex flex-wrap gap-2">

                    <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1.5 rounded-lg">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:13px">alternate_email</span>
                        {{ $member->username }}
                    </span>

                    <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1.5 rounded-lg min-w-0 max-w-full">
                        <span class="material-symbols-rounded text-slate-400 flex-shrink-0" style="font-size:13px">mail</span>
                        <span class="truncate">{{ $member->email }}</span>
                    </span>

                    @if($member->phone)
                    <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1.5 rounded-lg" dir="ltr">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:13px">phone</span>
                        {{ $member->phone }}
                    </span>
                    @endif

                    <span class="inline-flex items-center gap-1.5 bg-slate-100 text-xs font-bold px-3 py-1.5 rounded-lg
                                 {{ $isMale ? 'text-blue-600' : 'text-pink-600' }}">
                        <span class="material-symbols-rounded {{ $isMale ? 'text-blue-400' : 'text-pink-400' }}"
                              style="font-size:13px;font-variation-settings:'FILL' 1">{{ $isMale ? 'man' : 'woman' }}</span>
                        {{ $isMale ? 'ذكر' : 'أنثى' }}
                    </span>

                    <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-500 text-xs font-bold px-3 py-1.5 rounded-lg">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:13px">schedule</span>
                        انضم {{ $member->created_at->diffForHumans() }}
                    </span>

                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div class="border-t border-slate-100 mx-5"></div>

        {{-- Stats Row --}}
        <div class="grid grid-cols-3 divide-x divide-x-reverse divide-slate-100">
            <div class="flex flex-col items-center justify-center py-4 gap-0.5">
                <p class="text-lg font-black text-slate-800">{{ $member->subscriptions->count() }}</p>
                <p class="text-[11px] font-bold text-slate-400">اشتراكات</p>
            </div>
            <div class="flex flex-col items-center justify-center py-4 gap-0.5">
                <p class="text-lg font-black text-slate-800">{{ $member->meetingBookings->count() }}</p>
                <p class="text-[11px] font-bold text-slate-400">حجوزات</p>
            </div>
            <div class="flex flex-col items-center justify-center py-4 gap-0.5">
                <p class="text-lg font-black text-slate-800">{{ $attendanceStats->total ?? 0 }}</p>
                <p class="text-[11px] font-bold text-slate-400">حضور</p>
            </div>
        </div>

    </div>
</div>

{{-- ══════════ INFO GRID ══════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- ── Account Info ── --}}
    <div class="s-card">
        <div class="s-card-header">
            <div class="s-card-icon bg-blue-50">
                <span class="material-symbols-rounded text-blue-500"
                      style="font-size:17px;font-variation-settings:'FILL' 1">manage_accounts</span>
            </div>
            <div>
                <p class="font-black text-slate-800 text-sm">بيانات الحساب</p>
                <p class="text-[11px] text-slate-400 font-semibold">معلومات التسجيل الأساسية</p>
            </div>
        </div>
        <div class="s-card-body">
            <div class="info-row">
                <span class="info-label">الاسم الكامل</span>
                <span class="info-value">{{ $member->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">اسم المستخدم</span>
                <span class="info-value font-mono text-slate-600">{{ $member->username }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">البريد الإلكتروني</span>
                <span class="info-value text-sm">{{ $member->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">رقم الهاتف</span>
                <span class="info-value" dir="ltr">{{ $member->phone ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">الجنس</span>
                <span class="info-value flex items-center gap-1">
                    <span class="material-symbols-rounded {{ $isMale ? 'text-blue-400' : 'text-pink-400' }}"
                          style="font-size:15px;font-variation-settings:'FILL' 1">{{ $isMale ? 'man' : 'woman' }}</span>
                    {{ $isMale ? 'ذكر' : 'أنثى' }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">الحالة</span>
                <span class="badge {{ $st['class'] }}"><span class="dot"></span>{{ $st['label'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">تاريخ التسجيل</span>
                <span class="info-value">{{ $member->created_at->format('d/m/Y — H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">قبول الشروط</span>
                <span class="info-value text-sm">
                    {{ $member->terms_accepted_at ? \Carbon\Carbon::parse($member->terms_accepted_at)->format('d/m/Y') : '—' }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Fitness Profile ── --}}
    <div class="s-card">
        <div class="s-card-header">
            <div class="s-card-icon bg-emerald-50">
                <span class="material-symbols-rounded text-emerald-500"
                      style="font-size:17px;font-variation-settings:'FILL' 1">monitor_weight</span>
            </div>
            <div>
                <p class="font-black text-slate-800 text-sm">البيانات البدنية</p>
                <p class="text-[11px] text-slate-400 font-semibold">الملف الرياضي للعضو</p>
            </div>
        </div>

        @if($profile)
        <div class="p-4 sm:p-5">
            {{-- Weight Stats --}}
            <div class="flex gap-3 mb-5">
                <div class="stat-pill">
                    <p class="text-lg font-black text-slate-800">{{ $profile->start_weight ?? '—' }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">وزن البداية (كج)</p>
                </div>
                <div class="stat-pill">
                    <p class="text-lg font-black text-emerald-600">{{ $profile->current_weight ?? '—' }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">الوزن الحالي (كج)</p>
                </div>
                <div class="stat-pill">
                    <p class="text-lg font-black text-blue-600">{{ $profile->goal_weight ?? '—' }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">وزن الهدف (كج)</p>
                </div>
            </div>
            <div class="s-card-body" style="padding:0">
                <div class="info-row">
                    <span class="info-label">الطول</span>
                    <span class="info-value">{{ $profile->height ? $profile->height . ' سم' : '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">تاريخ الميلاد</span>
                    <span class="info-value">
                        {{ $profile->date_of_birth ? $profile->date_of_birth->format('d/m/Y') : '—' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">العمر</span>
                    <span class="info-value">{{ $profile->age ? $profile->age . ' سنة' : '—' }}</span>
                </div>
                @if($profile->current_weight && $profile->start_weight)
                <div class="info-row">
                    <span class="info-label">التغيير في الوزن</span>
                    @php $diff = round($profile->current_weight - $profile->start_weight, 1); @endphp
                    <span class="info-value font-black {{ $diff < 0 ? 'text-green-600' : ($diff > 0 ? 'text-red-500' : 'text-slate-400') }}">
                        {{ $diff > 0 ? '+' : '' }}{{ $diff }} كج
                    </span>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-10 gap-2 text-slate-300">
            <span class="material-symbols-rounded" style="font-size:36px">fitness_center</span>
            <p class="text-sm font-bold">لم يُكمل العضو ملفه البدني بعد</p>
        </div>
        @endif
    </div>

</div>

{{-- ══════════ ATTENDANCE ══════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Attendance Stats --}}
    <div class="s-card">
        <div class="s-card-header">
            <div class="s-card-icon bg-violet-50">
                <span class="material-symbols-rounded text-violet-500"
                      style="font-size:17px;font-variation-settings:'FILL' 1">event_available</span>
            </div>
            <div>
                <p class="font-black text-slate-800 text-sm">إحصائيات الحضور</p>
                <p class="text-[11px] text-slate-400 font-semibold">ملخص سجل الحضور</p>
            </div>
        </div>
        <div class="s-card-body">
            @if(($attendanceStats->total ?? 0) > 0)
            {{-- Progress Bar --}}
            @php
                $total   = $attendanceStats->total;
                $present = $attendanceStats->present ?? 0;
                $late    = $attendanceStats->late ?? 0;
                $absent  = $attendanceStats->absent ?? 0;
                $pPct    = $total ? round($present / $total * 100) : 0;
                $lPct    = $total ? round($late    / $total * 100) : 0;
                $aPct    = $total ? round($absent  / $total * 100) : 0;
            @endphp
            <div class="flex h-2.5 rounded-full overflow-hidden mb-4 gap-0.5">
                <div class="bg-green-400 rounded-full" style="width:{{ $pPct }}%"></div>
                <div class="bg-yellow-400 rounded-full" style="width:{{ $lPct }}%"></div>
                <div class="bg-red-400 rounded-full"   style="width:{{ $aPct }}%"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="stat-pill">
                    <p class="text-xl font-black text-slate-800">{{ $total }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">إجمالي</p>
                </div>
                <div class="stat-pill">
                    <p class="text-xl font-black text-green-600">{{ $present }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">حاضر</p>
                </div>
                <div class="stat-pill">
                    <p class="text-xl font-black text-yellow-500">{{ $late }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">متأخر</p>
                </div>
                <div class="stat-pill">
                    <p class="text-xl font-black text-red-500">{{ $absent }}</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">غائب</p>
                </div>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-8 gap-2 text-slate-300">
                <span class="material-symbols-rounded" style="font-size:32px">event_busy</span>
                <p class="text-sm font-bold">لا يوجد سجل حضور</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Recent Attendance --}}
    <div class="s-card lg:col-span-2">
        <div class="s-card-header">
            <div class="s-card-icon bg-violet-50">
                <span class="material-symbols-rounded text-violet-500"
                      style="font-size:17px;font-variation-settings:'FILL' 1">checklist</span>
            </div>
            <div>
                <p class="font-black text-slate-800 text-sm">آخر سجلات الحضور</p>
                <p class="text-[11px] text-slate-400 font-semibold">أحدث {{ $recentAttendance->count() }} سجل</p>
            </div>
        </div>
        @if($recentAttendance->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 gap-2 text-slate-300">
            <span class="material-symbols-rounded" style="font-size:32px">event_busy</span>
            <p class="text-sm font-bold">لا يوجد سجل حضور بعد</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th class="hidden sm:table-cell">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttendance as $att)
                    @php
                        $attMap = [
                            'present' => ['label'=>'حاضر',   'dot'=>'bg-green-400',  'class'=>'badge-green'],
                            'late'    => ['label'=>'متأخر',  'dot'=>'bg-yellow-400', 'class'=>'badge-yellow'],
                            'absent'  => ['label'=>'غائب',   'dot'=>'bg-red-400',    'class'=>'badge-red'],
                        ];
                        $a = $attMap[$att->status] ?? ['label'=>$att->status,'dot'=>'bg-slate-300','class'=>'badge-gray'];
                    @endphp
                    <tr>
                        <td class="text-[12px] font-bold text-slate-600">
                            {{ $att->attended_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="att-dot {{ $a['dot'] }}"></span>
                                <span class="text-[12px] font-bold text-slate-700">{{ $a['label'] }}</span>
                            </div>
                        </td>
                        <td class="hidden sm:table-cell text-[12px] text-slate-400">
                            {{ $att->notes ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

{{-- ══════════ SUBSCRIPTIONS ══════════ --}}
<div class="s-card mb-5">
    <div class="s-card-header">
        <div class="s-card-icon bg-blue-50">
            <span class="material-symbols-rounded text-blue-500"
                  style="font-size:17px;font-variation-settings:'FILL' 1">subscriptions</span>
        </div>
        <div>
            <p class="font-black text-slate-800 text-sm">سجل الاشتراكات</p>
            <p class="text-[11px] text-slate-400 font-semibold">{{ $member->subscriptions->count() }} اشتراك</p>
        </div>
    </div>
    @if($member->subscriptions->isEmpty())
    <div class="flex flex-col items-center justify-center py-10 gap-2 text-slate-300">
        <span class="material-symbols-rounded" style="font-size:36px">subscriptions</span>
        <p class="text-sm font-bold">لا يوجد اشتراكات بعد</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>الباقة</th>
                    <th class="hidden sm:table-cell">تاريخ البدء</th>
                    <th class="hidden sm:table-cell">تاريخ الانتهاء</th>
                    <th>المبلغ</th>
                    <th>النوع</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($member->subscriptions->sortByDesc('created_at') as $sub)
                @php
                    $ss   = $subStatusMap[$sub->status] ?? ['label'=>$sub->status,'class'=>'badge-gray'];
                    $pc   = $planColors[$sub->plan?->name ?? ''] ?? 'badge-blue';
                    $sCur = $sub->currency ?? 'SAR';
                    $sMeta = ['SAR'=>['sym'=>'ر.س','dec'=>0],'EGP'=>['sym'=>'ج.م','dec'=>0],'TND'=>['sym'=>'د.ت','dec'=>3],'USD'=>['sym'=>'$','dec'=>2]][$sCur] ?? ['sym'=>$sCur,'dec'=>0];
                @endphp
                <tr>
                    <td>
                        <span class="badge {{ $pc }}">{{ $sub->plan?->name ?? 'غير محدد' }}</span>
                    </td>
                    <td class="hidden sm:table-cell text-[12px] font-bold text-slate-600">
                        {{ $sub->start_date?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="hidden sm:table-cell text-[12px] font-bold text-slate-600">
                        {{ $sub->end_date?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="text-[13px] font-black text-slate-800">
                        {{ number_format((float)$sub->total, $sMeta['dec']) }}
                        <span class="text-[11px] text-slate-400 font-bold">{{ $sMeta['sym'] }}</span>
                    </td>
                    <td>
                        @if($sub->duration_months)
                            <span class="badge badge-blue">{{ $sub->duration_months }} شهور</span>
                        @elseif($sub->is_yearly)
                            <span class="badge badge-purple">سنوي <small>(قديم)</small></span>
                        @else
                            <span class="badge badge-gray">شهري <small>(قديم)</small></span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $ss['class'] }}">
                            <span class="dot"></span>{{ $ss['label'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ══════════ EVALUATIONS + BOOKINGS ══════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Evaluations --}}
    <div class="s-card">
        <div class="s-card-header">
            <div class="s-card-icon bg-orange-50">
                <span class="material-symbols-rounded text-orange-500"
                      style="font-size:17px;font-variation-settings:'FILL' 1">assignment</span>
            </div>
            <div>
                <p class="font-black text-slate-800 text-sm">تقييمات المدرب</p>
                <p class="text-[11px] text-slate-400 font-semibold">{{ $evaluations->count() }} تقييم</p>
            </div>
        </div>
        @if($evaluations->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 gap-2 text-slate-300">
            <span class="material-symbols-rounded" style="font-size:32px">assignment_late</span>
            <p class="text-sm font-bold">لا يوجد تقييمات بعد</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الوزن</th>
                        <th class="hidden sm:table-cell">الدهون %</th>
                        <th class="hidden sm:table-cell">الكتلة العضلية</th>
                        <th>المستوى</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluations as $ev)
                    @php
                        $levelMap = [
                            'beginner'     => ['label'=>'مبتدئ',    'class'=>'badge-gray'],
                            'intermediate' => ['label'=>'متوسط',    'class'=>'badge-blue'],
                            'advanced'     => ['label'=>'متقدم',    'class'=>'badge-purple'],
                            'professional' => ['label'=>'محترف',    'class'=>'badge-orange'],
                        ];
                        $lv = $levelMap[$ev->fitness_level] ?? ['label'=>$ev->fitness_level,'class'=>'badge-gray'];
                    @endphp
                    <tr>
                        <td class="text-[12px] font-bold text-slate-600">
                            {{ $ev->evaluated_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="text-[13px] font-black text-slate-800">
                            {{ $ev->weight ? $ev->weight . ' كج' : '—' }}
                        </td>
                        <td class="hidden sm:table-cell text-[12px] font-bold text-slate-600">
                            {{ $ev->body_fat_percentage ? $ev->body_fat_percentage . '%' : '—' }}
                        </td>
                        <td class="hidden sm:table-cell text-[12px] font-bold text-slate-600">
                            {{ $ev->muscle_mass ? $ev->muscle_mass . ' كج' : '—' }}
                        </td>
                        <td>
                            <span class="badge {{ $lv['class'] }}">{{ $lv['label'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Meeting Bookings --}}
    <div class="s-card">
        <div class="s-card-header">
            <div class="s-card-icon bg-cyan-50">
                <span class="material-symbols-rounded text-cyan-500"
                      style="font-size:17px;font-variation-settings:'FILL' 1">video_call</span>
            </div>
            <div>
                <p class="font-black text-slate-800 text-sm">حجوزات الجلسات</p>
                <p class="text-[11px] text-slate-400 font-semibold">{{ $member->meetingBookings->count() }} حجز</p>
            </div>
        </div>
        @if($member->meetingBookings->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 gap-2 text-slate-300">
            <span class="material-symbols-rounded" style="font-size:32px">event_busy</span>
            <p class="text-sm font-bold">لا يوجد حجوزات بعد</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th class="hidden sm:table-cell">الوقت</th>
                        <th>الحالة</th>
                        <th class="hidden sm:table-cell">رابط</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($member->meetingBookings as $booking)
                    @php
                        $bs = $bookingStatusMap[$booking->status] ?? ['label'=>$booking->status,'class'=>'badge-gray'];
                    @endphp
                    <tr>
                        <td class="text-[12px] font-bold text-slate-600">
                            {{ $booking->meeting_date?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="hidden sm:table-cell text-[12px] font-bold text-slate-600">
                            {{ $booking->meeting_time ? \Carbon\Carbon::parse($booking->meeting_time)->format('H:i') : '—' }}
                        </td>
                        <td>
                            <span class="badge {{ $bs['class'] }}">
                                <span class="dot"></span>{{ $bs['label'] }}
                            </span>
                        </td>
                        <td class="hidden sm:table-cell">
                            @if($booking->meet_link)
                            <a href="{{ $booking->meet_link }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[11px] font-bold text-cyan-500 hover:underline">
                                <span class="material-symbols-rounded" style="font-size:14px">open_in_new</span>
                                فتح
                            </a>
                            @else
                            <span class="text-[11px] text-slate-300 font-bold">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

@endsection

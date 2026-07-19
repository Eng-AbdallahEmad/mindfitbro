@extends('layouts.admin.app')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')
@section('page-subtitle', 'نظرة عامة على المنصة')

@section('style')
<style>
    /* ─── Stat Card ─── */
    .stat-card {
        background: #fff;
        border-radius: 20px;
        padding: 1.4rem 1.6rem;
        border: 1px solid #e2e8f0;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }

    /* ─── Table ─── */
    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }
    .admin-table th {
        background: #f8fafc;
        padding: .7rem 1rem;
        text-align: right;
        font-size: .75rem;
        font-weight: 900;
        color: #94a3b8;
        letter-spacing: .05em;
        text-transform: uppercase;
        border-bottom: 1px solid #e2e8f0;
    }
    .admin-table td {
        padding: .85rem 1rem;
        font-size: .855rem;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-table tr:hover td { background: #f8fafc; }

    /* ─── Badge ─── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .28rem .8rem;
        border-radius: 999px;
        font-size: .71rem;
        font-weight: 800;
        white-space: nowrap;
    }
    .badge-green  { background: #dcfce7; color: #15803d; }
    .badge-blue   { background: #dbeafe; color: #1d4ed8; }
    .badge-purple { background: #ede9fe; color: #7c3aed; }
    .badge-yellow { background: #fef9c3; color: #a16207; }
    .badge-red    { background: #fee2e2; color: #dc2626; }
    .badge-gray   { background: #f1f5f9; color: #64748b; }
    .badge-orange { background: #ffedd5; color: #c2410c; }

    .badge .dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
        opacity: .7;
        flex-shrink: 0;
    }

    /* ─── Avatar ─── */
    .avatar {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 900;
        flex-shrink: 0;
    }

    /* ─── Data Table Card ─── */
    .data-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .data-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .data-card-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ─── Table ─── */
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th {
        padding: .6rem 1.1rem;
        text-align: right;
        font-size: .7rem;
        font-weight: 900;
        color: #b0bec5;
        letter-spacing: .06em;
        background: #fafbfc;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .admin-table td {
        padding: .8rem 1.1rem;
        font-size: .84rem;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr {
        transition: background .15s;
    }
    .admin-table tbody tr:hover td { background: #f8fafc; }
</style>
@endsection

@section('content')

{{-- ══════════ WELCOME BANNER ══════════ --}}
<div class="rounded-2xl overflow-hidden mb-6"
     style="background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%);">
    <div class="flex items-center justify-between px-5 py-5 sm:px-7 sm:py-6">
        <div>
            <p class="text-blue-200 text-xs sm:text-sm font-bold mb-1">مرحباً بعودتك <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1;vertical-align:middle">waving_hand</span></p>
            <h2 class="text-white text-xl sm:text-2xl font-black">{{ Auth::guard('admin')->user()->name }}</h2>
            <p class="text-blue-200/70 text-xs sm:text-sm mt-1">هذا ملخص نشاط المنصة اليوم</p>
        </div>
        <div class="hidden sm:block opacity-10">
            <span class="material-symbols-rounded text-white"
                  style="font-size:96px;font-variation-settings:'FILL' 1">shield_person</span>
        </div>
    </div>
</div>

{{-- ══════════ STATS CARDS ══════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    {{-- Members --}}
    <div class="stat-card">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-rounded text-blue-500"
                      style="font-size:22px;font-variation-settings:'FILL' 1">group</span>
            </div>
            <span class="badge badge-blue">+{{ \App\Models\User::where('role', 'user')->whereDate('created_at', today())->count() }} اليوم</span>
        </div>
        <p class="text-3xl font-black text-slate-800">{{ number_format($stats['members']) }}</p>
        <p class="text-sm font-bold text-slate-400 mt-1">إجمالي الأعضاء</p>
    </div>

    {{-- Coaches --}}
    <div class="stat-card">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center">
                <span class="material-symbols-rounded text-purple-500"
                      style="font-size:22px;font-variation-settings:'FILL' 1">fitness_center</span>
            </div>
            <span class="badge badge-gray">نشطون</span>
        </div>
        <p class="text-3xl font-black text-slate-800">{{ number_format($stats['coaches']) }}</p>
        <p class="text-sm font-bold text-slate-400 mt-1">المدربون</p>
    </div>

    {{-- Subscriptions --}}
    <div class="stat-card">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center">
                <span class="material-symbols-rounded text-green-500"
                      style="font-size:22px;font-variation-settings:'FILL' 1">subscriptions</span>
            </div>
            <span class="badge badge-green">فعّال</span>
        </div>
        <p class="text-3xl font-black text-slate-800">{{ number_format($stats['subscriptions']) }}</p>
        <p class="text-sm font-bold text-slate-400 mt-1">الاشتراكات</p>
    </div>

    {{-- Revenue --}}
    <div class="stat-card">
        <div class="flex items-start justify-between mb-4">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 flex items-center justify-center">
                <span class="material-symbols-rounded text-yellow-500"
                      style="font-size:22px;font-variation-settings:'FILL' 1">payments</span>
            </div>
        </div>
        @php
            $revCurrencyMeta = ['SAR'=>'ر.س','EGP'=>'ج.م','TND'=>'د.ت','USD'=>'$'];
            $revDecimals     = ['SAR'=>0,'EGP'=>0,'TND'=>3,'USD'=>2];
        @endphp
        @forelse($stats['revenue_by_currency'] as $cur => $amount)
        <p class="text-2xl font-black text-slate-800 leading-tight">
            {{ number_format((float)$amount, $revDecimals[$cur] ?? 0) }}
            <span class="text-sm font-bold text-slate-400">{{ $revCurrencyMeta[$cur] ?? $cur }}</span>
        </p>
        @empty
        <p class="text-3xl font-black text-slate-800">0</p>
        @endforelse
        <p class="text-sm font-bold text-slate-400 mt-1">إجمالي الإيرادات</p>
    </div>

</div>

@php
    $avatarColors = [
        ['bg-blue-50',   'text-blue-500'],
        ['bg-violet-50', 'text-violet-500'],
        ['bg-emerald-50','text-emerald-500'],
        ['bg-orange-50', 'text-orange-500'],
        ['bg-pink-50',   'text-pink-500'],
        ['bg-cyan-50',   'text-cyan-500'],
    ];

    $planColors = [
        'النخبة'  => 'badge-blue',
        'إيليت'   => 'badge-blue',
        'الأساسي' => 'badge-gray',
    ];

    $statusMap = [
        'active'    => ['label' => 'نشط',    'class' => 'badge-green'],
        'expired'   => ['label' => 'منتهي',  'class' => 'badge-red'],
        'cancelled' => ['label' => 'ملغي',   'class' => 'badge-gray'],
        'waiting'   => ['label' => 'انتظار', 'class' => 'badge-yellow'],
    ];

    $memberStatusMap = [
        'active'   => ['label' => 'نشط',      'class' => 'badge-green'],
        'inactive' => ['label' => 'غير نشط',  'class' => 'badge-gray'],
        'banned'   => ['label' => 'محظور',    'class' => 'badge-red'],
    ];
@endphp

{{-- ══════════ TABLES ROW ══════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- ── Recent Subscriptions ── --}}
    <div class="data-card">

        {{-- Header --}}
        <div class="data-card-header">
            <div class="flex items-center gap-3">
                <div class="data-card-icon" style="background:#eff6ff;">
                    <span class="material-symbols-rounded text-blue-500"
                          style="font-size:19px;font-variation-settings:'FILL' 1">receipt_long</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm leading-tight">آخر الاشتراكات</p>
                    <p class="text-[11px] text-slate-400 font-semibold mt-0.5">
                        أحدث {{ $recentSubscriptions->count() }} اشتراك مسجّل
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.subscriptions.index') }}" class="text-[11px] font-bold text-blue-500 hover:text-blue-600 flex items-center gap-1 transition">
                عرض الكل
                <span class="material-symbols-rounded" style="font-size:14px">chevron_left</span>
            </a>
        </div>

        {{-- Table --}}
        @if($recentSubscriptions->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 gap-2">
            <span class="material-symbols-rounded text-slate-200" style="font-size:44px">inbox</span>
            <p class="text-sm font-bold text-slate-300">لا توجد اشتراكات بعد</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>المشترك</th>
                        <th>الباقة</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSubscriptions as $i => $sub)
                    @php
                        [$avatarBg, $avatarText] = $avatarColors[$i % count($avatarColors)];
                        $name   = $sub->user?->name ?? $sub->guest_name ?? 'زائر';
                        $email  = $sub->user?->email ?? $sub->guest_email ?? '—';
                        $plan   = $sub->plan?->name ?? 'غير محدد';
                        $pClass = $planColors[$plan] ?? 'badge-blue';
                        $st     = $statusMap[$sub->status] ?? ['label' => $sub->status, 'class' => 'badge-gray'];
                        $dCur   = $sub->currency ?? 'SAR';
                        $dMeta  = ['SAR'=>['sym'=>'ر.س','dec'=>0],'EGP'=>['sym'=>'ج.م','dec'=>0],'TND'=>['sym'=>'د.ت','dec'=>3],'USD'=>['sym'=>'$','dec'=>2]][$dCur] ?? ['sym'=>$dCur,'dec'=>0];
                    @endphp
                    <tr>
                        {{-- Member --}}
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="avatar {{ $avatarBg }} {{ $avatarText }}">
                                    {{ mb_substr($name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-700 text-[13px] truncate max-w-[130px]">{{ $name }}</p>
                                    <p class="text-[11px] text-slate-400 truncate max-w-[130px]">{{ $email }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- Plan --}}
                        <td>
                            <span class="badge {{ $pClass }}">{{ $plan }}</span>
                        </td>
                        {{-- Amount --}}
                        <td style="white-space:nowrap;">
                            <span class="font-black text-slate-800 text-[13px]">{{ number_format((float)$sub->total, $dMeta['dec']) }} <span class="text-[11px] text-slate-400 font-bold">{{ $dMeta['sym'] }}</span></span>
                        </td>
                        {{-- Status --}}
                        <td>
                            <span class="badge {{ $st['class'] }}">
                                <span class="dot"></span>
                                {{ $st['label'] }}
                            </span>
                        </td>
                        {{-- Date --}}
                        <td>
                            <p class="text-[12px] font-bold text-slate-500">{{ $sub->created_at->format('d/m/Y') }}</p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Recent Members ── --}}
    <div class="data-card">

        {{-- Header --}}
        <div class="data-card-header">
            <div class="flex items-center gap-3">
                <div class="data-card-icon" style="background:#f0fdf4;">
                    <span class="material-symbols-rounded text-emerald-500"
                          style="font-size:19px;font-variation-settings:'FILL' 1">person_add</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm leading-tight">أحدث الأعضاء</p>
                    <p class="text-[11px] text-slate-400 font-semibold mt-0.5">
                        آخر {{ $recentMembers->count() }} عضو سجّل في المنصة
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.members.index') }}" class="text-[11px] font-bold text-emerald-500 hover:text-emerald-600 flex items-center gap-1 transition">
                عرض الكل
                <span class="material-symbols-rounded" style="font-size:14px">chevron_left</span>
            </a>
        </div>

        {{-- Table --}}
        @if($recentMembers->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 gap-2">
            <span class="material-symbols-rounded text-slate-200" style="font-size:44px">group</span>
            <p class="text-sm font-bold text-slate-300">لا يوجد أعضاء بعد</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>العضو</th>
                        <th>الجنس</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentMembers as $i => $member)
                    @php
                        $isMale  = $member->gender === 'male';
                        [$mBg, $mText] = $isMale
                            ? ['bg-blue-50',  'text-blue-500']
                            : ['bg-pink-50',  'text-pink-500'];
                        $ms = $memberStatusMap[$member->status] ?? ['label' => $member->status, 'class' => 'badge-gray'];
                    @endphp
                    <tr>
                        {{-- Member --}}
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="avatar {{ $mBg }} {{ $mText }}">
                                    {{ mb_substr($member->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-700 text-[13px] truncate max-w-[140px]">{{ $member->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $member->username }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- Gender --}}
                        <td>
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-rounded {{ $isMale ? 'text-blue-400' : 'text-pink-400' }}"
                                      style="font-size:15px;font-variation-settings:'FILL' 1">
                                    {{ $isMale ? 'man' : 'woman' }}
                                </span>
                                <span class="text-[12px] font-bold text-slate-500">
                                    {{ $isMale ? 'ذكر' : 'أنثى' }}
                                </span>
                            </div>
                        </td>
                        {{-- Status --}}
                        <td>
                            <span class="badge {{ $ms['class'] }}">
                                <span class="dot"></span>
                                {{ $ms['label'] }}
                            </span>
                        </td>
                        {{-- Date --}}
                        <td>
                            <p class="text-[12px] font-bold text-slate-500">{{ $member->created_at->format('d/m/Y') }}</p>
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

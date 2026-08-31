@extends('layouts.admin.app')

@section('title', 'الاشتراكات')
@section('page-title', 'الاشتراكات')
@section('page-subtitle', 'إدارة جميع اشتراكات المنصة')

@section('style')
<style>
    .stat-card {
        background: #fff; border-radius: 18px; padding: 1.2rem 1.4rem;
        border: 1px solid #e8edf5; display: flex; align-items: center; gap: 1rem;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.07); transform: translateY(-2px); }
    .stat-icon { width: 46px; height: 46px; border-radius: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    .search-wrap { position: relative; }
    .search-icon { position: absolute; top: 50%; right: 14px; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 18px; }
    .search-input {
        width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px;
        padding: .7rem 2.7rem .7rem 1rem; font-size: .85rem; font-weight: 600; color: #1e293b;
        font-family: 'Cairo', sans-serif; outline: none; transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .search-input:focus { background: #fff; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
    .search-input::placeholder { color: #94a3b8; }

    .filter-select {
        background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px;
        padding: .68rem 1rem; font-size: .85rem; font-weight: 700; color: #374151;
        font-family: 'Cairo', sans-serif; outline: none; cursor: pointer;
        transition: border-color .2s, background .2s; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: left 12px center; padding-left: 32px;
    }
    .filter-select:focus { background-color: #fff; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }

    .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .25rem .75rem; border-radius: 999px; font-size: .71rem; font-weight: 800; white-space: nowrap; }
    .badge .dot { width:5px; height:5px; border-radius:50%; background:currentColor; opacity:.8; flex-shrink:0; }
    .badge-green  { background:#dcfce7; color:#16a34a; }
    .badge-yellow { background:#fefce8; color:#ca8a04; }
    .badge-gray   { background:#f1f5f9; color:#64748b; }
    .badge-red    { background:#fee2e2; color:#dc2626; }
    .badge-blue   { background:#eff6ff; color:#2563eb; }
    .badge-violet { background:#f5f3ff; color:#7c3aed; }
    .badge-cyan   { background:#ecfeff; color:#0891b2; }

    .plan-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .3rem .8rem; border-radius: 8px; font-size: .75rem; font-weight: 800;
    }

    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { padding: .65rem 1.1rem; text-align: right; font-size: .69rem; font-weight: 900; color: #94a3b8; letter-spacing: .05em; background: #f8fafc; border-bottom: 1px solid #f0f4f8; white-space: nowrap; }
    .admin-table td { padding: .85rem 1.1rem; font-size: .84rem; font-weight: 600; color: #374151; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr { transition: background .12s; }
    .admin-table tbody tr:hover td { background: #f8fafc; }

    .page-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 .5rem; border-radius: 9px; font-size: .8rem; font-weight: 800; text-decoration: none; transition: all .18s; border: 1.5px solid transparent; }
    .page-btn-active  { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .page-btn-normal  { background: #fff; color: #64748b; border-color: #e2e8f0; }
    .page-btn-normal:hover { border-color: #3b82f6; color: #3b82f6; }
    .page-btn-disabled { background: #f8fafc; color: #cbd5e1; border-color: #f1f5f9; pointer-events: none; }

    .filter-tag { display: inline-flex; align-items: center; gap: .4rem; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 8px; padding: .25rem .7rem; font-size: .75rem; font-weight: 800; }

    /* Modals */
    .modal-backdrop { position: fixed; inset: 0; background: rgba(15,23,42,.55); backdrop-filter: blur(3px); z-index: 200; opacity: 0; pointer-events: none; transition: opacity .25s ease; display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .modal-backdrop.is-open { opacity: 1; pointer-events: auto; }
    .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 460px; box-shadow: 0 24px 60px rgba(0,0,0,.18); transform: translateY(16px) scale(.97); transition: transform .25s ease; overflow: hidden; }
    .modal-backdrop.is-open .modal-box { transform: translateY(0) scale(1); }
    .modal-input { width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: .65rem .9rem; font-size: .85rem; font-weight: 600; color: #1e293b; font-family: 'Cairo', sans-serif; outline: none; transition: border-color .2s, box-shadow .2s; }
    .modal-input:focus { background:#fff; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
</style>
@endsection

@section('content')

@php
    $statusMap = [
        'pending_review' => ['بانتظار المراجعة', 'badge-orange', 'pending'],
        'approved'       => ['موافق عليه',        'badge-blue',   'thumb_up'],
        'active'         => ['نشط',               'badge-green',  'check_circle'],
        'expired'        => ['منتهي',              'badge-gray',   'event_busy'],
        'rejected'       => ['مرفوض',             'badge-red',    'cancel'],
        'cancelled'      => ['ملغي',               'badge-red',    'cancel'],
        'waiting'        => ['في الانتظار',        'badge-yellow', 'schedule'],
    ];
    $planColors = [
        1 => ['bg-blue-50',   'text-blue-600'],
        2 => ['bg-violet-50', 'text-violet-600'],
        3 => ['bg-amber-50',  'text-amber-600'],
    ];
@endphp

{{-- Flash --}}
@if(session('success'))
<div id="flashMsg" class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:20px">check_circle</span>
    {{ session('success') }}
    <button onclick="document.getElementById('flashMsg').remove()" class="mr-auto text-green-400 hover:text-green-600 transition">
        <span class="material-symbols-rounded" style="font-size:18px">close</span>
    </button>
</div>
@endif

@if(session('error'))
<div id="flashErrorMsg" class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded text-red-500 flex-shrink-0" style="font-size:20px">error</span>
    {{ session('error') }}
    <button onclick="document.getElementById('flashErrorMsg').remove()" class="mr-auto text-red-400 hover:text-red-600 transition">
        <span class="material-symbols-rounded" style="font-size:18px">close</span>
    </button>
</div>
@endif

{{-- ══ Stats ══ --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

    <div class="stat-card">
        <div class="stat-icon bg-blue-50">
            <span class="material-symbols-rounded text-blue-500" style="font-size:22px;font-variation-settings:'FILL' 1">receipt_long</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['total']) }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">إجمالي</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-green-50">
            <span class="material-symbols-rounded text-green-500" style="font-size:22px;font-variation-settings:'FILL' 1">check_circle</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['active']) }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">نشط</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-orange-50">
            <span class="material-symbols-rounded text-orange-400" style="font-size:22px;font-variation-settings:'FILL' 1">pending</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['approved']) }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">بإنتظار التأكيد</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-slate-100">
            <span class="material-symbols-rounded text-slate-400" style="font-size:22px;font-variation-settings:'FILL' 1">event_busy</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['expired']) }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">منتهي</p>
        </div>
    </div>

    <div class="stat-card lg:col-span-1 col-span-2">
        <div class="stat-icon bg-emerald-50">
            <span class="material-symbols-rounded text-emerald-500" style="font-size:22px;font-variation-settings:'FILL' 1">payments</span>
        </div>
        <div>
            @php
                $revMeta = ['SAR'=>['sym'=>'ر.س','dec'=>0],'EGP'=>['sym'=>'ج.م','dec'=>0],'TND'=>['sym'=>'د.ت','dec'=>3],'USD'=>['sym'=>'$','dec'=>2]];
            @endphp
            @forelse($stats['revenue_by_currency'] as $cur => $amount)
            <p class="text-base font-black text-slate-800 leading-tight" style="white-space:nowrap">
                {{ number_format((float)$amount, $revMeta[$cur]['dec'] ?? 0) }}
                <span class="text-xs font-bold text-slate-400">{{ $revMeta[$cur]['sym'] ?? $cur }}</span>
            </p>
            @empty
            <p class="text-xl font-black text-slate-800 leading-none">0</p>
            @endforelse
            <p class="text-xs font-bold text-slate-400 mt-1">إجمالي الإيرادات</p>
        </div>
    </div>

</div>

{{-- ══ Table Card ══ --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-blue-500" style="font-size:18px;font-variation-settings:'FILL' 1">receipt_long</span>
            </div>
            <div>
                <p class="font-black text-slate-800 text-sm">قائمة الاشتراكات</p>
                <p class="text-[11px] text-slate-400 font-semibold">{{ $subscriptions->total() }} اشتراك</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
        <form method="GET" action="{{ route('admin.subscriptions.index') }}">

            {{-- Search --}}
            <div class="search-wrap mb-3">
                <span class="material-symbols-rounded search-icon">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="ابحث باسم العضو أو البريد الإلكتروني أو كود الكوبون..."
                       class="search-input" autocomplete="off">
            </div>

            {{-- Dropdowns --}}
            <div class="flex flex-wrap gap-2">

                <select name="status" class="filter-select flex-1 min-w-[130px]">
                    <option value="">كل الحالات</option>
                    <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>بانتظار المراجعة</option>
                    <option value="approved"       {{ request('status') === 'approved'       ? 'selected' : '' }}>موافق عليه</option>
                    <option value="active"         {{ request('status') === 'active'         ? 'selected' : '' }}>نشط</option>
                    <option value="expired"        {{ request('status') === 'expired'        ? 'selected' : '' }}>منتهي</option>
                    <option value="rejected"       {{ request('status') === 'rejected'       ? 'selected' : '' }}>مرفوض</option>
                    <option value="cancelled"      {{ request('status') === 'cancelled'      ? 'selected' : '' }}>ملغي</option>
                    <option value="waiting"        {{ request('status') === 'waiting'        ? 'selected' : '' }}>في الانتظار (قديم)</option>
                </select>

                <select name="plan" class="filter-select flex-1 min-w-[120px]">
                    <option value="">كل الباقات</option>
                    @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>
                        {{ $plan->name }}
                    </option>
                    @endforeach
                </select>

                <select name="type" class="filter-select flex-1 min-w-[110px]">
                    <option value="">كل المدد</option>
                    <option value="3"      {{ request('type') === '3'      ? 'selected' : '' }}>٣ شهور</option>
                    <option value="6"      {{ request('type') === '6'      ? 'selected' : '' }}>٦ شهور</option>
                    <option value="legacy" {{ request('type') === 'legacy' ? 'selected' : '' }}>قديم (شهري)</option>
                </select>

                <select name="member" class="filter-select flex-1 min-w-[110px]">
                    <option value="">الكل</option>
                    <option value="registered" {{ request('member') === 'registered' ? 'selected' : '' }}>أعضاء مسجلون</option>
                    <option value="guest"      {{ request('member') === 'guest'      ? 'selected' : '' }}>ضيوف</option>
                </select>

                <button type="submit"
                    class="flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 text-white font-black text-xs px-4 py-2 rounded-xl transition">
                    <span class="material-symbols-rounded" style="font-size:15px">filter_alt</span>
                    فلتر
                </button>

                @if(request()->hasAny(['search','status','plan','type','member']))
                <a href="{{ route('admin.subscriptions.index') }}"
                   class="flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-xs px-4 py-2 rounded-xl transition">
                    <span class="material-symbols-rounded" style="font-size:15px">close</span>
                    مسح
                </a>
                @endif
            </div>

            {{-- Active tags --}}
            @if(request()->hasAny(['search','status','plan','type','member']))
            <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-slate-100">
                @if(request('search'))
                <span class="filter-tag"><span class="material-symbols-rounded" style="font-size:13px">search</span>"{{ request('search') }}"</span>
                @endif
                @if(request('status'))
                <span class="filter-tag"><span class="material-symbols-rounded" style="font-size:13px">circle</span>
                {{ ['pending_review'=>'بانتظار المراجعة','approved'=>'موافق عليه','active'=>'نشط','expired'=>'منتهي','rejected'=>'مرفوض','cancelled'=>'ملغي','waiting'=>'في الانتظار'][request('status')] ?? '' }}
                </span>
                @endif
                @if(request('plan'))
                <span class="filter-tag"><span class="material-symbols-rounded" style="font-size:13px">workspace_premium</span>
                {{ $plans->firstWhere('id', request('plan'))?->name }}
                </span>
                @endif
                @if(request('type'))
                <span class="filter-tag"><span class="material-symbols-rounded" style="font-size:13px">event_repeat</span>
                @php $tMap = ['3'=>'٣ شهور','6'=>'٦ شهور','legacy'=>'قديم']; @endphp
                {{ $tMap[request('type')] ?? request('type') }}
                </span>
                @endif
                @if(request('member'))
                <span class="filter-tag"><span class="material-symbols-rounded" style="font-size:13px">person</span>
                {{ request('member') === 'guest' ? 'ضيوف' : 'أعضاء مسجلون' }}
                </span>
                @endif
            </div>
            @endif

        </form>
    </div>

    {{-- Table --}}
    @if($subscriptions->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-slate-300">
        <span class="material-symbols-rounded mb-3" style="font-size:48px;font-variation-settings:'FILL' 1">receipt_long</span>
        <p class="font-black text-slate-400 text-sm">لا توجد اشتراكات</p>
        <p class="text-xs font-semibold mt-1">
            {{ request()->hasAny(['search','status','plan','type','member']) ? 'جرّب تغيير معايير البحث' : 'لم يتم تسجيل أي اشتراك بعد' }}
        </p>
    </div>
    @else

    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>العضو</th>
                    <th>الباقة</th>
                    <th class="hidden sm:table-cell">المبلغ / العملة</th>
                    <th class="hidden lg:table-cell">البداية</th>
                    <th class="hidden lg:table-cell">الانتهاء</th>
                    <th class="hidden md:table-cell">النوع</th>
                    <th>الحالة</th>
                    <th class="hidden md:table-cell">تاريخ الطلب</th>
                    <th style="text-align:left">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $sub)
                @php
                    $st = $statusMap[$sub->status] ?? ['غير معروف', 'badge-gray', 'help'];
                    $isGuest = is_null($sub->user_id);
                    $memberName  = $isGuest ? ($sub->guest_name  ?: 'ضيف') : $sub->user?->name;
                    $memberEmail = $isGuest ? ($sub->guest_email ?: '—')   : $sub->user?->email;
                    $planColor = $planColors[$sub->plan_id] ?? ['bg-slate-50', 'text-slate-500'];
                @endphp
                <tr>
                    {{-- ID --}}
                    <td class="text-slate-400 text-xs font-black">#{{ $sub->id }}</td>

                    {{-- Member --}}
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-black
                                {{ $isGuest ? 'bg-slate-100 text-slate-400' : 'bg-blue-50 text-blue-500' }}">
                                <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">
                                    {{ $isGuest ? 'person_off' : 'person' }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-black text-slate-800 text-sm leading-tight truncate max-w-[130px]">{{ $memberName }}</p>
                                <p class="text-[11px] text-slate-400 font-semibold truncate max-w-[130px]" dir="ltr">{{ $memberEmail }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Plan --}}
                    <td>
                        @if($sub->plan)
                        <span class="plan-pill {{ $planColor[0] }} {{ $planColor[1] }}">
                            <span class="material-symbols-rounded" style="font-size:13px;font-variation-settings:'FILL' 1">workspace_premium</span>
                            {{ $sub->plan->name }}
                        </span>
                        @else
                        <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Amount --}}
                    <td class="hidden sm:table-cell font-black text-slate-700" style="white-space:nowrap">
                        @php
                            $subRevMeta = ['SAR'=>['sym'=>'ر.س','dec'=>0],'EGP'=>['sym'=>'ج.م','dec'=>0],'TND'=>['sym'=>'د.ت','dec'=>3],'USD'=>['sym'=>'$','dec'=>2]];
                            $subCur = $sub->currency ?? 'SAR';
                            $subCurMeta = $subRevMeta[$subCur] ?? ['sym'=>$subCur,'dec'=>0];
                        @endphp
                        {{ number_format((float)$sub->total, $subCurMeta['dec']) }}
                        <span class="text-slate-400 font-semibold text-xs">{{ $subCurMeta['sym'] }}</span>
                        @if($sub->coupon_code)
                        <br><span class="text-[11px] text-emerald-500 font-bold">كوبون: {{ $sub->coupon_code }}</span>
                        @endif
                    </td>

                    {{-- Start --}}
                    <td class="hidden lg:table-cell text-slate-400 text-xs font-bold">
                        {{ $sub->start_date ? $sub->start_date->format('d/m/Y') : '—' }}
                    </td>

                    {{-- End --}}
                    <td class="hidden lg:table-cell text-xs font-bold
                        {{ $sub->end_date && $sub->end_date->isPast() && $sub->status === 'active' ? 'text-red-400' : 'text-slate-400' }}">
                        {{ $sub->end_date ? $sub->end_date->format('d/m/Y') : '—' }}
                    </td>

                    {{-- Type / Duration --}}
                    <td class="hidden md:table-cell">
                        @if($sub->duration_months)
                        <span class="badge badge-violet">
                            <span class="material-symbols-rounded" style="font-size:11px">event_repeat</span>
                            {{ $sub->duration_months === 3 ? '٣ شهور' : '٦ شهور' }}
                        </span>
                        @else
                        <span class="badge badge-cyan">
                            <span class="material-symbols-rounded" style="font-size:11px">calendar_month</span>
                            قديم
                        </span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="badge {{ $st[1] }}">
                            <span class="material-symbols-rounded" style="font-size:11px;font-variation-settings:'FILL' 1">{{ $st[2] }}</span>
                            {{ $st[0] }}
                        </span>
                    </td>

                    {{-- Date --}}
                    <td class="hidden md:table-cell text-slate-400 text-xs font-bold">
                        {{ $sub->created_at->format('d/m/Y') }}
                    </td>

                    {{-- Actions --}}
                    <td style="text-align:left">
                        <div class="flex items-center gap-1.5 justify-end">
                            <a href="{{ route('admin.subscriptions.show', $sub) }}"
                               title="عرض التفاصيل"
                               class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-500 flex items-center justify-center transition">
                                <span class="material-symbols-rounded" style="font-size:16px">visibility</span>
                            </a>
                            @php
                                $subActiveLink = $sub->meetingBookings->whereIn('status', ['pending', 'confirmed'])->sortByDesc('id')->first()?->meet_link;
                            @endphp
                            <button title="تعديل"
                                onclick="openEdit({{ $sub->id }}, '{{ $sub->status }}', '{{ $sub->start_date?->format('Y-m-d') ?? '' }}', '{{ $sub->end_date?->format('Y-m-d') ?? '' }}', {{ $sub->duration_months ?? 'null' }}, '{{ $subActiveLink }}')"
                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                                <span class="material-symbols-rounded" style="font-size:16px">edit_calendar</span>
                            </button>
                            <button title="حذف"
                                onclick="openDelete({{ $sub->id }}, '{{ addslashes($memberName) }}')"
                                class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition">
                                <span class="material-symbols-rounded" style="font-size:16px">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($subscriptions->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
        <p class="text-xs text-slate-400 font-semibold">
            عرض {{ $subscriptions->firstItem() }}–{{ $subscriptions->lastItem() }} من {{ $subscriptions->total() }} اشتراك
        </p>
        <div class="flex items-center gap-1.5">
            @if($subscriptions->onFirstPage())
                <span class="page-btn page-btn-disabled"><span class="material-symbols-rounded" style="font-size:16px">chevron_right</span></span>
            @else
                <a href="{{ $subscriptions->previousPageUrl() }}" class="page-btn page-btn-normal"><span class="material-symbols-rounded" style="font-size:16px">chevron_right</span></a>
            @endif
            @foreach($subscriptions->getUrlRange(max(1,$subscriptions->currentPage()-2), min($subscriptions->lastPage(),$subscriptions->currentPage()+2)) as $page => $url)
                @if($page == $subscriptions->currentPage())
                    <span class="page-btn page-btn-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn page-btn-normal">{{ $page }}</a>
                @endif
            @endforeach
            @if($subscriptions->hasMorePages())
                <a href="{{ $subscriptions->nextPageUrl() }}" class="page-btn page-btn-normal"><span class="material-symbols-rounded" style="font-size:16px">chevron_left</span></a>
            @else
                <span class="page-btn page-btn-disabled"><span class="material-symbols-rounded" style="font-size:16px">chevron_left</span></span>
            @endif
        </div>
    </div>
    @endif

    @endif
</div>

{{-- ══ EDIT MODAL ══ --}}
<div id="editModal" class="modal-backdrop" onclick="closeModal('editModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-blue-500" style="font-size:16px;font-variation-settings:'FILL' 1">edit_calendar</span>
                </div>
                <p class="font-black text-slate-800 text-sm">تعديل الاشتراك</p>
            </div>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        {{-- ── Meeting link — always shown; gates the rest ONLY while no link exists yet ── --}}
        <form id="meetingLinkForm" method="POST" class="p-6 flex flex-col gap-4">
            @csrf
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">رابط الاجتماع (Google Meet)</label>
                <input type="url" id="meetLinkInput" name="meet_link" class="modal-input" dir="ltr"
                       placeholder="https://meet.google.com/xxx-xxxx-xxx" required>
                <p id="meetingLinkHint" class="text-[11px] text-slate-400 font-semibold">بعد الحفظ، سيصل للعميل إيميل بميعاده المحدد ورابط الاجتماع.</p>
            </div>
            <button type="submit" id="meetingLinkSaveBtn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-black text-sm py-2.5 rounded-xl transition">حفظ الرابط</button>
        </form>

        {{-- ── Status/duration/dates — revealed once a meeting link already exists ── --}}
        <form id="editForm" method="POST" class="hidden p-6 pt-0 flex flex-col gap-4 border-t border-slate-100 mt-1">
            @csrf @method('PUT')
            <input type="hidden" name="from_modal" value="1">

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">حالة الاشتراك</label>
                <select id="editStatus" name="status" class="modal-input">
                    <option value="active">نشط</option>
                    <option value="expired">منتهي</option>
                    <option value="cancelled">ملغي</option>
                </select>
            </div>

            {{-- Duration + auto-calculate trigger --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">مدة الاشتراك</label>
                <div class="grid grid-cols-3 gap-2" id="durationBtns">
                    <button type="button" data-months="3"
                        class="dur-btn border-2 border-slate-200 rounded-xl py-2.5 text-sm font-black text-slate-500 hover:border-blue-400 hover:text-blue-600 transition">
                        3 شهور
                    </button>
                    <button type="button" data-months="6"
                        class="dur-btn border-2 border-slate-200 rounded-xl py-2.5 text-sm font-black text-slate-500 hover:border-blue-400 hover:text-blue-600 transition">
                        6 شهور
                    </button>
                    <button type="button" data-months=""
                        class="dur-btn border-2 border-slate-200 rounded-xl py-2.5 text-sm font-black text-slate-500 hover:border-slate-400 transition">
                        يدوي
                    </button>
                </div>
                <input type="hidden" id="editDuration" name="duration_months">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-black text-slate-500">تاريخ البدء</label>
                    <input type="date" id="editStart" name="start_date" class="modal-input" dir="ltr">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-black text-slate-500">تاريخ الانتهاء</label>
                    <input type="date" id="editEnd" name="end_date" class="modal-input" dir="ltr">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-black text-sm py-2.5 rounded-xl transition">حفظ</button>
        </form>

        <div class="px-6 pb-6">
            <button type="button" onclick="closeModal('editModal')" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">إغلاق</button>
        </div>
    </div>
</div>

{{-- ══ DELETE MODAL ══ --}}
<div id="deleteModal" class="modal-backdrop" onclick="closeModal('deleteModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-red-500" style="font-size:16px;font-variation-settings:'FILL' 1">delete_forever</span>
                </div>
                <p class="font-black text-slate-800 text-sm">حذف الاشتراك</p>
            </div>
            <button onclick="closeModal('deleteModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-slate-500 text-sm font-semibold leading-relaxed">
                هل أنت متأكد من حذف اشتراك <span id="deleteName" class="font-black text-slate-800"></span>؟
                <br><span class="text-red-400 text-xs font-bold"><span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1;vertical-align:middle">warning</span> سيتم حذف جميع الجلسات المرتبطة بهذا الاشتراك أيضاً.</span>
            </p>
        </div>
        <form id="deleteForm" method="POST" class="px-6 pb-6 flex gap-3">
            @csrf @method('DELETE')
            <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black text-sm py-2.5 rounded-xl transition">نعم، احذف</button>
            <button type="button" onclick="closeModal('deleteModal')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">إلغاء</button>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
    function closeModal(id) { document.getElementById(id).classList.remove('is-open'); document.body.style.overflow = ''; }
    function openModal(id)  { document.getElementById(id).classList.add('is-open');    document.body.style.overflow = 'hidden'; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') ['editModal','deleteModal'].forEach(closeModal); });

    // After saving the meeting link (or the status/dates form), the
    // controller flashes which subscription to reopen the edit modal for —
    // this is what makes "save the link → page refreshes → rest of the
    // fields are there" work without any AJAX.
    @if(session('reopen_subscription_id'))
        @php $reopenSub = $subscriptions->firstWhere('id', session('reopen_subscription_id')); @endphp
        @if($reopenSub)
            @php $reopenLink = $reopenSub->meetingBookings->whereIn('status', ['pending', 'confirmed'])->sortByDesc('id')->first()?->meet_link; @endphp
            document.addEventListener('DOMContentLoaded', function () {
                openEdit(
                    {{ $reopenSub->id }},
                    '{{ $reopenSub->status }}',
                    '{{ $reopenSub->start_date?->format('Y-m-d') ?? '' }}',
                    '{{ $reopenSub->end_date?->format('Y-m-d') ?? '' }}',
                    {{ $reopenSub->duration_months ?? 'null' }},
                    '{{ $reopenLink }}'
                );
            });
        @endif
    @endif

    // ── Duration buttons logic ──────────────────────────────
    function setActiveDur(months) {
        document.querySelectorAll('.dur-btn').forEach(btn => {
            const isActive = String(btn.dataset.months) === String(months ?? '');
            btn.classList.toggle('border-blue-500', isActive);
            btn.classList.toggle('text-blue-600',   isActive);
            btn.classList.toggle('bg-blue-50',      isActive);
            btn.classList.toggle('border-slate-200', !isActive);
            btn.classList.toggle('text-slate-500',   !isActive);
            btn.classList.toggle('bg-white',         !isActive);
        });
        document.getElementById('editDuration').value = months ?? '';
    }

    function recalcEnd() {
        const dur   = parseInt(document.getElementById('editDuration').value);
        const start = document.getElementById('editStart').value;
        if (!dur || !start) return;
        const d = new Date(start);
        d.setMonth(d.getMonth() + dur);
        // Format as YYYY-MM-DD
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('editEnd').value = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
    }

    document.querySelectorAll('.dur-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const m = btn.dataset.months ? parseInt(btn.dataset.months) : null;
            setActiveDur(m);
            recalcEnd();
        });
    });

    document.getElementById('editStart').addEventListener('change', recalcEnd);

    function openEdit(id, status, startDate, endDate, durationMonths, meetLink) {
        document.getElementById('editStatus').value = status;
        document.getElementById('editStart').value  = startDate;
        document.getElementById('editEnd').value    = endDate;
        document.getElementById('editForm').action  = `/admin/subscriptions/${id}`;
        setActiveDur(durationMonths);

        document.getElementById('meetLinkInput').value  = meetLink || '';
        document.getElementById('meetingLinkForm').action = `/admin/subscriptions/${id}/meeting-link`;

        // The gate is ONE-TIME: once a link already exists, both sections
        // show together from now on — no per-visit "must save first" step.
        const hasLink = !!meetLink;
        document.getElementById('editForm').classList.toggle('hidden', !hasLink);
        document.getElementById('meetingLinkSaveBtn').textContent = hasLink ? 'تحديث الرابط' : 'حفظ الرابط';
        document.getElementById('meetingLinkHint').textContent = hasLink
            ? 'لو غيّرت الرابط وحفظت، هيوصل للعميل إيميل بالتغيير.'
            : 'بعد الحفظ، سيصل للعميل إيميل بميعاده المحدد ورابط الاجتماع.';

        openModal('editModal');
    }

    function openDelete(id, name) {
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteForm').action = `/admin/subscriptions/${id}`;
        openModal('deleteModal');
    }
</script>
@endsection

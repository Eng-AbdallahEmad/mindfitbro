@extends('layouts.admin.app')

@section('title', 'المدربون')
@section('page-title', 'المدربون')
@section('page-subtitle', 'إدارة جميع مدربي المنصة')

@section('style')
<style>
    .stat-card {
        background: #fff; border-radius: 18px; padding: 1.2rem 1.4rem;
        border: 1px solid #e8edf5; display: flex; align-items: center; gap: 1rem;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.07); transform: translateY(-2px); }
    .stat-icon { width: 46px; height: 46px; border-radius: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    .search-wrap { position: relative; flex: 1; }
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
        padding: .7rem 1rem; font-size: .85rem; font-weight: 700; color: #374151;
        font-family: 'Cairo', sans-serif; outline: none; cursor: pointer;
        transition: border-color .2s, background .2s; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: left 12px center; padding-left: 32px;
    }
    .filter-select:focus { background-color: #fff; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }

    .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .28rem .8rem; border-radius: 999px; font-size: .71rem; font-weight: 800; white-space: nowrap; }
    .badge .dot { width:5px; height:5px; border-radius:50%; background:currentColor; opacity:.8; flex-shrink:0; }
    .badge-green  { background:#dcfce7; color:#16a34a; }
    .badge-gray   { background:#f1f5f9; color:#64748b; }
    .badge-red    { background:#fee2e2; color:#dc2626; }
    .badge-cyan   { background:#ecfeff; color:#0891b2; }

    .avatar { width: 38px; height: 38px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 900; flex-shrink: 0; }

    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { padding: .65rem 1.1rem; text-align: right; font-size: .69rem; font-weight: 900; color: #94a3b8; letter-spacing: .07em; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #f0f4f8; white-space: nowrap; }
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
    $avatarColors = [
        ['bg-cyan-50','text-cyan-500'],     ['bg-violet-50','text-violet-500'],
        ['bg-emerald-50','text-emerald-500'], ['bg-orange-50','text-orange-500'],
        ['bg-blue-50','text-blue-500'],     ['bg-pink-50','text-pink-500'],
    ];
    $statusMap = [
        'active'   => ['label' => 'نشط',     'class' => 'badge-green'],
        'inactive' => ['label' => 'غير نشط', 'class' => 'badge-gray'],
        'banned'   => ['label' => 'محظور',   'class' => 'badge-red'],
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

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-cyan-50">
            <span class="material-symbols-rounded text-cyan-500" style="font-size:22px;font-variation-settings:'FILL' 1">sports</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['total']) }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">إجمالي المدربين</p>
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
        <div class="stat-icon bg-slate-100">
            <span class="material-symbols-rounded text-slate-400" style="font-size:22px;font-variation-settings:'FILL' 1">pause_circle</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['inactive']) }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">غير نشط</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-red-50">
            <span class="material-symbols-rounded text-red-400" style="font-size:22px;font-variation-settings:'FILL' 1">block</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['banned']) }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">محظور</p>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-cyan-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-cyan-500" style="font-size:18px;font-variation-settings:'FILL' 1">sports</span>
            </div>
            <div>
                <p class="font-black text-slate-800 text-sm">قائمة المدربين</p>
                <p class="text-[11px] text-slate-400 font-semibold">{{ $coaches->total() }} مدرب مسجّل</p>
            </div>
        </div>
        <a href="{{ route('admin.members.create') }}"
           class="flex items-center gap-2 bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-black px-4 py-2 rounded-xl transition">
            <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">person_add</span>
            إضافة مدرب
        </a>
    </div>

    {{-- Filters --}}
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
        <form method="GET" action="{{ route('admin.coaches.index') }}">

            <div class="search-wrap mb-3">
                <span class="material-symbols-rounded search-icon">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="ابحث بالاسم أو اسم المستخدم أو البريد الإلكتروني أو رقم الهاتف..."
                       class="search-input" autocomplete="off">
            </div>

            <div class="flex flex-wrap gap-2">
                <select name="status" class="filter-select flex-1 min-w-[130px]">
                    <option value="">كل الحالات</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="banned"   {{ request('status') === 'banned'   ? 'selected' : '' }}>محظور</option>
                </select>
                <select name="gender" class="filter-select flex-1 min-w-[110px]">
                    <option value="">كل الأجناس</option>
                    <option value="male"   {{ request('gender') === 'male'   ? 'selected' : '' }}>ذكر</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>أنثى</option>
                </select>
                <button type="submit"
                    class="flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 text-white font-black text-xs px-4 py-2 rounded-xl transition">
                    <span class="material-symbols-rounded" style="font-size:15px">filter_alt</span>
                    فلتر
                </button>
                @if(request()->hasAny(['search','status','gender']))
                <a href="{{ route('admin.coaches.index') }}"
                   class="flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-xs px-4 py-2 rounded-xl transition">
                    <span class="material-symbols-rounded" style="font-size:15px">close</span>
                    مسح
                </a>
                @endif
            </div>

        </form>

        {{-- Active filter tags --}}
        @if(request()->hasAny(['search','status','gender']))
        <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-slate-100">
            @if(request('search'))
            <span class="filter-tag">
                <span class="material-symbols-rounded" style="font-size:13px">search</span>
                "{{ request('search') }}"
            </span>
            @endif
            @if(request('status'))
            <span class="filter-tag">
                <span class="material-symbols-rounded" style="font-size:13px">circle</span>
                {{ ['active'=>'نشط','inactive'=>'غير نشط','banned'=>'محظور'][request('status')] ?? '' }}
            </span>
            @endif
            @if(request('gender'))
            <span class="filter-tag">
                <span class="material-symbols-rounded" style="font-size:13px">person</span>
                {{ request('gender') === 'male' ? 'ذكر' : 'أنثى' }}
            </span>
            @endif
        </div>
        @endif
    </div>

    {{-- Table --}}
    @if($coaches->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-slate-300">
        <span class="material-symbols-rounded mb-3" style="font-size:48px;font-variation-settings:'FILL' 1">sports</span>
        <p class="font-black text-slate-400 text-sm">لا يوجد مدربون</p>
        <p class="text-xs font-semibold mt-1">
            @if(request()->hasAny(['search','status','gender']))
                جرّب تغيير معايير البحث
            @else
                أضف أول مدرب الآن
            @endif
        </p>
    </div>
    @else

    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>المدرب</th>
                    <th class="hidden md:table-cell">البريد</th>
                    <th class="hidden lg:table-cell">الهاتف</th>
                    <th class="hidden sm:table-cell">التقييمات</th>
                    <th class="hidden sm:table-cell">الجنس</th>
                    <th>الحالة</th>
                    <th class="hidden md:table-cell">تاريخ الانضمام</th>
                    <th style="text-align:left">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coaches as $i => $coach)
                @php
                    $color   = $avatarColors[$i % count($avatarColors)];
                    $initial = mb_substr($coach->name, 0, 1);
                    $st      = $statusMap[$coach->status] ?? ['label'=>$coach->status,'class'=>'badge-gray'];
                    $isBanned = $coach->status === 'banned';
                @endphp
                <tr>
                    {{-- Avatar + Name --}}
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar {{ $color[0] }} {{ $color[1] }}">{{ $initial }}</div>
                            <div>
                                <p class="font-black text-slate-800 text-sm leading-tight">{{ $coach->name }}</p>
                                <p class="text-[11px] text-slate-400 font-semibold">@ {{ $coach->username }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Email --}}
                    <td class="hidden md:table-cell text-slate-500" dir="ltr">{{ $coach->email }}</td>

                    {{-- Phone --}}
                    <td class="hidden lg:table-cell text-slate-500" dir="ltr">{{ $coach->phone ?: '—' }}</td>

                    {{-- Evaluations --}}
                    <td class="hidden sm:table-cell">
                        <span class="badge badge-cyan">
                            <span class="material-symbols-rounded" style="font-size:11px;font-variation-settings:'FILL' 1">assignment</span>
                            {{ number_format($coach->evaluations_as_coach_count) }} تقييم
                        </span>
                    </td>

                    {{-- Gender --}}
                    <td class="hidden sm:table-cell text-slate-500">
                        {{ $coach->gender === 'male' ? 'ذكر' : 'أنثى' }}
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="badge {{ $st['class'] }}">
                            <span class="dot"></span>{{ $st['label'] }}
                        </span>
                    </td>

                    {{-- Join Date --}}
                    <td class="hidden md:table-cell text-slate-400 text-xs">
                        {{ $coach->created_at->format('d/m/Y') }}
                    </td>

                    {{-- Actions --}}
                    <td style="text-align:left">
                        <div class="flex items-center gap-1.5 justify-end">
                            {{-- View --}}
                            <a href="{{ route('admin.coaches.show', $coach) }}"
                               title="عرض الملف"
                               class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-500 flex items-center justify-center transition">
                                <span class="material-symbols-rounded" style="font-size:16px">visibility</span>
                            </a>

                            {{-- Edit --}}
                            <button title="تعديل"
                                onclick="openEdit({{ $coach->id }}, '{{ addslashes($coach->name) }}', '{{ $coach->phone }}', '{{ $coach->status }}')"
                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                                <span class="material-symbols-rounded" style="font-size:16px">edit</span>
                            </button>

                            {{-- Ban / Unban --}}
                            <button title="{{ $isBanned ? 'رفع الحظر' : 'حظر' }}"
                                onclick="openBan({{ $coach->id }}, '{{ addslashes($coach->name) }}', {{ $isBanned ? 'true' : 'false' }})"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition
                                    {{ $isBanned ? 'bg-green-50 hover:bg-green-100 text-green-500' : 'bg-orange-50 hover:bg-orange-100 text-orange-500' }}">
                                <span class="material-symbols-rounded" style="font-size:16px">{{ $isBanned ? 'lock_open' : 'block' }}</span>
                            </button>

                            {{-- Delete --}}
                            <button title="حذف"
                                onclick="openDelete({{ $coach->id }}, '{{ addslashes($coach->name) }}')"
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
    @if($coaches->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
        <p class="text-xs text-slate-400 font-semibold">
            عرض {{ $coaches->firstItem() }}–{{ $coaches->lastItem() }} من {{ $coaches->total() }} مدرب
        </p>
        <div class="flex items-center gap-1.5">
            @if($coaches->onFirstPage())
                <span class="page-btn page-btn-disabled">
                    <span class="material-symbols-rounded" style="font-size:16px">chevron_right</span>
                </span>
            @else
                <a href="{{ $coaches->previousPageUrl() }}" class="page-btn page-btn-normal">
                    <span class="material-symbols-rounded" style="font-size:16px">chevron_right</span>
                </a>
            @endif

            @foreach($coaches->getUrlRange(max(1,$coaches->currentPage()-2), min($coaches->lastPage(),$coaches->currentPage()+2)) as $page => $url)
                @if($page == $coaches->currentPage())
                    <span class="page-btn page-btn-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn page-btn-normal">{{ $page }}</a>
                @endif
            @endforeach

            @if($coaches->hasMorePages())
                <a href="{{ $coaches->nextPageUrl() }}" class="page-btn page-btn-normal">
                    <span class="material-symbols-rounded" style="font-size:16px">chevron_left</span>
                </a>
            @else
                <span class="page-btn page-btn-disabled">
                    <span class="material-symbols-rounded" style="font-size:16px">chevron_left</span>
                </span>
            @endif
        </div>
    </div>
    @endif

    @endif

</div>

{{-- ══════════ EDIT MODAL ══════════ --}}
<div id="editModal" class="modal-backdrop" onclick="closeModal('editModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <span class="material-symbols-rounded text-slate-500" style="font-size:16px;font-variation-settings:'FILL' 1">edit</span>
                </div>
                <p class="font-black text-slate-800 text-sm">تعديل بيانات المدرب</p>
            </div>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <form id="editForm" method="POST" class="p-6 flex flex-col gap-4">
            @csrf @method('PUT')
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">الاسم الكامل</label>
                <input type="text" id="editName" name="name" required class="modal-input" placeholder="اسم المدرب">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">رقم الهاتف</label>
                <input type="text" id="editPhone" name="phone" class="modal-input" placeholder="رقم الهاتف (اختياري)" dir="ltr">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">كلمة مرور جديدة <span class="text-slate-400 font-semibold">(اتركها فارغة للإبقاء على الحالية)</span></label>
                <input type="password" name="password" class="modal-input" placeholder="••••••••" dir="ltr" autocomplete="new-password">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="modal-input" placeholder="••••••••" dir="ltr" autocomplete="new-password">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">الحالة</label>
                <select id="editStatus" name="status" class="modal-input">
                    <option value="active">نشط</option>
                    <option value="inactive">غير نشط</option>
                    <option value="banned">محظور</option>
                </select>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-black text-sm py-2.5 rounded-xl transition">حفظ التغييرات</button>
                <button type="button" onclick="closeModal('editModal')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════ BAN MODAL ══════════ --}}
<div id="banModal" class="modal-backdrop" onclick="closeModal('banModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div id="banIconWrap" class="w-8 h-8 rounded-lg flex items-center justify-center">
                    <span id="banIcon" class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1"></span>
                </div>
                <p id="banTitle" class="font-black text-slate-800 text-sm"></p>
            </div>
            <button onclick="closeModal('banModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-slate-500 text-sm font-semibold leading-relaxed"><span id="banDesc"></span></p>
        </div>
        <form id="banForm" method="POST" class="px-6 pb-6 flex gap-3">
            @csrf @method('PATCH')
            <button type="submit" id="banBtn" class="flex-1 font-black text-sm py-2.5 rounded-xl transition text-white">تأكيد</button>
            <button type="button" onclick="closeModal('banModal')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">إلغاء</button>
        </form>
    </div>
</div>

{{-- ══════════ DELETE MODAL ══════════ --}}
<div id="deleteModal" class="modal-backdrop" onclick="closeModal('deleteModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-red-500" style="font-size:16px;font-variation-settings:'FILL' 1">delete_forever</span>
                </div>
                <p class="font-black text-slate-800 text-sm">حذف المدرب</p>
            </div>
            <button onclick="closeModal('deleteModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-slate-500 text-sm font-semibold leading-relaxed">
                هل أنت متأكد من حذف المدرب <span id="deleteName" class="font-black text-slate-800"></span>؟
                <br>
                <span class="text-red-400 text-xs font-bold"><span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1;vertical-align:middle">warning</span> هذا الإجراء لا يمكن التراجع عنه.</span>
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
    document.addEventListener('keydown', e => { if (e.key === 'Escape') ['editModal','banModal','deleteModal'].forEach(closeModal); });

    function openEdit(id, name, phone, status) {
        document.getElementById('editName').value   = name;
        document.getElementById('editPhone').value  = phone || '';
        document.getElementById('editStatus').value = status;
        document.getElementById('editForm').action  = `/admin/coaches/${id}`;
        openModal('editModal');
    }

    function openBan(id, name, isBanned) {
        const wrap = document.getElementById('banIconWrap'), icon = document.getElementById('banIcon');
        const title = document.getElementById('banTitle'), desc = document.getElementById('banDesc'), btn = document.getElementById('banBtn');
        if (isBanned) {
            wrap.className = 'w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center';
            icon.className = 'material-symbols-rounded text-green-500'; icon.textContent = 'lock_open';
            title.textContent = 'رفع الحظر عن المدرب';
            desc.innerHTML = `هل تريد رفع الحظر عن المدرب <strong>${name}</strong>؟ سيتمكن من الدخول مجدداً.`;
            btn.className = 'flex-1 bg-green-500 hover:bg-green-600 font-black text-sm py-2.5 rounded-xl transition text-white';
        } else {
            wrap.className = 'w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center';
            icon.className = 'material-symbols-rounded text-orange-500'; icon.textContent = 'block';
            title.textContent = 'حظر المدرب';
            desc.innerHTML = `هل تريد حظر المدرب <strong>${name}</strong>؟ لن يتمكن من تسجيل الدخول حتى يتم رفع الحظر.`;
            btn.className = 'flex-1 bg-orange-500 hover:bg-orange-600 font-black text-sm py-2.5 rounded-xl transition text-white';
        }
        document.getElementById('banForm').action = `/admin/coaches/${id}/status`;
        openModal('banModal');
    }

    function openDelete(id, name) {
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteForm').action = `/admin/coaches/${id}`;
        openModal('deleteModal');
    }
</script>
@endsection

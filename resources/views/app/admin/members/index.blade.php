@extends('layouts.admin.app')

@section('title', 'الأعضاء')
@section('page-title', 'الأعضاء')
@section('page-subtitle', 'إدارة جميع أعضاء المنصة')

@section('style')
<style>
    /* ─── Stat Card ─── */
    .stat-card {
        background: #fff;
        border-radius: 18px;
        padding: 1.2rem 1.4rem;
        border: 1px solid #e8edf5;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.07); transform: translateY(-2px); }
    .stat-icon {
        width: 46px; height: 46px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    /* ─── Search ─── */
    .search-wrap {
        position: relative;
        flex: 1;
    }
    .search-icon {
        position: absolute;
        top: 50%; right: 14px;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
        font-size: 18px;
    }
    .search-input {
        width: 100%;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: .7rem 2.7rem .7rem 1rem;
        font-size: .85rem;
        font-weight: 600;
        color: #1e293b;
        font-family: 'Cairo', sans-serif;
        outline: none;
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .search-input:focus {
        background: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,.12);
    }
    .search-input::placeholder { color: #94a3b8; }

    /* ─── Select ─── */
    .filter-select {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: .7rem 1rem;
        font-size: .85rem;
        font-weight: 700;
        color: #374151;
        font-family: 'Cairo', sans-serif;
        outline: none;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 12px center;
        padding-left: 32px;
    }
    .filter-select:focus {
        background-color: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,.12);
    }

    /* ─── Badge ─── */
    .badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .28rem .8rem; border-radius: 999px;
        font-size: .71rem; font-weight: 800; white-space: nowrap;
    }
    .badge .dot { width:5px; height:5px; border-radius:50%; background:currentColor; opacity:.8; flex-shrink:0; }
    .badge-green  { background:#dcfce7; color:#16a34a; }
    .badge-gray   { background:#f1f5f9; color:#64748b; }
    .badge-red    { background:#fee2e2; color:#dc2626; }

    /* ─── Avatar ─── */
    .avatar {
        width: 38px; height: 38px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; font-weight: 900; flex-shrink: 0;
    }

    /* ─── Table ─── */
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th {
        padding: .65rem 1.1rem; text-align: right;
        font-size: .69rem; font-weight: 900; color: #94a3b8;
        letter-spacing: .07em; text-transform: uppercase;
        background: #f8fafc; border-bottom: 1px solid #f0f4f8;
        white-space: nowrap;
    }
    .admin-table td {
        padding: .85rem 1.1rem; font-size: .84rem;
        font-weight: 600; color: #374151;
        border-bottom: 1px solid #f8fafc; vertical-align: middle;
    }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr { transition: background .12s; }
    .admin-table tbody tr:hover td { background: #f8fafc; }

    /* ─── Pagination ─── */
    .page-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 34px; height: 34px; padding: 0 .5rem;
        border-radius: 9px; font-size: .8rem; font-weight: 800;
        text-decoration: none; transition: all .18s;
        border: 1.5px solid transparent;
    }
    .page-btn-active  { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .page-btn-normal  { background: #fff; color: #64748b; border-color: #e2e8f0; }
    .page-btn-normal:hover { border-color: #3b82f6; color: #3b82f6; }
    .page-btn-disabled { background: #f8fafc; color: #cbd5e1; border-color: #f1f5f9; pointer-events: none; }

    /* ─── Active Filter Tag ─── */
    .filter-tag {
        display: inline-flex; align-items: center; gap: .4rem;
        background: #eff6ff; color: #2563eb;
        border: 1px solid #bfdbfe;
        border-radius: 8px; padding: .25rem .7rem;
        font-size: .75rem; font-weight: 800;
    }

    /* ─── Modal ─── */
    .modal-backdrop {
        position: fixed; inset: 0;
        background: rgba(15,23,42,.55);
        backdrop-filter: blur(3px);
        z-index: 200;
        opacity: 0; pointer-events: none;
        transition: opacity .25s ease;
        display: flex; align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-backdrop.is-open { opacity: 1; pointer-events: auto; }
    .modal-box {
        background: #fff; border-radius: 20px;
        width: 100%; max-width: 440px;
        box-shadow: 0 24px 60px rgba(0,0,0,.18);
        transform: translateY(16px) scale(.97);
        transition: transform .25s ease;
        overflow: hidden;
    }
    .modal-backdrop.is-open .modal-box { transform: translateY(0) scale(1); }

    /* ─── Form Input (modal) ─── */
    .modal-input {
        width: 100%; background: #f8fafc;
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        padding: .65rem .9rem; font-size: .85rem; font-weight: 600;
        color: #1e293b; font-family: 'Cairo', sans-serif; outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .modal-input:focus { background:#fff; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
</style>
@endsection

@section('content')

@php
    $avatarColors = [
        ['bg-blue-50','text-blue-500'],    ['bg-violet-50','text-violet-500'],
        ['bg-emerald-50','text-emerald-500'], ['bg-orange-50','text-orange-500'],
        ['bg-pink-50','text-pink-500'],    ['bg-cyan-50','text-cyan-500'],
    ];
    $statusMap = [
        'active'   => ['label' => 'نشط',     'class' => 'badge-green'],
        'inactive' => ['label' => 'غير نشط', 'class' => 'badge-gray'],
        'banned'   => ['label' => 'محظور',   'class' => 'badge-red'],
    ];
@endphp

{{-- ══════════ FLASH ══════════ --}}
@if(session('success'))
<div id="flashMsg"
     class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700
            rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:20px">check_circle</span>
    {{ session('success') }}
    <button onclick="document.getElementById('flashMsg').remove()"
            class="mr-auto text-green-400 hover:text-green-600 transition">
        <span class="material-symbols-rounded" style="font-size:18px">close</span>
    </button>
</div>
@endif

{{-- ══════════ STATS ══════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="stat-card">
        <div class="stat-icon bg-blue-50">
            <span class="material-symbols-rounded text-blue-500" style="font-size:22px;font-variation-settings:'FILL' 1">group</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['total']) }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">إجمالي الأعضاء</p>
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

{{-- ══════════ TABLE CARD ══════════ --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-blue-500"
                      style="font-size:18px;font-variation-settings:'FILL' 1">group</span>
            </div>
            <div>
                <p class="font-black text-slate-800 text-sm">قائمة الأعضاء</p>
                <p class="text-[11px] text-slate-400 font-semibold">{{ $members->total() }} عضو مسجّل</p>
            </div>
        </div>
        <a href="{{ route('admin.members.create') }}"
           class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-black px-4 py-2 rounded-xl transition">
            <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">person_add</span>
            إضافة عضو
        </a>
    </div>

    {{-- ── Filters ── --}}
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
        <form method="GET" action="{{ route('admin.members.index') }}">

            {{-- Row 1: Search --}}
            <div class="search-wrap mb-3">
                <span class="material-symbols-rounded search-icon">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="ابحث بالاسم أو اسم المستخدم أو البريد الإلكتروني أو رقم الهاتف..."
                    class="search-input"
                    autocomplete="off"
                >
            </div>

            {{-- Row 2: Dropdowns + Buttons --}}
            <div class="flex flex-wrap gap-2">

                <select name="status" class="filter-select flex-1 min-w-[130px]">
                    <option value="">كل الحالات</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="banned"   {{ request('status') === 'banned'   ? 'selected' : '' }}>محظور</option>
                </select>

                <select name="gender" class="filter-select flex-1 min-w-[120px]">
                    <option value="">كل الأجناس</option>
                    <option value="male"   {{ request('gender') === 'male'   ? 'selected' : '' }}>ذكر</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>أنثى</option>
                </select>

                <button type="submit"
                    class="flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 active:scale-95 text-white text-sm font-bold px-5 py-2 rounded-xl transition">
                    <span class="material-symbols-rounded" style="font-size:16px">filter_alt</span>
                    فلتر
                </button>

                @if(request()->hasAny(['search','status','gender']))
                <a href="{{ route('admin.members.index') }}"
                   class="flex items-center gap-1.5 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 text-slate-500 hover:text-red-500 text-sm font-bold px-4 py-2 rounded-xl transition">
                    <span class="material-symbols-rounded" style="font-size:16px">close</span>
                    مسح
                </a>
                @endif

            </div>

            {{-- Active Filter Tags --}}
            @if(request()->hasAny(['search','status','gender']))
            <div class="flex flex-wrap gap-2 mt-3">
                @if(request('search'))
                <span class="filter-tag">
                    <span class="material-symbols-rounded" style="font-size:13px">search</span>
                    {{ request('search') }}
                </span>
                @endif
                @if(request('status'))
                <span class="filter-tag">
                    <span class="material-symbols-rounded" style="font-size:13px">info</span>
                    {{ ['active'=>'نشط','inactive'=>'غير نشط','banned'=>'محظور'][request('status')] ?? request('status') }}
                </span>
                @endif
                @if(request('gender'))
                <span class="filter-tag">
                    <span class="material-symbols-rounded" style="font-size:13px">wc</span>
                    {{ request('gender') === 'male' ? 'ذكر' : 'أنثى' }}
                </span>
                @endif
            </div>
            @endif

        </form>
    </div>

    {{-- ── Table / Empty ── --}}
    @if($members->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 gap-3">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
            <span class="material-symbols-rounded text-slate-300" style="font-size:30px">person_search</span>
        </div>
        <p class="font-black text-slate-400 text-sm">لا يوجد أعضاء مطابقون للبحث</p>
        <a href="{{ route('admin.members.index') }}"
           class="text-xs text-blue-500 font-bold hover:underline">عرض الكل</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="w-10">#</th>
                    <th>العضو</th>
                    <th class="hidden md:table-cell">البريد الإلكتروني</th>
                    <th class="hidden lg:table-cell">الهاتف</th>
                    <th class="hidden sm:table-cell">الجنس</th>
                    <th>الحالة</th>
                    <th class="hidden sm:table-cell">تاريخ التسجيل</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $i => $member)
                @php
                    [$bg, $tc] = $avatarColors[$i % count($avatarColors)];
                    $isMale = $member->gender === 'male';
                    $st = $statusMap[$member->status] ?? ['label' => $member->status, 'class' => 'badge-gray'];
                @endphp
                <tr>
                    {{-- # --}}
                    <td class="text-slate-300 text-xs font-black">
                        {{ $members->firstItem() + $i }}
                    </td>

                    {{-- Member --}}
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="avatar {{ $bg }} {{ $tc }}">
                                {{ mb_substr($member->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-slate-700 text-[13px] leading-tight truncate max-w-[150px]">
                                    {{ $member->name }}
                                </p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $member->username }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Email --}}
                    <td class="hidden md:table-cell">
                        <p class="text-[12px] text-slate-500 truncate max-w-[200px]">{{ $member->email }}</p>
                    </td>

                    {{-- Phone --}}
                    <td class="hidden lg:table-cell">
                        <p class="text-[12px] text-slate-500 font-bold" dir="ltr">{{ $member->phone ?? '—' }}</p>
                    </td>

                    {{-- Gender --}}
                    <td class="hidden sm:table-cell">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-rounded {{ $isMale ? 'text-blue-400' : 'text-pink-400' }}"
                                  style="font-size:16px;font-variation-settings:'FILL' 1">
                                {{ $isMale ? 'man' : 'woman' }}
                            </span>
                            <span class="text-[12px] font-bold text-slate-500">
                                {{ $isMale ? 'ذكر' : 'أنثى' }}
                            </span>
                        </div>
                    </td>

                    {{-- Status --}}
                    <td>
                        <span class="badge {{ $st['class'] }}">
                            <span class="dot"></span>
                            {{ $st['label'] }}
                        </span>
                    </td>

                    {{-- Date --}}
                    <td class="hidden sm:table-cell">
                        <p class="text-[12px] font-bold text-slate-600">{{ $member->created_at->format('d/m/Y') }}</p>
                        <p class="text-[10px] text-slate-300 mt-0.5">{{ $member->created_at->diffForHumans() }}</p>
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="flex items-center justify-center gap-1.5">

                            {{-- View --}}
                            <a href="{{ route('admin.members.show', $member) }}"
                               title="عرض الملف"
                               class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-500
                                      flex items-center justify-center transition">
                                <span class="material-symbols-rounded" style="font-size:16px">visibility</span>
                            </a>

                            {{-- Edit --}}
                            <button
                                title="تعديل"
                                onclick="openEdit({{ $member->id }}, '{{ addslashes($member->name) }}', '{{ $member->phone }}', '{{ $member->status }}')"
                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600
                                       flex items-center justify-center transition">
                                <span class="material-symbols-rounded" style="font-size:16px">edit</span>
                            </button>

                            {{-- Ban / Unban --}}
                            <button
                                title="{{ $member->status === 'banned' ? 'رفع الحظر' : 'حظر' }}"
                                onclick="openBan({{ $member->id }}, '{{ addslashes($member->name) }}', {{ $member->status === 'banned' ? 'true' : 'false' }})"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition
                                       {{ $member->status === 'banned'
                                          ? 'bg-green-50 hover:bg-green-100 text-green-600'
                                          : 'bg-orange-50 hover:bg-orange-100 text-orange-500' }}">
                                <span class="material-symbols-rounded" style="font-size:16px">
                                    {{ $member->status === 'banned' ? 'lock_open' : 'block' }}
                                </span>
                            </button>

                            {{-- Delete --}}
                            <button
                                title="حذف"
                                onclick="openDelete({{ $member->id }}, '{{ addslashes($member->name) }}')"
                                class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500
                                       flex items-center justify-center transition">
                                <span class="material-symbols-rounded" style="font-size:16px">delete</span>
                            </button>

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ── --}}
    @if($members->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-slate-100">
        <p class="text-xs font-bold text-slate-400 order-2 sm:order-1">
            عرض {{ $members->firstItem() }}–{{ $members->lastItem() }} من أصل {{ $members->total() }} عضو
        </p>
        <div class="flex items-center gap-1.5 order-1 sm:order-2">

            {{-- Prev --}}
            @if($members->onFirstPage())
                <span class="page-btn page-btn-disabled">
                    <span class="material-symbols-rounded" style="font-size:16px">chevron_right</span>
                </span>
            @else
                <a href="{{ $members->previousPageUrl() }}" class="page-btn page-btn-normal">
                    <span class="material-symbols-rounded" style="font-size:16px">chevron_right</span>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach($members->getUrlRange(max(1,$members->currentPage()-2), min($members->lastPage(),$members->currentPage()+2)) as $page => $url)
                @if($page == $members->currentPage())
                    <span class="page-btn page-btn-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn page-btn-normal">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($members->hasMorePages())
                <a href="{{ $members->nextPageUrl() }}" class="page-btn page-btn-normal">
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
                <p class="font-black text-slate-800 text-sm">تعديل بيانات العضو</p>
            </div>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <form id="editForm" method="POST" class="p-6 flex flex-col gap-4">
            @csrf @method('PUT')
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">الاسم الكامل</label>
                <input type="text" id="editName" name="name" required
                       class="modal-input" placeholder="اسم العضو">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-slate-500">رقم الهاتف</label>
                <input type="text" id="editPhone" name="phone"
                       class="modal-input" placeholder="رقم الهاتف (اختياري)" dir="ltr">
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
                <button type="submit"
                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-black text-sm py-2.5 rounded-xl transition">
                    حفظ التغييرات
                </button>
                <button type="button" onclick="closeModal('editModal')"
                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">
                    إلغاء
                </button>
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
            <p class="text-slate-500 text-sm font-semibold leading-relaxed">
                <span id="banDesc"></span>
            </p>
        </div>
        <form id="banForm" method="POST" class="px-6 pb-6 flex gap-3">
            @csrf @method('PATCH')
            <button type="submit" id="banBtn"
                class="flex-1 font-black text-sm py-2.5 rounded-xl transition text-white">
                تأكيد
            </button>
            <button type="button" onclick="closeModal('banModal')"
                class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">
                إلغاء
            </button>
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
                <p class="font-black text-slate-800 text-sm">حذف العضو</p>
            </div>
            <button onclick="closeModal('deleteModal')" class="text-slate-400 hover:text-slate-700 transition">
                <span class="material-symbols-rounded" style="font-size:20px">close</span>
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-slate-500 text-sm font-semibold leading-relaxed">
                هل أنت متأكد من حذف العضو
                <span id="deleteName" class="font-black text-slate-800"></span>؟
                <br>
                <span class="text-red-400 text-xs font-bold"><span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1;vertical-align:middle">warning</span> هذا الإجراء لا يمكن التراجع عنه وسيُحذف كل ما يتعلق بهذا العضو.</span>
            </p>
        </div>
        <form id="deleteForm" method="POST" class="px-6 pb-6 flex gap-3">
            @csrf @method('DELETE')
            <button type="submit"
                class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black text-sm py-2.5 rounded-xl transition">
                نعم، احذف
            </button>
            <button type="button" onclick="closeModal('deleteModal')"
                class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-2.5 rounded-xl transition">
                إلغاء
            </button>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
    function closeModal(id) {
        document.getElementById(id).classList.remove('is-open');
        document.body.style.overflow = '';
    }
    function openModal(id) {
        document.getElementById(id).classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') ['editModal','banModal','deleteModal'].forEach(closeModal);
    });

    function openEdit(id, name, phone, status) {
        document.getElementById('editName').value   = name;
        document.getElementById('editPhone').value  = phone || '';
        document.getElementById('editStatus').value = status;
        document.getElementById('editForm').action  = `/admin/members/${id}`;
        openModal('editModal');
    }

    function openBan(id, name, isBanned) {
        const wrap  = document.getElementById('banIconWrap');
        const icon  = document.getElementById('banIcon');
        const title = document.getElementById('banTitle');
        const desc  = document.getElementById('banDesc');
        const btn   = document.getElementById('banBtn');

        if (isBanned) {
            wrap.className   = 'w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center';
            icon.className   = 'material-symbols-rounded text-green-500';
            icon.textContent = 'lock_open';
            title.textContent = 'رفع الحظر عن العضو';
            desc.innerHTML   = `هل تريد رفع الحظر عن العضو <strong>${name}</strong>؟ سيتمكن من الدخول للمنصة مجدداً.`;
            btn.className    = 'flex-1 bg-green-500 hover:bg-green-600 font-black text-sm py-2.5 rounded-xl transition text-white';
        } else {
            wrap.className   = 'w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center';
            icon.className   = 'material-symbols-rounded text-orange-500';
            icon.textContent = 'block';
            title.textContent = 'حظر العضو';
            desc.innerHTML   = `هل تريد حظر العضو <strong>${name}</strong>؟ لن يتمكن من تسجيل الدخول حتى يتم رفع الحظر.`;
            btn.className    = 'flex-1 bg-orange-500 hover:bg-orange-600 font-black text-sm py-2.5 rounded-xl transition text-white';
        }

        document.getElementById('banForm').action = `/admin/members/${id}/status`;
        openModal('banModal');
    }

    function openDelete(id, name) {
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteForm').action = `/admin/members/${id}`;
        openModal('deleteModal');
    }
</script>
@endsection

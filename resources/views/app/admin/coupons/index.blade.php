@extends('layouts.admin.app')

@section('title', 'أكواد الخصم')
@section('page-title', 'أكواد الخصم')
@section('page-subtitle', 'إنشاء وإدارة كوبونات الخصم للاشتراكات')

@section('style')
<style>
    .stat-card {
        background: #fff; border-radius: 18px; padding: 1.2rem 1.4rem;
        border: 1px solid #e8edf5; display: flex; align-items: center; gap: 1rem;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.07); transform: translateY(-2px); }
    .stat-icon { width: 46px; height: 46px; border-radius: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .25rem .75rem; border-radius: 999px; font-size: .71rem; font-weight: 800; white-space: nowrap; }
    .badge-green  { background: #dcfce7; color: #16a34a; }
    .badge-red    { background: #fee2e2; color: #dc2626; }
    .badge-yellow { background: #fefce8; color: #ca8a04; }
    .badge-blue   { background: #eff6ff; color: #2563eb; }
    .badge-violet { background: #f5f3ff; color: #7c3aed; }
    .badge-gray   { background: #f1f5f9; color: #64748b; }

    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { padding: .65rem 1.1rem; text-align: right; font-size: .69rem; font-weight: 900; color: #94a3b8; letter-spacing: .05em; background: #f8fafc; border-bottom: 1px solid #f0f4f8; white-space: nowrap; }
    .admin-table td { padding: .85rem 1.1rem; font-size: .84rem; font-weight: 600; color: #374151; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr { transition: background .12s; }
    .admin-table tbody tr:hover td { background: #f8fafc; }

    .code-chip {
        display: inline-flex; align-items: center; gap: .4rem;
        font-family: 'Courier New', monospace; font-size: .82rem; font-weight: 800;
        color: #1e293b; background: #f1f5f9; border: 1.5px solid #e2e8f0;
        border-radius: 8px; padding: .25rem .7rem; letter-spacing: .08em;
    }

    /* Modal */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.45); backdrop-filter: blur(4px);
        z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem;
        opacity: 0; pointer-events: none; transition: opacity .2s;
    }
    .modal-overlay.open { opacity: 1; pointer-events: auto; }
    .modal-box {
        background: #fff; border-radius: 20px; width: 100%; max-width: 520px;
        max-height: 90vh; overflow-y: auto;
        transform: translateY(20px); transition: transform .25s cubic-bezier(.4,0,.2,1);
        box-shadow: 0 24px 80px rgba(0,0,0,.18);
    }
    .modal-overlay.open .modal-box { transform: translateY(0); }
    .modal-box.modal-sm { max-width: 420px; }

    .form-label { font-size: .8rem; font-weight: 700; color: #374151; margin-bottom: .35rem; display: block; }
    .form-input {
        width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px;
        padding: .65rem .9rem; font-size: .875rem; color: #1e293b;
        transition: border-color .2s, box-shadow .2s; outline: none;
        font-family: 'Cairo', sans-serif;
    }
    .form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
    .form-input.is-error { border-color: #f87171; }

    .toggle-switch { position: relative; display: inline-flex; align-items: center; gap: .6rem; cursor: pointer; }
    .toggle-switch input { display: none; }
    .toggle-track { width: 40px; height: 22px; border-radius: 99px; background: #e2e8f0; transition: background .2s; position: relative; }
    .toggle-thumb { width: 16px; height: 16px; border-radius: 50%; background: #fff; position: absolute; top: 3px; right: 3px; transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
    .toggle-switch input:checked ~ .toggle-track { background: #22c55e; }
    .toggle-switch input:checked ~ .toggle-track .toggle-thumb { transform: translateX(-18px); }

    .type-selector { display: flex; gap: .5rem; }
    .type-btn {
        flex: 1; padding: .6rem; border-radius: 10px; border: 2px solid #e2e8f0;
        background: #f8fafc; font-weight: 700; font-size: .83rem; cursor: pointer;
        transition: all .15s; text-align: center; color: #64748b; font-family: 'Cairo', sans-serif;
    }
    .type-btn.selected { background: #eff6ff; color: #2563eb; border-color: #3b82f6; }

    .note-box {
        background: #fefce8; border: 1.5px solid #fde047; border-radius: 10px;
        padding: .65rem .9rem; font-size: .78rem; color: #854d0e; font-weight: 700;
        display: flex; align-items: flex-start; gap: .5rem;
    }
</style>
@endsection

@section('content')

@php
    $totalCoupons  = $coupons->count();
    $activeCoupons = $coupons->where('is_active', true)
        ->filter(fn($c) => !$c->expires_at || $c->expires_at->isFuture())
        ->count();
    $expiredCoupons = $coupons->filter(fn($c) => $c->expires_at && $c->expires_at->isPast())->count();
    $totalUsage    = $coupons->sum('subscriptions_count');
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
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded text-red-500 flex-shrink-0" style="font-size:20px">error</span>
    {{ session('error') }}
</div>
@endif
@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 mb-5 text-sm font-bold">
    @foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach
</div>
@endif

{{-- ── Stats ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-violet-50">
            <span class="material-symbols-rounded text-violet-500" style="font-size:22px;font-variation-settings:'FILL' 1">confirmation_number</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ $totalCoupons }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">إجمالي الأكواد</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-green-50">
            <span class="material-symbols-rounded text-green-500" style="font-size:22px;font-variation-settings:'FILL' 1">check_circle</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ $activeCoupons }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">نشطة</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-red-50">
            <span class="material-symbols-rounded text-red-400" style="font-size:22px;font-variation-settings:'FILL' 1">event_busy</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ $expiredCoupons }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">منتهية الصلاحية</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-emerald-50">
            <span class="material-symbols-rounded text-emerald-500" style="font-size:22px;font-variation-settings:'FILL' 1">trending_down</span>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800 leading-none">{{ $totalUsage }}</p>
            <p class="text-xs font-bold text-slate-400 mt-1">إجمالي الاستخدامات</p>
        </div>
    </div>
</div>

{{-- ── Table Card ── --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-violet-500" style="font-size:18px;font-variation-settings:'FILL' 1">confirmation_number</span>
            </div>
            <div>
                <p class="font-black text-slate-800 text-sm">أكواد الخصم</p>
                <p class="text-[11px] text-slate-400 font-semibold">{{ $totalCoupons }} كود</p>
            </div>
        </div>
        <button onclick="openModal('createModal')"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
            <span class="material-symbols-rounded" style="font-size:18px">add</span>
            إضافة كود
        </button>
    </div>

    {{-- Table --}}
    @if($coupons->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-slate-300">
        <span class="material-symbols-rounded mb-3" style="font-size:56px;font-variation-settings:'FILL' 1">confirmation_number</span>
        <p class="font-black text-slate-400 text-sm">لا توجد أكواد خصم بعد</p>
        <button onclick="openModal('createModal')" class="mt-4 text-blue-600 font-bold text-sm hover:underline">أضف أول كود</button>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>الكود</th>
                    <th>النوع</th>
                    <th>القيمة</th>
                    <th class="hidden md:table-cell">الاستخدامات</th>
                    <th class="hidden lg:table-cell">تاريخ الانتهاء</th>
                    <th>الحالة</th>
                    <th style="text-align:left">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coupons as $coupon)
                @php
                    $isExpired   = $coupon->expires_at && $coupon->expires_at->isPast();
                    $isExhausted = $coupon->max_uses !== null && $coupon->subscriptions_count >= $coupon->max_uses;
                    $effectiveActive = $coupon->is_active && !$isExpired && !$isExhausted;
                @endphp
                <tr>
                    {{-- Code --}}
                    <td>
                        <span class="code-chip">
                            <span class="material-symbols-rounded text-slate-400" style="font-size:14px;font-variation-settings:'FILL' 1">local_offer</span>
                            {{ $coupon->code }}
                        </span>
                    </td>

                    {{-- Type --}}
                    <td>
                        <span class="badge {{ $coupon->type === 'percentage' ? 'badge-blue' : 'badge-violet' }}">
                            <span class="material-symbols-rounded" style="font-size:11px;font-variation-settings:'FILL' 1">
                                {{ $coupon->type === 'percentage' ? 'percent' : 'payments' }}
                            </span>
                            {{ $coupon->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}
                        </span>
                    </td>

                    {{-- Value --}}
                    <td class="font-black text-slate-700">
                        @if($coupon->type === 'percentage')
                            <span class="text-blue-600">{{ rtrim(rtrim(number_format((float)$coupon->value, 2), '0'), '.') }}%</span>
                        @else
                            <span class="text-violet-600">{{ rtrim(rtrim(number_format((float)$coupon->value, 2), '0'), '.') }}</span>
                            <span class="text-slate-400 text-xs font-semibold">بعملة العميل</span>
                        @endif
                    </td>

                    {{-- Usage --}}
                    <td class="hidden md:table-cell">
                        <span class="font-black text-slate-700">{{ $coupon->subscriptions_count }}</span>
                        @if($coupon->max_uses !== null)
                            <span class="text-slate-400 font-semibold"> / {{ $coupon->max_uses }}</span>
                            @if($isExhausted)
                                <span class="badge badge-red mr-1">مستنفَد</span>
                            @endif
                        @else
                            <span class="text-slate-300 font-semibold"> / ∞</span>
                        @endif
                    </td>

                    {{-- Expiry --}}
                    <td class="hidden lg:table-cell text-xs font-bold
                        {{ $isExpired ? 'text-red-400' : 'text-slate-400' }}">
                        @if($coupon->expires_at)
                            {{ $coupon->expires_at->format('d/m/Y') }}
                            @if($isExpired) <span class="badge badge-red mr-1">منتهي</span> @endif
                        @else
                            <span class="text-slate-300">بلا انتهاء</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>
                        @if($effectiveActive)
                            <span class="badge badge-green"><span class="material-symbols-rounded" style="font-size:11px;font-variation-settings:'FILL' 1">check_circle</span> نشط</span>
                        @else
                            <span class="badge badge-gray"><span class="material-symbols-rounded" style="font-size:11px">cancel</span> معطَّل</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td style="text-align:left">
                        <div class="flex items-center gap-1.5 justify-end">
                            <button title="تعديل"
                                onclick="openEditModal({{ $coupon->id }}, '{{ addslashes($coupon->code) }}', '{{ $coupon->type }}', '{{ (float)$coupon->value }}', '{{ $coupon->expires_at?->format('Y-m-d') ?? '' }}', {{ $coupon->max_uses ?? 'null' }}, {{ $coupon->is_active ? 'true' : 'false' }})"
                                class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                                <span class="material-symbols-rounded" style="font-size:16px">edit</span>
                            </button>
                            <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $coupon->is_active ? 'تعطيل' : 'تفعيل' }}"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center transition
                                           {{ $coupon->is_active ? 'bg-orange-50 hover:bg-orange-100 text-orange-500' : 'bg-green-50 hover:bg-green-100 text-green-600' }}">
                                    <span class="material-symbols-rounded" style="font-size:16px">{{ $coupon->is_active ? 'pause_circle' : 'play_circle' }}</span>
                                </button>
                            </form>
                            <button title="حذف"
                                onclick="openDeleteModal({{ $coupon->id }}, '{{ addslashes($coupon->code) }}', {{ $coupon->subscriptions_count }})"
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
    @endif
</div>

{{-- ════════════════════════════════════════
     MODAL: Create Coupon
════════════════════════════════════════ --}}
<div id="createModal" class="modal-overlay" onclick="if(event.target===this)closeModal('createModal')">
    <div class="modal-box">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-base font-black text-slate-800 flex items-center gap-2">
                <span class="material-symbols-rounded text-blue-500" style="font-size:20px">add_circle</span>
                إضافة كود خصم
            </h3>
            <button onclick="closeModal('createModal')" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.coupons.store') }}" class="p-6 space-y-4">
            @csrf

            {{-- Code --}}
            <div>
                <label class="form-label">الكود <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}"
                       placeholder="مثال: WELCOME20"
                       class="form-input font-mono uppercase @error('code') is-error @enderror"
                       style="text-transform:uppercase;letter-spacing:.08em"
                       maxlength="50" required
                       oninput="this.value=this.value.toUpperCase().replace(/\s/g,'')">
                <p class="text-[11px] text-slate-400 mt-1 font-semibold">يتم تحويله تلقائياً لأحرف كبيرة، بدون مسافات</p>
                @error('code')<p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Type --}}
            <div>
                <label class="form-label">نوع الخصم <span class="text-red-500">*</span></label>
                <div class="type-selector" id="createTypeSelector">
                    <button type="button" class="type-btn selected" data-type="percentage" onclick="selectType('create','percentage')">
                        <span class="material-symbols-rounded align-middle" style="font-size:16px;vertical-align:middle">percent</span>
                        نسبة مئوية
                    </button>
                    <button type="button" class="type-btn" data-type="fixed" onclick="selectType('create','fixed')">
                        <span class="material-symbols-rounded align-middle" style="font-size:16px;vertical-align:middle">payments</span>
                        مبلغ ثابت
                    </button>
                </div>
                <input type="hidden" name="type" id="createTypeInput" value="percentage">
            </div>

            {{-- Fixed-amount note (hidden by default) --}}
            <div id="createFixedNote" class="note-box hidden">
                <span class="material-symbols-rounded flex-shrink-0 mt-0.5" style="font-size:16px;font-variation-settings:'FILL' 1">warning</span>
                <span>المبلغ الثابت يُطبَّق بعملة العميل كما هي (بدون تحويل). للمتاجر متعددة العملات، استخدم نسبة مئوية بدلاً منه.</span>
            </div>

            {{-- Value --}}
            <div>
                <label class="form-label">قيمة الخصم <span class="text-red-500">*</span></label>
                <div style="position:relative;">
                    <input type="number" name="value" value="{{ old('value') }}"
                           id="createValueInput"
                           placeholder="مثال: 10" step="0.01" min="0.01"
                           class="form-input @error('value') is-error @enderror"
                           required style="padding-inline-end:3.5rem">
                    <span id="createValueSuffix"
                          style="position:absolute;inset-block:0;inset-inline-end:.9rem;display:flex;align-items:center;font-size:.8rem;font-weight:700;color:#94a3b8;pointer-events:none">
                        %
                    </span>
                </div>
                @error('value')<p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Expiry --}}
                <div>
                    <label class="form-label">تاريخ الانتهاء <span class="text-slate-300">(اختياري)</span></label>
                    <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                           class="form-input" dir="ltr">
                </div>
                {{-- Max uses --}}
                <div>
                    <label class="form-label">الحد الأقصى للاستخدام <span class="text-slate-300">(اختياري)</span></label>
                    <input type="number" name="max_uses" value="{{ old('max_uses') }}"
                           placeholder="غير محدود" min="1" class="form-input">
                </div>
            </div>

            {{-- Active toggle --}}
            <div class="flex items-center justify-between bg-slate-50 rounded-xl p-3.5">
                <div>
                    <p class="text-sm font-bold text-slate-700">نشط فوراً</p>
                    <p class="text-xs text-slate-400 font-semibold">الكود قابل للاستخدام فور الإنشاء</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <div class="toggle-track"><div class="toggle-thumb"></div></div>
                </label>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal('createModal')"
                    class="flex-1 border border-slate-200 text-slate-600 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">
                    إلغاء
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                    إنشاء الكود
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════
     MODAL: Edit Coupon
════════════════════════════════════════ --}}
<div id="editModal" class="modal-overlay" onclick="if(event.target===this)closeModal('editModal')">
    <div class="modal-box">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-base font-black text-slate-800 flex items-center gap-2">
                <span class="material-symbols-rounded text-slate-500" style="font-size:20px">edit</span>
                تعديل الكود: <span id="editCodeLabel" class="font-mono text-blue-600"></span>
            </h3>
            <button onclick="closeModal('editModal')" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')

            {{-- Type --}}
            <div>
                <label class="form-label">نوع الخصم</label>
                <div class="type-selector" id="editTypeSelector">
                    <button type="button" class="type-btn" data-type="percentage" onclick="selectType('edit','percentage')">
                        <span class="material-symbols-rounded align-middle" style="font-size:16px;vertical-align:middle">percent</span>
                        نسبة مئوية
                    </button>
                    <button type="button" class="type-btn" data-type="fixed" onclick="selectType('edit','fixed')">
                        <span class="material-symbols-rounded align-middle" style="font-size:16px;vertical-align:middle">payments</span>
                        مبلغ ثابت
                    </button>
                </div>
                <input type="hidden" name="type" id="editTypeInput" value="percentage">
            </div>

            {{-- Fixed-amount note --}}
            <div id="editFixedNote" class="note-box hidden">
                <span class="material-symbols-rounded flex-shrink-0 mt-0.5" style="font-size:16px;font-variation-settings:'FILL' 1">warning</span>
                <span>المبلغ الثابت يُطبَّق بعملة العميل كما هي (بدون تحويل). للمتاجر متعددة العملات، استخدم نسبة مئوية.</span>
            </div>

            {{-- Value --}}
            <div>
                <label class="form-label">قيمة الخصم <span class="text-red-500">*</span></label>
                <div style="position:relative;">
                    <input type="number" name="value" id="editValueInput"
                           step="0.01" min="0.01" class="form-input" required
                           style="padding-inline-end:3.5rem">
                    <span id="editValueSuffix"
                          style="position:absolute;inset-block:0;inset-inline-end:.9rem;display:flex;align-items:center;font-size:.8rem;font-weight:700;color:#94a3b8;pointer-events:none">
                        %
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">تاريخ الانتهاء <span class="text-slate-300">(اختياري)</span></label>
                    <input type="date" name="expires_at" id="editExpiry" class="form-input" dir="ltr">
                </div>
                <div>
                    <label class="form-label">الحد الأقصى للاستخدام <span class="text-slate-300">(اختياري)</span></label>
                    <input type="number" name="max_uses" id="editMaxUses" placeholder="غير محدود" min="1" class="form-input">
                </div>
            </div>

            <div class="flex items-center justify-between bg-slate-50 rounded-xl p-3.5">
                <div>
                    <p class="text-sm font-bold text-slate-700">نشط</p>
                    <p class="text-xs text-slate-400 font-semibold">تفعيل أو تعطيل الكود</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" id="editIsActive" value="1">
                    <div class="toggle-track"><div class="toggle-thumb"></div></div>
                </label>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeModal('editModal')"
                    class="flex-1 border border-slate-200 text-slate-600 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">
                    إلغاء
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════
     MODAL: Delete Coupon
════════════════════════════════════════ --}}
<div id="deleteModal" class="modal-overlay" onclick="if(event.target===this)closeModal('deleteModal')">
    <div class="modal-box modal-sm">
        <div class="p-6">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-red-500" style="font-size:28px;font-variation-settings:'FILL' 1">delete_forever</span>
                </div>
            </div>
            <h3 class="text-base font-black text-slate-800 text-center mb-1">حذف الكود</h3>
            <p class="text-sm text-slate-500 font-semibold text-center mb-1">
                هل أنت متأكد من حذف
                <strong id="deleteCouponCode" class="font-mono text-slate-800"></strong>؟
            </p>
            <p id="deleteUsageWarn" class="text-xs text-amber-600 font-bold text-center mb-4 hidden">
                <span class="material-symbols-rounded align-middle" style="font-size:14px;font-variation-settings:'FILL' 1;vertical-align:middle">warning</span>
                هذا الكود استُخدم <span id="deleteUsageCount"></span> مرة — بيانات الاشتراكات التاريخية ستظل محفوظة.
            </p>
            <form id="deleteForm" method="POST" class="mt-4">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('deleteModal')"
                        class="flex-1 border border-slate-200 text-slate-600 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                        حذف نهائياً
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
// ── Modal helpers ──────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['createModal','editModal','deleteModal'].forEach(closeModal);
    }
});

// ── Type selector ──────────────────────────────────────
function selectType(prefix, type) {
    document.getElementById(prefix + 'TypeInput').value = type;
    document.querySelectorAll('#' + prefix + 'TypeSelector .type-btn').forEach(btn => {
        btn.classList.toggle('selected', btn.dataset.type === type);
    });
    const noteEl   = document.getElementById(prefix + 'FixedNote');
    const suffixEl = document.getElementById(prefix + 'ValueSuffix');
    if (noteEl)   noteEl.classList.toggle('hidden', type !== 'fixed');
    if (suffixEl) suffixEl.textContent = type === 'percentage' ? '%' : '—';
}

// ── Edit modal ─────────────────────────────────────────
function openEditModal(id, code, type, value, expires, maxUses, isActive) {
    document.getElementById('editCodeLabel').textContent = code;
    document.getElementById('editForm').action = '/admin/coupons/' + id;
    document.getElementById('editValueInput').value = value;
    document.getElementById('editExpiry').value    = expires || '';
    document.getElementById('editMaxUses').value   = maxUses || '';
    document.getElementById('editIsActive').checked = isActive;

    selectType('edit', type);
    openModal('editModal');
}

// ── Delete modal ───────────────────────────────────────
function openDeleteModal(id, code, usageCount) {
    document.getElementById('deleteCouponCode').textContent = code;
    document.getElementById('deleteForm').action = '/admin/coupons/' + id;
    const warnEl = document.getElementById('deleteUsageWarn');
    document.getElementById('deleteUsageCount').textContent = usageCount;
    warnEl.classList.toggle('hidden', usageCount === 0);
    openModal('deleteModal');
}

// Auto-open create modal on validation error
@if($errors->any() && old('code'))
openModal('createModal');
@endif
</script>
@endsection

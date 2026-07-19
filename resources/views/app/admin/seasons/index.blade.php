@extends('layouts.admin.app')

@section('title', 'المواسم والخصومات')
@section('page-title', 'المواسم والخصومات الموسمية')
@section('page-subtitle', 'أنشئ مواسم ترويجية بخصومات مؤقتة تُطبَّق تلقائيًا على جميع الباقات')

@section('style')
<style>
.card { background:#fff; border:1px solid #e8edf5; border-radius:20px; overflow:hidden; }
.card-header { display:flex; align-items:center; gap:.85rem; padding:1rem 1.4rem; border-bottom:1px solid #f1f5f9; background:#fafbfd; }
.card-header h3 { font-size:.92rem; font-weight:900; color:#1e293b; }
.card-body { padding:1.4rem; }

.season-row { display:grid; grid-template-columns: 1fr auto; gap:1rem; align-items:center; padding:1rem 1.2rem; border-bottom:1px solid #f1f5f9; }
.season-row:last-child { border-bottom:none; }
.season-row:hover { background:#fafbfd; }

.badge { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .75rem; border-radius:999px; font-size:.72rem; font-weight:800; white-space:nowrap; }
.badge-green  { background:#dcfce7; color:#16a34a; }
.badge-gray   { background:#f1f5f9; color:#64748b; }
.badge-orange { background:#fff7ed; color:#ea580c; }

.form-label { display:block; font-size:.78rem; font-weight:700; color:#374151; margin-bottom:.35rem; }
.form-input { width:100%; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:10px; padding:.6rem .85rem; font-size:.85rem; font-weight:600; color:#1e293b; font-family:'Cairo',sans-serif; outline:none; transition:border-color .2s; }
.form-input:focus { background:#fff; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
.form-input.has-error { border-color:#ef4444; }

.toggle-switch { display:inline-flex; align-items:center; gap:.6rem; cursor:pointer; user-select:none; }
.toggle-track { width:40px; height:22px; background:#e2e8f0; border-radius:999px; position:relative; transition:background .2s; flex-shrink:0; }
.toggle-track.active { background:#22c55e; }
.toggle-thumb { width:16px; height:16px; background:#fff; border-radius:50%; position:absolute; top:3px; left:3px; transition:transform .2s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
.toggle-track.active .toggle-thumb { transform:translateX(18px); }

.modal-backdrop { position:fixed; inset:0; background:rgba(15,23,42,.55); backdrop-filter:blur(3px); z-index:200; opacity:0; pointer-events:none; transition:opacity .25s; display:flex; align-items:center; justify-content:center; padding:1rem; }
.modal-backdrop.is-open { opacity:1; pointer-events:auto; }
.modal-box { background:#fff; border-radius:20px; width:100%; max-width:560px; box-shadow:0 24px 60px rgba(0,0,0,.18); transform:translateY(16px) scale(.97); transition:transform .25s; overflow:hidden; }
.modal-backdrop.is-open .modal-box { transform:translateY(0) scale(1); }
.modal-header { display:flex; align-items:center; justify-content:space-between; padding:1.1rem 1.4rem; border-bottom:1px solid #f1f5f9; }
.modal-header h3 { font-size:.95rem; font-weight:900; color:#1e293b; }
.modal-body { padding:1.4rem; display:flex; flex-direction:column; gap:1rem; }
.modal-footer { display:flex; gap:.75rem; padding:1rem 1.4rem; border-top:1px solid #f1f5f9; background:#fafbfd; }
.btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; padding:.6rem 1.3rem; border-radius:12px; font-size:.85rem; font-weight:700; border:none; cursor:pointer; font-family:'Cairo',sans-serif; transition:opacity .2s; }
.btn-primary { background:#3b82f6; color:#fff; }
.btn-red { background:#ef4444; color:#fff; }
.btn-ghost { background:#f1f5f9; color:#475569; }
.btn:hover { opacity:.88; }
.btn:disabled { opacity:.5; cursor:not-allowed; }
.error-msg { font-size:.72rem; color:#ef4444; font-weight:600; margin-top:.25rem; }
</style>
@endsection

@section('content')

@php
    $now = now();
    $activeSeason = $seasons->first(fn ($s) => $s->is_active && $s->starts_at <= $now && $s->ends_at >= $now);
@endphp

{{-- Flash messages --}}
@foreach(['success','error'] as $type)
@if(session($type))
<div class="flex items-center gap-3 {{ $type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }} border rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded flex-shrink-0" style="font-size:18px;font-variation-settings:'FILL' 1">{{ $type === 'success' ? 'check_circle' : 'error' }}</span>
    {{ session($type) }}
</div>
@endif
@endforeach

@if($errors->has('overlap'))
<div class="flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">warning</span>
    {{ $errors->first('overlap') }}
</div>
@endif

{{-- Current active season banner --}}
@if($activeSeason)
<div class="flex items-center gap-3 bg-amber-50 border border-amber-300 rounded-2xl px-5 py-3.5 mb-5">
    <span class="material-symbols-rounded text-amber-500 flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 1">local_offer</span>
    <div class="text-sm font-bold">
        <span class="text-slate-600">الموسم الفعال الآن: </span>
        <span class="text-amber-700">{{ $activeSeason->name_ar }}</span>
        <span class="text-slate-400 font-semibold text-xs mr-2">خصم {{ $activeSeason->discount_percentage }}% · حتى {{ $activeSeason->ends_at->format('d/m/Y H:i') }}</span>
    </div>
</div>
@else
<div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 mb-5">
    <span class="material-symbols-rounded text-slate-400 flex-shrink-0" style="font-size:18px">event_busy</span>
    <span class="text-sm font-bold text-slate-500">لا يوجد موسم فعال حاليًا — الأسعار تُعرض بدون خصم</span>
</div>
@endif

<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-lg font-black text-slate-800">المواسم</h2>
        <p class="text-xs text-slate-400 font-semibold mt-0.5">{{ $seasons->count() }} موسم · {{ $seasons->where('is_active', true)->count() }} مفعّل</p>
    </div>
    <button onclick="openModal('createModal')"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
        <span class="material-symbols-rounded" style="font-size:18px">add</span>
        إضافة موسم
    </button>
</div>

{{-- Seasons list --}}
<div class="card">
    @if($seasons->isEmpty())
    <div class="text-center py-16">
        <span class="material-symbols-rounded text-slate-300" style="font-size:56px">local_offer</span>
        <p class="text-slate-400 font-bold mt-3">لا توجد مواسم بعد</p>
        <button onclick="openModal('createModal')" class="mt-4 text-blue-600 font-bold text-sm hover:underline">أضف أول موسم</button>
    </div>
    @else
    @foreach($seasons as $season)
    @php
        $isCurrentlyActive = $season->is_active && $season->starts_at <= $now && $season->ends_at >= $now;
        $isScheduled       = $season->is_active && $season->starts_at > $now;
        $isExpired         = $season->ends_at < $now;
    @endphp
    <div class="season-row">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <span class="font-black text-slate-800 text-sm">{{ $season->name_ar }}</span>
                <span class="text-slate-400 text-xs font-semibold">/</span>
                <span class="text-slate-500 text-xs font-semibold">{{ $season->name_en }}</span>
                @if($isCurrentlyActive)
                    <span class="badge badge-green">فعال الآن</span>
                @elseif($isScheduled)
                    <span class="badge badge-orange">مجدول</span>
                @elseif($isExpired || !$season->is_active)
                    <span class="badge badge-gray">{{ $isExpired ? 'منتهي' : 'موقوف' }}</span>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 font-semibold">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-rounded" style="font-size:13px">percent</span>
                    خصم {{ $season->discount_percentage }}%
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-rounded" style="font-size:13px">calendar_today</span>
                    {{ $season->starts_at->format('d/m/Y') }} ← {{ $season->ends_at->format('d/m/Y') }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            {{-- Toggle active --}}
            <form action="{{ route('admin.seasons.toggle', $season) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" title="{{ $season->is_active ? 'إيقاف' : 'تفعيل' }}"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition
                           {{ $season->is_active ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                    <span class="material-symbols-rounded" style="font-size:17px;font-variation-settings:'FILL' 1">
                        {{ $season->is_active ? 'toggle_on' : 'toggle_off' }}
                    </span>
                </button>
            </form>

            {{-- Edit --}}
            <button onclick="openEdit({{ $season->id }}, {{ json_encode(['name_ar'=>$season->name_ar,'name_en'=>$season->name_en,'discount_percentage'=>$season->discount_percentage,'starts_at'=>$season->starts_at->format('Y-m-d\TH:i'),'ends_at'=>$season->ends_at->format('Y-m-d\TH:i'),'is_active'=>$season->is_active]) }})"
                class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                <span class="material-symbols-rounded" style="font-size:17px">edit</span>
            </button>

            {{-- Delete --}}
            <form action="{{ route('admin.seasons.destroy', $season) }}" method="POST"
                  onsubmit="return confirm('حذف موسم «{{ $season->name_ar }}»؟ لن يؤثر ذلك على الاشتراكات المسجلة.')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition">
                    <span class="material-symbols-rounded" style="font-size:17px">delete</span>
                </button>
            </form>
        </div>
    </div>
    @endforeach
    @endif
</div>

{{-- ════ Create Modal ════ --}}
<div class="modal-backdrop" id="createModal" onclick="closeOnBackdrop(event,'createModal')">
    <div class="modal-box">
        <div class="modal-header">
            <h3>إضافة موسم جديد</h3>
            <button onclick="closeModal('createModal')" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form action="{{ route('admin.seasons.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">الاسم بالعربي <span class="text-red-500">*</span></label>
                        <input type="text" name="name_ar" class="form-input {{ $errors->has('name_ar') ? 'has-error' : '' }}"
                               value="{{ old('name_ar') }}" placeholder="مثال: عيد الفطر" required>
                        @error('name_ar')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">الاسم بالإنجليزي <span class="text-red-500">*</span></label>
                        <input type="text" name="name_en" class="form-input {{ $errors->has('name_en') ? 'has-error' : '' }}"
                               value="{{ old('name_en') }}" placeholder="e.g. Eid Al-Fitr" required>
                        @error('name_en')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">نسبة الخصم % (1–90) <span class="text-red-500">*</span></label>
                    <input type="number" name="discount_percentage" class="form-input {{ $errors->has('discount_percentage') ? 'has-error' : '' }}"
                           value="{{ old('discount_percentage') }}" min="1" max="90" step="0.01" placeholder="20" required>
                    @error('discount_percentage')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">تاريخ البداية <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="starts_at" class="form-input {{ $errors->has('starts_at') ? 'has-error' : '' }}"
                               value="{{ old('starts_at') }}" required>
                        @error('starts_at')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">تاريخ النهاية <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="ends_at" class="form-input {{ $errors->has('ends_at') ? 'has-error' : '' }}"
                               value="{{ old('ends_at') }}" required>
                        @error('ends_at')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>
                </div>

                <label class="toggle-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }}
                           onchange="this.previousElementSibling.value = this.checked ? '1' : '0'">
                    <div class="toggle-track" id="createToggleTrack"><div class="toggle-thumb"></div></div>
                    <span class="text-sm font-bold text-slate-600">فعّال</span>
                </label>
                <p class="text-xs text-slate-400 font-semibold -mt-2">لو فعّلته وكان متداخلًا مع موسم نشط آخر، سيُرفض الحفظ بتحذير واضح.</p>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('createModal')" class="btn btn-ghost flex-1">إلغاء</button>
                <button type="submit" class="btn btn-primary flex-1">
                    <span class="material-symbols-rounded" style="font-size:16px">add</span>
                    إنشاء الموسم
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════ Edit Modal ════ --}}
<div class="modal-backdrop" id="editModal" onclick="closeOnBackdrop(event,'editModal')">
    <div class="modal-box">
        <div class="modal-header">
            <h3>تعديل الموسم</h3>
            <button onclick="closeModal('editModal')" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form id="editForm" action="" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">الاسم بالعربي <span class="text-red-500">*</span></label>
                        <input type="text" name="name_ar" id="editNameAr" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">الاسم بالإنجليزي <span class="text-red-500">*</span></label>
                        <input type="text" name="name_en" id="editNameEn" class="form-input" required>
                    </div>
                </div>
                <div>
                    <label class="form-label">نسبة الخصم % (1–90) <span class="text-red-500">*</span></label>
                    <input type="number" name="discount_percentage" id="editDiscount" class="form-input" min="1" max="90" step="0.01" required>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">تاريخ البداية <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="starts_at" id="editStartsAt" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">تاريخ النهاية <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="ends_at" id="editEndsAt" class="form-input" required>
                    </div>
                </div>
                <label class="toggle-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="editIsActive" value="1"
                           onchange="this.previousElementSibling.value = this.checked ? '1' : '0'; document.getElementById('editToggleTrack').classList.toggle('active', this.checked)">
                    <div class="toggle-track" id="editToggleTrack"><div class="toggle-thumb"></div></div>
                    <span class="text-sm font-bold text-slate-600">فعّال</span>
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editModal')" class="btn btn-ghost flex-1">إلغاء</button>
                <button type="submit" class="btn btn-primary flex-1">
                    <span class="material-symbols-rounded" style="font-size:16px">save</span>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('is-open'); }
function closeModal(id) { document.getElementById(id).classList.remove('is-open'); }
function closeOnBackdrop(e, id) { if (e.target === document.getElementById(id)) closeModal(id); }

function openEdit(id, data) {
    document.getElementById('editForm').action = '/admin/seasons/' + id;
    document.getElementById('editNameAr').value  = data.name_ar;
    document.getElementById('editNameEn').value  = data.name_en;
    document.getElementById('editDiscount').value = data.discount_percentage;
    document.getElementById('editStartsAt').value = data.starts_at;
    document.getElementById('editEndsAt').value   = data.ends_at;
    const cb = document.getElementById('editIsActive');
    cb.checked = !!data.is_active;
    cb.previousElementSibling.value = data.is_active ? '1' : '0';
    document.getElementById('editToggleTrack').classList.toggle('active', !!data.is_active);
    openModal('editModal');
}

// Auto-open create modal if there were validation errors on store
@if($errors->any() && !$errors->has('overlap') && old('name_ar'))
document.addEventListener('DOMContentLoaded', () => openModal('createModal'));
@endif
</script>

@endsection

@extends('layouts.admin.app')

@section('title', $meta['label'])
@section('page-title', $meta['label'])
@section('page-subtitle', 'تعديل محتوى الصفحة بالعربية والإنجليزية')

@section('style')
<style>
.settings-card { background:#fff; border-radius:20px; border:1px solid #e2e8f0; overflow:hidden; }
.settings-card-header { padding:1.1rem 1.4rem; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:.75rem; background:#fafbfc; }
.section-number { display:inline-flex; align-items:center; justify-content:center; min-width:34px; height:28px; padding:0 .4rem; border-radius:9px; background:#eff6ff; color:#2563eb; font-size:.78rem; font-weight:900; font-family:'Cairo',sans-serif; flex-shrink:0; }

.form-label { font-size:.8rem; font-weight:700; color:#374151; margin-bottom:.4rem; display:block; }
.form-input {
    width:100%; border:1.5px solid #e2e8f0; border-radius:10px;
    padding:.65rem .9rem; font-size:.875rem; color:#1e293b;
    transition:border-color .2s, box-shadow .2s; outline:none;
    font-family:'Cairo', sans-serif;
}
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }

.lang-badge { width:20px; height:20px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-size:10px; font-weight:900; flex-shrink:0; }
.lang-badge-ar { background:#dcfce7; color:#15803d; }
.lang-badge-en { background:#dbeafe; color:#1d4ed8; }

.btn-primary { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; padding:.75rem 1.6rem; border-radius:12px; font-size:.85rem; font-weight:800; border:none; cursor:pointer; font-family:'Cairo',sans-serif; background:#3b82f6; color:#fff; transition:opacity .2s; }
.btn-primary:hover { opacity:.9; }
</style>
@endsection

@section('content')

{{-- Flash --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded flex-shrink-0" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
    {{ session('success') }}
</div>
@endif

{{-- Back link --}}
<a href="{{ route('admin.pages.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-slate-600 mb-5 transition-colors">
    <span class="material-symbols-rounded" style="font-size:16px">{{ app()->getLocale() === 'ar' ? 'arrow_forward' : 'arrow_back' }}</span>
    كل صفحات الموقع
</a>

{{-- Legal requirement warning --}}
@if($meta['legal'])
<div class="flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded flex-shrink-0 mt-0.5" style="font-size:18px;font-variation-settings:'FILL' 1">warning</span>
    <span>هذه الصفحة مطلوبة قانونياً لتفعيل بوابة الدفع (Paymob) — يرجى التعديل بحذر وعدم حذف أي بند أو رقم من البنود.</span>
</div>
@endif

<form method="POST" action="{{ route('admin.pages.update', $page) }}" novalidate>
    @csrf
    @method('PUT')

    <div class="flex flex-col gap-5">

        @foreach($sections as $section)
        <div class="settings-card">
            <div class="settings-card-header">
                @if($section['number'])
                <span class="section-number">{{ $section['number'] }}</span>
                @endif
                <h3 class="text-sm font-black text-slate-800">{{ $section['title'] }}</h3>
            </div>

            <div class="p-6 flex flex-col gap-6">
                @foreach($section['fields'] as $field)
                    @if($field['type'] === 'setting_single')
                        @php
                            $settingValue = old("settings_single.{$field['key']}", \App\Models\Setting::get($field['key'], ''));
                        @endphp
                        <div>
                            <label class="form-label">{{ $field['label'] }}</label>
                            <input type="text" name="settings_single[{{ $field['key'] }}]" value="{{ $settingValue }}"
                                   class="form-input" dir="ltr">
                        </div>
                    @elseif($field['type'] === 'setting_bilingual')
                        @php
                            $settingAr = old("settings_bilingual.{$field['key']}.ar", \App\Models\Setting::get($field['key'] . '_ar', ''));
                            $settingEn = old("settings_bilingual.{$field['key']}.en", \App\Models\Setting::get($field['key'] . '_en', ''));
                        @endphp
                        <div>
                            <label class="form-label">{{ $field['label'] }}</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="flex items-center gap-1.5 mb-1.5">
                                        <span class="lang-badge lang-badge-ar">ع</span>
                                        <span class="text-[11px] font-bold text-slate-400">بالعربية</span>
                                    </div>
                                    <input type="text" name="settings_bilingual[{{ $field['key'] }}][ar]" value="{{ $settingAr }}"
                                           class="form-input font-arabic" dir="rtl">
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5 mb-1.5">
                                        <span class="lang-badge lang-badge-en">EN</span>
                                        <span class="text-[11px] font-bold text-slate-400">بالإنجليزية</span>
                                    </div>
                                    <input type="text" name="settings_bilingual[{{ $field['key'] }}][en]" value="{{ $settingEn }}"
                                           class="form-input" dir="ltr">
                                </div>
                            </div>
                        </div>
                    @else
                    @php
                        $row      = $content->get($field['key']);
                        $valueAr  = old("fields.{$field['key']}.ar", $row->value_ar ?? '');
                        $valueEn  = old("fields.{$field['key']}.en", $row->value_en ?? '');
                    @endphp

                    <div>
                        <label class="form-label">{{ $field['label'] }}</label>

                        @if($field['type'] === 'list')
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 flex items-start gap-2.5 mb-3">
                            <span class="material-symbols-rounded text-amber-500 flex-shrink-0" style="font-size:16px;font-variation-settings:'FILL' 1">info</span>
                            <p class="text-xs font-bold text-amber-700">
                                كل سطر = عنصر واحد في القائمة
                                @if(isset($field['paired_with']))
                                — يجب أن يتطابق عدد الأسطر هنا مع عدد الأسطر في الحقل المقابل
                                @endif
                            </p>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Arabic --}}
                            <div>
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="lang-badge lang-badge-ar">ع</span>
                                    <span class="text-[11px] font-bold text-slate-400">بالعربية</span>
                                </div>
                                @if($field['type'] === 'text')
                                <input type="text" name="fields[{{ $field['key'] }}][ar]" value="{{ $valueAr }}"
                                       class="form-input font-arabic" dir="rtl">
                                @else
                                <textarea name="fields[{{ $field['key'] }}][ar]" rows="{{ $field['type'] === 'list' ? 5 : 3 }}"
                                          class="form-input font-arabic resize-none leading-relaxed" dir="rtl">{{ $valueAr }}</textarea>
                                @endif
                            </div>

                            {{-- English --}}
                            <div>
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="lang-badge lang-badge-en">EN</span>
                                    <span class="text-[11px] font-bold text-slate-400">بالإنجليزية</span>
                                </div>
                                @if($field['type'] === 'text')
                                <input type="text" name="fields[{{ $field['key'] }}][en]" value="{{ $valueEn }}"
                                       class="form-input" dir="ltr">
                                @else
                                <textarea name="fields[{{ $field['key'] }}][en]" rows="{{ $field['type'] === 'list' ? 5 : 3 }}"
                                          class="form-input resize-none leading-relaxed" dir="ltr">{{ $valueEn }}</textarea>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-rounded" style="font-size:18px">save</span>
                حفظ التغييرات
            </button>
        </div>

    </div>
</form>

@endsection

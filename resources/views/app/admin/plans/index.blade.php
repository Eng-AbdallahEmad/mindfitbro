@extends('layouts.admin.app')

@section('title', 'الباقات')

@section('page-title', 'الباقات')
@section('page-subtitle', 'إدارة باقات الاشتراك والمميزات')

@section('style')
<style>
    .plan-card {
        background: #fff;
        border-radius: 20px;
        border: 2px solid #e2e8f0;
        transition: border-color .2s, box-shadow .2s, transform .2s;
        position: relative;
        overflow: hidden;
    }
    .plan-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 8px 32px rgba(59,130,246,.12);
        transform: translateY(-2px);
    }
    .plan-card.inactive { opacity: .6; }

    .tab-btn {
        padding: .55rem 1.25rem;
        border-radius: 10px;
        font-size: .85rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: background .15s, color .15s;
        background: transparent;
        color: #64748b;
    }
    .tab-btn.active {
        background: #3b82f6;
        color: #fff;
    }
    .tab-btn:not(.active):hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.45);
        backdrop-filter: blur(4px);
        z-index: 100;
        display: flex; align-items: center; justify-content: center;
        padding: 1rem;
        opacity: 0; pointer-events: none;
        transition: opacity .2s;
    }
    .modal-overlay.open { opacity: 1; pointer-events: auto; }
    .modal-box {
        background: #fff;
        border-radius: 20px;
        width: 100%; max-width: 620px;
        max-height: 90vh;
        overflow-y: auto;
        transform: translateY(20px);
        transition: transform .25s cubic-bezier(.4,0,.2,1);
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
    .toggle-track {
        width: 40px; height: 22px; border-radius: 99px;
        background: #e2e8f0; transition: background .2s; position: relative;
    }
    .toggle-thumb {
        width: 16px; height: 16px; border-radius: 50%; background: #fff;
        position: absolute; top: 3px; right: 3px;
        transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2);
    }
    .toggle-switch input:checked ~ .toggle-track { background: #22c55e; }
    .toggle-switch input:checked ~ .toggle-track .toggle-thumb { transform: translateX(-18px); }

    .feature-row {
        display: flex; align-items: center; gap: .75rem;
        padding: .55rem .75rem; border-radius: 10px;
        transition: background .15s;
    }
    .feature-row:hover { background: #f8fafc; }

    .badge-popular {
        position: absolute; top: 14px; left: 14px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff; font-size: .65rem; font-weight: 900;
        padding: .2rem .65rem; border-radius: 99px; letter-spacing: .05em;
    }
    .badge-inactive {
        position: absolute; top: 14px; left: 14px;
        background: #e2e8f0; color: #94a3b8;
        font-size: .65rem; font-weight: 900;
        padding: .2rem .65rem; border-radius: 99px;
    }
</style>
@endsection

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
<div class="mb-5 rounded-xl bg-green-50 border border-green-200 p-4 flex items-center gap-3">
    <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:20px">check_circle</span>
    <p class="text-sm font-bold text-green-700">{{ session('success') }}</p>
</div>
@endif
@if(session('error'))
<div class="mb-5 rounded-xl bg-red-50 border border-red-200 p-4 flex items-center gap-3">
    <span class="material-symbols-rounded text-red-500 flex-shrink-0" style="font-size:20px">error</span>
    <p class="text-sm font-bold text-red-700">{{ session('error') }}</p>
</div>
@endif

{{-- Tabs --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-1.5 inline-flex gap-1 mb-6">
    <button class="tab-btn active" onclick="switchTab('plans', this)">
        <span class="material-symbols-rounded align-middle" style="font-size:16px;vertical-align:middle">workspace_premium</span>
        الباقات ({{ $plans->count() }})
    </button>
    <button class="tab-btn" onclick="switchTab('features', this)">
        <span class="material-symbols-rounded align-middle" style="font-size:16px;vertical-align:middle">check_circle</span>
        المميزات ({{ $features->count() }})
    </button>
</div>

{{-- ═══════════ TAB: PLANS ═══════════ --}}
<div id="tab-plans">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-black text-slate-800">باقات الاشتراك</h2>
            <p class="text-xs text-slate-400 font-semibold mt-0.5">{{ $plans->count() }} باقة — {{ $plans->where('is_active', true)->count() }} نشطة</p>
        </div>
        <button onclick="openCreateModal()"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors">
            <span class="material-symbols-rounded" style="font-size:18px">add</span>
            إضافة باقة
        </button>
    </div>

    {{-- Popular badge status info --}}
    <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 mb-5 text-sm font-bold" dir="rtl">
        <span class="material-symbols-rounded text-blue-400 flex-shrink-0 mt-0.5" style="font-size:17px">auto_awesome</span>
        <div>
            <span class="text-slate-600">شارة "الأكثر طلبًا" تظهر حاليًا على: </span>
            <span class="text-blue-700">{{ $popularPlanName ?? '—' }}</span>
            <span class="text-slate-400 font-semibold text-xs mr-1">({{ $popularIsAuto ? 'تلقائي — بناءً على المبيعات' : 'يدوي — من إعداد الأدمن' }})</span>
            @if(!$popularIsAuto)
            <span class="text-amber-600 text-xs font-semibold mr-1">· المبيعات أقل من {{ $minCount }} اشتراكات، يُستخدم الاختيار اليدوي</span>
            @endif
        </div>
    </div>

    {{-- Plans Grid --}}
    @if($plans->isEmpty())
    <div class="text-center py-16 bg-white rounded-2xl border border-slate-100">
        <span class="material-symbols-rounded text-slate-300" style="font-size:56px">workspace_premium</span>
        <p class="text-slate-400 font-bold mt-3">لا توجد باقات بعد</p>
        <button onclick="openCreateModal()" class="mt-4 text-blue-600 font-bold text-sm hover:underline">أضف أول باقة</button>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($plans as $plan)
        <div class="plan-card {{ !$plan->is_active ? 'inactive' : '' }}">

            {{-- Badge --}}
            @if($plan->popular && $plan->is_active)
                <span class="badge-popular">الأكثر شعبية <span class="material-symbols-rounded" style="font-size:14px;font-variation-settings:'FILL' 1;color:#F59E0B;vertical-align:middle">star</span></span>
            @elseif(!$plan->is_active)
                <span class="badge-inactive">معطّل</span>
            @endif

            <div class="p-6">
                {{-- Icon + Name --}}
                <div class="flex items-center gap-3 mb-4 mt-2">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 {{ $plan->iconBgClass }}">
                        <span class="material-symbols-rounded {{ $plan->iconColorClass }}"
                              style="font-size:22px;font-variation-settings:'FILL' 1">{{ $plan->iconName }}</span>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-800">{{ $plan->name }}</h3>
                        <span class="text-[11px] text-slate-400 font-semibold font-mono">{{ $plan->key }}</span>
                    </div>
                </div>

                {{-- Desc --}}
                @if($plan->desc)
                <p class="text-xs text-slate-500 font-semibold mb-4 leading-relaxed line-clamp-2">{{ $plan->desc }}</p>
                @endif

                {{-- Pricing matrix: SAR 3m/6m prominently, then currency badges --}}
                @php
                    $sar3m = $plan->priceFor('SAR', 3);
                    $sar6m = $plan->priceFor('SAR', 6);
                @endphp
                <div class="flex items-end gap-2 mb-3">
                    <div class="bg-blue-50 rounded-xl px-3 py-2 text-center flex-1">
                        <p class="text-[10px] font-bold text-blue-400 mb-0.5">٣ شهور — ر.س</p>
                        <p class="text-lg font-black text-blue-700">{{ $sar3m ? number_format($sar3m->price, 0) : '—' }}</p>
                    </div>
                    <div class="bg-indigo-50 rounded-xl px-3 py-2 text-center flex-1">
                        <p class="text-[10px] font-bold text-indigo-400 mb-0.5">٦ شهور — ر.س</p>
                        <p class="text-lg font-black text-indigo-700">{{ $sar6m ? number_format($sar6m->price, 0) : '—' }}</p>
                    </div>
                </div>

                {{-- Other-currency price badges (3m only for brevity) --}}
                @php
                    $otherCurrencyPrices = $plan->prices
                        ->whereNotIn('currency', ['SAR'])
                        ->where('duration_months', 3);
                @endphp
                @if($otherCurrencyPrices->isNotEmpty())
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @foreach($otherCurrencyPrices as $pp)
                    @php $ppDec = ['TND' => 3][$pp->currency] ?? 0; @endphp
                    <span class="text-[11px] font-bold bg-slate-50 border border-slate-200 text-slate-600 px-2 py-0.5 rounded-lg">
                        {{ $pp->currency }} ٣ش: {{ number_format($pp->price, $ppDec) }}
                    </span>
                    @endforeach
                </div>
                @endif

                {{-- Meta --}}
                <div class="flex items-center gap-3 text-xs text-slate-500 font-semibold mb-4">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-rounded" style="font-size:14px">check_circle</span>
                        {{ $plan->features->count() }} ميزة
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-rounded" style="font-size:14px">group</span>
                        {{ $plan->subscriptions_count }} اشتراك
                    </span>
                </div>

                {{-- Features preview --}}
                @if($plan->features->isNotEmpty())
                <div class="border-t border-slate-100 pt-3 mb-4 space-y-1">
                    @foreach($plan->features->take(4) as $feature)
                    <div class="flex items-center gap-2 text-xs text-slate-600 font-semibold">
                        <span class="material-symbols-rounded text-green-500" style="font-size:13px;font-variation-settings:'FILL' 1">check_circle</span>
                        {{ $feature->name }}
                    </div>
                    @endforeach
                    @if($plan->features->count() > 4)
                    <p class="text-[11px] text-slate-400 font-semibold">+ {{ $plan->features->count() - 4 }} مزايا أخرى</p>
                    @endif
                </div>
                @endif

                {{-- Actions --}}
                <div class="flex items-center gap-2 border-t border-slate-100 pt-4">
                    <button onclick="openEditModal({{ $plan->id }})"
                        class="flex-1 flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2 rounded-xl transition-colors">
                        <span class="material-symbols-rounded" style="font-size:15px">edit</span>
                        تعديل
                    </button>

                    <form method="POST" action="{{ route('admin.plans.toggle', $plan) }}" class="flex-1">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-1.5 text-xs font-bold py-2 rounded-xl transition-colors
                                   {{ $plan->is_active ? 'bg-orange-50 hover:bg-orange-100 text-orange-600' : 'bg-green-50 hover:bg-green-100 text-green-600' }}">
                            <span class="material-symbols-rounded" style="font-size:15px">{{ $plan->is_active ? 'pause_circle' : 'play_circle' }}</span>
                            {{ $plan->is_active ? 'تعطيل' : 'تفعيل' }}
                        </button>
                    </form>

                    <button onclick="openDeleteModal({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->subscriptions_count }})"
                        class="flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 transition-colors flex-shrink-0">
                        <span class="material-symbols-rounded" style="font-size:16px">delete</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ═══════════ TAB: FEATURES ═══════════ --}}
<div id="tab-features" class="hidden">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- Add Feature Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-base font-black text-slate-800 mb-5 flex items-center gap-2">
                    <span class="material-symbols-rounded text-blue-500" style="font-size:20px">add_circle</span>
                    إضافة ميزة جديدة
                </h3>
                @if($errors->has('key') || $errors->has('name'))
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3">
                    @foreach(['key','name'] as $f)
                        @error($f)<p class="text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                    @endforeach
                </div>
                @endif
                <form method="POST" action="{{ route('admin.features.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="form-label">المعرف الفريد <span class="text-red-500">*</span></label>
                        <input type="text" name="key" value="{{ old('key') }}"
                               placeholder="مثال: ai_coaching"
                               class="form-input font-mono @error('key') is-error @enderror">
                        <p class="text-[11px] text-slate-400 mt-1 font-semibold">حروف إنجليزية وأرقام وشرطة سفلية فقط</p>
                    </div>
                    <div>
                        <label class="form-label">اسم الميزة <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="مثال: تدريب بالذكاء الاصطناعي"
                               class="form-input @error('name') is-error @enderror">
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                        إضافة الميزة
                    </button>
                </form>
            </div>
        </div>

        {{-- Features List --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h3 class="text-base font-black text-slate-800 mb-5 flex items-center gap-2">
                    <span class="material-symbols-rounded text-green-500" style="font-size:20px">checklist</span>
                    المميزات المتاحة ({{ $features->count() }})
                </h3>

                @if($features->isEmpty())
                <div class="text-center py-10">
                    <span class="material-symbols-rounded text-slate-300" style="font-size:48px">check_circle</span>
                    <p class="text-slate-400 font-bold text-sm mt-3">لا توجد مميزات بعد</p>
                </div>
                @else
                <div class="space-y-1 max-h-[480px] overflow-y-auto">
                    @foreach($features as $feature)
                    <div class="feature-row group">
                        <span class="material-symbols-rounded text-green-500 flex-shrink-0" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-700 truncate">{{ $feature->name }}</p>
                            <p class="text-[11px] text-slate-400 font-mono">{{ $feature->key }}</p>
                        </div>
                        <span class="text-[11px] text-slate-300 font-semibold hidden group-hover:inline">
                            {{ $feature->plans->count() }} باقة
                        </span>
                        <button onclick="openEditFeatureModal({{ $feature->id }}, '{{ addslashes($feature->name) }}')"
                            class="opacity-0 group-hover:opacity-100 w-7 h-7 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-500 hover:text-blue-600 flex items-center justify-center transition-all">
                            <span class="material-symbols-rounded" style="font-size:14px">edit</span>
                        </button>
                        <button onclick="openDeleteFeatureModal({{ $feature->id }}, '{{ addslashes($feature->name) }}')"
                            class="opacity-0 group-hover:opacity-100 w-7 h-7 rounded-lg bg-slate-100 hover:bg-red-100 text-slate-500 hover:text-red-600 flex items-center justify-center transition-all">
                            <span class="material-symbols-rounded" style="font-size:14px">delete</span>
                        </button>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════ --}}
{{-- MODAL: Create Plan                  --}}
{{-- ═══════════════════════════════════ --}}
<div id="createModal" class="modal-overlay" onclick="if(event.target===this)closeModal('createModal')">
    <div class="modal-box">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-base font-black text-slate-800">إضافة باقة جديدة</h3>
            <button onclick="closeModal('createModal')" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.plans.store') }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">اسم الباقة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" placeholder="مثال: الذهبية" class="form-input">
                </div>
                <div>
                    <label class="form-label">المعرف الفريد <span class="text-red-500">*</span></label>
                    <input type="text" name="key" placeholder="مثال: gold" class="form-input font-mono">
                </div>
            </div>

            <div>
                <label class="form-label">وصف الباقة</label>
                <textarea name="desc" rows="2" placeholder="وصف مختصر للباقة..." class="form-input resize-none"></textarea>
            </div>

            {{-- Price Matrix: 4 currencies × 2 durations --}}
            <div>
                <p class="form-label flex items-center gap-1.5 mb-2">
                    <span class="material-symbols-rounded" style="font-size:15px">currency_exchange</span>
                    جدول الأسعار
                    <span class="text-slate-400 font-semibold text-[11px]">— الريال السعودي مطلوب، باقي العملات اختيارية (تُعرض سعر الريال عند الترك فارغة)</span>
                </p>
                <div class="space-y-2">
                    @foreach(['SAR' => ['label'=>'الريال السعودي','sym'=>'ر.س','step'=>'1','req'=>true], 'EGP' => ['label'=>'الجنيه المصري','sym'=>'ج.م','step'=>'1','req'=>false], 'TND' => ['label'=>'الدينار التونسي','sym'=>'د.ت','step'=>'0.001','req'=>false], 'USD' => ['label'=>'الدولار الأمريكي','sym'=>'$','step'=>'0.01','req'=>false]] as $cur => $info)
                    <div class="rounded-xl border {{ $info['req'] ? 'border-blue-200 bg-blue-50' : 'border-slate-200 bg-slate-50' }} p-3">
                        <p class="text-[11px] font-black text-slate-600 mb-2">
                            {{ $info['label'] }} ({{ $cur }})
                            @if($info['req'])<span class="text-red-500 mr-1">*</span>@endif
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label text-[11px]">٣ شهور ({{ $info['sym'] }})</label>
                                <input type="number" name="prices[{{ $cur }}][3]" min="0" step="{{ $info['step'] }}"
                                       placeholder="{{ $info['req'] ? 'مطلوب' : 'اختياري' }}"
                                       {{ $info['req'] ? 'required' : '' }}
                                       class="form-input text-sm">
                            </div>
                            <div>
                                <label class="form-label text-[11px]">٦ شهور ({{ $info['sym'] }})</label>
                                <input type="number" name="prices[{{ $cur }}][6]" min="0" step="{{ $info['step'] }}"
                                       placeholder="{{ $info['req'] ? 'مطلوب' : 'اختياري' }}"
                                       {{ $info['req'] ? 'required' : '' }}
                                       class="form-input text-sm">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Style Variant --}}
            <div>
                <label class="form-label">شكل زر الاشتراك <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-2 mt-1">
                    @foreach(['outline' => ['label'=>'إطار', 'cls'=>'border-2 border-blue-600 text-blue-600'], 'solid' => ['label'=>'ممتلئ', 'cls'=>'bg-blue-600 text-white'], 'accent' => ['label'=>'مميز', 'cls'=>'bg-yellow-400 text-gray-900']] as $val => $meta)
                    <label class="flex flex-col items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="style_variant" value="{{ $val }}" class="sr-only peer" {{ $val === 'outline' ? 'checked' : '' }}>
                        <div class="w-full py-2 rounded-xl text-xs font-black text-center border-2 border-transparent peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-200 transition-all {{ $meta['cls'] }}">
                            {{ $meta['label'] }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="form-label">أيقونة Material</label>
                    <input type="text" name="icon" placeholder="fitness_center" class="form-input font-mono">
                </div>
                <div>
                    <label class="form-label">خلفية الأيقونة <span class="text-slate-400 font-normal text-[11px]">(Tailwind)</span></label>
                    <input type="text" name="icon_bg" placeholder="bg-blue-50" class="form-input font-mono">
                </div>
                <div>
                    <label class="form-label">لون الأيقونة <span class="text-slate-400 font-normal text-[11px]">(Tailwind)</span></label>
                    <input type="text" name="icon_color" placeholder="text-primary" class="form-input font-mono">
                </div>
            </div>

            <div>
                <label class="form-label">ترتيب العرض</label>
                <input type="number" name="sort_order" min="0" value="{{ $plans->count() }}" class="form-input w-32">
            </div>

            {{-- Features Checkboxes --}}
            @if($features->isNotEmpty())
            <div>
                <label class="form-label mb-2">المميزات المشمولة</label>
                <div class="border border-slate-200 rounded-xl p-3 max-h-48 overflow-y-auto space-y-1.5">
                    @foreach($features as $feature)
                    <label class="flex items-center gap-2.5 cursor-pointer hover:bg-slate-50 rounded-lg px-2 py-1">
                        <input type="checkbox" name="features[{{ $feature->id }}]" value="1"
                               class="w-4 h-4 rounded accent-blue-600">
                        <span class="text-sm font-semibold text-slate-700">{{ $feature->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Toggles --}}
            <div class="flex items-center gap-6 pt-1">
                <label class="toggle-switch">
                    <input type="checkbox" name="popular" value="1">
                    <div class="toggle-track"><div class="toggle-thumb"></div></div>
                    <span class="text-sm font-bold text-slate-600">الأكثر شعبية</span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <div class="toggle-track"><div class="toggle-thumb"></div></div>
                    <span class="text-sm font-bold text-slate-600">نشطة</span>
                </label>
            </div>
            <p class="text-[11px] text-slate-400 font-semibold mt-1" dir="rtl">
                يُستخدم هذا الاختيار فقط احتياطيًا عندما تكون المبيعات أقل من {{ $minCount }} اشتراكات — بعدها تُحدَّد الشارة تلقائيًا حسب الأكثر مبيعًا.
            </p>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('createModal')"
                    class="flex-1 border border-slate-200 text-slate-600 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">
                    إلغاء
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                    إنشاء الباقة
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════ --}}
{{-- MODAL: Edit Plan                    --}}
{{-- ═══════════════════════════════════ --}}
<div id="editModal" class="modal-overlay" onclick="if(event.target===this)closeModal('editModal')">
    <div class="modal-box">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-base font-black text-slate-800">تعديل الباقة</h3>
            <button onclick="closeModal('editModal')" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">اسم الباقة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="editName" class="form-input">
                </div>
                <div>
                    <label class="form-label">المعرف الفريد</label>
                    <input type="text" id="editKey" class="form-input font-mono bg-slate-50" disabled>
                </div>
            </div>
            <div>
                <label class="form-label">وصف الباقة</label>
                <textarea name="desc" id="editDesc" rows="2" class="form-input resize-none"></textarea>
            </div>
            {{-- Price Matrix --}}
            <div>
                <p class="form-label flex items-center gap-1.5 mb-2">
                    <span class="material-symbols-rounded" style="font-size:15px">currency_exchange</span>
                    جدول الأسعار
                    <span class="text-slate-400 font-semibold text-[11px]">— الريال مطلوب، باقي العملات اختيارية</span>
                </p>
                <div class="space-y-2">
                    @foreach(['SAR' => ['label'=>'الريال السعودي','sym'=>'ر.س','step'=>'1','req'=>true], 'EGP' => ['label'=>'الجنيه المصري','sym'=>'ج.م','step'=>'1','req'=>false], 'TND' => ['label'=>'الدينار التونسي','sym'=>'د.ت','step'=>'0.001','req'=>false], 'USD' => ['label'=>'الدولار الأمريكي','sym'=>'$','step'=>'0.01','req'=>false]] as $cur => $info)
                    <div class="rounded-xl border {{ $info['req'] ? 'border-blue-200 bg-blue-50' : 'border-slate-200 bg-slate-50' }} p-3">
                        <p class="text-[11px] font-black text-slate-600 mb-2">
                            {{ $info['label'] }} ({{ $cur }})
                            @if($info['req'])<span class="text-red-500 mr-1">*</span>@endif
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label text-[11px]">٣ شهور ({{ $info['sym'] }})</label>
                                <input type="number" name="prices[{{ $cur }}][3]" id="editP{{ $cur }}3"
                                       min="0" step="{{ $info['step'] }}"
                                       placeholder="{{ $info['req'] ? 'مطلوب' : 'اختياري' }}"
                                       {{ $info['req'] ? 'required' : '' }}
                                       class="form-input text-sm">
                            </div>
                            <div>
                                <label class="form-label text-[11px]">٦ شهور ({{ $info['sym'] }})</label>
                                <input type="number" name="prices[{{ $cur }}][6]" id="editP{{ $cur }}6"
                                       min="0" step="{{ $info['step'] }}"
                                       placeholder="{{ $info['req'] ? 'مطلوب' : 'اختياري' }}"
                                       {{ $info['req'] ? 'required' : '' }}
                                       class="form-input text-sm">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            {{-- Style Variant --}}
            <div>
                <label class="form-label">شكل زر الاشتراك <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-2 mt-1" id="editVariantGroup">
                    @foreach(['outline' => ['label'=>'إطار', 'cls'=>'border-2 border-blue-600 text-blue-600'], 'solid' => ['label'=>'ممتلئ', 'cls'=>'bg-blue-600 text-white'], 'accent' => ['label'=>'مميز', 'cls'=>'bg-yellow-400 text-gray-900']] as $val => $meta)
                    <label class="flex flex-col items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="style_variant" value="{{ $val }}" class="sr-only peer edit-variant-radio" data-val="{{ $val }}">
                        <div class="w-full py-2 rounded-xl text-xs font-black text-center border-2 border-transparent peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-200 transition-all {{ $meta['cls'] }}">
                            {{ $meta['label'] }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="form-label">أيقونة Material</label>
                    <input type="text" name="icon" id="editIcon" class="form-input font-mono">
                </div>
                <div>
                    <label class="form-label">خلفية الأيقونة <span class="text-slate-400 font-normal text-[11px]">(Tailwind)</span></label>
                    <input type="text" name="icon_bg" id="editIconBg" class="form-input font-mono">
                </div>
                <div>
                    <label class="form-label">لون الأيقونة <span class="text-slate-400 font-normal text-[11px]">(Tailwind)</span></label>
                    <input type="text" name="icon_color" id="editIconColor" class="form-input font-mono">
                </div>
            </div>
            <div>
                <label class="form-label">ترتيب العرض</label>
                <input type="number" name="sort_order" id="editSortOrder" min="0" class="form-input w-32">
            </div>

            {{-- Features Checkboxes --}}
            @if($features->isNotEmpty())
            <div>
                <label class="form-label mb-2">المميزات المشمولة</label>
                <div class="border border-slate-200 rounded-xl p-3 max-h-48 overflow-y-auto space-y-1.5" id="editFeaturesWrap">
                    @foreach($features as $feature)
                    <label class="flex items-center gap-2.5 cursor-pointer hover:bg-slate-50 rounded-lg px-2 py-1">
                        <input type="checkbox" name="features[{{ $feature->id }}]" value="1"
                               class="w-4 h-4 rounded accent-blue-600 edit-feature-cb"
                               data-id="{{ $feature->id }}">
                        <span class="text-sm font-semibold text-slate-700">{{ $feature->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex items-center gap-6 pt-1">
                <label class="toggle-switch">
                    <input type="checkbox" name="popular" id="editPopular" value="1">
                    <div class="toggle-track"><div class="toggle-thumb"></div></div>
                    <span class="text-sm font-bold text-slate-600">الأكثر شعبية</span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_active" id="editIsActive" value="1">
                    <div class="toggle-track"><div class="toggle-thumb"></div></div>
                    <span class="text-sm font-bold text-slate-600">نشطة</span>
                </label>
            </div>
            <p class="text-[11px] text-slate-400 font-semibold mt-1" dir="rtl">
                يُستخدم هذا الاختيار فقط احتياطيًا عندما تكون المبيعات أقل من {{ $minCount }} اشتراكات — بعدها تُحدَّد الشارة تلقائيًا حسب الأكثر مبيعًا.
            </p>

            <div class="flex gap-3 pt-2">
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

{{-- ═══════════════════════════════════ --}}
{{-- MODAL: Delete Plan                  --}}
{{-- ═══════════════════════════════════ --}}
<div id="deleteModal" class="modal-overlay" onclick="if(event.target===this)closeModal('deleteModal')">
    <div class="modal-box modal-sm">
        <div class="p-6">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-red-500" style="font-size:28px;font-variation-settings:'FILL' 1">delete_forever</span>
                </div>
            </div>
            <h3 class="text-base font-black text-slate-800 text-center mb-1">حذف الباقة</h3>
            <p class="text-sm text-slate-500 font-semibold text-center mb-1">هل أنت متأكد من حذف باقة <strong id="deletePlanName" class="text-slate-700"></strong>؟</p>
            <p id="deleteWarnMsg" class="text-xs text-red-500 font-bold text-center mb-5 hidden">تحذير: هذه الباقة لها اشتراكات مرتبطة!</p>
            <form id="deleteForm" method="POST">
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

{{-- ═══════════════════════════════════ --}}
{{-- MODAL: Edit Feature                 --}}
{{-- ═══════════════════════════════════ --}}
<div id="editFeatureModal" class="modal-overlay" onclick="if(event.target===this)closeModal('editFeatureModal')">
    <div class="modal-box modal-sm">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-base font-black text-slate-800">تعديل الميزة</h3>
            <button onclick="closeModal('editFeatureModal')" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form id="editFeatureForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="form-label">اسم الميزة <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="editFeatureName" class="form-input">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('editFeatureModal')"
                    class="flex-1 border border-slate-200 text-slate-600 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">
                    إلغاء
                </button>
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════ --}}
{{-- MODAL: Delete Feature               --}}
{{-- ═══════════════════════════════════ --}}
<div id="deleteFeatureModal" class="modal-overlay" onclick="if(event.target===this)closeModal('deleteFeatureModal')">
    <div class="modal-box modal-sm">
        <div class="p-6">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-rounded text-red-500" style="font-size:28px;font-variation-settings:'FILL' 1">remove_circle</span>
                </div>
            </div>
            <h3 class="text-base font-black text-slate-800 text-center mb-1">حذف الميزة</h3>
            <p class="text-sm text-slate-500 font-semibold text-center mb-5">هل أنت متأكد من حذف ميزة <strong id="deleteFeatureName" class="text-slate-700"></strong>؟</p>
            <form id="deleteFeatureForm" method="POST">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('deleteFeatureModal')"
                        class="flex-1 border border-slate-200 text-slate-600 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                        حذف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
// Plans data for edit modal
const plansData = @json($plansJson);

// ── Tab Switching ──────────────────────────────
function switchTab(tab, btn) {
    document.querySelectorAll('[id^="tab-"]').forEach(t => t.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.remove('hidden');
    btn.classList.add('active');
}

// ── Modal Helpers ──────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

document.addEventListener('keydown', e => { if(e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open')); });

// ── Create Plan ────────────────────────────────
function openCreateModal() { openModal('createModal'); }

// ── Edit Plan ──────────────────────────────────
function openEditModal(id) {
    const p = plansData[id];
    if (!p) return;

    document.getElementById('editForm').action = `/admin/plans/${id}`;
    document.getElementById('editName').value       = p.name;
    document.getElementById('editKey').value        = p.key;
    document.getElementById('editDesc').value       = p.desc || '';
    document.getElementById('editIcon').value       = p.icon || '';
    document.getElementById('editIconBg').value     = p.icon_bg || '';
    document.getElementById('editIconColor').value  = p.icon_color || '';
    document.getElementById('editSortOrder').value  = p.sort_order;
    document.getElementById('editPopular').checked  = p.popular;
    document.getElementById('editIsActive').checked = p.is_active;

    // Style variant radio
    const variant = p.style_variant || 'outline';
    document.querySelectorAll('.edit-variant-radio').forEach(r => {
        r.checked = r.dataset.val === variant;
    });

    // Feature checkboxes
    document.querySelectorAll('.edit-feature-cb').forEach(cb => {
        cb.checked = p.feature_ids.includes(parseInt(cb.dataset.id));
    });

    // Price matrix — keys are "CURRENCY_DURATION" e.g. "SAR_3", "EGP_6"
    const prices = p.prices || {};
    ['SAR', 'EGP', 'TND', 'USD'].forEach(cur => {
        [3, 6].forEach(dur => {
            const el = document.getElementById(`editP${cur}${dur}`);
            if (el) el.value = prices[`${cur}_${dur}`] != null ? prices[`${cur}_${dur}`] : '';
        });
    });

    openModal('editModal');
}

// ── Delete Plan ────────────────────────────────
function openDeleteModal(id, name, subsCount) {
    document.getElementById('deletePlanName').textContent = name;
    document.getElementById('deleteForm').action = `/admin/plans/${id}`;
    const warn = document.getElementById('deleteWarnMsg');
    warn.classList.toggle('hidden', subsCount === 0);
    openModal('deleteModal');
}

// ── Edit Feature ───────────────────────────────
function openEditFeatureModal(id, name) {
    document.getElementById('editFeatureName').value = name;
    document.getElementById('editFeatureForm').action = `/admin/features/${id}`;
    openModal('editFeatureModal');
}

// ── Delete Feature ─────────────────────────────
function openDeleteFeatureModal(id, name) {
    document.getElementById('deleteFeatureName').textContent = name;
    document.getElementById('deleteFeatureForm').action = `/admin/features/${id}`;
    openModal('deleteFeatureModal');
}

// Auto-open features tab if validation error came from feature form
@if(old('key') || old('name'))
    switchTab('features', document.querySelectorAll('.tab-btn')[1]);
@endif
</script>
@endsection

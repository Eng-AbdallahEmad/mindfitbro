@extends('layouts.admin.app')

@section('title', 'الإعدادات')
@section('page-title', 'الإعدادات')
@section('page-subtitle', 'إدارة إعدادات الموقع والمحتوى')

@section('style')
<style>
    .settings-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .settings-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: .75rem;
        background: #fafbfc;
    }
    .tab-nav-item {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .7rem 1.1rem;
        border-radius: 12px;
        font-size: .85rem;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: background .15s, color .15s;
        border: none;
        background: transparent;
        width: 100%;
        text-align: right;
    }
    .tab-nav-item:hover { background: #f1f5f9; color: #1e293b; }
    .tab-nav-item.active { background: #eff6ff; color: #2563eb; }
    .tab-nav-item.active .tab-icon { color: #3b82f6; }
    .tab-icon { font-size: 18px; flex-shrink: 0; }

    .form-label { font-size: .8rem; font-weight: 700; color: #374151; margin-bottom: .4rem; display: block; }
    .form-hint  { font-size: .72rem; color: #94a3b8; font-weight: 600; margin-top: .3rem; }
    .form-input {
        width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px;
        padding: .65rem .9rem; font-size: .875rem; color: #1e293b;
        transition: border-color .2s, box-shadow .2s; outline: none;
        font-family: 'Cairo', sans-serif;
    }
    .form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
    .section-divider {
        font-size: .7rem; font-weight: 900; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .1em;
        padding: .5rem 0 .75rem;
        border-bottom: 1px dashed #e2e8f0;
        margin-bottom: 1.25rem;
    }
</style>
@endsection

@php
    // Build flat key→value map from grouped collection
    $s = $grouped->flatten()->pluck('value', 'key');
@endphp

@section('content')

{{-- Flash --}}
@if(session('success'))
<div class="mb-5 rounded-xl bg-green-50 border border-green-200 p-4 flex items-center gap-3">
    <span class="material-symbols-rounded text-green-500" style="font-size:20px">check_circle</span>
    <p class="text-sm font-bold text-green-700">{{ session('success') }}</p>
</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" novalidate>
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">

        {{-- ── Sidebar Nav ── --}}
        <div class="lg:col-span-1">
            <div class="settings-card p-3 space-y-1 sticky top-20">
                @php
                    $tabs = [
                        'general'      => ['icon' => 'tune',          'label' => 'الإعدادات العامة'],
                        'social'       => ['icon' => 'share',          'label' => 'السوشيال ميديا'],
                        'stats'        => ['icon' => 'bar_chart',      'label' => 'الإحصائيات'],
                        'videos'       => ['icon' => 'play_circle',    'label' => 'الفيديوهات'],
                        'testimonials' => ['icon' => 'face',           'label' => 'صور المراجعين'],
                        'before-after' => ['icon' => 'compare',        'label' => 'قبل وبعد'],
                        'family-reward'=> ['icon' => 'card_giftcard',  'label' => 'جائزة الأبطال'],
                        'booking'      => ['icon' => 'event_available','label' => 'مواعيد الحجز'],
                        'marquee'      => ['icon' => 'campaign',        'label' => 'الشريط الإعلاني'],
                        'sections'     => ['icon' => 'view_agenda',     'label' => 'أقسام الصفحة'],
                    ];
                @endphp
                @foreach($tabs as $key => $tab)
                <button type="button" class="tab-nav-item {{ $loop->first ? 'active' : '' }}" data-tab="{{ $key }}" onclick="switchTab('{{ $key }}', this)">
                    <span class="material-symbols-rounded tab-icon" style="font-variation-settings:'FILL' 1">{{ $tab['icon'] }}</span>
                    {{ $tab['label'] }}
                </button>
                @endforeach

                <div class="pt-3 border-t border-slate-100 mt-1">
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                        <span class="material-symbols-rounded" style="font-size:18px">save</span>
                        حفظ الإعدادات
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Panels ── --}}
        <div class="lg:col-span-3 space-y-0">

            {{-- ════ GENERAL ════ --}}
            <div id="tab-general" class="settings-card">
                <div class="settings-card-header">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-blue-500" style="font-size:18px;font-variation-settings:'FILL' 1">tune</span>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">الإعدادات العامة</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">معلومات التواصل والموقع</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <p class="section-divider">معلومات الموقع</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="form-label">اسم الموقع</label>
                            <input type="text" name="settings[site_name]"
                                   value="{{ $s->get('site_name', 'MindFitBro') }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">الموقع / المدينة</label>
                            <input type="text" name="settings[location]"
                                   value="{{ $s->get('location') }}" class="form-input">
                        </div>
                    </div>

                    <p class="section-divider">التواصل</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="form-label">رقم الهاتف (للعرض)</label>
                            <input type="text" name="settings[contact_phone]" dir="ltr"
                                   value="{{ $s->get('contact_phone') }}" class="form-input"
                                   placeholder="+966593035979">
                            <p class="form-hint">يظهر في قسم التواصل</p>
                        </div>
                        <div>
                            <label class="form-label">رقم الهاتف (منسَّق)</label>
                            <input type="text" name="settings[contact_phone_display]" dir="ltr"
                                   value="{{ $s->get('contact_phone_display') }}" class="form-input"
                                   placeholder="+966 593 035 979">
                            <p class="form-hint">يظهر في بطاقة المعلومات مع مسافات</p>
                        </div>
                        <div>
                            <label class="form-label">رقم واتساب (بدون +)</label>
                            <input type="text" name="settings[whatsapp_number]" dir="ltr"
                                   value="{{ $s->get('whatsapp_number') }}" class="form-input"
                                   placeholder="966593035979">
                            <p class="form-hint">يُستخدم في رابط wa.me/...</p>
                        </div>
                        <div>
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="settings[contact_email]" dir="ltr"
                                   value="{{ $s->get('contact_email') }}" class="form-input"
                                   placeholder="info@mindfitbro.com">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════ SOCIAL ════ --}}
            <div id="tab-social" class="settings-card hidden">
                <div class="settings-card-header">
                    <span class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-purple-500" style="font-size:18px;font-variation-settings:'FILL' 1">share</span>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">السوشيال ميديا</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">روابط حسابات التواصل الاجتماعي</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center flex-shrink-0">
                            <svg viewBox="0 0 20 20" width="18" height="18" fill="white" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 2c2.138 0 2.389.009 3.232.047 2.1.098 3.074 1.074 3.172 3.172.038.841.047 1.093.047 3.231 0 2.14-.009 2.39-.047 3.232-.098 2.1-1.07 3.074-3.172 3.172-.843.038-1.093.047-3.232.047-2.138 0-2.39-.009-3.232-.047-2.103-.098-3.074-1.073-3.172-3.172C2.558 12.39 2.55 12.14 2.55 10c0-2.138.009-2.39.047-3.231.098-2.1 1.072-3.074 3.172-3.172C7.611 2.008 7.862 2 10 2zm0-1.8C7.825.2 7.555.21 6.703.248 3.9.38 1.88 2.397 1.748 5.2 1.71 6.055 1.7 6.325 1.7 10s.01 3.944.048 4.8c.132 2.8 2.148 4.819 4.955 4.952.853.038 1.122.048 3.297.048s2.444-.01 3.298-.048c2.803-.133 4.82-2.15 4.954-4.952.038-.856.048-1.125.048-4.8s-.01-3.944-.048-4.8C17.12 2.4 15.104.38 12.298.248 11.445.21 11.175.2 10 .2zm0 3.06a6.74 6.74 0 1 0 0 13.48 6.74 6.74 0 0 0 0-13.48zm0 11.11a4.37 4.37 0 1 1 0-8.74 4.37 4.37 0 0 1 0 8.74zm6.965-11.4a1.575 1.575 0 1 1-3.15 0 1.575 1.575 0 0 1 3.15 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <label class="form-label mb-1">Instagram</label>
                            <input type="text" name="settings[instagram_url]" dir="ltr"
                                   value="{{ $s->get('instagram_url', '') }}" class="form-input"
                                   placeholder="https://instagram.com/mindfitbro">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-black flex items-center justify-center flex-shrink-0">
                            <svg width="18" height="18" fill="white" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.656 1.029c1.637-0.025 3.262-0.012 4.886-0.025 0.054 2.031 0.878 3.859 2.189 5.213l-0.002-0.002c1.411 1.271 3.247 2.095 5.271 2.235l0.028 0.002v5.036c-1.912-0.048-3.71-0.489-5.331-1.247l0.082 0.034c-0.784-0.377-1.447-0.764-2.077-1.196l0.052 0.034c-0.012 3.649 0.012 7.298-0.025 10.934-0.103 1.853-0.719 3.543-1.707 4.954l0.020-0.031c-1.652 2.366-4.328 3.919-7.371 4.011l-0.014 0c-0.123 0.006-0.268 0.009-0.414 0.009-1.73 0-3.347-0.482-4.725-1.319l0.040 0.023c-2.508-1.509-4.238-4.091-4.558-7.094l-0.004-0.041c-0.025-0.625-0.037-1.25-0.012-1.862 0.49-4.779 4.494-8.476 9.361-8.476 0.547 0 1.083 0.047 1.604 0.136l-0.056-0.008c0.025 1.849-0.050 3.699-0.050 5.548-0.423-0.153-0.911-0.242-1.42-0.242-1.868 0-3.457 1.194-4.045 2.861l-0.009 0.030c-0.133 0.427-0.21 0.918-0.21 1.426 0 0.206 0.013 0.41 0.037 0.61l-0.002-0.024c0.332 2.046 2.086 3.59 4.201 3.59 0.061 0 0.121-0.001 0.181-0.004l-0.009 0c1.463-0.044 2.733-0.831 3.451-1.994l0.010-0.018c0.267-0.372 0.45-0.822 0.511-1.311l0.001-0.014c0.125-2.237 0.075-4.461 0.087-6.698 0.012-5.036-0.012-10.060 0.025-15.083z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <label class="form-label mb-1">TikTok</label>
                            <input type="text" name="settings[tiktok_url]" dir="ltr"
                                   value="{{ $s->get('tiktok_url', '') }}" class="form-input"
                                   placeholder="https://tiktok.com/@mindfitbro">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center flex-shrink-0">
                            <svg width="18" height="18" fill="white" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.932 20.459v-8.917l7.839 4.459zM30.368 8.735c-0.354-1.301-1.354-2.307-2.625-2.663l-0.027-0.006c-3.193-0.406-6.886-0.638-10.634-0.638-0.381 0-0.761 0.002-1.14 0.007l0.058-0.001c-0.322-0.004-0.701-0.007-1.082-0.007-3.748 0-7.443 0.232-11.070 0.681l0.434-0.044c-1.297 0.363-2.297 1.368-2.644 2.643l-0.006 0.026c-0.4 2.109-0.628 4.536-0.628 7.016 0 0.088 0 0.176 0.001 0.263l-0-0.014c-0 0.074-0.001 0.162-0.001 0.25 0 2.48 0.229 4.906 0.666 7.259l-0.038-0.244c0.354 1.301 1.354 2.307 2.625 2.663l0.027 0.006c3.193 0.406 6.886 0.638 10.634 0.638 0.38 0 0.76-0.002 1.14-0.007l-0.058 0.001c0.322 0.004 0.702 0.007 1.082 0.007 3.749 0 7.443-0.232 11.070-0.681l-0.434 0.044c1.298-0.362 2.298-1.368 2.646-2.643l0.006-0.026c0.399-2.109 0.627-4.536 0.627-7.015 0-0.088-0-0.176-0.001-0.263l0 0.013c0-0.074 0.001-0.162 0.001-0.25 0-2.48-0.229-4.906-0.666-7.259l0.038 0.244z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <label class="form-label mb-1">YouTube</label>
                            <input type="text" name="settings[youtube_url]" dir="ltr"
                                   value="{{ $s->get('youtube_url', '') }}" class="form-input"
                                   placeholder="https://youtube.com/@mindfitbro">
                        </div>
                    </div>

                </div>
            </div>

            {{-- ════ STATS ════ --}}
            <div id="tab-stats" class="settings-card hidden">
                <div class="settings-card-header">
                    <span class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-green-500" style="font-size:18px;font-variation-settings:'FILL' 1">bar_chart</span>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">الإحصائيات</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">الأرقام التي تظهر في الصفحة الرئيسية</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    <p class="section-divider">قسم Hero + لماذا نحن</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="form-label">عدد النجاحات (Hero)</label>
                            <input type="text" name="settings[hero_success_count]"
                                   value="{{ $s->get('hero_success_count', '500') }}" class="form-input">
                            <p class="form-hint">يظهر في أعلى الصفحة</p>
                        </div>
                        <div>
                            <label class="form-label">بطاقة 1 (عمليات)</label>
                            <input type="text" name="settings[whyus_card1_count]"
                                   value="{{ $s->get('whyus_card1_count', '+2,500') }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">بطاقة 2 (عملاء)</label>
                            <input type="text" name="settings[whyus_card2_count]"
                                   value="{{ $s->get('whyus_card2_count', '+20,000') }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">بطاقة 3 (ساعات)</label>
                            <input type="text" name="settings[whyus_card3_count]"
                                   value="{{ $s->get('whyus_card3_count', '+10,000') }}" class="form-input">
                        </div>
                    </div>

                    <p class="section-divider">قسم الشهادات</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">عدد العملاء السعداء</label>
                            <input type="text" name="settings[testimonials_clients]"
                                   value="{{ $s->get('testimonials_clients', '500') }}" class="form-input">
                            <p class="form-hint">يُضاف + تلقائياً</p>
                        </div>
                        <div>
                            <label class="form-label">التقييم</label>
                            <input type="text" name="settings[testimonials_rating]"
                                   value="{{ $s->get('testimonials_rating', '5.0') }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">نسبة الرضا (%)</label>
                            <input type="text" name="settings[testimonials_satisfaction]"
                                   value="{{ $s->get('testimonials_satisfaction', '100') }}" class="form-input">
                            <p class="form-hint">بدون %</p>
                        </div>
                    </div>

                    <p class="section-divider">قسم الشركاء</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">مدربون معتمدون</label>
                            <input type="text" name="settings[partners_certified]"
                                   value="{{ $s->get('partners_certified', '20') }}" class="form-input">
                            <p class="form-hint">بدون +</p>
                        </div>
                        <div>
                            <label class="form-label">الدول</label>
                            <input type="text" name="settings[partners_countries]"
                                   value="{{ $s->get('partners_countries', '8') }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">سنوات الشراكة</label>
                            <input type="text" name="settings[partners_years]"
                                   value="{{ $s->get('partners_years', '3') }}" class="form-input">
                        </div>
                    </div>

                </div>
            </div>

            {{-- ════ VIDEOS ════ --}}
            <div id="tab-videos" class="settings-card hidden">
                <div class="settings-card-header">
                    <span class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-red-500" style="font-size:18px;font-variation-settings:'FILL' 1">play_circle</span>
                    </span>
                    <div class="flex-1">
                        <h3 class="text-sm font-black text-slate-800">الفيديوهات</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">فيديوهات قسم "لماذا نحن" — {{ $videos->count() }} فيديو</p>
                    </div>
                    <button type="button" onclick="openVideoModal()"
                        class="flex items-center gap-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors">
                        <span class="material-symbols-rounded" style="font-size:15px">add</span>
                        إضافة فيديو
                    </button>
                </div>

                {{-- Videos List --}}
                <div class="p-5 space-y-3">
                    @if($videos->isEmpty())
                    <div class="text-center py-10 rounded-xl bg-slate-50 border border-dashed border-slate-200">
                        <span class="material-symbols-rounded text-slate-300" style="font-size:48px">videocam_off</span>
                        <p class="text-slate-400 font-bold text-sm mt-2">لا توجد فيديوهات بعد</p>
                        <button type="button" onclick="openVideoModal()" class="mt-3 text-red-500 font-bold text-xs hover:underline">
                            أضف أول فيديو
                        </button>
                    </div>
                    @else
                    @foreach($videos as $video)
                    <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50 group hover:border-slate-200 transition-colors">

                        {{-- Thumbnail --}}
                        <div class="w-20 h-14 rounded-lg overflow-hidden bg-slate-200 flex-shrink-0 relative">
                            @if($video->thumbnail_url)
                                <img src="{{ $video->thumbnail_src }}" alt="{{ $video->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="material-symbols-rounded text-slate-400" style="font-size:24px">videocam</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="material-symbols-rounded text-white" style="font-size:20px;font-variation-settings:'FILL' 1">play_circle</span>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $video->title }}</p>
                            <p class="text-[11px] text-slate-400 font-mono truncate mt-0.5">{{ $video->video_url }}</p>
                        </div>

                        {{-- Order Badge --}}
                        <span class="text-xs font-black text-slate-300 bg-slate-100 w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0">
                            {{ $video->sort_order }}
                        </span>

                        {{-- Status Toggle --}}
                        <button type="button"
                            onclick="toggleVideo({{ $video->id }})"
                            title="{{ $video->is_active ? 'إخفاء' : 'إظهار' }}"
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0
                                   {{ $video->is_active ? 'bg-green-50 text-green-500 hover:bg-green-100' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                            <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">
                                {{ $video->is_active ? 'visibility' : 'visibility_off' }}
                            </span>
                        </button>

                        {{-- Edit --}}
                        <button type="button"
                            onclick="openEditVideoModal({{ $video->id }}, '{{ addslashes($video->title) }}', '{{ addslashes($video->video_url) }}', '{{ addslashes($video->thumbnail_url ?? '') }}', {{ $video->sort_order }})"
                            class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-500 flex items-center justify-center transition-colors flex-shrink-0">
                            <span class="material-symbols-rounded" style="font-size:15px">edit</span>
                        </button>

                        {{-- Delete --}}
                        <button type="button"
                            onclick="openDeleteVideoModal({{ $video->id }}, '{{ addslashes($video->title) }}')"
                            class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors flex-shrink-0">
                            <span class="material-symbols-rounded" style="font-size:15px">delete</span>
                        </button>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- ════ TESTIMONIALS ════ --}}
            <div id="tab-testimonials" class="settings-card hidden">
                <div class="settings-card-header">
                    <span class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-purple-500" style="font-size:18px;font-variation-settings:'FILL' 1">face</span>
                    </span>
                    <div class="flex-1">
                        <h3 class="text-sm font-black text-slate-800">صور المراجعين</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">صور سلايدر قسم التقييمات — {{ $testimonials->count() }} صورة</p>
                    </div>
                    <button type="button" onclick="openTestimonialModal()"
                        class="flex items-center gap-1.5 bg-purple-500 hover:bg-purple-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors">
                        <span class="material-symbols-rounded" style="font-size:15px">add</span>
                        إضافة صورة
                    </button>
                </div>

                <div class="p-5 space-y-3">
                    @if($testimonials->isEmpty())
                    <div class="text-center py-10 rounded-xl bg-slate-50 border border-dashed border-slate-200">
                        <span class="material-symbols-rounded text-slate-300" style="font-size:48px">image_not_supported</span>
                        <p class="text-slate-400 font-bold text-sm mt-2">لا توجد صور بعد</p>
                        <button type="button" onclick="openTestimonialModal()" class="mt-3 text-purple-500 font-bold text-xs hover:underline">
                            أضف أول صورة
                        </button>
                    </div>
                    @else
                    @foreach($testimonials as $item)
                    <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50 group hover:border-slate-200 transition-colors">

                        {{-- Image Preview --}}
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-200 flex-shrink-0 relative">
                            <img src="{{ $item->image_src }}" alt="{{ $item->alt_text ?: '' }}" class="w-full h-full object-cover">
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $item->alt_text ?: '—' }}</p>
                            <p class="text-[10px] text-slate-400 font-mono truncate mt-0.5">{{ Str::limit($item->image_path, 40) }}</p>
                        </div>

                        {{-- Order Badge --}}
                        <span class="text-xs font-black text-slate-300 bg-slate-100 w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0">
                            {{ $item->sort_order }}
                        </span>

                        {{-- Toggle --}}
                        <button type="button"
                            onclick="toggleTestimonial({{ $item->id }})"
                            title="{{ $item->is_active ? 'إخفاء' : 'إظهار' }}"
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0
                                   {{ $item->is_active ? 'bg-green-50 text-green-500 hover:bg-green-100' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                            <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">
                                {{ $item->is_active ? 'visibility' : 'visibility_off' }}
                            </span>
                        </button>

                        {{-- Edit --}}
                        <button type="button"
                            onclick="openEditTestimonialModal({{ $item->id }}, '{{ addslashes($item->alt_text ?? '') }}', '{{ addslashes($item->image_path) }}', {{ $item->sort_order }})"
                            class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-500 flex items-center justify-center transition-colors flex-shrink-0">
                            <span class="material-symbols-rounded" style="font-size:15px">edit</span>
                        </button>

                        {{-- Delete --}}
                        <button type="button"
                            onclick="openDeleteTestimonialModal({{ $item->id }}, '{{ addslashes($item->alt_text ?? 'هذه الصورة') }}')"
                            class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors flex-shrink-0">
                            <span class="material-symbols-rounded" style="font-size:15px">delete</span>
                        </button>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- ════ BEFORE / AFTER ════ --}}
            <div id="tab-before-after" class="settings-card hidden">
                <div class="settings-card-header">
                    <span class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-teal-500" style="font-size:18px;font-variation-settings:'FILL' 1">compare</span>
                    </span>
                    <div class="flex-1">
                        <h3 class="text-sm font-black text-slate-800">قبل وبعد</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">صور قسم التحول — {{ $beforeAfters->count() }} صورة</p>
                    </div>
                    <button type="button" onclick="openBeforeAfterModal()"
                        class="flex items-center gap-1.5 bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors">
                        <span class="material-symbols-rounded" style="font-size:15px">add</span>
                        إضافة صورة
                    </button>
                </div>

                <div class="p-5 space-y-3">
                    @if($beforeAfters->isEmpty())
                    <div class="text-center py-10 rounded-xl bg-slate-50 border border-dashed border-slate-200">
                        <span class="material-symbols-rounded text-slate-300" style="font-size:48px">image_not_supported</span>
                        <p class="text-slate-400 font-bold text-sm mt-2">لا توجد صور بعد</p>
                        <button type="button" onclick="openBeforeAfterModal()" class="mt-3 text-teal-500 font-bold text-xs hover:underline">
                            أضف أول صورة
                        </button>
                    </div>
                    @else
                    @foreach($beforeAfters as $item)
                    <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50 group hover:border-slate-200 transition-colors">

                        {{-- Image Preview --}}
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-200 flex-shrink-0">
                            <img src="{{ $item->image_src }}" alt="{{ $item->alt_text ?: '' }}" class="w-full h-full object-cover object-top">
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $item->alt_text ?: '—' }}</p>
                            <p class="text-[10px] text-slate-400 font-mono truncate mt-0.5">{{ Str::limit($item->image_path, 40) }}</p>
                        </div>

                        {{-- Order Badge --}}
                        <span class="text-xs font-black text-slate-300 bg-slate-100 w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0">
                            {{ $item->sort_order }}
                        </span>

                        {{-- Toggle --}}
                        <button type="button"
                            onclick="toggleBeforeAfter({{ $item->id }})"
                            title="{{ $item->is_active ? 'إخفاء' : 'إظهار' }}"
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0
                                   {{ $item->is_active ? 'bg-green-50 text-green-500 hover:bg-green-100' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}">
                            <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">
                                {{ $item->is_active ? 'visibility' : 'visibility_off' }}
                            </span>
                        </button>

                        {{-- Edit --}}
                        <button type="button"
                            onclick="openEditBeforeAfterModal({{ $item->id }}, '{{ addslashes($item->alt_text ?? '') }}', '{{ addslashes($item->image_path) }}', {{ $item->sort_order }})"
                            class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-500 flex items-center justify-center transition-colors flex-shrink-0">
                            <span class="material-symbols-rounded" style="font-size:15px">edit</span>
                        </button>

                        {{-- Delete --}}
                        <button type="button"
                            onclick="openDeleteBeforeAfterModal({{ $item->id }}, '{{ addslashes($item->alt_text ?? 'هذه الصورة') }}')"
                            class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors flex-shrink-0">
                            <span class="material-symbols-rounded" style="font-size:15px">delete</span>
                        </button>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- ════ FAMILY REWARD ════ --}}
            <div id="tab-family-reward" class="settings-card hidden"
                 x-data="{ mode: '{{ $s->get('family_reward_discount_mode', 'fixed') }}' }">
                <div class="settings-card-header">
                    <span class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-yellow-500" style="font-size:18px;font-variation-settings:'FILL' 1">card_giftcard</span>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">جائزة الأبطال</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">دعوات الخصم لمشتركي الباقة المميزة</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Enable toggle --}}
                    <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <div>
                            <p class="text-sm font-black text-slate-800">تفعيل برنامج الدعوات</p>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">عند التفعيل تظهر قسم الدعوات في لوحة تحكم المشتركين</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="settings[family_reward_enabled]" value="0">
                            <input type="checkbox" name="settings[family_reward_enabled]" value="1"
                                   class="sr-only peer"
                                   {{ $s->get('family_reward_enabled', '0') === '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 rtl:peer-checked:after:-translate-x-full"></div>
                        </label>
                    </div>

                    {{-- Reward plan dropdown --}}
                    <div>
                        <label class="form-label">الباقة المميزة (Elite Plan)</label>
                        <select name="settings[family_reward_plan_id]" class="form-input">
                            <option value="">-- اختر الباقة --</option>
                            @foreach($plans as $plan)
                            <option value="{{ $plan->id }}"
                                {{ (string)$s->get('family_reward_plan_id', '') === (string)$plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                            @endforeach
                        </select>
                        <p class="form-hint">مشتركو هذه الباقة فقط يحصلون على صلاحية إرسال دعوات الخصم</p>
                    </div>

                    {{-- Max invites --}}
                    <div>
                        <label class="form-label">الحد الأقصى للدعوات لكل مشترك</label>
                        <input type="number" name="settings[family_reward_max_invites]"
                               value="{{ $s->get('family_reward_max_invites', '5') }}"
                               class="form-input" min="1" max="50" style="max-width:120px">
                        <p class="form-hint">عدد الدعوات التي يمكن لكل مشترك إرسالها طوال فترة اشتراكه</p>
                    </div>

                    {{-- Discount mode --}}
                    <div>
                        <label class="form-label">نوع الخصم</label>
                        <div class="flex items-center gap-4 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="settings[family_reward_discount_mode]" value="fixed"
                                       x-model="mode"
                                       {{ $s->get('family_reward_discount_mode', 'fixed') === 'fixed' ? 'checked' : '' }}
                                       class="text-blue-600">
                                <span class="text-sm font-bold text-slate-700">نسبة ثابتة</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="settings[family_reward_discount_mode]" value="range"
                                       x-model="mode"
                                       {{ $s->get('family_reward_discount_mode', 'fixed') === 'range' ? 'checked' : '' }}
                                       class="text-blue-600">
                                <span class="text-sm font-bold text-slate-700">نطاق عشوائي</span>
                            </label>
                        </div>
                    </div>

                    {{-- Fixed value --}}
                    <div x-show="mode === 'fixed'">
                        <label class="form-label">نسبة الخصم الثابتة (%)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="settings[family_reward_discount_value]"
                                   value="{{ $s->get('family_reward_discount_value', '20') }}"
                                   class="form-input" min="1" max="100" style="max-width:120px">
                            <span class="text-slate-500 font-bold text-sm">%</span>
                        </div>
                        <p class="form-hint">كل دعوة تُولّد كوبون بهذه النسبة بالضبط</p>
                    </div>

                    {{-- Range values --}}
                    <div x-show="mode === 'range'" style="display:none">
                        <label class="form-label">نطاق الخصم العشوائي (%)</label>
                        <div class="flex items-center gap-3">
                            <div>
                                <label class="form-label text-[11px]">من</label>
                                <input type="number" name="settings[family_reward_discount_min]"
                                       value="{{ $s->get('family_reward_discount_min', '10') }}"
                                       class="form-input" min="1" max="99" style="max-width:100px">
                            </div>
                            <span class="text-slate-400 font-bold mt-4">—</span>
                            <div>
                                <label class="form-label text-[11px]">إلى</label>
                                <input type="number" name="settings[family_reward_discount_max]"
                                       value="{{ $s->get('family_reward_discount_max', '30') }}"
                                       class="form-input" min="2" max="100" style="max-width:100px">
                            </div>
                            <span class="text-slate-500 font-bold text-sm mt-4">%</span>
                        </div>
                        <p class="form-hint">قيمة عشوائية تُسحب من هذا النطاق لكل دعوة على حدة</p>
                    </div>

                    {{-- Info box --}}
                    <div class="rounded-xl bg-blue-50 border border-blue-100 p-4 flex items-start gap-3">
                        <span class="material-symbols-rounded text-blue-500 flex-shrink-0" style="font-size:18px">info</span>
                        <p class="text-xs text-blue-700 font-semibold leading-relaxed">
                            كل دعوة تُولّد كود خصم بصيغة <strong>FAM-XXXX</strong> صالح 30 يوماً لاستخدام واحد.
                            يمكن متابعة جميع الدعوات من صفحة <a href="{{ route('admin.family-invitations.index') }}" class="underline font-black">سجل الدعوات</a>.
                        </p>
                    </div>

                </div>
            </div>

            {{-- ════ BOOKING ════ --}}
            <div id="tab-booking" class="settings-card hidden">
                <div class="settings-card-header">
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-emerald-500" style="font-size:18px;font-variation-settings:'FILL' 1">event_available</span>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">مواعيد الحجز</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">الأيام والأوقات المتاحة لحجز الجلسة الأولى</p>
                    </div>
                </div>
                <div class="p-6 space-y-6">

                    {{-- Hidden inputs (JS keeps them in sync) --}}
                    <input type="hidden" name="settings[booking_available_days]"
                           id="availableDaysInput"
                           value="{{ $s->get('booking_available_days', '0,1,2,3,4') }}">
                    <input type="hidden" name="settings[booking_time_slots]"
                           id="timeSlotsInput"
                           value="{{ $s->get('booking_time_slots', '09:00,10:00,11:00,12:00,14:00,15:00,16:00,17:00,18:00') }}">

                    {{-- ── Days ── --}}
                    <div>
                        <p class="section-divider">الأيام المتاحة للحجز</p>
                        <p class="text-[11px] text-slate-400 font-semibold mb-3">اضغط على اليوم لتفعيله أو إيقافه</p>
                        <div class="flex flex-wrap gap-2" id="dayToggles"></div>
                    </div>

                    {{-- ── Time Slots ── --}}
                    <div>
                        <p class="section-divider">الأوقات المتاحة للحجز</p>
                        <p class="text-[11px] text-slate-400 font-semibold mb-3">اضغط على الوقت لتفعيله أو إيقافه — الأوقات بتوقيت المنطقة الزمنية للموقع</p>
                        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2" id="timeToggles"></div>
                    </div>

                </div>
            </div>

            {{-- ════ MARQUEE ════ --}}
            <div id="tab-marquee" class="settings-card hidden">
                <div class="settings-card-header">
                    <span class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-orange-500" style="font-size:18px;font-variation-settings:'FILL' 1">campaign</span>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">الشريط الإعلاني</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">النصوص التي تظهر في الشريط المتحرك أسفل الهيرو</p>
                    </div>
                </div>
                <div class="p-6 space-y-6">

                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 flex items-start gap-3">
                        <span class="material-symbols-rounded text-amber-500 flex-shrink-0" style="font-size:18px;font-variation-settings:'FILL' 1">info</span>
                        <p class="text-xs font-bold text-amber-700">كل سطر = عنصر واحد في الشريط. ابدأ سطرًا جديدًا لإضافة عنصر.</p>
                    </div>

                    {{-- Arabic items --}}
                    <div>
                        <label class="form-label mb-1">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-5 h-5 rounded-md bg-green-100 flex items-center justify-center text-[10px] font-black text-green-700">ع</span>
                                العناصر بالعربية
                            </span>
                        </label>
                        <textarea name="settings[marquee_items_ar]" rows="7"
                                  class="form-input font-arabic resize-none leading-relaxed" dir="rtl"
                                  placeholder="اكتب كل عنصر في سطر منفصل...">{{ $s->get('marquee_items_ar', "خصومات إبريل 40%\nعروض العيد لسه مخلصتش\nجاهز للجاي...؟") }}</textarea>
                    </div>

                    {{-- English items --}}
                    <div>
                        <label class="form-label mb-1">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-5 h-5 rounded-md bg-blue-100 flex items-center justify-center text-[10px] font-black text-blue-700">EN</span>
                                العناصر بالإنجليزية
                            </span>
                        </label>
                        <textarea name="settings[marquee_items_en]" rows="7"
                                  class="form-input resize-none leading-relaxed" dir="ltr"
                                  placeholder="Write each item on a separate line...">{{ $s->get('marquee_items_en', "April Discounts 40%\nEid Offers Are Still Going!\nReady for What's Coming?") }}</textarea>
                    </div>

                </div>
            </div>

            {{-- ════ SECTIONS ════ --}}
            <div id="tab-sections" class="settings-card hidden">
                <div class="settings-card-header">
                    <span class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-rounded text-violet-500" style="font-size:18px;font-variation-settings:'FILL' 1">view_agenda</span>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">أقسام الصفحة الرئيسية</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">تحكم في إظهار أو إخفاء أقسام الصفحة الرئيسية</p>
                    </div>
                </div>
                <div class="p-6 space-y-3">

                    {{-- Partners --}}
                    @php $partnersVisible = $s->get('section_partners_visible', '1') === '1'; @endphp
                    <label class="flex items-center justify-between gap-4 p-4 rounded-xl border border-slate-100 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-rounded text-slate-500" style="font-size:18px;font-variation-settings:'FILL' 1">handshake</span>
                            </span>
                            <div>
                                <p class="text-sm font-black text-slate-800">قسم الشركاء</p>
                                <p class="text-[11px] text-slate-400 font-semibold">بنتشارك مع الأفضل — شعارات الشركاء والإحصائيات</p>
                            </div>
                        </div>
                        <div class="relative flex-shrink-0">
                            <input type="hidden" name="settings[section_partners_visible]" value="0">
                            <input type="checkbox" name="settings[section_partners_visible]" value="1"
                                   class="sr-only peer" {{ $partnersVisible ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-checked:bg-blue-600 rounded-full transition-colors duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                        </div>
                    </label>

                </div>
            </div>

        </div>
    </div>

    {{-- Floating Save (mobile) --}}
    <div class="fixed bottom-6 left-6 lg:hidden z-50">
        <button type="submit"
            class="flex items-center gap-2 bg-blue-600 text-white font-bold px-5 py-3 rounded-2xl shadow-xl text-sm">
            <span class="material-symbols-rounded" style="font-size:18px">save</span>
            حفظ
        </button>
    </div>

</form>

{{-- ══════════════ VIDEO MODALS (outside settings form) ══════════════ --}}

{{-- MODAL: Add Video --}}
<div id="videoModal" class="fixed inset-0 bg-black/60 z-[500] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh] transform translate-y-3 transition-transform duration-200" id="videoModalBox">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-red-500" style="font-size:17px;font-variation-settings:'FILL' 1">video_library</span>
            </div>
            <h3 class="text-sm font-black text-slate-800 flex-1">إضافة فيديو</h3>
            <button type="button" onclick="closeVideoModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors flex-shrink-0">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.videos.store') }}" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
            @csrf
            <div class="p-5 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label class="form-label">عنوان الفيديو <span class="text-red-400">*</span></label>
                    <input type="text" name="title" class="form-input" placeholder="مثال: تمرين الصدر مع الكوتش أحمد" required>
                </div>
                <div>
                    <label class="form-label">رابط الفيديو <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="url" name="video_url" class="form-input" style="direction:ltr;text-align:left;padding-right:2.5rem" placeholder="https://..." required>
                        <span class="material-symbols-rounded absolute top-1/2 -translate-y-1/2 right-3 text-slate-300 pointer-events-none" style="font-size:16px">link</span>
                    </div>
                    <p class="form-hint text-right">يدعم: YouTube، Google Drive، أو أي رابط مباشر</p>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 rounded-full px-2 py-0.5">اختياري</span>
                        <label class="form-label mb-0">صورة مصغرة</label>
                    </div>
                    <div id="addThumbZone" onclick="document.getElementById('addThumbFile').click()"
                        class="relative border-2 border-dashed border-slate-200 rounded-xl overflow-hidden cursor-pointer hover:border-red-300 hover:bg-red-50/30 transition-all" style="height:100px">
                        <div id="addThumbEmpty" class="absolute inset-0 flex flex-col items-center justify-center gap-1 text-slate-400 pointer-events-none">
                            <span class="material-symbols-rounded" style="font-size:28px">add_photo_alternate</span>
                            <p class="text-xs font-bold">اضغط لرفع صورة</p>
                            <p class="text-[10px] text-slate-300">PNG أو JPG — حتى 5MB</p>
                        </div>
                        <img id="addThumbPreview" src="" alt="" class="hidden w-full h-full object-cover">
                        <button type="button" id="addThumbClear"
                            class="hidden absolute top-1.5 left-1.5 w-6 h-6 bg-black/60 hover:bg-black/80 text-white rounded-full items-center justify-center transition-colors"
                            onclick="event.stopPropagation(); clearThumbZone('add')">
                            <span class="material-symbols-rounded" style="font-size:13px">close</span>
                        </button>
                    </div>
                    <input type="file" id="addThumbFile" name="thumbnail_file" accept="image/*" class="hidden"
                        onchange="previewThumbFile(this,'addThumbPreview','addThumbEmpty','addThumbClear','addThumbUrl')">
                    <div class="flex items-center gap-2 my-2.5">
                        <div class="flex-1 h-px bg-slate-100"></div>
                        <span class="text-[10px] text-slate-400 font-bold">أو رابط خارجي</span>
                        <div class="flex-1 h-px bg-slate-100"></div>
                    </div>
                    <div class="relative">
                        <input type="url" name="thumbnail_url" id="addThumbUrl" class="form-input" style="direction:ltr;text-align:left;padding-right:2.5rem" placeholder="https://...">
                        <span class="material-symbols-rounded absolute top-1/2 -translate-y-1/2 right-3 text-slate-300 pointer-events-none" style="font-size:16px">link</span>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-4 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:16px">sort</span>
                        <span class="text-xs font-bold text-slate-600">ترتيب العرض</span>
                    </div>
                    <input type="number" name="sort_order" value="{{ $videos->count() }}" min="0"
                        class="w-16 text-center border border-slate-200 rounded-lg py-1.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-blue-400">
                </div>
            </div>
            <div class="flex gap-2.5 px-5 py-4 border-t border-slate-100 flex-shrink-0">
                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">إضافة الفيديو</button>
                <button type="button" onclick="closeVideoModal()" class="flex-1 border border-slate-200 text-slate-500 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Edit Video --}}
<div id="editVideoModal" class="fixed inset-0 bg-black/60 z-[500] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh] transform translate-y-3 transition-transform duration-200" id="editVideoModalBox">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-blue-500" style="font-size:17px;font-variation-settings:'FILL' 1">edit_video</span>
            </div>
            <h3 class="text-sm font-black text-slate-800 flex-1">تعديل الفيديو</h3>
            <button type="button" onclick="closeEditVideoModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors flex-shrink-0">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form id="editVideoForm" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
            @csrf @method('PUT')
            <div class="p-5 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label class="form-label">عنوان الفيديو <span class="text-red-400">*</span></label>
                    <input type="text" name="title" id="evTitle" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">رابط الفيديو <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="url" name="video_url" id="evUrl" class="form-input" style="direction:ltr;text-align:left;padding-right:2.5rem" required>
                        <span class="material-symbols-rounded absolute top-1/2 -translate-y-1/2 right-3 text-slate-300 pointer-events-none" style="font-size:16px">link</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 rounded-full px-2 py-0.5">اختياري</span>
                        <label class="form-label mb-0">صورة مصغرة</label>
                    </div>
                    <div id="editThumbZone" onclick="document.getElementById('editThumbFile').click()"
                        class="relative border-2 border-dashed border-slate-200 rounded-xl overflow-hidden cursor-pointer hover:border-blue-300 hover:bg-blue-50/30 transition-all" style="height:100px">
                        <div id="editThumbEmpty" class="absolute inset-0 flex flex-col items-center justify-center gap-1 text-slate-400 pointer-events-none">
                            <span class="material-symbols-rounded" style="font-size:28px">add_photo_alternate</span>
                            <p class="text-xs font-bold">اضغط لتغيير الصورة</p>
                            <p class="text-[10px] text-slate-300">PNG أو JPG — حتى 5MB</p>
                        </div>
                        <img id="editThumbPreview" src="" alt="" class="hidden w-full h-full object-cover">
                        <button type="button" id="editThumbClear"
                            class="hidden absolute top-1.5 left-1.5 w-6 h-6 bg-black/60 hover:bg-black/80 text-white rounded-full items-center justify-center transition-colors"
                            onclick="event.stopPropagation(); clearThumbZone('edit')">
                            <span class="material-symbols-rounded" style="font-size:13px">close</span>
                        </button>
                    </div>
                    <input type="file" id="editThumbFile" name="thumbnail_file" accept="image/*" class="hidden"
                        onchange="previewThumbFile(this,'editThumbPreview','editThumbEmpty','editThumbClear','editThumbUrl')">
                    <div class="flex items-center gap-2 my-2.5">
                        <div class="flex-1 h-px bg-slate-100"></div>
                        <span class="text-[10px] text-slate-400 font-bold">أو رابط خارجي</span>
                        <div class="flex-1 h-px bg-slate-100"></div>
                    </div>
                    <div class="relative">
                        <input type="url" name="thumbnail_url" id="editThumbUrl" class="form-input" style="direction:ltr;text-align:left;padding-right:2.5rem" placeholder="https://...">
                        <span class="material-symbols-rounded absolute top-1/2 -translate-y-1/2 right-3 text-slate-300 pointer-events-none" style="font-size:16px">link</span>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-4 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:16px">sort</span>
                        <span class="text-xs font-bold text-slate-600">ترتيب العرض</span>
                    </div>
                    <input type="number" name="sort_order" id="evOrder" min="0"
                        class="w-16 text-center border border-slate-200 rounded-lg py-1.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-blue-400">
                </div>
            </div>
            <div class="flex gap-2.5 px-5 py-4 border-t border-slate-100 flex-shrink-0">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">حفظ التعديلات</button>
                <button type="button" onclick="closeEditVideoModal()" class="flex-1 border border-slate-200 text-slate-500 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════ TESTIMONIAL MODALS ══════════════ --}}

{{-- MODAL: Add Testimonial --}}
<div id="testimonialModal" class="fixed inset-0 bg-black/60 z-[500] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh] transform translate-y-3 transition-transform duration-200" id="testimonialModalBox">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-purple-500" style="font-size:17px;font-variation-settings:'FILL' 1">add_photo_alternate</span>
            </div>
            <h3 class="text-sm font-black text-slate-800 flex-1">إضافة صورة</h3>
            <button type="button" onclick="closeTestimonialModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors flex-shrink-0">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
            @csrf
            <div class="p-5 space-y-4 overflow-y-auto flex-1">
                {{-- Image Upload --}}
                <div>
                    <label class="form-label mb-2">الصورة <span class="text-red-400">*</span></label>
                    <div id="addTstZone" onclick="document.getElementById('addTstFile').click()"
                        class="relative border-2 border-dashed border-slate-200 rounded-xl overflow-hidden cursor-pointer hover:border-purple-300 hover:bg-purple-50/30 transition-all" style="height:120px">
                        <div id="addTstEmpty" class="absolute inset-0 flex flex-col items-center justify-center gap-1 text-slate-400 pointer-events-none">
                            <span class="material-symbols-rounded" style="font-size:32px">add_photo_alternate</span>
                            <p class="text-xs font-bold">اضغط لرفع صورة</p>
                            <p class="text-[10px] text-slate-300">PNG أو JPG — حتى 5MB</p>
                        </div>
                        <img id="addTstPreview" src="" alt="" class="hidden w-full h-full object-cover">
                        <button type="button" id="addTstClear"
                            class="hidden absolute top-1.5 left-1.5 w-6 h-6 bg-black/60 hover:bg-black/80 text-white rounded-full items-center justify-center transition-colors"
                            onclick="event.stopPropagation(); clearTstZone('add')">
                            <span class="material-symbols-rounded" style="font-size:13px">close</span>
                        </button>
                    </div>
                    <input type="file" id="addTstFile" name="image_file" accept="image/*" class="hidden"
                        onchange="previewThumbFile(this,'addTstPreview','addTstEmpty','addTstClear','addTstUrl')">
                    <div class="flex items-center gap-2 my-2.5">
                        <div class="flex-1 h-px bg-slate-100"></div>
                        <span class="text-[10px] text-slate-400 font-bold">أو رابط خارجي</span>
                        <div class="flex-1 h-px bg-slate-100"></div>
                    </div>
                    <div class="relative">
                        <input type="url" name="image_url" id="addTstUrl" class="form-input" style="direction:ltr;text-align:left;padding-right:2.5rem" placeholder="https://...">
                        <span class="material-symbols-rounded absolute top-1/2 -translate-y-1/2 right-3 text-slate-300 pointer-events-none" style="font-size:16px">link</span>
                    </div>
                </div>
                {{-- Alt Text --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 rounded-full px-2 py-0.5">اختياري</span>
                        <label class="form-label mb-0">اسم الشخص</label>
                    </div>
                    <input type="text" name="alt_text" class="form-input" placeholder="مثال: أحمد محمد">
                </div>
                {{-- Sort Order --}}
                <div class="flex items-center justify-between gap-4 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:16px">sort</span>
                        <span class="text-xs font-bold text-slate-600">ترتيب العرض</span>
                    </div>
                    <input type="number" name="sort_order" value="{{ $testimonials->count() }}" min="0"
                        class="w-16 text-center border border-slate-200 rounded-lg py-1.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-purple-400">
                </div>
            </div>
            <div class="flex gap-2.5 px-5 py-4 border-t border-slate-100 flex-shrink-0">
                <button type="submit" class="flex-1 bg-purple-500 hover:bg-purple-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">إضافة الصورة</button>
                <button type="button" onclick="closeTestimonialModal()" class="flex-1 border border-slate-200 text-slate-500 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Edit Testimonial --}}
<div id="editTestimonialModal" class="fixed inset-0 bg-black/60 z-[500] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh] transform translate-y-3 transition-transform duration-200" id="editTestimonialModalBox">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-blue-500" style="font-size:17px;font-variation-settings:'FILL' 1">edit</span>
            </div>
            <h3 class="text-sm font-black text-slate-800 flex-1">تعديل الصورة</h3>
            <button type="button" onclick="closeEditTestimonialModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors flex-shrink-0">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form id="editTestimonialForm" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
            @csrf @method('PUT')
            <div class="p-5 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label class="form-label mb-2">الصورة</label>
                    <div id="editTstZone" onclick="document.getElementById('editTstFile').click()"
                        class="relative border-2 border-dashed border-slate-200 rounded-xl overflow-hidden cursor-pointer hover:border-blue-300 hover:bg-blue-50/30 transition-all" style="height:120px">
                        <div id="editTstEmpty" class="absolute inset-0 flex flex-col items-center justify-center gap-1 text-slate-400 pointer-events-none">
                            <span class="material-symbols-rounded" style="font-size:28px">add_photo_alternate</span>
                            <p class="text-xs font-bold">اضغط لتغيير الصورة</p>
                            <p class="text-[10px] text-slate-300">PNG أو JPG — حتى 5MB</p>
                        </div>
                        <img id="editTstPreview" src="" alt="" class="hidden w-full h-full object-cover">
                        <button type="button" id="editTstClear"
                            class="hidden absolute top-1.5 left-1.5 w-6 h-6 bg-black/60 hover:bg-black/80 text-white rounded-full items-center justify-center transition-colors"
                            onclick="event.stopPropagation(); clearTstZone('edit')">
                            <span class="material-symbols-rounded" style="font-size:13px">close</span>
                        </button>
                    </div>
                    <input type="file" id="editTstFile" name="image_file" accept="image/*" class="hidden"
                        onchange="previewThumbFile(this,'editTstPreview','editTstEmpty','editTstClear','editTstUrl')">
                    <div class="flex items-center gap-2 my-2.5">
                        <div class="flex-1 h-px bg-slate-100"></div>
                        <span class="text-[10px] text-slate-400 font-bold">أو رابط خارجي</span>
                        <div class="flex-1 h-px bg-slate-100"></div>
                    </div>
                    <div class="relative">
                        <input type="url" name="image_url" id="editTstUrl" class="form-input" style="direction:ltr;text-align:left;padding-right:2.5rem" placeholder="https://...">
                        <span class="material-symbols-rounded absolute top-1/2 -translate-y-1/2 right-3 text-slate-300 pointer-events-none" style="font-size:16px">link</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 rounded-full px-2 py-0.5">اختياري</span>
                        <label class="form-label mb-0">اسم الشخص</label>
                    </div>
                    <input type="text" name="alt_text" id="etAlt" class="form-input">
                </div>
                <div class="flex items-center justify-between gap-4 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:16px">sort</span>
                        <span class="text-xs font-bold text-slate-600">ترتيب العرض</span>
                    </div>
                    <input type="number" name="sort_order" id="etOrder" min="0"
                        class="w-16 text-center border border-slate-200 rounded-lg py-1.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-blue-400">
                </div>
            </div>
            <div class="flex gap-2.5 px-5 py-4 border-t border-slate-100 flex-shrink-0">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">حفظ التعديلات</button>
                <button type="button" onclick="closeEditTestimonialModal()" class="flex-1 border border-slate-200 text-slate-500 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Delete Testimonial --}}
<div id="deleteTestimonialModal" class="fixed inset-0 bg-black/60 z-[500] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200">
    <div class="bg-white rounded-2xl w-full max-w-xs shadow-2xl transform translate-y-3 transition-transform duration-200" id="deleteTestimonialModalBox">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-rounded text-red-500" style="font-size:22px;font-variation-settings:'FILL' 1">delete_forever</span>
            </div>
            <h3 class="text-sm font-black text-slate-800 mb-1.5">تأكيد الحذف</h3>
            <p class="text-xs text-slate-500 font-semibold mb-5">سيتم حذف صورة "<span id="dtName" class="font-bold text-slate-700"></span>" نهائياً</p>
            <form id="deleteTestimonialForm" method="POST">
                @csrf @method('DELETE')
                <div class="flex gap-2.5">
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">نعم، احذف</button>
                    <button type="button" onclick="closeDeleteTestimonialModal()" class="flex-1 border border-slate-200 text-slate-500 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: Delete Video --}}
<div id="deleteVideoModal" class="fixed inset-0 bg-black/60 z-[500] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200">
    <div class="bg-white rounded-2xl w-full max-w-xs shadow-2xl transform translate-y-3 transition-transform duration-200" id="deleteVideoModalBox">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-rounded text-red-500" style="font-size:22px;font-variation-settings:'FILL' 1">delete_forever</span>
            </div>
            <h3 class="text-sm font-black text-slate-800 mb-1.5">تأكيد الحذف</h3>
            <p class="text-xs text-slate-500 font-semibold mb-5 leading-relaxed">
                سيتم حذف الفيديو<br>
                "<span id="dvTitle" class="font-bold text-slate-700"></span>"<br>
                بشكل نهائي ولا يمكن التراجع
            </p>
            <form id="deleteVideoForm" method="POST">
                @csrf @method('DELETE')
                <div class="flex gap-2.5">
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">نعم، احذف</button>
                    <button type="button" onclick="closeDeleteVideoModal()" class="flex-1 border border-slate-200 text-slate-500 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ BEFORE/AFTER MODALS ══════════════ --}}

{{-- MODAL: Add Before/After --}}
<div id="baModal" class="fixed inset-0 bg-black/60 z-[500] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh] transform translate-y-3 transition-transform duration-200" id="baModalBox">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-teal-500" style="font-size:17px;font-variation-settings:'FILL' 1">add_photo_alternate</span>
            </div>
            <h3 class="text-sm font-black text-slate-800 flex-1">إضافة صورة قبل/بعد</h3>
            <button type="button" onclick="closeBeforeAfterModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors flex-shrink-0">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.before-afters.store') }}" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
            @csrf
            <div class="p-5 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label class="form-label mb-2">الصورة <span class="text-red-400">*</span></label>
                    <div id="addBaZone" onclick="document.getElementById('addBaFile').click()"
                        class="relative border-2 border-dashed border-slate-200 rounded-xl overflow-hidden cursor-pointer hover:border-teal-300 hover:bg-teal-50/30 transition-all" style="height:120px">
                        <div id="addBaEmpty" class="absolute inset-0 flex flex-col items-center justify-center gap-1 text-slate-400 pointer-events-none">
                            <span class="material-symbols-rounded" style="font-size:32px">add_photo_alternate</span>
                            <p class="text-xs font-bold">اضغط لرفع صورة</p>
                            <p class="text-[10px] text-slate-300">PNG أو JPG — حتى 5MB</p>
                        </div>
                        <img id="addBaPreview" src="" alt="" class="hidden w-full h-full object-cover">
                        <button type="button" id="addBaClear"
                            class="hidden absolute top-1.5 left-1.5 w-6 h-6 bg-black/60 hover:bg-black/80 text-white rounded-full items-center justify-center transition-colors"
                            onclick="event.stopPropagation(); clearBaZone('add')">
                            <span class="material-symbols-rounded" style="font-size:13px">close</span>
                        </button>
                    </div>
                    <input type="file" id="addBaFile" name="image_file" accept="image/*" class="hidden"
                        onchange="previewThumbFile(this,'addBaPreview','addBaEmpty','addBaClear','addBaUrl')">
                    <div class="flex items-center gap-2 my-2.5">
                        <div class="flex-1 h-px bg-slate-100"></div>
                        <span class="text-[10px] text-slate-400 font-bold">أو رابط خارجي</span>
                        <div class="flex-1 h-px bg-slate-100"></div>
                    </div>
                    <div class="relative">
                        <input type="url" name="image_url" id="addBaUrl" class="form-input" style="direction:ltr;text-align:left;padding-right:2.5rem" placeholder="https://...">
                        <span class="material-symbols-rounded absolute top-1/2 -translate-y-1/2 right-3 text-slate-300 pointer-events-none" style="font-size:16px">link</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 rounded-full px-2 py-0.5">اختياري</span>
                        <label class="form-label mb-0">وصف الصورة</label>
                    </div>
                    <input type="text" name="alt_text" class="form-input" placeholder="مثال: Client 1">
                </div>
                <div class="flex items-center justify-between gap-4 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:16px">sort</span>
                        <span class="text-xs font-bold text-slate-600">ترتيب العرض</span>
                    </div>
                    <input type="number" name="sort_order" value="{{ $beforeAfters->count() }}" min="0"
                        class="w-16 text-center border border-slate-200 rounded-lg py-1.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-teal-400">
                </div>
            </div>
            <div class="flex gap-2.5 px-5 py-4 border-t border-slate-100 flex-shrink-0">
                <button type="submit" class="flex-1 bg-teal-500 hover:bg-teal-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">إضافة الصورة</button>
                <button type="button" onclick="closeBeforeAfterModal()" class="flex-1 border border-slate-200 text-slate-500 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Edit Before/After --}}
<div id="editBaModal" class="fixed inset-0 bg-black/60 z-[500] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh] transform translate-y-3 transition-transform duration-200" id="editBaModalBox">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-rounded text-blue-500" style="font-size:17px;font-variation-settings:'FILL' 1">edit</span>
            </div>
            <h3 class="text-sm font-black text-slate-800 flex-1">تعديل صورة قبل/بعد</h3>
            <button type="button" onclick="closeEditBeforeAfterModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors flex-shrink-0">
                <span class="material-symbols-rounded" style="font-size:18px">close</span>
            </button>
        </div>
        <form id="editBaForm" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
            @csrf @method('PUT')
            <div class="p-5 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label class="form-label mb-2">الصورة</label>
                    <div id="editBaZone" onclick="document.getElementById('editBaFile').click()"
                        class="relative border-2 border-dashed border-slate-200 rounded-xl overflow-hidden cursor-pointer hover:border-blue-300 hover:bg-blue-50/30 transition-all" style="height:120px">
                        <div id="editBaEmpty" class="absolute inset-0 flex flex-col items-center justify-center gap-1 text-slate-400 pointer-events-none">
                            <span class="material-symbols-rounded" style="font-size:28px">add_photo_alternate</span>
                            <p class="text-xs font-bold">اضغط لتغيير الصورة</p>
                            <p class="text-[10px] text-slate-300">PNG أو JPG — حتى 5MB</p>
                        </div>
                        <img id="editBaPreview" src="" alt="" class="hidden w-full h-full object-cover">
                        <button type="button" id="editBaClear"
                            class="hidden absolute top-1.5 left-1.5 w-6 h-6 bg-black/60 hover:bg-black/80 text-white rounded-full items-center justify-center transition-colors"
                            onclick="event.stopPropagation(); clearBaZone('edit')">
                            <span class="material-symbols-rounded" style="font-size:13px">close</span>
                        </button>
                    </div>
                    <input type="file" id="editBaFile" name="image_file" accept="image/*" class="hidden"
                        onchange="previewThumbFile(this,'editBaPreview','editBaEmpty','editBaClear','editBaUrl')">
                    <div class="flex items-center gap-2 my-2.5">
                        <div class="flex-1 h-px bg-slate-100"></div>
                        <span class="text-[10px] text-slate-400 font-bold">أو رابط خارجي</span>
                        <div class="flex-1 h-px bg-slate-100"></div>
                    </div>
                    <div class="relative">
                        <input type="url" name="image_url" id="editBaUrl" class="form-input" style="direction:ltr;text-align:left;padding-right:2.5rem" placeholder="https://...">
                        <span class="material-symbols-rounded absolute top-1/2 -translate-y-1/2 right-3 text-slate-300 pointer-events-none" style="font-size:16px">link</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 rounded-full px-2 py-0.5">اختياري</span>
                        <label class="form-label mb-0">وصف الصورة</label>
                    </div>
                    <input type="text" name="alt_text" id="ebAlt" class="form-input">
                </div>
                <div class="flex items-center justify-between gap-4 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-rounded text-slate-400" style="font-size:16px">sort</span>
                        <span class="text-xs font-bold text-slate-600">ترتيب العرض</span>
                    </div>
                    <input type="number" name="sort_order" id="ebOrder" min="0"
                        class="w-16 text-center border border-slate-200 rounded-lg py-1.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-blue-400">
                </div>
            </div>
            <div class="flex gap-2.5 px-5 py-4 border-t border-slate-100 flex-shrink-0">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">حفظ التعديلات</button>
                <button type="button" onclick="closeEditBeforeAfterModal()" class="flex-1 border border-slate-200 text-slate-500 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Delete Before/After --}}
<div id="deleteBaModal" class="fixed inset-0 bg-black/60 z-[500] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200">
    <div class="bg-white rounded-2xl w-full max-w-xs shadow-2xl transform translate-y-3 transition-transform duration-200" id="deleteBaModalBox">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-rounded text-red-500" style="font-size:22px;font-variation-settings:'FILL' 1">delete_forever</span>
            </div>
            <h3 class="text-sm font-black text-slate-800 mb-1.5">تأكيد الحذف</h3>
            <p class="text-xs text-slate-500 font-semibold mb-5">سيتم حذف صورة "<span id="dbName" class="font-bold text-slate-700"></span>" نهائياً</p>
            <form id="deleteBaForm" method="POST">
                @csrf @method('DELETE')
                <div class="flex gap-2.5">
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">نعم، احذف</button>
                    <button type="button" onclick="closeDeleteBeforeAfterModal()" class="flex-1 border border-slate-200 text-slate-500 font-bold py-2.5 rounded-xl text-sm hover:bg-slate-50 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
function switchTab(tab, btn) {
    ['general','social','stats','videos','testimonials','before-after','family-reward','booking'].forEach(t => {
        document.getElementById('tab-' + t).classList.add('hidden');
    });
    document.querySelectorAll('.tab-nav-item').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.remove('hidden');
    btn.classList.add('active');
}

// ── Toggle Video Active (fetch — avoids nested forms) ───────
function toggleVideo(id) {
    const token = document.querySelector('input[name="_token"]').value;
    fetch(`/admin/videos/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
    }).then(() => window.location.reload())
      .catch(() => alert('حدث خطأ، يرجى المحاولة مرة أخرى'));
}

// ── Video Modal Helpers ──────────────────────────────────────
function _openModal(id, boxId) {
    const m = document.getElementById(id), b = document.getElementById(boxId);
    m.classList.remove('opacity-0','pointer-events-none');
    m.classList.add('opacity-100');
    b.classList.remove('translate-y-4');
}
function _closeModal(id, boxId) {
    const m = document.getElementById(id), b = document.getElementById(boxId);
    m.classList.add('opacity-0','pointer-events-none');
    m.classList.remove('opacity-100');
    b.classList.add('translate-y-4');
}

// Add
function openVideoModal()  { _openModal('videoModal','videoModalBox'); }
function closeVideoModal() { _closeModal('videoModal','videoModalBox'); }

// Edit
function openEditVideoModal(id, title, url, thumb, order) {
    document.getElementById('editVideoForm').action = `/admin/videos/${id}`;
    document.getElementById('evTitle').value    = title;
    document.getElementById('evUrl').value      = url;
    document.getElementById('evOrder').value    = order;

    // Reset thumbnail zone
    clearThumbZone('edit');
    document.getElementById('editThumbUrl').value = '';

    if (thumb) {
        const isExternal = thumb.startsWith('http');
        if (isExternal) {
            document.getElementById('editThumbUrl').value = thumb;
        } else {
            // Local uploaded file — show preview
            showThumbPreview('editThumbPreview', 'editThumbEmpty', 'editThumbClear', '/' + thumb);
        }
    }

    _openModal('editVideoModal','editVideoModalBox');
}
function closeEditVideoModal() { _closeModal('editVideoModal','editVideoModalBox'); }

// Delete
function openDeleteVideoModal(id, title) {
    document.getElementById('deleteVideoForm').action = `/admin/videos/${id}`;
    document.getElementById('dvTitle').textContent = title;
    _openModal('deleteVideoModal','deleteVideoModalBox');
}
function closeDeleteVideoModal() { _closeModal('deleteVideoModal','deleteVideoModalBox'); }

// ── Thumbnail Upload Helpers ──────────────────────────────
function previewThumbFile(input, previewId, emptyId, clearId, urlId) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById(urlId).value = ''; // clear URL field
    const reader = new FileReader();
    reader.onload = (e) => showThumbPreview(previewId, emptyId, clearId, e.target.result);
    reader.readAsDataURL(file);
}

function showThumbPreview(previewId, emptyId, clearId, src) {
    const preview = document.getElementById(previewId);
    const empty   = document.getElementById(emptyId);
    const clear   = document.getElementById(clearId);
    preview.src = src;
    preview.classList.remove('hidden');
    empty.classList.add('hidden');
    clear.classList.remove('hidden');
    clear.classList.add('flex');
}

function clearThumbZone(prefix) {
    const preview = document.getElementById(prefix + 'ThumbPreview');
    const empty   = document.getElementById(prefix + 'ThumbEmpty');
    const clear   = document.getElementById(prefix + 'ThumbClear');
    const file    = document.getElementById(prefix + 'ThumbFile');
    preview.src = ''; preview.classList.add('hidden');
    empty.classList.remove('hidden');
    clear.classList.add('hidden'); clear.classList.remove('flex');
    file.value = '';
}

// ── Testimonials ────────────────────────────────────────────
function openTestimonialModal()  { _openModal('testimonialModal','testimonialModalBox'); }
function closeTestimonialModal() { _closeModal('testimonialModal','testimonialModalBox'); }

function openEditTestimonialModal(id, alt, imgPath, order) {
    document.getElementById('editTestimonialForm').action = `/admin/testimonials/${id}`;
    document.getElementById('etAlt').value   = alt;
    document.getElementById('etOrder').value = order;
    clearTstZone('edit');
    document.getElementById('editTstUrl').value = '';
    if (imgPath) {
        const isExternal = imgPath.startsWith('http');
        if (isExternal) {
            document.getElementById('editTstUrl').value = imgPath;
        } else {
            showThumbPreview('editTstPreview','editTstEmpty','editTstClear', '/' + imgPath);
        }
    }
    _openModal('editTestimonialModal','editTestimonialModalBox');
}
function closeEditTestimonialModal() { _closeModal('editTestimonialModal','editTestimonialModalBox'); }

function openDeleteTestimonialModal(id, name) {
    document.getElementById('deleteTestimonialForm').action = `/admin/testimonials/${id}`;
    document.getElementById('dtName').textContent = name;
    _openModal('deleteTestimonialModal','deleteTestimonialModalBox');
}
function closeDeleteTestimonialModal() { _closeModal('deleteTestimonialModal','deleteTestimonialModalBox'); }

function toggleTestimonial(id) {
    const token = document.querySelector('input[name="_token"]').value;
    fetch(`/admin/testimonials/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
    }).then(() => window.location.reload())
      .catch(() => alert('حدث خطأ، يرجى المحاولة مرة أخرى'));
}

function clearTstZone(prefix) {
    const preview = document.getElementById(prefix + 'TstPreview');
    const empty   = document.getElementById(prefix + 'TstEmpty');
    const clear   = document.getElementById(prefix + 'TstClear');
    const file    = document.getElementById(prefix + 'TstFile');
    if (preview) { preview.src = ''; preview.classList.add('hidden'); }
    if (empty)   { empty.classList.remove('hidden'); }
    if (clear)   { clear.classList.add('hidden'); clear.classList.remove('flex'); }
    if (file)    { file.value = ''; }
}

// ── Before/After ────────────────────────────────────────────
function openBeforeAfterModal()  { _openModal('baModal','baModalBox'); }
function closeBeforeAfterModal() { _closeModal('baModal','baModalBox'); }

function openEditBeforeAfterModal(id, alt, imgPath, order) {
    document.getElementById('editBaForm').action = `/admin/before-afters/${id}`;
    document.getElementById('ebAlt').value   = alt;
    document.getElementById('ebOrder').value = order;
    clearBaZone('edit');
    document.getElementById('editBaUrl').value = '';
    if (imgPath) {
        const isExternal = imgPath.startsWith('http');
        if (isExternal) {
            document.getElementById('editBaUrl').value = imgPath;
        } else {
            showThumbPreview('editBaPreview','editBaEmpty','editBaClear', '/' + imgPath);
        }
    }
    _openModal('editBaModal','editBaModalBox');
}
function closeEditBeforeAfterModal() { _closeModal('editBaModal','editBaModalBox'); }

function openDeleteBeforeAfterModal(id, name) {
    document.getElementById('deleteBaForm').action = `/admin/before-afters/${id}`;
    document.getElementById('dbName').textContent = name;
    _openModal('deleteBaModal','deleteBaModalBox');
}
function closeDeleteBeforeAfterModal() { _closeModal('deleteBaModal','deleteBaModalBox'); }

function toggleBeforeAfter(id) {
    const token = document.querySelector('input[name="_token"]').value;
    fetch(`/admin/before-afters/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
    }).then(() => window.location.reload())
      .catch(() => alert('حدث خطأ، يرجى المحاولة مرة أخرى'));
}

function clearBaZone(prefix) {
    const preview = document.getElementById(prefix + 'BaPreview');
    const empty   = document.getElementById(prefix + 'BaEmpty');
    const clear   = document.getElementById(prefix + 'BaClear');
    const file    = document.getElementById(prefix + 'BaFile');
    if (preview) { preview.src = ''; preview.classList.add('hidden'); }
    if (empty)   { empty.classList.remove('hidden'); }
    if (clear)   { clear.classList.add('hidden'); clear.classList.remove('flex'); }
    if (file)    { file.value = ''; }
}

// Close on backdrop click
['videoModal','editVideoModal','deleteVideoModal',
 'testimonialModal','editTestimonialModal','deleteTestimonialModal',
 'baModal','editBaModal','deleteBaModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('opacity-0','pointer-events-none');
            this.classList.remove('opacity-100');
        }
    });
});

// ── Booking Settings UI ──────────────────────────────────────────────────────
(function() {
    const DAY_NAMES  = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
    const DAY_SHORT  = ['أحد','اثنين','ثلاثاء','أربعاء','خميس','جمعة','سبت'];

    function to12H(t) {
        const [h, m] = t.split(':').map(Number);
        const ampm = h >= 12 ? 'PM' : 'AM';
        const h12  = h % 12 || 12;
        return `${h12}:${m.toString().padStart(2,'0')} ${ampm}`;
    }

    function btnClass(active) {
        return active
            ? 'px-3 py-2 rounded-xl border-2 border-blue-500 bg-blue-50 text-blue-700 font-black text-xs cursor-pointer transition-all select-none'
            : 'px-3 py-2 rounded-xl border-2 border-slate-200 bg-white text-slate-400 font-bold text-xs cursor-pointer transition-all hover:border-blue-300 select-none';
    }

    function timeClass(active) {
        return active
            ? 'py-1.5 rounded-lg border-2 border-blue-500 bg-blue-50 text-blue-700 font-black text-[11px] cursor-pointer transition-all select-none text-center'
            : 'py-1.5 rounded-lg border border-slate-200 bg-white text-slate-400 font-bold text-[11px] cursor-pointer transition-all hover:border-blue-300 select-none text-center';
    }

    function initDays() {
        const input = document.getElementById('availableDaysInput');
        const wrap  = document.getElementById('dayToggles');
        if (!input || !wrap) return;

        let selected = input.value.split(',').map(Number).filter(n => !isNaN(n));

        for (let i = 0; i < 7; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = `<span class="block text-[10px] opacity-60 mb-0.5">${DAY_NAMES[i]}</span><span>${DAY_SHORT[i]}</span>`;
            btn.className = btnClass(selected.includes(i));

            btn.addEventListener('click', () => {
                const idx = selected.indexOf(i);
                if (idx === -1) { selected.push(i); selected.sort((a,b)=>a-b); }
                else { selected.splice(idx,1); }
                btn.className = btnClass(selected.includes(i));
                input.value = selected.join(',');
            });

            wrap.appendChild(btn);
        }
    }

    function initTimes() {
        const input = document.getElementById('timeSlotsInput');
        const wrap  = document.getElementById('timeToggles');
        if (!input || !wrap) return;

        let selected = input.value.split(',').map(s=>s.trim()).filter(Boolean);
        const slots  = [];
        for (let h = 5; h < 24; h++) {
            slots.push(`${String(h).padStart(2,'0')}:00`);
            if (h < 23) slots.push(`${String(h).padStart(2,'0')}:30`);
        }

        slots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = to12H(slot);
            btn.className   = timeClass(selected.includes(slot));

            btn.addEventListener('click', () => {
                const idx = selected.indexOf(slot);
                if (idx === -1) { selected.push(slot); selected.sort(); }
                else { selected.splice(idx,1); }
                btn.className = timeClass(selected.includes(slot));
                input.value   = selected.join(',');
            });

            wrap.appendChild(btn);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDays();
        initTimes();
    });
})();

// Auto-switch to requested tab
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if (tab) {
        const btn = document.querySelector(`.tab-nav-item[data-tab="${tab}"]`);
        if (btn) switchTab(tab, btn);
    }
});
</script>
@endsection

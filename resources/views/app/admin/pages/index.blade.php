@extends('layouts.admin.app')

@section('title', 'صفحات الموقع')
@section('page-title', 'صفحات الموقع')
@section('page-subtitle', 'تعديل محتوى صفحات "من نحن"، "تواصل معنا"، وسياسات التسليم والاسترداد')

@section('style')
<style>
.page-card { background:#fff; border:1px solid #e8edf5; border-radius:20px; padding:1.4rem; display:flex; align-items:center; gap:1rem; transition:border-color .2s, box-shadow .2s; }
.page-card:hover { border-color:#c7d7ee; box-shadow:0 8px 24px rgba(30,64,175,.06); }
.page-card-icon { width:48px; height:48px; border-radius:14px; background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.legal-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .65rem; border-radius:999px; font-size:.68rem; font-weight:800; background:#fff7ed; color:#ea580c; }
</style>
@endsection

@section('content')

@if(session('success'))
<div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-3.5 mb-5 font-bold text-sm">
    <span class="material-symbols-rounded flex-shrink-0" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    @foreach($pages as $p)
    <a href="{{ route('admin.pages.edit', $p['key']) }}" class="page-card">
        <div class="page-card-icon">
            <span class="material-symbols-rounded text-blue-600" style="font-size:24px">{{ $p['icon'] }}</span>
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-black text-slate-800">{{ $p['label'] }}</h3>
                @if($p['legal'])
                <span class="legal-badge">
                    <span class="material-symbols-rounded" style="font-size:13px">gavel</span>
                    مستند قانوني
                </span>
                @endif
            </div>
            <p class="text-xs text-slate-400 font-semibold mt-0.5">تعديل المحتوى بالعربية والإنجليزية</p>
        </div>
        <span class="material-symbols-rounded text-slate-300" style="font-size:20px">chevron_left</span>
    </a>
    @endforeach
</div>

@endsection

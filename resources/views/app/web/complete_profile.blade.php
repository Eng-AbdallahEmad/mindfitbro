@extends('layouts.web.app')
@section('title', 'إكمال بيانات الحساب')

@section('style')
<style>
    .cp-wrap {
        min-height: 100vh;
        background: #F0F4FB;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        font-family: 'Cairo', sans-serif;
        direction: rtl;
    }
    .cp-card {
        background: #fff;
        border-radius: 28px;
        box-shadow: 0 8px 40px rgba(23,77,173,0.10);
        width: 100%;
        max-width: 480px;
        overflow: hidden;
    }
    .cp-header {
        background: linear-gradient(135deg, #174DAD 0%, #0f3a87 100%);
        padding: 40px 36px 32px;
        text-align: center;
    }
    .cp-icon { font-size: 48px; margin-bottom: 12px; }
    .cp-badge {
        display: inline-block;
        background: #D4ED57;
        color: #1C1C1C;
        font-size: 11px;
        font-weight: 900;
        padding: 4px 14px;
        border-radius: 20px;
        margin-bottom: 12px;
    }
    .cp-title { color: #fff; font-size: 22px; font-weight: 900; margin-bottom: 6px; }
    .cp-sub   { color: rgba(255,255,255,0.7); font-size: 13px; }

    .cp-body { padding: 32px 36px; }

    .cp-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #6B7280;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .cp-input {
        width: 100%;
        background: #F4F7FF;
        border: 2px solid #e0e8ff;
        border-radius: 14px;
        padding: .85rem 1.1rem;
        font-size: .9rem;
        color: #1c1c1c;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        font-family: 'Cairo', sans-serif;
        text-align: right;
        direction: rtl;
    }
    .cp-input:focus { border-color: #174DAD; box-shadow: 0 0 0 3px rgba(23,77,173,.12); }
    .cp-error { font-size: 12px; color: #EF4444; margin-top: 4px; font-weight: 600; }

    .gender-row { display: flex; gap: 12px; }
    .gender-opt { flex: 1; }
    .gender-opt input[type="radio"] { display: none; }
    .gender-opt label {
        display: block;
        text-align: center;
        padding: .75rem;
        border-radius: 14px;
        border: 2px solid #e0e8ff;
        background: #F4F7FF;
        font-size: 14px;
        font-weight: 700;
        color: #6B7280;
        cursor: pointer;
        transition: all .2s;
    }
    .gender-opt input[type="radio"]:checked + label {
        border-color: #174DAD;
        background: #EBF0FF;
        color: #174DAD;
    }

    .cp-btn {
        width: 100%;
        background: #174DAD;
        color: #fff;
        font-size: 15px;
        font-weight: 900;
        padding: 1rem;
        border-radius: 14px;
        border: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        transition: opacity .2s;
    }
    .cp-btn:hover { opacity: .9; }

    .field-group { margin-bottom: 20px; }
</style>
@endsection

@section('content')
<div class="cp-wrap">
    <div class="cp-card">
        <div class="cp-header">
            <div class="cp-icon"><span class="material-symbols-rounded" style="font-size:32px;font-variation-settings:'FILL' 1">auto_awesome</span></div>
            <div class="cp-badge">خطوة أخيرة</div>
            <h1 class="cp-title">أكمل ملفك الشخصي</h1>
            <p class="cp-sub">هذه المعلومات تساعد الكوتش في تصميم برنامجك المناسب</p>
        </div>

        <div class="cp-body">
            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 rounded-xl p-3 text-sm font-semibold text-center">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('complete-profile.store') }}">
                @csrf

                {{-- Username --}}
                <div class="field-group">
                    <label class="cp-label" for="username">اسم المستخدم</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="مثال: ahmed_fit"
                        class="cp-input"
                        dir="ltr"
                        autocomplete="username"
                    >
                    @error('username')<p class="cp-error">{{ $message }}</p>@enderror
                </div>

                {{-- Phone --}}
                <div class="field-group">
                    <label class="cp-label" for="phone">رقم الهاتف</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="{{ \App\Services\Web\ContactInfo::current()['phone_placeholder'] }}"
                        class="cp-input"
                        dir="ltr"
                        autocomplete="tel"
                    >
                    @error('phone')<p class="cp-error">{{ $message }}</p>@enderror
                </div>

                {{-- Gender --}}
                <div class="field-group">
                    <label class="cp-label">الجنس</label>
                    <div class="gender-row">
                        <div class="gender-opt">
                            <input type="radio" id="g-male" name="gender" value="male" {{ old('gender') === 'male' ? 'checked' : '' }}>
                            <label for="g-male">ذكر</label>
                        </div>
                        <div class="gender-opt">
                            <input type="radio" id="g-female" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }}>
                            <label for="g-female">أنثى</label>
                        </div>
                    </div>
                    @error('gender')<p class="cp-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="cp-btn">
                    الانتقال للوحة التحكم ←
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

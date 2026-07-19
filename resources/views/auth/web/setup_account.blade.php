@extends('layouts.web.app')
@section('title', 'إعداد حسابك')

@section('style')
<style>
    .sa-wrap {
        min-height: 100vh;
        background: #F0F4FB;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        font-family: 'Cairo', sans-serif;
        direction: rtl;
    }
    .sa-card {
        background: #fff;
        border-radius: 28px;
        box-shadow: 0 8px 40px rgba(23,77,173,0.10);
        width: 100%;
        max-width: 480px;
        overflow: hidden;
    }
    .sa-header {
        background: linear-gradient(135deg, #174DAD 0%, #0f3a87 100%);
        padding: 40px 36px 32px;
        text-align: center;
    }
    .sa-badge {
        display: inline-block;
        background: #D4ED57;
        color: #1C1C1C;
        font-size: 11px;
        font-weight: 900;
        padding: 4px 14px;
        border-radius: 20px;
        margin-bottom: 12px;
    }
    .sa-title { color: #fff; font-size: 22px; font-weight: 900; margin-bottom: 6px; }
    .sa-sub   { color: rgba(255,255,255,0.7); font-size: 13px; }

    .sa-body { padding: 32px 36px; }

    .sa-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #6B7280;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .sa-input {
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
    .sa-input[dir="ltr"] { text-align: left; }
    .sa-input:focus { border-color: #174DAD; box-shadow: 0 0 0 3px rgba(23,77,173,.12); }
    .sa-error { font-size: 12px; color: #EF4444; margin-top: 4px; font-weight: 600; }

    .sa-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 24px 0 20px;
        color: #9CA3AF;
        font-size: 11px;
        font-weight: 700;
    }
    .sa-divider::before, .sa-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #E5E7EB;
    }

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

    .sa-btn {
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
        margin-top: 8px;
    }
    .sa-btn:hover { opacity: .9; }

    .field-group { margin-bottom: 20px; }

    .pw-toggle {
        position: relative;
    }
    .pw-toggle .sa-input { padding-left: 2.8rem; }
    .pw-toggle .pw-eye {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #9CA3AF;
        background: none;
        border: none;
        padding: 0;
        display: flex;
        align-items: center;
    }
    .pw-toggle .pw-eye:hover { color: #374151; }
</style>
@endsection

@section('content')
<div class="sa-wrap">
    <div class="sa-card">
        <div class="sa-header">
            <span class="material-symbols-rounded" style="font-size:36px;color:#D4ED57;font-variation-settings:'FILL' 1;display:block;margin-bottom:10px">lock_open</span>
            <div class="sa-badge">خطوة واحدة فقط</div>
            <h1 class="sa-title">أكمل إعداد حسابك</h1>
            <p class="sa-sub">أدخل بياناتك لتفعيل اشتراكك والوصول للوحة التحكم</p>
        </div>

        <div class="sa-body">

            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 text-sm font-semibold">
                <ul class="list-none space-y-1">
                    @foreach($errors->all() as $e)
                        <li>• {{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('setup-account.store', $token) }}">
                @csrf

                {{-- Username --}}
                <div class="field-group">
                    <label class="sa-label" for="username">اسم المستخدم</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="مثال: ahmed_fit"
                        class="sa-input"
                        dir="ltr"
                        autocomplete="username"
                    >
                    <p style="font-size:11px;color:#9CA3AF;margin-top:4px;font-weight:600;">حروف إنجليزية، أرقام، وشرطة سفلية فقط</p>
                    @error('username')<p class="sa-error">{{ $message }}</p>@enderror
                </div>

                {{-- Password section divider --}}
                <div class="sa-divider">كلمة المرور</div>

                {{-- Password --}}
                <div class="field-group">
                    <label class="sa-label" for="password">كلمة المرور</label>
                    <div class="pw-toggle">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="8 أحرف على الأقل"
                            class="sa-input"
                            dir="ltr"
                            autocomplete="new-password"
                        >
                        <button type="button" class="pw-eye" onclick="togglePw('password')">
                            <span class="material-symbols-rounded" style="font-size:20px" id="eye-password">visibility</span>
                        </button>
                    </div>
                    @error('password')<p class="sa-error">{{ $message }}</p>@enderror
                </div>

                {{-- Password confirmation --}}
                <div class="field-group">
                    <label class="sa-label" for="password_confirmation">تأكيد كلمة المرور</label>
                    <div class="pw-toggle">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="أعد كتابة كلمة المرور"
                            class="sa-input"
                            dir="ltr"
                            autocomplete="new-password"
                        >
                        <button type="button" class="pw-eye" onclick="togglePw('password_confirmation')">
                            <span class="material-symbols-rounded" style="font-size:20px" id="eye-password_confirmation">visibility</span>
                        </button>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="sa-divider">معلومات شخصية</div>

                {{-- Phone --}}
                <div class="field-group">
                    <label class="sa-label" for="phone">رقم الهاتف</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="+966 5X XXX XXXX"
                        class="sa-input"
                        dir="ltr"
                        autocomplete="tel"
                    >
                    @error('phone')<p class="sa-error">{{ $message }}</p>@enderror
                </div>

                {{-- Gender --}}
                <div class="field-group">
                    <label class="sa-label">الجنس</label>
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
                    @error('gender')<p class="sa-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="sa-btn">
                    <span class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;margin-left:6px;font-variation-settings:'FILL' 1">check_circle</span>
                    تفعيل الحساب والدخول للوحة التحكم
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePw(id) {
    var inp = document.getElementById(id);
    var eye = document.getElementById('eye-' + id);
    if (inp.type === 'password') {
        inp.type = 'text';
        eye.textContent = 'visibility_off';
    } else {
        inp.type = 'password';
        eye.textContent = 'visibility';
    }
}
</script>
@endsection

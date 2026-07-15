@extends('layouts.admin.auth')

@section('title', 'تسجيل دخول الأدمن')

@section('style')
<style>
    /* ─── Background ─── */
    .admin-bg {
        background: radial-gradient(ellipse 90% 80% at 50% 0%,
            #1a1a2e 0%, #16213e 40%, #0f3460 80%, #0a1628 100%);
    }

    /* ─── Card ─── */
    .admin-card {
        background: rgba(255, 255, 255, 0.04);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        animation: cardIn .5s cubic-bezier(.4,0,.2,1) both;
    }
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ─── Input ─── */
    .admin-input {
        width: 100%;
        background: rgba(255,255,255,0.06);
        border: 1.5px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: .85rem 1.1rem;
        font-size: .9rem;
        color: #fff;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        font-family: 'Cairo', sans-serif;
        text-align: right;
    }
    .admin-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59,130,246,.15);
    }
    .admin-input::placeholder { color: rgba(255,255,255,0.3); }
    .admin-input.is-error { border-color: #f87171; }

    /* ─── Password Toggle ─── */
    .pass-wrap { position: relative; }
    .pass-toggle {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,0.35);
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        display: flex;
        align-items: center;
        transition: color .2s;
    }
    .pass-toggle:hover { color: #fff; }

    /* ─── Submit ─── */
    .admin-btn {
        width: 100%;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: .9rem;
        font-size: .95rem;
        font-weight: 900;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
        transition: opacity .2s, transform .15s;
    }
    .admin-btn:hover { opacity: .9; transform: translateY(-1px); }
    .admin-btn:active { transform: translateY(0); }

    /* ─── Orbs ─── */
    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(90px);
        opacity: 0.12;
        pointer-events: none;
    }
    .orb-1 { width: 400px; height: 400px; background: #3b82f6; top: -100px; right: -100px; }
    .orb-2 { width: 300px; height: 300px; background: #8b5cf6; bottom: -80px; left: -80px; }

    /* ─── Checkbox ─── */
    .admin-check {
        width: 17px; height: 17px;
        border-radius: 5px;
        accent-color: #3b82f6;
        cursor: pointer;
    }
</style>
@endsection

@section('content')

<div class="admin-bg min-h-screen flex items-center justify-center p-6 relative overflow-hidden">

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="admin-card w-full max-w-md p-8 sm:p-10 relative z-10">

        {{-- Logo --}}
        <div class="flex justify-center mb-8">
            <img src="{{ asset('assets/logo/mindfitbro.png') }}" alt="MindFitBro" class="w-[180px] object-contain">
        </div>

        {{-- Heading --}}
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-300 text-xs font-bold tracking-widest px-4 py-1.5 rounded-full mb-3">
                <span class="material-symbols-rounded" style="font-size:15px;font-variation-settings:'FILL' 1">shield_person</span>
                لوحة التحكم
            </span>
            <h1 class="text-white text-2xl font-black">تسجيل دخول الأدمن</h1>
            <p class="text-white/40 text-sm mt-1">أدخل بياناتك للوصول إلى لوحة التحكم</p>
        </div>

        {{-- Error Alert --}}
        @if ($errors->any())
        <div class="mb-5 rounded-xl bg-red-500/10 border border-red-500/20 p-4 flex items-start gap-3">
            <span class="material-symbols-rounded text-red-400 flex-shrink-0" style="font-size:18px">error</span>
            <div class="text-sm text-red-300 font-semibold">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Success --}}
        @if (session('success'))
        <div class="mb-5 rounded-xl bg-green-500/10 border border-green-500/20 p-4 flex items-center gap-3">
            <span class="material-symbols-rounded text-green-400 flex-shrink-0" style="font-size:18px">check_circle</span>
            <p class="text-sm text-green-300 font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.login.post') }}" class="flex flex-col gap-5">
            @csrf

            {{-- Email --}}
            <div class="flex flex-col gap-2">
                <label for="email" class="text-sm font-bold text-white/70 text-right">البريد الإلكتروني</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    placeholder="admin@mindfitbro.com"
                    autofocus
                    class="admin-input @error('email') is-error @enderror"
                >
                @error('email')
                    <p class="text-xs text-red-400 font-semibold text-right">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="flex flex-col gap-2">
                <label for="password" class="text-sm font-bold text-white/70 text-right">كلمة المرور</label>
                <div class="pass-wrap">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        class="admin-input @error('password') is-error @enderror"
                    >
                    <button type="button" class="pass-toggle" onclick="togglePass()">
                        <span class="material-symbols-rounded" id="passIcon" style="font-size:20px">visibility</span>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-400 font-semibold text-right">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember --}}
            <div class="flex items-center justify-end gap-2">
                <label for="remember" class="text-sm text-white/50 cursor-pointer select-none">تذكرني</label>
                <input type="checkbox" name="remember" id="remember" class="admin-check">
            </div>

            {{-- Submit --}}
            <button type="submit" class="admin-btn mt-1">
                دخول إلى لوحة التحكم
            </button>

        </form>

        {{-- Security Note --}}
        <p class="flex items-center justify-center gap-2 text-white/25 text-xs font-semibold mt-7">
            <span class="material-symbols-rounded text-green-400/60" style="font-size:14px">lock</span>
            اتصال آمن ومشفر — للمصرح لهم فقط
        </p>

    </div>
</div>

@endsection

@section('script')
<script>
function togglePass() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('passIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endsection

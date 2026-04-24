@extends('layouts.web.app')

@section('title', 'إنشاء حساب جديد')

@section('style')
<style>

    /* ─── Split Layout ─── */
    .auth-left {
        background: radial-gradient(ellipse 80% 70% at 60% 50%,
            #6a8fd8 0%, #3a68c8 35%, #1e4db7 65%, #1a3fa0 100%);
        position: relative;
        overflow: hidden;
    }

    /* ─── Orbs ─── */
    .orb {
        border-radius: 50%;
        filter: blur(70px);
        opacity: 0.22;
        animation: drift 9s ease-in-out infinite alternate;
        position: absolute;
        pointer-events: none;
    }
    .orb-1 { width:380px; height:380px; background:#90b8f8; top:-80px; right:-80px; animation-duration:11s; }
    .orb-2 { width:280px; height:280px; background:#D4ED57;  bottom:-60px; left:-60px; animation-duration:14s; animation-delay:-5s; }
    @keyframes drift {
        from { transform: translate(0,0) scale(1); }
        to   { transform: translate(24px,16px) scale(1.07); }
    }

    /* ─── Steps Progress ─── */
    .reg-step-line {
        height: 3px;
        border-radius: 999px;
        background: rgba(255,255,255,0.15);
        flex: 1;
        overflow: hidden;
    }
    .reg-step-line-fill {
        height: 100%;
        background: #D4ED57;
        border-radius: 999px;
        transition: width .5s ease;
    }

    /* ─── Benefit Items ─── */
    .benefit-item {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        animation: fadeUp .5s ease both;
    }
    .benefit-item:nth-child(1) { animation-delay: .1s; }
    .benefit-item:nth-child(2) { animation-delay: .2s; }
    .benefit-item:nth-child(3) { animation-delay: .3s; }
    .benefit-item:nth-child(4) { animation-delay: .4s; }
    @keyframes fadeUp {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* ─── Form Input ─── */
    .auth-input {
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
    }
    .auth-input:focus {
        border-color: #174DAD;
        box-shadow: 0 0 0 4px rgba(23,77,173,.1);
    }
    .auth-input::placeholder { color: #b0bec5; }
    .auth-input.error { border-color: #f87171; }

    /* ─── Password Toggle ─── */
    .pass-wrap { position: relative; }
    .pass-toggle {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        display: flex;
        align-items: center;
        transition: color .2s;
    }
    .pass-toggle:hover { color: #174DAD; }

    /* ─── Password Strength ─── */
    .strength-bar {
        height: 4px;
        border-radius: 999px;
        background: #e5e7eb;
        overflow: hidden;
        flex: 1;
    }
    .strength-fill {
        height: 100%;
        border-radius: 999px;
        transition: width .4s ease, background .4s ease;
    }

    /* ─── Page Enter Animation ─── */
    .auth-card {
        animation: cardIn .55s cubic-bezier(.4,0,.2,1) both;
    }
    @keyframes cardIn {
        from { opacity:0; transform:translateY(28px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* ─── Gender Pill ─── */
    .gender-pill {
        cursor: pointer;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .75rem;
        border-radius: 14px;
        border: 2px solid #e5e7eb;
        background: #fff;
        font-size: .85rem;
        font-weight: 800;
        color: #6b7280;
        transition: all .22s;
        font-family: 'Cairo', sans-serif;
    }
    .gender-pill:hover   { border-color: #174DAD; color: #174DAD; background: #eff5ff; }
    .gender-pill.active  { border-color: #174DAD; background: #174DAD; color: #D4ED57; }

    /* ─── Divider ─── */
    .or-line {
        display: flex;
        align-items: center;
        gap: .75rem;
        color: #d1d5db;
        font-size: .78rem;
        font-weight: 700;
    }
    .or-line::before,
    .or-line::after {
        content:'';
        flex:1;
        height:1px;
        background: #e5e7eb;
    }

    /* ─── Custom check ─── */
    .custom-check {
        width: 18px; height: 18px;
        border-radius: 6px;
        cursor: pointer;
        accent-color: #174DAD;
        flex-shrink: 0;
    }

    /* ─── Floating stat card ─── */
    .stat-float {
        background: rgba(255,255,255,0.12);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 16px;
        padding: .75rem 1.1rem;
        animation: floatCard 5s ease-in-out infinite alternate;
    }
    .stat-float:nth-child(2) { animation-delay: -2.5s; }
    @keyframes floatCard {
        from { transform: translateY(0px); }
        to   { transform: translateY(-10px); }
    }

</style>
@endsection

@section('content')

<div class="min-h-screen flex flex-col lg:flex-row" x-data="registerForm()">

    {{-- ══════════════ LEFT PANEL ══════════════ --}}
    <div class="auth-left hidden lg:flex flex-col justify-between items-center w-4/12 flex-shrink-0 p-12 xl:p-16">

        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="relative z-10">
            <img src="{{ asset('assets/logo/mindfitbro.png') }}" alt="MindFitBro" class="w-[250px] object-contain">
        </a>

        {{-- Center Content --}}
        <div class="relative z-10 flex flex-col gap-8">

            <div class="text-center">
                <h2 class="font-display text-3xl xl:text-5xl text-white font-black leading-tight mb-4">
                    ابدأ رحلتك
                    <span class="text-accent">اليوم</span>
                </h2>
                <p class="font-arabic text-white/70 text-base leading-relaxed max-w-sm">
                    انضم لأكثر من 5,000 عضو واحصل على خطتك المخصصة من أول يوم
                </p>
            </div>

            {{-- Benefits --}}
            <div class="flex flex-col gap-4">

                <div class="benefit-item">
                    <div class="w-8 h-8 rounded-xl bg-accent/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="material-symbols-rounded text-accent" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                    </div>
                    <div class="font-arabic">
                        <p class="text-white font-black text-sm">خطة تدريب مخصصة ليك</p>
                        <p class="text-white/50 text-xs">مبنية على أهدافك وجسمك تحديداً</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="w-8 h-8 rounded-xl bg-accent/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="material-symbols-rounded text-accent" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                    </div>
                    <div class="font-arabic">
                        <p class="text-white font-black text-sm">متابعة يومية مع الكوتش</p>
                        <p class="text-white/50 text-xs">مش بس خطة وخلاص</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="w-8 h-8 rounded-xl bg-accent/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="material-symbols-rounded text-accent" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                    </div>
                    <div class="font-arabic">
                        <p class="text-white font-black text-sm">مجتمع بيدعمك كل يوم</p>
                        <p class="text-white/50 text-xs">أكثر من 5,000 عضو بنفس الهدف</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="w-8 h-8 rounded-xl bg-accent/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="material-symbols-rounded text-accent" style="font-size:16px;font-variation-settings:'FILL' 1">check_circle</span>
                    </div>
                    <div class="font-arabic">
                        <p class="text-white font-black text-sm">ضمان استرداد 7 أيام</p>
                        <p class="text-white/50 text-xs">بدون أي شروط</p>
                    </div>
                </div>

            </div>

            {{-- Floating Stat --}}
            <div class="stat-float flex items-center gap-4 w-full">
                <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-rounded text-accent" style="font-size:20px;font-variation-settings:'FILL' 1">emoji_events</span>
                </div>
                <div class="font-arabic">
                    <p class="text-white font-black text-sm">98% نسبة رضا</p>
                    <p class="text-white/50 text-xs">من أعضائنا يوصون بالبرنامج</p>
                </div>
            </div>

        </div>

        {{-- Bottom --}}
        <div class="relative z-10">
            <p class="font-arabic text-white/40 text-xs leading-relaxed">
                "لأن MindFitBro مش برنامج، ده أسلوب حياة"
            </p>
        </div>

    </div>

    {{-- ══════════════ RIGHT PANEL (form) ══════════════ --}}
    <div class="flex-1 bg-lightBg flex items-center justify-center p-6 lg:p-10 min-h-screen lg:min-h-0">

        <div class="auth-card w-full max-w-md">

            {{-- Mobile Logo --}}
            <div class="flex justify-center mb-6 lg:hidden">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('assets/logo/mindfitbro-b.png') }}" alt="MindFitBro" class="w-[160px] object-contain">
                </a>
            </div>

            {{-- Heading --}}
            <div class="mb-6 text-right">
                <span class="inline-block bg-accent text-darkBg text-[11px] font-black tracking-widest px-4 py-1.5 rounded-full font-arabic mb-3">
                    مجاني للتجربة 7 أيام 🎉
                </span>
                <h1 class="font-display text-3xl lg:text-4xl font-black text-textColor">
                    إنشاء حساب
                </h1>
                <p class="font-arabic text-gray-400 text-sm mt-1">
                    عندك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">سجّل دخولك</a>
                </p>
            </div>

            {{-- Error Alert --}}
            @if ($errors->any())
            <div class="mb-5 rounded-2xl bg-red-50 border border-red-200 p-4 flex items-start gap-3 font-arabic">
                <span class="material-symbols-rounded text-red-500 flex-shrink-0" style="font-size:20px">error</span>
                <div class="text-sm text-red-600 font-semibold">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('register.post') }}" class="flex flex-col gap-4">
                @csrf

                {{-- Name --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-black text-textColor font-arabic">الاسم الكامل</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="مثال: عبدالله عماد خليفه"
                        autofocus
                        autocomplete="name"
                        class="auth-input @error('name') error @enderror"
                    >
                    @error('name')
                        <p class="text-xs text-red-500 font-arabic font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- User Name --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-black text-textColor font-arabic">اسم المستخدم</label>
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="مثال: proteams123"
                        autofocus
                        autocomplete="username"
                        class="auth-input @error('username') error @enderror"
                    >
                    @error('username')
                        <p class="text-xs text-red-500 font-arabic font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-black text-textColor font-arabic">رقم الجوال</label>
                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="+966 5xx xxx xxx"
                        autocomplete="tel"
                        class="auth-input @error('phone') error @enderror"
                    >
                    @error('phone')
                        <p class="text-xs text-red-500 font-arabic font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-black text-textColor font-arabic">البريد الإلكتروني</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="example@email.com"
                        autocomplete="email"
                        class="auth-input @error('email') error @enderror"
                    >
                    @error('email')
                        <p class="text-xs text-red-500 font-arabic font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gender --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-black text-textColor font-arabic">الجنس</label>
                    <div class="flex gap-3">
                        <button type="button"
                            class="gender-pill"
                            :class="{ active: gender === 'male' }"
                            @click="gender = 'male'">
                            <span class="material-symbols-rounded" style="font-size:18px">male</span>
                            ذكر
                        </button>
                        <button type="button"
                            class="gender-pill"
                            :class="{ active: gender === 'female' }"
                            @click="gender = 'female'">
                            <span class="material-symbols-rounded" style="font-size:18px">female</span>
                            أنثى
                        </button>
                    </div>
                    <input type="hidden" name="gender" :value="gender">
                </div>

                {{-- Password --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-black text-textColor font-arabic">كلمة السر</label>
                    <div class="pass-wrap">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="••••••••"
                            autocomplete="new-password"
                            x-model="password"
                            @input="checkStrength()"
                            class="auth-input @error('password') error @enderror"
                        >
                        <button type="button" class="pass-toggle" onclick="togglePass('password', this)">
                            <span class="material-symbols-rounded" style="font-size:20px">visibility</span>
                        </button>
                    </div>

                    {{-- Strength Indicator --}}
                    <div x-show="password.length > 0" x-transition class="flex flex-col gap-1.5">
                        <div class="flex gap-1.5">
                            <template x-for="i in 4" :key="i">
                                <div class="strength-bar">
                                    <div class="strength-fill"
                                        :style="`width:${strength >= i ? 100 : 0}%; background:${strengthColor}`">
                                    </div>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs font-bold font-arabic transition-colors"
                            :style="`color:${strengthColor}`"
                            x-text="strengthLabel">
                        </p>
                    </div>

                    @error('password')
                        <p class="text-xs text-red-500 font-arabic font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-black text-textColor font-arabic">تأكيد كلمة السر</label>
                    <div class="pass-wrap">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="••••••••"
                            autocomplete="new-password"
                            x-model="passwordConfirm"
                            class="auth-input"
                            :class="passwordConfirm && password !== passwordConfirm ? 'error' : ''"
                        >
                        <button type="button" class="pass-toggle" onclick="togglePass('password_confirmation', this)">
                            <span class="material-symbols-rounded" style="font-size:20px">visibility</span>
                        </button>
                    </div>
                    <p x-show="passwordConfirm && password !== passwordConfirm"
                        class="text-xs text-red-500 font-arabic font-semibold">
                        كلمتا السر غير متطابقتين
                    </p>
                </div>

                {{-- Terms --}}
                <div class="flex items-start gap-2.5 font-arabic mt-1">
                    <input type="checkbox" name="terms" id="terms" class="custom-check mt-0.5">
                    <label for="terms" class="text-sm text-gray-500 font-semibold cursor-pointer leading-relaxed">
                        أوافق على
                        <a href="{{ route('terms-of-service') }}" class="text-primary font-bold hover:underline">الشروط والأحكام</a>
                        و
                        <a href="{{ route('privacy-policy') }}" class="text-primary font-bold hover:underline">سياسة الخصوصية</a>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="group font-arabic text-textColor bg-accent px-5 py-3.5 rounded-full text-base font-black flex justify-center items-center gap-2 transition hover:bg-yellow-300 w-full mt-1">
                    إنشاء الحساب
                    <svg class="transition-transform duration-300 group-hover:-translate-x-2"
                        width="22" height="12" viewBox="0 0 29 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.000447464 5.68288V8.31848H1.36843L1.36822 5.68288H0.000447464ZM2.80722 2.71685C2.60162 2.71685 2.40833 2.7969 2.26296 2.94233C2.11758 3.08773 2.03755 3.28102 2.03756 3.4866L2.03772 5.34545L2.03785 5.34811L2.03772 5.35076L2.03813 10.5141C2.03819 10.9384 2.38346 11.2836 2.80778 11.2836H4.10235L4.10172 2.71684L2.80722 2.71685ZM6.81911 0.22537C6.67374 0.0800182 6.48051 1.07288e-06 6.27496 1.07288e-06L5.54063 0.000130946C5.11631 0.00017794 4.77111 0.345439 4.77111 0.769769L4.7719 11.616L4.77202 11.6184L4.7719 11.6207L4.77202 13.2304C4.77202 13.436 4.8521 13.6292 4.9975 13.7746C5.14287 13.9199 5.3361 14 5.54167 14L6.27581 13.9999C6.70015 13.9998 7.04538 13.6545 7.04535 13.2302L7.04508 8.65474L7.04498 8.65282L7.04508 8.65088L7.04461 0.76958C7.04459 0.564018 6.96451 0.370721 6.81911 0.22537ZM7.71443 5.68239L7.71458 8.31799L28.5106 8.31717L28.5107 5.68156L7.71443 5.68239Z" fill="#202020"/>
                    </svg>
                </button>
            </form>

            {{-- Trust --}}
            <p class="flex items-center justify-center gap-2 text-gray-400 text-xs font-arabic font-semibold mt-6">
                <span class="material-symbols-rounded text-green-500" style="font-size:16px">lock</span>
                بياناتك محمية بالكامل — SSL 256-bit
            </p>

        </div>
    </div>

</div>

@endsection

@section('script')
<script>
function registerForm() {
    return {
        gender         : '',
        password       : '',
        passwordConfirm: '',
        strength       : 0,
        strengthColor  : '#e5e7eb',
        strengthLabel  : '',

        checkStrength() {
            const p = this.password;
            let score = 0;
            if (p.length >= 8)               score++;
            if (/[A-Z]/.test(p))             score++;
            if (/[0-9]/.test(p))             score++;
            if (/[^A-Za-z0-9]/.test(p))      score++;

            this.strength = score;

            const map = {
                0: { color:'#e5e7eb',   label:'' },
                1: { color:'#f87171',   label:'ضعيفة جداً' },
                2: { color:'#fb923c',   label:'ضعيفة' },
                3: { color:'#facc15',   label:'متوسطة' },
                4: { color:'#4ade80',   label:'قوية 💪' },
            };

            this.strengthColor = map[score].color;
            this.strengthLabel = map[score].label;
        },
    }
}

function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('.material-symbols-rounded');
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

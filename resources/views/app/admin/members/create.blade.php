@extends('layouts.admin.app')

@section('title', 'إضافة عضو جديد')
@section('page-title', 'إضافة عضو جديد')
@section('page-subtitle', 'إنشاء حساب عضو يدوياً مع كامل بياناته')

@section('style')
<style>
    /* ─── Section Card ─── */
    .form-card {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 20px;
        overflow: hidden;
    }
    .form-card-header {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: 1rem 1.4rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfd;
    }
    .form-card-icon {
        width: 38px; height: 38px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .form-card-body { padding: 1.5rem 1.4rem; }

    /* ─── Field ─── */
    .field-label {
        display: block;
        font-size: .78rem;
        font-weight: 900;
        color: #64748b;
        margin-bottom: .45rem;
    }
    .field-label span { color: #ef4444; margin-right: 2px; }

    .form-input {
        width: 100%;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: .72rem 1rem;
        font-size: .88rem;
        font-weight: 600;
        color: #1e293b;
        font-family: 'Cairo', sans-serif;
        outline: none;
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .form-input:focus {
        background: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,.1);
    }
    .form-input::placeholder { color: #b0bec5; }
    .form-input.is-error { border-color: #f87171; background: #fff5f5; }
    .form-input.is-error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.1); }

    /* ─── Radio / Toggle Group ─── */
    .radio-group {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
    }
    .radio-option {
        flex: 1;
        min-width: 80px;
    }
    .radio-option input[type="radio"] { display: none; }
    .radio-option label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        padding: .65rem .75rem;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-size: .82rem;
        font-weight: 800;
        color: #64748b;
        cursor: pointer;
        transition: all .18s;
        text-align: center;
        white-space: nowrap;
    }
    .radio-option input:checked + label {
        border-color: var(--radio-color, #3b82f6);
        background: var(--radio-bg, #eff6ff);
        color: var(--radio-color, #3b82f6);
    }

    /* ─── Password Strength ─── */
    .pass-wrap { position: relative; }
    .pass-toggle {
        position: absolute; left: 14px; top: 50%;
        transform: translateY(-50%);
        color: #94a3b8; cursor: pointer;
        background: none; border: none; padding: 0;
        display: flex; align-items: center; transition: color .2s;
    }
    .pass-toggle:hover { color: #3b82f6; }

    .strength-bar {
        height: 4px; border-radius: 4px; background: #e2e8f0;
        overflow: hidden; margin-top: .5rem;
    }
    .strength-fill { height: 100%; border-radius: 4px; transition: width .3s, background .3s; width: 0; }

    /* ─── Error text ─── */
    .field-error { font-size: .75rem; color: #ef4444; font-weight: 700; margin-top: .35rem; }

    /* ─── Hint ─── */
    .field-hint { font-size: .74rem; color: #94a3b8; font-weight: 600; margin-top: .35rem; }

    /* ─── Submit bar ─── */
    .submit-bar {
        position: sticky;
        bottom: 1rem;
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(12px);
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1rem 1.4rem;
        display: flex;
        align-items: center;
        justify-content: between;
        gap: .75rem;
        box-shadow: 0 8px 32px rgba(0,0,0,.08);
        margin-top: 1.5rem;
    }
</style>
@endsection

@section('content')

{{-- ── Back + breadcrumb ── --}}
<div class="flex items-center gap-2 mb-6 text-sm font-bold text-slate-400">
    <a href="{{ route('admin.members.index') }}"
       class="flex items-center gap-1.5 hover:text-blue-500 transition">
        <span class="material-symbols-rounded" style="font-size:17px">arrow_forward_ios</span>
        الأعضاء
    </a>
    <span class="material-symbols-rounded" style="font-size:15px">chevron_left</span>
    <span class="text-slate-600">إضافة عضو جديد</span>
</div>

{{-- ── Flash error (e.g. mail send failure) ── --}}
@if(session('error'))
<div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-2xl px-5 py-4 mb-5">
    <span class="material-symbols-rounded text-red-500 flex-shrink-0" style="font-size:20px;font-variation-settings:'FILL' 1">error</span>
    <p class="text-red-700 font-black text-sm">{{ session('error') }}</p>
</div>
@endif

{{-- ── Validation errors global ── --}}
@if ($errors->any())
<div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-2xl px-5 py-4 mb-5">
    <span class="material-symbols-rounded text-red-500 flex-shrink-0 mt-0.5" style="font-size:20px">error</span>
    <div>
        <p class="text-red-700 font-black text-sm mb-1">يوجد {{ $errors->count() }} خطأ في البيانات المدخلة</p>
        <ul class="text-red-600 text-xs font-semibold space-y-0.5">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST" action="{{ route('admin.members.store') }}" id="createForm">
@csrf

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- ════════════ COLUMN: main (2/3) ════════════ --}}
    <div class="xl:col-span-2 flex flex-col gap-5">

        {{-- ── Section 1: Account Info ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon bg-blue-50">
                    <span class="material-symbols-rounded text-blue-500" style="font-size:18px;font-variation-settings:'FILL' 1">person</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">معلومات الحساب</p>
                    <p class="text-[11px] text-slate-400 font-semibold">البيانات الأساسية للعضو</p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Name --}}
                    <div>
                        <label class="field-label">الاسم الكامل <span>*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="مثال: أحمد محمد"
                               class="form-input @error('name') is-error @enderror">
                        @error('name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="field-label">اسم المستخدم <span>*</span></label>
                        <input type="text" name="username" value="{{ old('username') }}"
                               placeholder="مثال: ahmed_99" dir="ltr"
                               class="form-input @error('username') is-error @enderror"
                               autocomplete="off">
                        @error('username')<p class="field-error">{{ $message }}</p>@enderror
                        <p class="field-hint">أحرف إنجليزية وأرقام وشرطات فقط — حساس لحالة الأحرف</p>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="field-label">البريد الإلكتروني <span>*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="example@email.com" dir="ltr"
                               class="form-input @error('email') is-error @enderror">
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="field-label">رقم الهاتف</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="+20 100 000 0000" dir="ltr"
                               class="form-input @error('phone') is-error @enderror">
                        @error('phone')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                </div>

                {{-- Gender --}}
                <div class="mt-4">
                    <label class="field-label">الجنس <span>*</span></label>
                    <div class="radio-group">
                        <div class="radio-option" style="--radio-color:#3b82f6;--radio-bg:#eff6ff">
                            <input type="radio" name="gender" id="gender_male" value="male"
                                   {{ old('gender') === 'male' ? 'checked' : '' }}>
                            <label for="gender_male">
                                <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">man</span>
                                ذكر
                            </label>
                        </div>
                        <div class="radio-option" style="--radio-color:#ec4899;--radio-bg:#fdf2f8">
                            <input type="radio" name="gender" id="gender_female" value="female"
                                   {{ old('gender') === 'female' ? 'checked' : '' }}>
                            <label for="gender_female">
                                <span class="material-symbols-rounded" style="font-size:16px;font-variation-settings:'FILL' 1">woman</span>
                                أنثى
                            </label>
                        </div>
                    </div>
                    @error('gender')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ── Section 2: Password ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon bg-amber-50">
                    <span class="material-symbols-rounded text-amber-500" style="font-size:18px;font-variation-settings:'FILL' 1">lock</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">كلمة المرور</p>
                    <p class="text-[11px] text-slate-400 font-semibold">8 أحرف على الأقل</p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Password --}}
                    <div>
                        <label class="field-label">كلمة المرور <span>*</span></label>
                        <div class="pass-wrap">
                            <input type="password" name="password" id="password"
                                   placeholder="••••••••" dir="ltr" autocomplete="new-password"
                                   class="form-input @error('password') is-error @enderror"
                                   oninput="checkStrength(this.value)">
                            <button type="button" class="pass-toggle" onclick="togglePass('password',this)">
                                <span class="material-symbols-rounded" style="font-size:18px">visibility</span>
                            </button>
                        </div>
                        <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                        <p id="strengthLabel" class="field-hint"></p>
                        @error('password')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Confirm --}}
                    <div>
                        <label class="field-label">تأكيد كلمة المرور <span>*</span></label>
                        <div class="pass-wrap">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   placeholder="••••••••" dir="ltr" autocomplete="new-password"
                                   class="form-input">
                            <button type="button" class="pass-toggle" onclick="togglePass('password_confirmation',this)">
                                <span class="material-symbols-rounded" style="font-size:18px">visibility</span>
                            </button>
                        </div>
                        <p class="field-hint">أعد كتابة كلمة المرور للتأكيد</p>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Section 3: Fitness Profile ── --}}
        <div id="fitnessSection" class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon bg-emerald-50">
                    <span class="material-symbols-rounded text-emerald-500" style="font-size:18px;font-variation-settings:'FILL' 1">fitness_center</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">الملف الرياضي</p>
                    <p class="text-[11px] text-slate-400 font-semibold">اختياري — يمكن تعديله لاحقاً</p>
                </div>
            </div>
            <div class="form-card-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    {{-- DOB --}}
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="field-label">تاريخ الميلاد</label>
                        <input type="date" name="date_of_birth"
                               value="{{ old('date_of_birth') }}"
                               max="{{ date('Y-m-d') }}"
                               class="form-input @error('date_of_birth') is-error @enderror">
                        @error('date_of_birth')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Height --}}
                    <div>
                        <label class="field-label">الطول (سم)</label>
                        <input type="number" name="height" value="{{ old('height') }}"
                               placeholder="175" min="50" max="250" step="0.1" dir="ltr"
                               class="form-input @error('height') is-error @enderror">
                        @error('height')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Start Weight --}}
                    <div>
                        <label class="field-label">وزن البداية (كجم)</label>
                        <input type="number" name="start_weight" value="{{ old('start_weight') }}"
                               placeholder="80" min="20" max="500" step="0.1" dir="ltr"
                               class="form-input @error('start_weight') is-error @enderror">
                        @error('start_weight')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Current Weight --}}
                    <div>
                        <label class="field-label">الوزن الحالي (كجم)</label>
                        <input type="number" name="current_weight" value="{{ old('current_weight') }}"
                               placeholder="78" min="20" max="500" step="0.1" dir="ltr"
                               class="form-input @error('current_weight') is-error @enderror">
                        @error('current_weight')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Goal Weight --}}
                    <div>
                        <label class="field-label">الوزن المستهدف (كجم)</label>
                        <input type="number" name="goal_weight" value="{{ old('goal_weight') }}"
                               placeholder="70" min="20" max="500" step="0.1" dir="ltr"
                               class="form-input @error('goal_weight') is-error @enderror">
                        @error('goal_weight')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>
        </div>

    </div>{{-- end main col --}}

    {{-- ════════════ COLUMN: sidebar (1/3) ════════════ --}}
    <div class="flex flex-col gap-5">

        {{-- ── Section: Role & Status ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon bg-violet-50">
                    <span class="material-symbols-rounded text-violet-500" style="font-size:18px;font-variation-settings:'FILL' 1">admin_panel_settings</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">الصلاحيات والحالة</p>
                    <p class="text-[11px] text-slate-400 font-semibold">دور الحساب ووضعه</p>
                </div>
            </div>
            <div class="form-card-body flex flex-col gap-4">

                {{-- Role --}}
                <div>
                    <label class="field-label">دور الحساب <span>*</span></label>
                    <div class="radio-group flex-col">
                        <div class="radio-option w-full" style="--radio-color:#7c3aed;--radio-bg:#f5f3ff">
                            <input type="radio" name="role" id="role_user" value="user"
                                   {{ old('role', 'user') === 'user' ? 'checked' : '' }}>
                            <label for="role_user" class="justify-start gap-3 px-4">
                                <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1">person</span>
                                <div class="text-right">
                                    <p class="font-black text-sm">عضو</p>
                                    <p class="text-[11px] font-semibold opacity-70">وصول عادي للمنصة</p>
                                </div>
                            </label>
                        </div>
                        <div class="radio-option w-full" style="--radio-color:#0891b2;--radio-bg:#ecfeff">
                            <input type="radio" name="role" id="role_coach" value="coach"
                                   {{ old('role') === 'coach' ? 'checked' : '' }}>
                            <label for="role_coach" class="justify-start gap-3 px-4">
                                <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1">sports</span>
                                <div class="text-right">
                                    <p class="font-black text-sm">مدرب</p>
                                    <p class="text-[11px] font-semibold opacity-70">صلاحيات المدرب والمتابعة</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    @error('role')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="field-label">حالة الحساب <span>*</span></label>
                    <div class="radio-group flex-col">
                        <div class="radio-option w-full" style="--radio-color:#16a34a;--radio-bg:#f0fdf4">
                            <input type="radio" name="status" id="status_active" value="active"
                                   {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
                            <label for="status_active" class="justify-start gap-3 px-4">
                                <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1">check_circle</span>
                                <div class="text-right">
                                    <p class="font-black text-sm">نشط</p>
                                    <p class="text-[11px] font-semibold opacity-70">يمكنه تسجيل الدخول</p>
                                </div>
                            </label>
                        </div>
                        <div class="radio-option w-full" style="--radio-color:#64748b;--radio-bg:#f8fafc">
                            <input type="radio" name="status" id="status_inactive" value="inactive"
                                   {{ old('status') === 'inactive' ? 'checked' : '' }}>
                            <label for="status_inactive" class="justify-start gap-3 px-4">
                                <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1">pause_circle</span>
                                <div class="text-right">
                                    <p class="font-black text-sm">غير نشط</p>
                                    <p class="text-[11px] font-semibold opacity-70">حساب معطّل مؤقتاً</p>
                                </div>
                            </label>
                        </div>
                        <div class="radio-option w-full" style="--radio-color:#dc2626;--radio-bg:#fff5f5">
                            <input type="radio" name="status" id="status_banned" value="banned"
                                   {{ old('status') === 'banned' ? 'checked' : '' }}>
                            <label for="status_banned" class="justify-start gap-3 px-4">
                                <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1">block</span>
                                <div class="text-right">
                                    <p class="font-black text-sm">محظور</p>
                                    <p class="text-[11px] font-semibold opacity-70">ممنوع من الدخول</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    @error('status')<p class="field-error">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- ── Section: Summary ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon bg-slate-100">
                    <span class="material-symbols-rounded text-slate-500" style="font-size:18px;font-variation-settings:'FILL' 1">info</span>
                </div>
                <div>
                    <p class="font-black text-slate-800 text-sm">ملاحظات</p>
                </div>
            </div>
            <div class="form-card-body flex flex-col gap-3">
                <div class="flex items-start gap-2.5">
                    <span class="material-symbols-rounded text-blue-400 flex-shrink-0 mt-0.5" style="font-size:16px;font-variation-settings:'FILL' 1">info</span>
                    <p class="text-xs text-slate-500 font-semibold leading-relaxed">سيتم قبول شروط الاستخدام تلقائياً نيابةً عن العضو عند الإنشاء اليدوي.</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="material-symbols-rounded text-amber-400 flex-shrink-0 mt-0.5" style="font-size:16px;font-variation-settings:'FILL' 1">warning</span>
                    <p class="text-xs text-slate-500 font-semibold leading-relaxed">يُنصح بإرسال كلمة المرور للعضو بطريقة آمنة بعد إنشاء الحساب.</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="material-symbols-rounded text-emerald-400 flex-shrink-0 mt-0.5" style="font-size:16px;font-variation-settings:'FILL' 1">fitness_center</span>
                    <p class="text-xs text-slate-500 font-semibold leading-relaxed">بيانات الملف الرياضي اختيارية ويمكن إضافتها أو تعديلها لاحقاً من صفحة العضو.</p>
                </div>
            </div>
        </div>

        {{-- ── Action Buttons ── --}}
        <div class="flex flex-col gap-2.5">
            <button type="submit"
                class="w-full flex items-center justify-center gap-2.5 bg-blue-500 hover:bg-blue-600
                       text-white font-black text-sm py-3 rounded-2xl transition shadow-sm shadow-blue-200">
                <span class="material-symbols-rounded" style="font-size:18px;font-variation-settings:'FILL' 1">person_add</span>
                إنشاء الحساب
            </button>
            <a href="{{ route('admin.members.index') }}"
               class="w-full flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200
                      text-slate-600 font-black text-sm py-3 rounded-2xl transition">
                <span class="material-symbols-rounded" style="font-size:16px">arrow_forward_ios</span>
                العودة للقائمة
            </a>
        </div>

    </div>{{-- end sidebar col --}}

</div>{{-- end grid --}}
</form>

@endsection

@section('script')
<script>
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

    // ── Fitness section visibility ──
    function syncFitnessSection() {
        const isCoach = document.getElementById('role_coach').checked;
        const section = document.getElementById('fitnessSection');
        if (isCoach) {
            section.style.opacity = '0';
            section.style.transform = 'translateY(-8px)';
            section.style.pointerEvents = 'none';
            setTimeout(() => { section.style.display = 'none'; }, 220);
        } else {
            section.style.display = '';
            requestAnimationFrame(() => {
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
                section.style.pointerEvents = '';
            });
        }
    }
    document.getElementById('role_user').addEventListener('change', syncFitnessSection);
    document.getElementById('role_coach').addEventListener('change', syncFitnessSection);

    // ── Init on page load ──
    document.getElementById('fitnessSection').style.transition = 'opacity .22s ease, transform .22s ease';
    syncFitnessSection();

    function checkStrength(val) {
        const fill  = document.getElementById('strengthFill');
        const label = document.getElementById('strengthLabel');
        let score = 0;
        if (val.length >= 8)  score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { w: '0%',   color: '#e2e8f0', text: '' },
            { w: '25%',  color: '#ef4444', text: 'ضعيفة جداً' },
            { w: '50%',  color: '#f97316', text: 'ضعيفة' },
            { w: '75%',  color: '#eab308', text: 'متوسطة' },
            { w: '100%', color: '#22c55e', text: 'قوية' },
        ];
        const lvl = val.length === 0 ? levels[0] : levels[score] || levels[1];
        fill.style.width      = lvl.w;
        fill.style.background = lvl.color;
        label.textContent     = lvl.text;
        label.style.color     = lvl.color;
    }
</script>
@endsection

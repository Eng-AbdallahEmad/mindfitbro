@extends('layouts.web.app')
@section('title', 'استمارة التقييم')

@section('style')
<style>
/* ── Layout ──────────────────────────────────────────── */
.asmt-wrap {
    min-height: 100vh;
    background: #F0F4FB;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 2rem 1rem 4rem;
    font-family: 'Cairo', sans-serif;
    direction: rtl;
}
.asmt-card {
    background: #fff;
    border-radius: 28px;
    box-shadow: 0 8px 40px rgba(23,77,173,0.10);
    width: 100%;
    max-width: 600px;
    overflow: hidden;
}
.asmt-header {
    background: linear-gradient(135deg, #174DAD 0%, #0f3a87 100%);
    padding: 36px 36px 28px;
    text-align: center;
}
.asmt-badge {
    display: inline-block;
    background: #D4ED57;
    color: #1C1C1C;
    font-size: 11px;
    font-weight: 900;
    padding: 4px 14px;
    border-radius: 20px;
    margin-bottom: 10px;
}
.asmt-title { color: #fff; font-size: 20px; font-weight: 900; margin-bottom: 4px; }
.asmt-sub   { color: rgba(255,255,255,0.65); font-size: 12px; }

/* ── Progress bar ─────────────────────────────────── */
.step-bar {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 20px 36px 0;
}
.step-dot {
    flex: 1;
    height: 4px;
    border-radius: 9999px;
    background: #E5E7EB;
    transition: background .3s;
}
.step-dot.done  { background: #16a34a; }
.step-dot.active{ background: #174DAD; }

/* ── Body ─────────────────────────────────────────── */
.asmt-body { padding: 28px 36px 36px; }

.step-title {
    font-size: 15px;
    font-weight: 900;
    color: #174DAD;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.step-count {
    font-size: 11px;
    color: #9CA3AF;
    font-weight: 700;
    margin-bottom: 20px;
}

/* ── Field styles ─────────────────────────────────── */
.f-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #6B7280;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.f-input {
    width: 100%;
    background: #F4F7FF;
    border: 2px solid #e0e8ff;
    border-radius: 12px;
    padding: .75rem 1rem;
    font-size: .875rem;
    color: #1c1c1c;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    font-family: 'Cairo', sans-serif;
    text-align: right;
    direction: rtl;
}
.f-input:focus { border-color: #174DAD; box-shadow: 0 0 0 3px rgba(23,77,173,.10); }
.f-error { font-size: 11px; color: #EF4444; margin-top: 3px; font-weight: 600; }
.f-group { margin-bottom: 18px; }

/* ── Radio / Chip grid ────────────────────────────── */
.chip-grid { display: flex; flex-wrap: wrap; gap: 8px; }
.chip-opt input[type="radio"],
.chip-opt input[type="checkbox"] { display: none; }
.chip-opt label {
    display: block;
    padding: 7px 14px;
    border-radius: 10px;
    border: 2px solid #e0e8ff;
    background: #F4F7FF;
    font-size: 13px;
    font-weight: 700;
    color: #6B7280;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.chip-opt input[type="radio"]:checked + label,
.chip-opt input[type="checkbox"]:checked + label {
    border-color: #174DAD;
    background: #EBF0FF;
    color: #174DAD;
}
.chip-opt.danger input[type="radio"]:checked + label {
    border-color: #EF4444;
    background: #FEF2F2;
    color: #DC2626;
}
.chip-opt.success input[type="radio"]:checked + label {
    border-color: #16a34a;
    background: #F0FDF4;
    color: #15803D;
}

/* ── Range slider ─────────────────────────────────── */
.range-wrap { position: relative; padding-bottom: 4px; }
.range-wrap input[type="range"] {
    width: 100%;
    accent-color: #174DAD;
    height: 6px;
}
.range-val {
    display: inline-block;
    background: #174DAD;
    color: #fff;
    font-size: 13px;
    font-weight: 900;
    padding: 2px 10px;
    border-radius: 20px;
    margin-top: 4px;
    font-family: 'Cairo', sans-serif;
}

/* ── Divider ──────────────────────────────────────── */
.asmt-divider {
    display: flex; align-items: center; gap: 10px;
    margin: 16px 0 14px;
    color: #9CA3AF; font-size: 11px; font-weight: 700;
}
.asmt-divider::before, .asmt-divider::after {
    content: ''; flex: 1; height: 1px; background: #E5E7EB;
}

/* ── Nav buttons ──────────────────────────────────── */
.btn-row { display: flex; gap: 10px; margin-top: 24px; }
.btn-next {
    flex: 1;
    background: #174DAD; color: #fff;
    font-size: 14px; font-weight: 900;
    padding: .85rem; border-radius: 12px;
    border: none; cursor: pointer;
    font-family: 'Cairo', sans-serif;
    transition: opacity .2s;
}
.btn-next:hover { opacity: .9; }
.btn-prev {
    background: #F4F7FF; color: #374151;
    border: 2px solid #e0e8ff;
    font-size: 14px; font-weight: 900;
    padding: .85rem 1.4rem; border-radius: 12px;
    cursor: pointer; font-family: 'Cairo', sans-serif;
    transition: border-color .2s;
}
.btn-prev:hover { border-color: #174DAD; color: #174DAD; }
.btn-submit {
    flex: 1;
    background: #16a34a; color: #fff;
    font-size: 14px; font-weight: 900;
    padding: .85rem; border-radius: 12px;
    border: none; cursor: pointer;
    font-family: 'Cairo', sans-serif;
    transition: opacity .2s;
}
.btn-submit:hover { opacity: .9; }

/* ── Declaration checkbox ─────────────────────────── */
.decl-box {
    display: flex; align-items: flex-start; gap: 10px;
    background: #F0FDF4; border: 1.5px solid #BBF7D0;
    border-radius: 14px; padding: 16px;
}
.decl-box input[type="checkbox"] { margin-top: 3px; width: 16px; height: 16px; accent-color: #16a34a; flex-shrink: 0; }
.decl-text { font-size: 12px; color: #166534; line-height: 1.7; font-weight: 600; }

@media (max-width: 480px) {
    .asmt-body { padding: 20px 20px 28px; }
    .step-bar  { padding: 16px 20px 0; }
    .asmt-header { padding: 28px 20px 20px; }
}
</style>
@endsection

@section('content')
<div class="asmt-wrap" x-data="assessmentForm()">

    <div class="asmt-card">

        {{-- Header --}}
        <div class="asmt-header">
            <span class="material-symbols-rounded" style="font-size:34px;color:#D4ED57;font-variation-settings:'FILL' 1;display:block;margin-bottom:8px">assignment_ind</span>
            <div class="asmt-badge">استمارة التقييم الموحدة</div>
            <h1 class="asmt-title">تقييم المتدرب — {{ $subscription->plan->name ?? 'الباقة' }}</h1>
            <p class="asmt-sub">أجب بصدق لنتمكن من تصميم برنامجك المثالي</p>
        </div>

        {{-- Step progress bar --}}
        <div class="step-bar">
            @for ($i = 1; $i <= 6; $i++)
                <div class="step-dot"
                     :class="{ done: {{ $i }} < currentStep, active: {{ $i }} === currentStep }"></div>
            @endfor
        </div>

        {{-- Body --}}
        <div class="asmt-body">

            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 text-sm font-semibold">
                <p class="font-black mb-1">يوجد أخطاء في النموذج — راجع الحقول التالية:</p>
                <ul class="list-none space-y-0.5">
                    @foreach($errors->all() as $e)
                        <li>• {{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('info'))
            <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl p-3 text-sm font-semibold">
                {{ session('info') }}
            </div>
            @endif

            <form method="POST" action="{{ route('assessment.store', $subscription) }}" id="assessmentForm">
                @csrf

                {{-- ════════════════════════════════
                     STEP 1 — المعلومات الأساسية
                ════════════════════════════════ --}}
                <div x-show="currentStep === 1" x-cloak>
                    <div class="step-title">
                        <span class="material-symbols-rounded" style="font-size:20px;font-variation-settings:'FILL' 1">person</span>
                        المعلومات الأساسية
                    </div>
                    <p class="step-count">الخطوة 1 من 6</p>

                    <div class="f-group">
                        <label class="f-label" for="date_of_birth">تاريخ الميلاد</label>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                               value="{{ old('date_of_birth', $assessment?->date_of_birth?->format('Y-m-d')) }}"
                               class="f-input" style="direction:ltr;text-align:left;" max="{{ now()->subYears(10)->format('Y-m-d') }}">
                        @error('date_of_birth')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="height">الطول (سم)</label>
                        <input type="number" id="height" name="height" min="100" max="250" step="0.5"
                               value="{{ old('height', $assessment?->height) }}"
                               placeholder="مثال: 175" class="f-input" style="direction:ltr;text-align:left;">
                        @error('height')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="current_weight">الوزن الحالي (كجم)</label>
                        <input type="number" id="current_weight" name="current_weight" min="30" max="300" step="0.5"
                               value="{{ old('current_weight', $assessment?->current_weight) }}"
                               placeholder="مثال: 80" class="f-input" style="direction:ltr;text-align:left;">
                        @error('current_weight')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="target_weight">الوزن المستهدف (كجم)</label>
                        <input type="number" id="target_weight" name="target_weight" min="30" max="300" step="0.5"
                               value="{{ old('target_weight', $assessment?->target_weight) }}"
                               placeholder="مثال: 70" class="f-input" style="direction:ltr;text-align:left;">
                        @error('target_weight')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="btn-row">
                        <button type="button" class="btn-next" @click="nextStep(1)">
                            التالي <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">arrow_back</span>
                        </button>
                    </div>
                </div>

                {{-- ════════════════════════════════
                     STEP 2 — أهداف التدريب
                ════════════════════════════════ --}}
                <div x-show="currentStep === 2" x-cloak>
                    <div class="step-title">
                        <span class="material-symbols-rounded" style="font-size:20px;font-variation-settings:'FILL' 1">fitness_center</span>
                        أهداف التدريب
                    </div>
                    <p class="step-count">الخطوة 2 من 6</p>

                    <div class="f-group">
                        <label class="f-label">الهدف الرئيسي</label>
                        <div class="chip-grid">
                            @foreach(['weight_loss'=>'خسارة الوزن','muscle_gain'=>'بناء العضلات','endurance'=>'اللياقة والتحمل','flexibility'=>'المرونة','general_fitness'=>'لياقة عامة','other'=>'أخرى'] as $val => $label)
                            <div class="chip-opt">
                                <input type="radio" id="goal_{{ $val }}" name="primary_goal" value="{{ $val }}"
                                       {{ old('primary_goal', $assessment?->primary_goal) === $val ? 'checked' : '' }}>
                                <label for="goal_{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('primary_goal')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">مستوى الخبرة</label>
                        <div class="chip-grid">
                            @foreach(['beginner'=>'مبتدئ','intermediate'=>'متوسط','advanced'=>'متقدم'] as $val => $label)
                            <div class="chip-opt">
                                <input type="radio" id="exp_{{ $val }}" name="experience_level" value="{{ $val }}"
                                       {{ old('experience_level', $assessment?->experience_level) === $val ? 'checked' : '' }}>
                                <label for="exp_{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('experience_level')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="workout_days_per_week">عدد أيام التمرين في الأسبوع</label>
                        <div class="chip-grid">
                            @for($d = 2; $d <= 6; $d++)
                            <div class="chip-opt">
                                <input type="radio" id="days_{{ $d }}" name="workout_days_per_week" value="{{ $d }}"
                                       {{ (int)old('workout_days_per_week', $assessment?->workout_days_per_week) === $d ? 'checked' : '' }}>
                                <label for="days_{{ $d }}">{{ $d }} أيام</label>
                            </div>
                            @endfor
                        </div>
                        @error('workout_days_per_week')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">مدة الجلسة الواحدة</label>
                        <div class="chip-grid">
                            @foreach([30=>'30 دقيقة',45=>'45 دقيقة',60=>'ساعة',90=>'ساعة ونصف'] as $val => $label)
                            <div class="chip-opt">
                                <input type="radio" id="dur_{{ $val }}" name="session_duration_minutes" value="{{ $val }}"
                                       {{ (int)old('session_duration_minutes', $assessment?->session_duration_minutes) === $val ? 'checked' : '' }}>
                                <label for="dur_{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('session_duration_minutes')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="asmt-divider">تفاصيل إضافية</div>

                    <div class="f-group">
                        <label class="f-label">مكان التدريب</label>
                        <div class="chip-grid">
                            @foreach(['home'=>'المنزل','gym'=>'الجيم','outdoor'=>'الهواء الطلق','both'=>'متعدد'] as $val => $label)
                            <div class="chip-opt">
                                <input type="radio" id="loc_{{ $val }}" name="training_location" value="{{ $val }}"
                                       {{ old('training_location', $assessment?->training_details['location'] ?? '') === $val ? 'checked' : '' }}>
                                <label for="loc_{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('training_location')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">توافر المعدات</label>
                        <div class="chip-grid">
                            @foreach(['none'=>'لا توجد','basic'=>'أساسية','full'=>'كاملة'] as $val => $label)
                            <div class="chip-opt">
                                <input type="radio" id="eq_{{ $val }}" name="equipment_level" value="{{ $val }}"
                                       {{ old('equipment_level', $assessment?->training_details['equipment_level'] ?? '') === $val ? 'checked' : '' }}>
                                <label for="eq_{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('equipment_level')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">المدة المستهدفة للبرنامج</label>
                        <div class="chip-grid">
                            @foreach([12=>'3 أشهر',16=>'4 أشهر',24=>'6 أشهر'] as $val => $label)
                            <div class="chip-opt">
                                <input type="radio" id="tdur_{{ $val }}" name="target_duration_weeks" value="{{ $val }}"
                                       {{ (int)old('target_duration_weeks', $assessment?->training_details['target_duration_weeks'] ?? 12) === $val ? 'checked' : '' }}>
                                <label for="tdur_{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('target_duration_weeks')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">تقييم لياقتك الحالية (1 = ضعيف جداً، 10 = ممتاز)</label>
                        <div class="range-wrap">
                            <input type="range" name="fitness_score" id="fitness_score"
                                   min="1" max="10" step="1"
                                   value="{{ old('fitness_score', $assessment?->training_details['fitness_score'] ?? 5) }}"
                                   x-model="fitnessScore">
                            <span class="range-val" x-text="fitnessScore"></span>
                        </div>
                        @error('fitness_score')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="btn-row">
                        <button type="button" class="btn-prev" @click="prevStep()">
                            <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">arrow_forward</span>
                            السابق
                        </button>
                        <button type="button" class="btn-next" @click="nextStep(2)">
                            التالي <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">arrow_back</span>
                        </button>
                    </div>
                </div>

                {{-- ════════════════════════════════
                     STEP 3 — التغذية
                ════════════════════════════════ --}}
                <div x-show="currentStep === 3" x-cloak>
                    <div class="step-title">
                        <span class="material-symbols-rounded" style="font-size:20px;font-variation-settings:'FILL' 1">restaurant</span>
                        التغذية
                    </div>
                    <p class="step-count">الخطوة 3 من 6</p>

                    <div class="f-group">
                        <label class="f-label">نوع النظام الغذائي</label>
                        <div class="chip-grid">
                            @foreach(['normal'=>'عادي','vegetarian'=>'نباتي','vegan'=>'نباتي صارم','keto'=>'كيتو','low_carb'=>'قليل الكارب','other'=>'أخرى'] as $val => $label)
                            <div class="chip-opt">
                                <input type="radio" id="diet_{{ $val }}" name="diet_type" value="{{ $val }}"
                                       {{ old('diet_type', $assessment?->nutrition['diet_type'] ?? '') === $val ? 'checked' : '' }}>
                                <label for="diet_{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('diet_type')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="meals_count">عدد الوجبات يومياً</label>
                        <div class="chip-grid">
                            @for($m = 2; $m <= 6; $m++)
                            <div class="chip-opt">
                                <input type="radio" id="meal_{{ $m }}" name="meals_count" value="{{ $m }}"
                                       {{ (int)old('meals_count', $assessment?->nutrition['meals_count'] ?? 3) === $m ? 'checked' : '' }}>
                                <label for="meal_{{ $m }}">{{ $m }} وجبات</label>
                            </div>
                            @endfor
                        </div>
                        @error('meals_count')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">وجبات خفيفة بين الوجبات؟</label>
                        <div class="chip-grid">
                            <div class="chip-opt success">
                                <input type="radio" id="snack_yes" name="has_snacks" value="yes"
                                       {{ old('has_snacks', $assessment?->nutrition['has_snacks'] ?? '') === 'yes' ? 'checked' : '' }}>
                                <label for="snack_yes">نعم</label>
                            </div>
                            <div class="chip-opt danger">
                                <input type="radio" id="snack_no" name="has_snacks" value="no"
                                       {{ old('has_snacks', $assessment?->nutrition['has_snacks'] ?? '') === 'no' ? 'checked' : '' }}>
                                <label for="snack_no">لا</label>
                            </div>
                        </div>
                        @error('has_snacks')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">كمية الماء يومياً (لتر)</label>
                        <div class="range-wrap">
                            <input type="range" name="water_intake" id="water_intake"
                                   min="0.5" max="5" step="0.5"
                                   value="{{ old('water_intake', $assessment?->nutrition['water_intake'] ?? 2) }}"
                                   x-model="waterIntake">
                            <span class="range-val" x-text="waterIntake + ' لتر'"></span>
                        </div>
                        @error('water_intake')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">هل تتناول مكملات غذائية؟</label>
                        <div class="chip-grid">
                            <div class="chip-opt success">
                                <input type="radio" id="supp_yes" name="has_supplements" value="yes"
                                       {{ old('has_supplements', $assessment?->nutrition['has_supplements'] ?? '') === 'yes' ? 'checked' : '' }}
                                       x-model="hasSupplements">
                                <label for="supp_yes">نعم</label>
                            </div>
                            <div class="chip-opt danger">
                                <input type="radio" id="supp_no" name="has_supplements" value="no"
                                       {{ old('has_supplements', $assessment?->nutrition['has_supplements'] ?? '') === 'no' ? 'checked' : '' }}
                                       x-model="hasSupplements">
                                <label for="supp_no">لا</label>
                            </div>
                        </div>
                        @error('has_supplements')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group" x-show="hasSupplements === 'yes'">
                        <label class="f-label" for="supplements_details">اذكر المكملات التي تتناولها</label>
                        <textarea id="supplements_details" name="supplements_details" rows="2"
                                  class="f-input" placeholder="مثال: بروتين، كرياتين، فيتامين د...">{{ old('supplements_details', $assessment?->nutrition['supplements_details'] ?? '') }}</textarea>
                    </div>

                    <div class="asmt-divider">الأطعمة</div>

                    <div class="f-group">
                        <label class="f-label" for="preferred_foods">الأطعمة المفضلة (اختياري)</label>
                        <textarea id="preferred_foods" name="preferred_foods" rows="2"
                                  class="f-input" placeholder="مثال: دجاج، بيض، أرز...">{{ old('preferred_foods', $assessment?->nutrition['preferred_foods'] ?? '') }}</textarea>
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="disliked_foods">الأطعمة غير المرغوبة (اختياري)</label>
                        <textarea id="disliked_foods" name="disliked_foods" rows="2"
                                  class="f-input" placeholder="مثال: بروكلي، سمك...">{{ old('disliked_foods', $assessment?->nutrition['disliked_foods'] ?? '') }}</textarea>
                    </div>

                    <div class="btn-row">
                        <button type="button" class="btn-prev" @click="prevStep()">
                            <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">arrow_forward</span>
                            السابق
                        </button>
                        <button type="button" class="btn-next" @click="nextStep(3)">
                            التالي <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">arrow_back</span>
                        </button>
                    </div>
                </div>

                {{-- ════════════════════════════════
                     STEP 4 — الصحة
                ════════════════════════════════ --}}
                <div x-show="currentStep === 4" x-cloak>
                    <div class="step-title">
                        <span class="material-symbols-rounded" style="font-size:20px;font-variation-settings:'FILL' 1">health_and_safety</span>
                        السجل الصحي
                    </div>
                    <p class="step-count">الخطوة 4 من 6</p>

                    <div class="f-group">
                        <label class="f-label">هل لديك إصابات أو آلام مزمنة؟</label>
                        <div class="chip-grid">
                            <div class="chip-opt danger">
                                <input type="radio" id="inj_yes" name="has_injuries" value="yes"
                                       {{ old('has_injuries', $assessment?->health['has_injuries'] ?? '') === 'yes' ? 'checked' : '' }}
                                       x-model="hasInjuries">
                                <label for="inj_yes">نعم</label>
                            </div>
                            <div class="chip-opt success">
                                <input type="radio" id="inj_no" name="has_injuries" value="no"
                                       {{ old('has_injuries', $assessment?->health['has_injuries'] ?? '') === 'no' ? 'checked' : '' }}
                                       x-model="hasInjuries">
                                <label for="inj_no">لا</label>
                            </div>
                        </div>
                        @error('has_injuries')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group" x-show="hasInjuries === 'yes'">
                        <label class="f-label" for="injuries_details">اذكر الإصابات أو الآلام</label>
                        <textarea id="injuries_details" name="injuries_details" rows="2"
                                  class="f-input" placeholder="مثال: ألم في الركبة اليسرى، انزلاق غضروفي...">{{ old('injuries_details', $assessment?->health['injuries_details'] ?? '') }}</textarea>
                        @error('injuries_details')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="health_conditions">هل تعاني من أمراض مزمنة؟ (اختياري)</label>
                        <textarea id="health_conditions" name="health_conditions" rows="2"
                                  class="f-input" placeholder="مثال: سكري، ضغط، ربو... أو اكتب 'لا'">{{ old('health_conditions', $assessment?->health['health_conditions'] ?? '') }}</textarea>
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="allergies">حساسية تجاه أطعمة أو مواد؟ (اختياري)</label>
                        <textarea id="allergies" name="allergies" rows="2"
                                  class="f-input" placeholder="مثال: حساسية من المكسرات، اللاكتوز...">{{ old('allergies', $assessment?->health['allergies'] ?? '') }}</textarea>
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="medications">أدوية تتناولها بشكل منتظم؟ (اختياري)</label>
                        <textarea id="medications" name="medications" rows="2"
                                  class="f-input" placeholder="مثال: دواء للضغط، مضادات اكتئاب...">{{ old('medications', $assessment?->health['medications'] ?? '') }}</textarea>
                    </div>

                    <div class="btn-row">
                        <button type="button" class="btn-prev" @click="prevStep()">
                            <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">arrow_forward</span>
                            السابق
                        </button>
                        <button type="button" class="btn-next" @click="nextStep(4)">
                            التالي <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">arrow_back</span>
                        </button>
                    </div>
                </div>

                {{-- ════════════════════════════════
                     STEP 5 — نمط الحياة
                ════════════════════════════════ --}}
                <div x-show="currentStep === 5" x-cloak>
                    <div class="step-title">
                        <span class="material-symbols-rounded" style="font-size:20px;font-variation-settings:'FILL' 1">self_improvement</span>
                        نمط الحياة
                    </div>
                    <p class="step-count">الخطوة 5 من 6</p>

                    <div class="f-group">
                        <label class="f-label">مستوى نشاطك اليومي (خارج التمرين)</label>
                        <div class="chip-grid">
                            @foreach(['sedentary'=>'مستقر (مكتب)','light'=>'خفيف','moderate'=>'معتدل','active'=>'نشيط','very_active'=>'نشيط جداً'] as $val => $label)
                            <div class="chip-opt">
                                <input type="radio" id="act_{{ $val }}" name="daily_activity" value="{{ $val }}"
                                       {{ old('daily_activity', $assessment?->lifestyle['daily_activity'] ?? '') === $val ? 'checked' : '' }}>
                                <label for="act_{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('daily_activity')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">ساعات النوم يومياً</label>
                        <div class="range-wrap">
                            <input type="range" name="sleep_hours" id="sleep_hours"
                                   min="3" max="14" step="0.5"
                                   value="{{ old('sleep_hours', $assessment?->lifestyle['sleep_hours'] ?? 7) }}"
                                   x-model="sleepHours">
                            <span class="range-val" x-text="sleepHours + ' ساعات'"></span>
                        </div>
                        @error('sleep_hours')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">هل تدخن؟</label>
                        <div class="chip-grid">
                            <div class="chip-opt danger">
                                <input type="radio" id="smk_yes" name="smoking" value="yes"
                                       {{ old('smoking', $assessment?->lifestyle['smoking'] ?? '') === 'yes' ? 'checked' : '' }}>
                                <label for="smk_yes">نعم</label>
                            </div>
                            <div class="chip-opt success">
                                <input type="radio" id="smk_no" name="smoking" value="no"
                                       {{ old('smoking', $assessment?->lifestyle['smoking'] ?? '') === 'no' ? 'checked' : '' }}>
                                <label for="smk_no">لا</label>
                            </div>
                        </div>
                        @error('smoking')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label">مدى التزامك (1 = منخفض جداً، 10 = ملتزم كلياً)</label>
                        <div class="range-wrap">
                            <input type="range" name="commitment_score" id="commitment_score"
                                   min="1" max="10" step="1"
                                   value="{{ old('commitment_score', $assessment?->lifestyle['commitment_score'] ?? 7) }}"
                                   x-model="commitmentScore">
                            <span class="range-val" x-text="commitmentScore + '/10'"></span>
                        </div>
                        @error('commitment_score')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="challenges">التحديات التي تواجهها في رحلة اللياقة (اختياري)</label>
                        <textarea id="challenges" name="challenges" rows="3"
                                  class="f-input" placeholder="مثال: ضيق الوقت، قلة الدافعية، صعوبة في النظام الغذائي...">{{ old('challenges', $assessment?->lifestyle['challenges'] ?? '') }}</textarea>
                    </div>

                    <div class="btn-row">
                        <button type="button" class="btn-prev" @click="prevStep()">
                            <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">arrow_forward</span>
                            السابق
                        </button>
                        <button type="button" class="btn-next" @click="nextStep(5)">
                            التالي <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">arrow_back</span>
                        </button>
                    </div>
                </div>

                {{-- ════════════════════════════════
                     STEP 6 — الإقرار والتوقيع
                ════════════════════════════════ --}}
                <div x-show="currentStep === 6" x-cloak>
                    <div class="step-title">
                        <span class="material-symbols-rounded" style="font-size:20px;font-variation-settings:'FILL' 1">verified</span>
                        الإقرار والتوقيع
                    </div>
                    <p class="step-count">الخطوة 6 من 6 — الخطوة الأخيرة!</p>

                    <div class="f-group">
                        <div class="decl-box">
                            <input type="checkbox" id="declaration_accepted" name="declaration_accepted" value="1"
                                   {{ old('declaration_accepted') ? 'checked' : '' }}>
                            <p class="decl-text">
                                أُقرّ بأن جميع المعلومات المُدخلة في هذه الاستمارة صحيحة ودقيقة على حدّ علمي.
                                أوافق على مشاركة هذه البيانات مع الكوتش المسؤول عنّي لأغراض تصميم البرنامج التدريبي والغذائي.
                                أُدرك أن هذه المعلومات ستُستخدم حصرياً لخدمتي وتحقيق أهدافي.
                            </p>
                        </div>
                        @error('declaration_accepted')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="signature_text">التوقيع (اكتب اسمك الكامل كتوقيع)</label>
                        <input type="text" id="signature_text" name="signature_text"
                               value="{{ old('signature_text') }}"
                               placeholder="اسمك الكامل"
                               class="f-input">
                        @error('signature_text')<p class="f-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="btn-row">
                        <button type="button" class="btn-prev" @click="prevStep()">
                            <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle">arrow_forward</span>
                            السابق
                        </button>
                        <button type="submit" class="btn-submit">
                            <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;font-variation-settings:'FILL' 1">check_circle</span>
                            إرسال الاستمارة
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function assessmentForm() {
    // If validation failed, jump to the first step with an error
    const errorFields = @json($errors->keys());
    const stepFields = {
        1: ['date_of_birth','current_weight','target_weight','height'],
        2: ['primary_goal','experience_level','workout_days_per_week','session_duration_minutes','training_location','equipment_level','target_duration_weeks','fitness_score'],
        3: ['meals_count','has_snacks','diet_type','water_intake','has_supplements','supplements_details','preferred_foods','disliked_foods'],
        4: ['has_injuries','injuries_details','health_conditions','allergies','medications'],
        5: ['daily_activity','sleep_hours','smoking','commitment_score','challenges'],
        6: ['declaration_accepted','signature_text'],
    };

    let startStep = 1;
    if (errorFields.length > 0) {
        for (let s = 1; s <= 6; s++) {
            if (stepFields[s].some(f => errorFields.includes(f))) {
                startStep = s;
                break;
            }
        }
    }

    return {
        currentStep: startStep,
        fitnessScore: {{ old('fitness_score', $assessment?->training_details['fitness_score'] ?? 5) }},
        waterIntake:  {{ old('water_intake',  $assessment?->nutrition['water_intake'] ?? 2) }},
        sleepHours:   {{ old('sleep_hours',   $assessment?->lifestyle['sleep_hours'] ?? 7) }},
        commitmentScore: {{ old('commitment_score', $assessment?->lifestyle['commitment_score'] ?? 7) }},
        hasInjuries:    '{{ old('has_injuries', $assessment?->health['has_injuries'] ?? '') }}',
        hasSupplements: '{{ old('has_supplements', $assessment?->nutrition['has_supplements'] ?? '') }}',

        nextStep(from) {
            this.currentStep = from + 1;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
    };
}
</script>
@endsection

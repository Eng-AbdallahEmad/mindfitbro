@extends('layouts.web.app')
@section('title', 'رحلتك مع MindFitBro')

@section('style')
<style>
/* ══════════════════════════════════════════════════
   JOURNEY PAGE  — all layout via <style>, no Tailwind
   responsive classes that may not be compiled.
══════════════════════════════════════════════════ */

/* page shell */
.jny-page {
    min-height: 100vh;
    background: #EEF2FB;
    font-family: 'Noto Kufi Arabic', 'Cairo', sans-serif;
}

/* ── hero ── */
.jny-hero {
    background: linear-gradient(135deg, #174DAD 0%, #0f3a87 100%);
    padding: 52px 24px 80px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.jny-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.07) 1px, transparent 1px);
    background-size: 22px 22px;
    pointer-events: none;
}
.hero-inner { position: relative; z-index: 1; }

/* trophy icon — yellow circle, DARK icon so it's visible */
.trophy-ring {
    width: 80px; height: 80px;
    background: #D4ED57;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 0 0 12px rgba(212,237,87,.18), 0 0 0 26px rgba(212,237,87,.08);
}

/* ── content wrapper ── */
.jny-wrap {
    max-width: 760px;
    margin: -44px auto 0;   /* slight overlap into hero bottom */
    padding: 0 16px 80px;
    position: relative;
    z-index: 2;             /* ensures cards sit above hero background */
}

/* ── stats grid ── */
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
}
@media (min-width: 600px) {
    .stats-grid { grid-template-columns: repeat(4, 1fr); }
}

/* stat card */
.stat-card {
    background: #fff;
    border-radius: 20px;
    padding: 18px 12px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(23,77,173,.08);
    border: 1px solid rgba(23,77,173,.05);
}
.stat-icon-wrap {
    width: 44px; height: 44px;
    border-radius: 14px;
    background: #EFF5FF;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 10px;
}
.stat-lbl { font-size: 10px; font-weight: 800; color: #9CA3AF; letter-spacing: .4px; margin-bottom: 6px; }
.stat-val { font-size: 24px; font-weight: 900; color: #174DAD; line-height: 1; }
.stat-val.c-green { color: #10B981; }
.stat-val.c-red   { color: #EF4444; }
.stat-sub { font-size: 11px; color: #9CA3AF; margin-top: 4px; }

/* ── generic content card ── */
.jny-card {
    background: #fff;
    border-radius: 22px;
    padding: 22px;
    box-shadow: 0 2px 16px rgba(23,77,173,.07);
    border: 1px solid rgba(23,77,173,.05);
    margin-bottom: 16px;
}
.sec-hd {
    font-size: 10px; font-weight: 900; color: #9CA3AF;
    letter-spacing: .8px; text-transform: uppercase;
    margin-bottom: 18px;
    display: flex; align-items: center; gap: 8px;
}
.sec-hd::after { content: ''; flex: 1; height: 1px; background: #EEF2FB; }

/* ── before/after cells ── */
.ba-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}
.ba-cell {
    flex: 1;
    background: #F4F7FF;
    border-radius: 14px;
    padding: 14px 10px;
    text-align: center;
}
.ba-lbl  { font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; margin-bottom: 4px; }
.ba-val  { font-size: 20px; font-weight: 900; color: #1C1C1C; line-height: 1.1; }
.ba-unit { font-size: 12px; font-weight: 600; color: #9CA3AF; }
.ba-arrow { font-size: 20px; color: #CBD5E1; flex-shrink: 0; }

/* ── sparkline bars ── */
.spark-wrap { display: flex; align-items: flex-end; gap: 3px; height: 48px; border-radius: 8px; overflow: hidden; }
.spark-bar {
    flex: 1; border-radius: 3px 3px 0 0;
    background: rgba(23,77,173,.18);
    transition: background .2s;
    min-height: 6px;
    cursor: default;
}
.spark-bar:hover { background: rgba(23,77,173,.4); }

/* ── progress bars (body composition) ── */
.pbr-row { margin-bottom: 18px; }
.pbr-row:last-child { margin-bottom: 0; }
.pbr-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    margin-bottom: 6px;
}
.pbr-bg { background: #E5EAF3; border-radius: 6px; height: 7px; overflow: hidden; }
.pbr-fill { height: 100%; border-radius: 6px; background: #174DAD; transition: width .9s ease; }

/* ── coach rating stars ── */
.stars-row { display: flex; gap: 4px; justify-content: center; }
.star-btn {
    font-size: 30px; background: none; border: none; cursor: pointer;
    color: #D1D5DB; transition: color .15s, transform .15s;
    padding: 4px; line-height: 1;
}
.star-btn.lit, .star-btn:hover { color: #F59E0B; transform: scale(1.1); }

/* ── renewal CTAs ── */
.cta-p {
    display: block; width: 100%; padding: 14px;
    background: #174DAD; color: #fff;
    font-size: 14px; font-weight: 900; font-family: inherit;
    border-radius: 14px; text-align: center;
    text-decoration: none; border: none; cursor: pointer;
    transition: opacity .2s; margin-bottom: 10px;
}
.cta-p:hover { opacity: .9; }
.cta-s {
    display: block; width: 100%; padding: 13px;
    background: #F4F7FF; color: #374151;
    font-size: 13px; font-weight: 700; font-family: inherit;
    border-radius: 14px; text-align: center;
    text-decoration: none; border: none; cursor: pointer;
    transition: background .2s; margin-bottom: 10px;
}
.cta-s:hover { background: #E0E8FF; }

/* flash notices */
.flash-ok  { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 16px; padding: 14px; text-align: center; font-size: 13px; font-weight: 700; margin-bottom: 16px; }
.flash-inf { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; border-radius: 16px; padding: 14px; text-align: center; font-size: 13px; font-weight: 700; margin-bottom: 16px; }
</style>
@endsection

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $dir   = $isRtl ? 'rtl' : 'ltr';
@endphp

<div class="jny-page" dir="{{ $dir }}">

    {{-- ══════════════════════════════════════════════════
         HERO
    ══════════════════════════════════════════════════ --}}
    <div class="jny-hero">
        <div class="hero-inner">

            {{-- Trophy circle: yellow bg, BLUE icon (was yellow-on-yellow → invisible) --}}
            <div class="trophy-ring">
                <span class="material-symbols-rounded"
                      style="font-size:40px;font-variation-settings:'FILL' 1;color:#174DAD">emoji_events</span>
            </div>

            <div style="display:inline-block;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);
                        color:#fff;font-size:12px;font-weight:900;padding:5px 18px;border-radius:20px;margin-bottom:14px;">
                أكملت رحلتك
            </div>

            <h1 style="font-size:1.5rem;font-weight:900;color:#fff;margin:0 0 8px;">
                أنجزت برنامجك!&nbsp;<span class="material-symbols-rounded"
                    style="font-size:24px;font-variation-settings:'FILL' 1;vertical-align:middle">celebration</span>
            </h1>

            <p style="color:rgba(255,255,255,.65);font-size:.875rem;margin:0 0 6px;">
                {{ $subscription->plan?->name }} — {{ $subscription->duration_months }} شهر
            </p>

            @if($subscription->start_date && $subscription->end_date)
            <p style="color:rgba(255,255,255,.45);font-size:.75rem;margin:0;" dir="ltr">
                {{ $subscription->start_date->format('d/m/Y') }} — {{ $subscription->end_date->format('d/m/Y') }}
            </p>
            @endif

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         CONTENT
    ══════════════════════════════════════════════════ --}}
    <div class="jny-wrap">

        {{-- Flash messages --}}
        @if(session('success'))<div class="flash-ok">{{ session('success') }}</div>@endif
        @if(session('info'))<div class="flash-inf">{{ session('info') }}</div>@endif

        @if(! $hasData)
        {{-- ── Empty state ── --}}
        <div class="jny-card" style="text-align:center;padding:40px 24px;">
            <span class="material-symbols-rounded"
                  style="font-size:52px;font-variation-settings:'FILL' 1;color:#10B981">eco</span>
            <h2 style="font-size:1.2rem;font-weight:900;color:#1c1c1c;margin:16px 0 8px;">شكراً لانضمامك إلينا!</h2>
            <p style="color:#6B7280;font-size:.875rem;line-height:1.75;margin-bottom:24px;">
                أتممت برنامجك مع MindFitBro.<br>
                لم يتم تسجيل بيانات تتبع خلال فترة اشتراكك.
            </p>
            @include('app.web.journey._renewal_ctas')
        </div>

        @else

        {{-- ══════════════════════════════════════════════════
             STATS ROW — CSS grid, no Tailwind responsive classes
        ══════════════════════════════════════════════════ --}}
        <div class="stats-grid">

            @if($firstWeight)
            <div class="stat-card">
                <div class="stat-icon-wrap">
                    <span class="material-symbols-rounded"
                          style="font-size:22px;font-variation-settings:'FILL' 1;color:#174DAD">monitor_weight</span>
                </div>
                <div class="stat-lbl">فقدان الوزن</div>
                @if($weightDelta !== null)
                    <div class="stat-val {{ $weightDelta < 0 ? 'c-green' : ($weightDelta > 0 ? 'c-red' : '') }}">
                        {{ $weightDelta < 0 ? '' : ($weightDelta > 0 ? '+' : '') }}{{ abs($weightDelta) }}
                    </div>
                    <div class="stat-sub">كجم</div>
                @else
                    <div class="stat-val">{{ number_format((float)$firstWeight->weight, 1) }}</div>
                    <div class="stat-sub">كجم (تسجيل واحد)</div>
                @endif
            </div>
            @endif

            @if($attendTotal > 0)
            <div class="stat-card">
                <div class="stat-icon-wrap">
                    <span class="material-symbols-rounded"
                          style="font-size:22px;font-variation-settings:'FILL' 1;color:#174DAD">calendar_month</span>
                </div>
                <div class="stat-lbl">الجلسات</div>
                <div class="stat-val {{ ($attendRate ?? 0) >= 75 ? 'c-green' : '' }}">{{ $attendPresent + $attendLate }}</div>
                <div class="stat-sub">من {{ $attendTotal }}</div>
            </div>
            @endif

            @if($workoutTotal > 0)
            <div class="stat-card">
                <div class="stat-icon-wrap">
                    <span class="material-symbols-rounded"
                          style="font-size:22px;font-variation-settings:'FILL' 1;color:#174DAD">fitness_center</span>
                </div>
                <div class="stat-lbl">التمارين</div>
                <div class="stat-val {{ ($workoutRate ?? 0) >= 70 ? 'c-green' : '' }}">{{ $workoutRate }}%</div>
                <div class="stat-sub">إتمام</div>
            </div>
            @endif

            @if($lastEval)
            <div class="stat-card">
                <div class="stat-icon-wrap">
                    <span class="material-symbols-rounded"
                          style="font-size:22px;font-variation-settings:'FILL' 1;color:#174DAD">military_tech</span>
                </div>
                <div class="stat-lbl">اللياقة</div>
                @php $lvlMap = ['beginner' => 'مبتدئ', 'intermediate' => 'متوسط', 'advanced' => 'متقدم']; @endphp
                <div class="stat-val" style="font-size:18px">
                    {{ $lvlMap[$lastEval->fitness_level] ?? $lastEval->fitness_level }}
                </div>
                <div class="stat-sub">المستوى النهائي</div>
            </div>
            @endif

        </div>{{-- /stats-grid --}}

        {{-- ══════════════════════════════════════════════════
             WEIGHT JOURNEY
        ══════════════════════════════════════════════════ --}}
        @if($weightLogs->count() >= 2)
        <div class="jny-card">
            <div class="sec-hd">رحلة الوزن</div>

            {{-- Before / After / Goal --}}
            <div class="ba-row">
                <div class="ba-cell">
                    <div class="ba-lbl">البداية</div>
                    <div class="ba-val">{{ number_format((float)$firstWeight->weight, 1) }} <span class="ba-unit">كجم</span></div>
                    <div style="font-size:11px;color:#9CA3AF;margin-top:4px">{{ $firstWeight->logged_at->format('d/m/Y') }}</div>
                </div>

                <div class="ba-arrow">
                    <span class="material-symbols-rounded" style="font-size:20px;color:#CBD5E1">
                        {{ $isRtl ? 'arrow_back' : 'arrow_forward' }}
                    </span>
                </div>

                <div class="ba-cell">
                    <div class="ba-lbl">النهاية</div>
                    <div class="ba-val {{ $weightDelta < 0 ? 'c-green' : '' }}">
                        {{ number_format((float)$lastWeight->weight, 1) }} <span class="ba-unit">كجم</span>
                    </div>
                    <div style="font-size:11px;color:#9CA3AF;margin-top:4px">{{ $lastWeight->logged_at->format('d/m/Y') }}</div>
                </div>

                @if($profile?->goal_weight)
                <div class="ba-arrow">
                    <span class="material-symbols-rounded" style="font-size:20px;color:#174DAD;font-variation-settings:'FILL' 1">gps_fixed</span>
                </div>
                <div class="ba-cell">
                    <div class="ba-lbl">الهدف</div>
                    <div class="ba-val" style="color:#174DAD">
                        {{ number_format((float)$profile->goal_weight, 1) }} <span class="ba-unit">كجم</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Sparkline --}}
            @if($weightLogs->count() > 2)
            @php
                $wMin   = (float)$weightLogs->min('weight');
                $wMax   = (float)$weightLogs->max('weight');
                $wRange = max($wMax - $wMin, 1);
            @endphp
            <div class="spark-wrap">
                @foreach($weightLogs as $wl)
                @php $pct = round(((float)$wl->weight - $wMin) / $wRange * 100); @endphp
                <div class="spark-bar"
                     style="height:{{ max(8, $pct) }}%;"
                     title="{{ number_format((float)$wl->weight,1) }} كجم — {{ $wl->logged_at->format('d/m') }}">
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════
             BODY COMPOSITION — before / after (evaluations)
        ══════════════════════════════════════════════════ --}}
        @if($firstEval && $lastEval && $firstEval->id !== $lastEval->id)
        <div class="jny-card">
            <div class="sec-hd">تحليل الجسم — قبل وبعد</div>

            @if($firstEval->body_fat_percentage !== null && $lastEval->body_fat_percentage !== null)
            @php $fatDelta = round($lastEval->body_fat_percentage - $firstEval->body_fat_percentage, 1); @endphp
            <div class="pbr-row">
                <div class="pbr-meta">
                    <span style="color:#6B7280;font-weight:600;font-size:13px">نسبة الدهون</span>
                    <span style="font-weight:800;font-size:13px;color:{{ $fatDelta < 0 ? '#10B981' : '#EF4444' }}">
                        {{ $fatDelta < 0 ? '' : '+' }}{{ $fatDelta }}%
                    </span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;font-size:11px;color:#9CA3AF">
                    <span>{{ number_format($firstEval->body_fat_percentage,1) }}%</span>
                    <div class="pbr-bg" style="flex:1">
                        <div class="pbr-fill {{ $fatDelta < 0 ? '' : '' }}"
                             style="width:{{ min(100, max(0, 100 - abs($fatDelta / max($firstEval->body_fat_percentage,1) * 100))) }}%;
                                    background:{{ $fatDelta < 0 ? '#10B981' : '#EF4444' }}"></div>
                    </div>
                    <span>{{ number_format($lastEval->body_fat_percentage,1) }}%</span>
                </div>
            </div>
            @endif

            @if($firstEval->muscle_mass !== null && $lastEval->muscle_mass !== null)
            @php $muscleDelta = round($lastEval->muscle_mass - $firstEval->muscle_mass, 1); @endphp
            <div class="pbr-row">
                <div class="pbr-meta">
                    <span style="color:#6B7280;font-weight:600;font-size:13px">الكتلة العضلية</span>
                    <span style="font-weight:800;font-size:13px;color:{{ $muscleDelta > 0 ? '#10B981' : '#EF4444' }}">
                        {{ $muscleDelta > 0 ? '+' : '' }}{{ $muscleDelta }} كجم
                    </span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;font-size:11px;color:#9CA3AF">
                    <span>{{ number_format($firstEval->muscle_mass,1) }}</span>
                    <div class="pbr-bg" style="flex:1">
                        <div class="pbr-fill"
                             style="width:{{ min(100, max(10, $lastEval->muscle_mass / max($firstEval->muscle_mass,1) * 100)) }}%;
                                    background:#174DAD"></div>
                    </div>
                    <span>{{ number_format($lastEval->muscle_mass,1) }}</span>
                </div>
            </div>
            @endif

        </div>
        @endif

        {{-- ══════════════════════════════════════════════════
             COACH NOTES
        ══════════════════════════════════════════════════ --}}
        @if($lastEval?->notes)
        <div class="jny-card">
            <div class="sec-hd">ملاحظات الكوتش</div>
            <div style="display:flex;gap:14px;align-items:flex-start">
                <div style="width:42px;height:42px;border-radius:50%;background:#174DAD;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <span class="material-symbols-rounded"
                          style="font-size:20px;font-variation-settings:'FILL' 1;color:#fff">chat</span>
                </div>
                <div style="flex:1;background:#F4F7FF;border-radius:18px;padding:14px 16px;
                            font-size:.875rem;color:#374151;line-height:1.75">
                    {{ $lastEval->notes }}
                </div>
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════
             PDF REPORT
        ══════════════════════════════════════════════════ --}}
        <div class="jny-card">
            <div class="sec-hd">تقرير رحلتك</div>
            <div style="display:flex;align-items:center;gap:16px">
                <span class="material-symbols-rounded"
                      style="font-size:40px;font-variation-settings:'FILL' 1;color:#174DAD;flex-shrink:0">description</span>
                <div style="flex:1">
                    <p style="font-weight:800;color:#1c1c1c;font-size:.875rem;margin:0 0 4px">حمّل تقريرك الشامل</p>
                    <p style="color:#9CA3AF;font-size:.75rem;margin:0">ملخص رحلتك الكاملة — الأوزان، الحضور، التقييمات</p>
                </div>
                <a href="{{ route('journey.pdf', $subscription) }}" target="_blank"
                   style="flex-shrink:0;background:#174DAD;color:#fff;font-size:.8125rem;font-weight:800;
                          padding:10px 18px;border-radius:12px;text-decoration:none;
                          font-family:inherit;transition:opacity .2s"
                   onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    تحميل PDF
                </a>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             COACH RATING
        ══════════════════════════════════════════════════ --}}
        @if($coach)
        <div class="jny-card" x-data="coachRating()">
            <div class="sec-hd">قيّم كوتشك</div>

            @if($existingRating)
            {{-- Already rated --}}
            <div style="text-align:center;padding:16px 0">
                <div style="margin-bottom:8px">
                    <span class="material-symbols-rounded"
                          style="font-size:32px;font-variation-settings:'FILL' 1;color:#F59E0B">star</span>
                </div>
                <p style="font-weight:700;color:#374151;font-size:.875rem;margin:0 0 4px">
                    أعطيت {{ $coach->name }} تقييم
                    @for($s = 1; $s <= $existingRating->stars; $s++)
                    <span class="material-symbols-rounded"
                          style="font-size:16px;font-variation-settings:'FILL' 1;color:#F59E0B;vertical-align:middle">star</span>
                    @endfor
                </p>
                @if($existingRating->comment)
                <p style="font-size:.75rem;color:#9CA3AF;margin:4px 0 0">"{{ $existingRating->comment }}"</p>
                @endif
            </div>

            @else
            {{-- Rating form --}}
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px">
                <div style="width:48px;height:48px;border-radius:50%;background:#174DAD;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <span class="material-symbols-rounded"
                          style="font-size:24px;font-variation-settings:'FILL' 1;color:#fff">person</span>
                </div>
                <div>
                    <p style="font-weight:900;color:#1c1c1c;margin:0 0 2px">{{ $coach->name }}</p>
                    <p style="font-size:.75rem;color:#9CA3AF;margin:0">كوتشك خلال فترة الاشتراك</p>
                </div>
            </div>

            <form action="{{ route('journey.rate', $subscription) }}" method="POST">
                @csrf
                <input type="hidden" name="stars" :value="selected">

                <p style="text-align:center;color:#6B7280;font-size:.875rem;margin:0 0 14px">كيف كانت تجربتك؟</p>

                <div class="stars-row" dir="ltr" style="margin-bottom:8px">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="star-btn"
                            :class="{ lit: {{ $i }} <= (hovered || selected) }"
                            @mouseenter="hovered = {{ $i }}"
                            @mouseleave="hovered = 0"
                            @click="selected = {{ $i }}">
                        <span class="material-symbols-rounded"
                              style="font-size:30px;font-variation-settings:'FILL' 1">star</span>
                    </button>
                    @endfor
                </div>

                <p style="text-align:center;font-size:.75rem;color:#9CA3AF;min-height:18px;margin:0 0 14px"
                   x-text="ratingLabel"></p>

                @error('stars')
                <p style="text-align:center;color:#EF4444;font-size:.75rem;margin:0 0 10px">{{ $message }}</p>
                @enderror

                <textarea name="comment" rows="3"
                    style="width:100%;border:1px solid #E5EAF3;border-radius:14px;padding:12px 14px;
                           font-size:.875rem;font-family:inherit;resize:none;background:#F8FAFF;
                           outline:none;box-sizing:border-box;color:#374151;
                           transition:border-color .2s"
                    onfocus="this.style.borderColor='#174DAD'"
                    onblur="this.style.borderColor='#E5EAF3'"
                    placeholder="أضف تعليقاً اختيارياً..."></textarea>

                <button type="submit"
                        style="display:block;width:100%;margin-top:14px;padding:14px;
                               background:#174DAD;color:#fff;font-size:.9375rem;font-weight:900;
                               font-family:inherit;border:none;border-radius:14px;cursor:pointer;
                               transition:opacity .2s"
                        :disabled="!selected"
                        :style="!selected ? 'opacity:.45;cursor:not-allowed' : ''"
                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    إرسال التقييم
                </button>
            </form>
            @endif
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════
             RENEWAL CTAs
        ══════════════════════════════════════════════════ --}}
        <div class="jny-card">
            <div class="sec-hd">ماذا بعد؟</div>

            @if($subscription->plan)
            <a href="{{ route('purchase.form', $subscription->plan) }}" class="cta-p">
                <span class="material-symbols-rounded"
                      style="font-size:17px;vertical-align:middle;margin-{{ $isRtl ? 'left' : 'right' }}:6px">refresh</span>
                جدّد نفس الباقة — {{ $subscription->plan->name }}
            </a>
            @endif

            <a href="{{ route('home') }}#programs" class="cta-s">
                <span class="material-symbols-rounded"
                      style="font-size:17px;vertical-align:middle;margin-{{ $isRtl ? 'left' : 'right' }}:6px">shuffle</span>
                اختر باقة مختلفة
            </a>

            @php $wa = config('app.whatsapp', env('CONTACT_PHONE', '')); @endphp
            @if($wa)
            <a href="https://wa.me/{{ ltrim($wa, '+') }}" target="_blank" rel="noopener" class="cta-s">
                <span class="material-symbols-rounded"
                      style="font-size:17px;vertical-align:middle;margin-{{ $isRtl ? 'left' : 'right' }}:6px">chat</span>
                تواصل مع الكوتش
            </a>
            @endif
        </div>

        @endif{{-- /hasData --}}
    </div>{{-- /jny-wrap --}}
</div>{{-- /jny-page --}}
@endsection

@section('script')
<script>
function coachRating() {
    return {
        selected: 0,
        hovered:  0,
        labels: ['', 'سيء', 'مقبول', 'جيد', 'جيد جداً', 'ممتاز'],
        get ratingLabel() {
            return this.labels[this.hovered || this.selected] || '';
        }
    };
}
</script>
@endsection

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    direction: rtl;
    font-family: dejavusans, sans-serif;
    color: #1C1C1C;
    font-size: 12px;
    line-height: 1.6;
}
.page-header {
    background: #174DAD;
    color: #fff;
    padding: 24px 30px;
    margin-bottom: 24px;
}
.page-header h1 { font-size: 22px; font-weight: bold; margin-bottom: 4px; }
.page-header p  { font-size: 11px; color: rgba(255,255,255,0.75); }
.badge {
    display: inline-block;
    background: #D4ED57;
    color: #1C1C1C;
    font-size: 10px;
    font-weight: bold;
    padding: 3px 12px;
    border-radius: 12px;
    margin-bottom: 10px;
}
.section {
    margin: 0 20px 20px;
    background: #fff;
    border: 1px solid #E5EAF3;
    border-radius: 10px;
    padding: 16px 20px;
}
.section-title {
    font-size: 10px;
    font-weight: bold;
    color: #9CA3AF;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #F0F4FB;
    padding-bottom: 8px;
    margin-bottom: 14px;
}
table { width: 100%; border-collapse: collapse; }
th, td { padding: 8px 10px; border-bottom: 1px solid #F0F4FB; font-size: 11px; text-align: right; }
th { background: #F4F7FF; font-weight: bold; color: #374151; font-size: 10px; }
td.val { font-weight: bold; color: #174DAD; }
.stat-grid { width: 100%; }
.stat-grid td { width: 25%; padding: 10px; text-align: center; border: 1px solid #E5EAF3; border-radius: 8px; }
.big-num { font-size: 22px; font-weight: bold; color: #174DAD; display: block; }
.small-label { font-size: 10px; color: #9CA3AF; display: block; margin-top: 2px; }
.green { color: #10B981; }
.red   { color: #EF4444; }
.footer-text { text-align: center; font-size: 10px; color: #9CA3AF; margin-top: 10px; }
</style>
</head>
<body>

{{-- ── Header ── --}}
<div class="page-header">
    <div class="badge">تقرير رحلتك</div>
    <h1>{{ $user->name }}</h1>
    <p>
        {{ $subscription->plan?->name }} —
        {{ $subscription->duration_months }} شهر —
        @if($subscription->start_date && $subscription->end_date)
        {{ $subscription->start_date->format('d/m/Y') }} إلى {{ $subscription->end_date->format('d/m/Y') }}
        @endif
    </p>
</div>

{{-- ── Summary stats ── --}}
<div class="section">
    <div class="section-title">ملخص الإنجازات</div>
    <table class="stat-grid" cellspacing="6">
        <tr>
            @if($firstWeight && $weightDelta !== null)
            <td>
                <span class="big-num {{ $weightDelta < 0 ? 'green' : ($weightDelta > 0 ? 'red' : '') }}">
                    {{ $weightDelta < 0 ? '' : ('+') }}{{ $weightDelta }} كجم
                </span>
                <span class="small-label">تغيّر الوزن</span>
            </td>
            @endif
            @if($attendTotal > 0)
            <td>
                <span class="big-num {{ ($attendRate ?? 0) >= 75 ? 'green' : '' }}">{{ $attendPresent + $attendLate }}/{{ $attendTotal }}</span>
                <span class="small-label">الحضور</span>
            </td>
            @endif
            @if($workoutTotal > 0)
            <td>
                <span class="big-num">{{ $workoutRate }}%</span>
                <span class="small-label">إتمام التمارين</span>
            </td>
            @endif
            @if($lastEval)
            @php $lvlMap = ['beginner'=>'مبتدئ','intermediate'=>'متوسط','advanced'=>'متقدم']; @endphp
            <td>
                <span class="big-num" style="font-size:14px;">{{ $lvlMap[$lastEval->fitness_level] ?? '—' }}</span>
                <span class="small-label">مستوى اللياقة</span>
            </td>
            @endif
        </tr>
    </table>
</div>

{{-- ── Weight log ── --}}
@if($weightLogs->count() >= 2)
<div class="section">
    <div class="section-title">سجل الوزن</div>
    <table>
        <tr><th>التاريخ</th><th>الوزن (كجم)</th><th>التغيير</th></tr>
        @php $prevW = null; @endphp
        @foreach($weightLogs as $wl)
        @php
            $delta = $prevW !== null ? round((float)$wl->weight - $prevW, 2) : null;
            $prevW = (float)$wl->weight;
        @endphp
        <tr>
            <td dir="ltr">{{ $wl->logged_at->format('d/m/Y') }}</td>
            <td class="val">{{ number_format((float)$wl->weight, 1) }}</td>
            <td class="{{ $delta !== null ? ($delta < 0 ? 'green' : ($delta > 0 ? 'red' : '')) : '' }}">
                {{ $delta !== null ? (($delta > 0 ? '+' : '') . $delta) : '—' }}
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endif

{{-- ── Body composition ── --}}
@if($firstEval && $lastEval && $firstEval->id !== $lastEval->id)
<div class="section">
    <div class="section-title">تحليل الجسم — قبل وبعد</div>
    <table>
        <tr><th>القياس</th><th>البداية</th><th>النهاية</th><th>التغيير</th></tr>
        @if($firstEval->weight && $lastEval->weight)
        @php $d = round($lastEval->weight - $firstEval->weight, 1); @endphp
        <tr>
            <td>الوزن (كجم)</td>
            <td class="val">{{ number_format($firstEval->weight, 1) }}</td>
            <td class="val">{{ number_format($lastEval->weight, 1) }}</td>
            <td class="{{ $d < 0 ? 'green' : ($d > 0 ? 'red' : '') }}">{{ ($d > 0 ? '+' : '') . $d }}</td>
        </tr>
        @endif
        @if($firstEval->body_fat_percentage !== null && $lastEval->body_fat_percentage !== null)
        @php $d = round($lastEval->body_fat_percentage - $firstEval->body_fat_percentage, 1); @endphp
        <tr>
            <td>نسبة الدهون (%)</td>
            <td class="val">{{ number_format($firstEval->body_fat_percentage, 1) }}</td>
            <td class="val">{{ number_format($lastEval->body_fat_percentage, 1) }}</td>
            <td class="{{ $d < 0 ? 'green' : ($d > 0 ? 'red' : '') }}">{{ ($d > 0 ? '+' : '') . $d }}</td>
        </tr>
        @endif
        @if($firstEval->muscle_mass !== null && $lastEval->muscle_mass !== null)
        @php $d = round($lastEval->muscle_mass - $firstEval->muscle_mass, 1); @endphp
        <tr>
            <td>الكتلة العضلية (كجم)</td>
            <td class="val">{{ number_format($firstEval->muscle_mass, 1) }}</td>
            <td class="val">{{ number_format($lastEval->muscle_mass, 1) }}</td>
            <td class="{{ $d > 0 ? 'green' : ($d < 0 ? 'red' : '') }}">{{ ($d > 0 ? '+' : '') . $d }}</td>
        </tr>
        @endif
        @if($firstEval->height)
        <tr>
            <td>الطول (سم)</td>
            <td class="val" colspan="3">{{ number_format($firstEval->height, 1) }}</td>
        </tr>
        @endif
    </table>
</div>
@endif

{{-- ── Attendance ── --}}
@if($attendTotal > 0)
<div class="section">
    <div class="section-title">سجل الحضور</div>
    <table>
        <tr><th>البند</th><th>العدد</th><th>النسبة</th></tr>
        <tr><td>حاضر</td><td class="val green">{{ $attendPresent }}</td><td>{{ $attendTotal > 0 ? round($attendPresent/$attendTotal*100) : 0 }}%</td></tr>
        <tr><td>متأخر</td><td class="val" style="color:#F59E0B;">{{ $attendLate }}</td><td>{{ $attendTotal > 0 ? round($attendLate/$attendTotal*100) : 0 }}%</td></tr>
        <tr><td>غائب</td><td class="val red">{{ $attendAbsent }}</td><td>{{ $attendTotal > 0 ? round($attendAbsent/$attendTotal*100) : 0 }}%</td></tr>
        <tr><td><strong>الإجمالي</strong></td><td class="val"><strong>{{ $attendTotal }}</strong></td><td><strong>{{ $attendRate }}%</strong> حضور</td></tr>
    </table>
</div>
@endif

{{-- ── Coach notes ── --}}
@if($lastEval?->notes)
<div class="section">
    <div class="section-title">ملاحظات الكوتش</div>
    <p style="color:#374151; line-height:1.8;">{{ $lastEval->notes }}</p>
</div>
@endif

{{-- ── Footer ── --}}
<p class="footer-text">
    تم إنشاء هذا التقرير بواسطة MindFitBro — {{ now()->format('d/m/Y') }}
</p>

</body>
</html>

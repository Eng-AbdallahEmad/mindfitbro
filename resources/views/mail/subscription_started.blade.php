<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>رحلتك بدأت اليوم — MindFitBro</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
<style>
body { margin:0; padding:0; background:#F0F4FB; font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif; }
</style>
</head>
<body style="margin:0;padding:0;background:#F0F4FB;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;direction:rtl;">

@php
    $planName = __('messages.plans_data.'.$subscription->plan->key.'.name', [], null) ?: $subscription->plan->name ?? '—';
    $startStr = \Carbon\Carbon::parse($subscription->start_date)->locale('ar')->isoFormat('dddd، D MMMM YYYY');
    $endStr   = $subscription->end_date
                ? \Carbon\Carbon::parse($subscription->end_date)->locale('ar')->isoFormat('D MMMM YYYY')
                : null;
@endphp

<div style="max-width:600px;margin:0 auto;padding:32px 16px;">
<div style="background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(23,77,173,0.10);">

    {{-- ── Header ── --}}
    <div style="background:linear-gradient(135deg,#174DAD 0%,#0f3a87 100%);padding:44px 36px;text-align:center;position:relative;overflow:hidden;">
        <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,0.07) 1px,transparent 1px);background-size:22px 22px;pointer-events:none;"></div>
        <div style="position:relative;z-index:1;">
            <div style="display:inline-block;background:#D4ED57;color:#1C1C1C;font-size:12px;font-weight:900;padding:5px 18px;border-radius:20px;margin-bottom:16px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">رحلتك بدأت اليوم</div>
            <div style="color:#ffffff;font-size:28px;font-weight:900;margin-bottom:8px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">يلا نبدأ!</div>
            <div style="color:rgba(255,255,255,0.75);font-size:13px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">باقتك فُعِّلت وجاهزة — حان وقت الانطلاق</div>
        </div>
    </div>

    {{-- ── Body ── --}}
    <div style="padding:36px;">

        {{-- Greeting --}}
        <p style="font-size:15px;color:#374151;margin:0 0 28px;line-height:1.8;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            أهلاً <strong style="color:#174DAD;">{{ $customerName }}</strong>،<br>
            اشتراكك في MindFitBro بدأ رسمياً اليوم!
            ادخل لحسابك الآن واستمتع ببرنامجك التدريبي مع كوتشك المخصص.
        </p>

        {{-- Subscription chips --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
            <tr>
                <td>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="background:#F4F7FF;border-radius:14px;padding:14px;text-align:center;width:33%;">
                                <div style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">الباقة</div>
                                <div style="font-size:13px;font-weight:900;color:#174DAD;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $planName }}</div>
                            </td>
                            <td style="width:8px;"></td>
                            <td style="background:#F4F7FF;border-radius:14px;padding:14px;text-align:center;width:33%;">
                                <div style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">تاريخ البدء</div>
                                <div style="font-size:12px;font-weight:900;color:#174DAD;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $startStr }}</div>
                            </td>
                            @if($endStr)
                            <td style="width:8px;"></td>
                            <td style="background:#F4F7FF;border-radius:14px;padding:14px;text-align:center;width:33%;">
                                <div style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">ينتهي في</div>
                                <div style="font-size:12px;font-weight:900;color:#374151;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $endStr }}</div>
                            </td>
                            @endif
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Divider --}}
        <div style="height:1px;background:#F0F4FB;margin:0 0 24px;"></div>

        {{-- What's waiting section --}}
        <div style="margin-bottom:24px;">
            <div style="font-size:13px;font-weight:900;color:#1C1C1C;margin-bottom:14px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">اللي بينتظرك في لوحة التحكم:</div>
            @foreach([
                ['icon'=>'fitness_center', 'label'=>'برنامجك التدريبي اليومي'],
                ['icon'=>'monitoring',     'label'=>'تتبع وزنك وتقدمك'],
                ['icon'=>'calendar_month', 'label'=>'جدول أيام التمرين والراحة'],
                ['icon'=>'chat',           'label'=>'التواصل مع كوتشك'],
            ] as $item)
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
                <tr>
                    <td style="background:#F8FAFF;border-radius:12px;padding:12px 14px;">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width:32px;vertical-align:middle;">
                                    <div style="width:28px;height:28px;background:#EFF5FF;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-align:center;line-height:28px;">
                                        <span style="font-size:16px;color:#174DAD;">&#x{{ dechex(0x1F4AA) }};</span>
                                    </div>
                                </td>
                                <td style="padding-right:10px;font-size:13px;font-weight:700;color:#374151;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $item['label'] }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            @endforeach
        </div>

        {{-- CTA button --}}
        <table align="center" cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
            <tr>
                <td style="background:#D4ED57;border-radius:14px;text-align:center;">
                    <a href="{{ url('/dashboard') }}"
                       style="display:inline-block;padding:15px 40px;color:#1C1C1C;font-size:15px;font-weight:900;text-decoration:none;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
                        ادخل لوحة التحكم
                    </a>
                </td>
            </tr>
        </table>

        {{-- Footer text --}}
        <p style="font-size:12px;color:#9CA3AF;text-align:center;margin:0;line-height:1.7;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            فريق MindFitBro معك في كل خطوة — نتمنى لك رحلة ناجحة!
        </p>

    </div>

    {{-- ── Footer bar ── --}}
    <div style="background:#F8FAFF;border-top:1px solid #E0E8FF;padding:18px 36px;text-align:center;">
        <p style="margin:0;font-size:11px;color:#9CA3AF;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            هذه الرسالة أُرسلت تلقائياً من منصة MindFitBro — يُرجى عدم الرد عليها.
        </p>
    </div>

</div>
</div>

</body>
</html>

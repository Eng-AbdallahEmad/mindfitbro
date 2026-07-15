<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تم تحديد رابط جلستك الأولى — MindFitBro</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
<style>
body { margin:0; padding:0; background:#F0F4FB; font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif; }
</style>
</head>
<body style="margin:0;padding:0;background:#F0F4FB;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;direction:rtl;">

@php
    $meetDate = \Carbon\Carbon::parse($booking->meeting_date)->locale('ar');
    $meetTime = \Carbon\Carbon::parse($booking->meeting_time)->format('g:i A');
    $dayName  = $meetDate->isoFormat('dddd');
    $dateStr  = $meetDate->isoFormat('D MMMM Y');
@endphp

<div style="max-width:600px;margin:0 auto;padding:32px 16px;">
<div style="background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(23,77,173,0.10);">

    {{-- ── Header ── --}}
    <div style="background:linear-gradient(135deg,#174DAD 0%,#0f3a87 100%);padding:44px 36px;text-align:center;">
        <div style="display:inline-block;background:#D4ED57;color:#1C1C1C;font-size:12px;font-weight:900;padding:5px 18px;border-radius:20px;margin-bottom:16px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">جلسة مؤكدة</div>
        <div style="color:#ffffff;font-size:26px;font-weight:900;margin-bottom:8px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">تم تحديد رابط جلستك الأولى!</div>
        <div style="color:rgba(255,255,255,0.75);font-size:13px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">جلستك مع الكوتش جاهزة — كل اللي تحتاجه هنا</div>
    </div>

    {{-- ── Body ── --}}
    <div style="padding:36px;">

        {{-- Greeting --}}
        <p style="font-size:15px;color:#374151;margin:0 0 28px;line-height:1.8;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            أهلاً <strong style="color:#174DAD;">{{ $customerName }}</strong>،<br>
            يسعدنا إبلاغك بأن كوتشك قام بتحديد رابط الجلسة الأولى.
            الجلسة ستكون عبر Google Meet في الموعد التالي:
        </p>

        {{-- Date + Time chips --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
            <tr>
                <td style="padding-left:4px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="background:#F4F7FF;border-radius:14px;padding:16px;text-align:center;width:50%;">
                                <div style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">التاريخ</div>
                                <div style="font-size:13px;font-weight:900;color:#174DAD;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $dayName }}</div>
                                <div style="font-size:12px;font-weight:700;color:#374151;margin-top:2px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $dateStr }}</div>
                            </td>
                            <td style="width:10px;"></td>
                            <td style="background:#F4F7FF;border-radius:14px;padding:16px;text-align:center;width:50%;">
                                <div style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">الوقت</div>
                                <div style="font-size:20px;font-weight:900;color:#174DAD;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;" dir="ltr">{{ $meetTime }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Meet Link box --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
            <tr>
                <td style="background:#F0FDF4;border:1.5px solid #BBF7D0;border-radius:16px;padding:18px 20px;">
                    <div style="font-size:12px;font-weight:700;color:#166534;margin-bottom:6px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">رابط Google Meet</div>
                    <div style="font-size:12px;color:#15803D;word-break:break-all;font-family:'Courier New',monospace;" dir="ltr">{{ $booking->meet_link }}</div>
                </td>
            </tr>
        </table>

        {{-- CTA button --}}
        <table align="center" cellpadding="0" cellspacing="0" style="margin:0 auto 28px;">
            <tr>
                <td style="background:#174DAD;border-radius:14px;text-align:center;">
                    <a href="{{ $booking->meet_link }}"
                       style="display:inline-block;padding:15px 36px;color:#ffffff;font-size:15px;font-weight:900;text-decoration:none;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
                        انضم للجلسة
                    </a>
                </td>
            </tr>
        </table>

        {{-- Notice --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
            <tr>
                <td style="background:#FEF9C3;border:1.5px solid #FDE68A;border-radius:14px;padding:16px 18px;">
                    <p style="margin:0;font-size:12px;color:#92400E;line-height:1.8;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
                        <strong>تذكير:</strong> احرص على الانضمام للجلسة في الوقت المحدد.
                        إذا واجهت أي مشكلة في الرابط، تواصل مع الكوتش مباشرة.
                    </p>
                </td>
            </tr>
        </table>

        {{-- Footer text --}}
        <p style="font-size:12px;color:#9CA3AF;text-align:center;margin:0;line-height:1.7;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            فريق MindFitBro — معك في كل خطوة من رحلتك
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

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>رمز التحقق — MindFitBro</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
<style>
body { margin:0; padding:0; background:#F0F4FB; font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif; }
</style>
</head>
<body style="margin:0;padding:0;background:#F0F4FB;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;direction:rtl;">

<div style="max-width:560px;margin:0 auto;padding:32px 16px;">
<div style="background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(23,77,173,0.10);">

    {{-- ── Header ── --}}
    <div style="background:linear-gradient(135deg,#174DAD 0%,#0f3a87 100%);padding:44px 36px;text-align:center;">
        <div style="display:inline-block;background:#D4ED57;color:#1C1C1C;font-size:12px;font-weight:900;padding:5px 18px;border-radius:20px;margin-bottom:16px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">رمز التحقق</div>
        <div style="color:#ffffff;font-size:24px;font-weight:900;line-height:1.3;margin-bottom:8px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">تحقق من بريدك الإلكتروني</div>
        <div style="color:rgba(255,255,255,0.70);font-size:13px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">تم إنشاء حساب كوتش لك على MindFitBro</div>
    </div>

    {{-- ── Body ── --}}
    <div style="padding:40px 36px;text-align:center;">

        {{-- Greeting ── --}}
        <p style="font-size:15px;color:#374151;margin-bottom:28px;line-height:1.8;text-align:right;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            أهلاً <strong style="color:#174DAD;">{{ $coachName }}</strong>،<br>
            طلب أحد مديري المنصة إنشاء حساب كوتش لك. لإتمام العملية،
            يرجى مشاركة رمز التحقق أدناه مع الإدارة.
        </p>

        {{-- OTP label ── --}}
        <div style="font-size:12px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            رمز التحقق المكون من 6 أرقام
        </div>

        {{-- OTP box ── --}}
        <div style="display:inline-block;background:#F4F7FF;border:2px solid #174DAD;border-radius:20px;padding:24px 44px;margin-bottom:28px;">
            <div style="font-size:52px;font-weight:900;letter-spacing:12px;color:#174DAD;font-family:'Courier New',Courier,monospace;line-height:1;" dir="ltr">{{ $otp }}</div>
            <div style="font-size:11px;color:#9CA3AF;margin-top:8px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">أدخل هذا الرمز في لوحة الإدارة</div>
        </div>

        {{-- Timer warning ── --}}
        <div style="background:#FFFBE6;border:1.5px solid #FDE68A;border-radius:14px;padding:16px 20px;margin-bottom:28px;text-align:right;">
            <span style="font-size:13px;color:#92400E;line-height:1.7;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
                <strong style="color:#78350F;">صالح لمدة 15 دقيقة فقط</strong> من وقت الإرسال.<br>
                إذا لم تطلب هذا الرمز، تجاهل هذا البريد تماماً.
            </span>
        </div>

        {{-- Security notice ── --}}
        <p style="font-size:12px;color:#9CA3AF;line-height:1.8;text-align:right;margin-bottom:8px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            لا تشارك هذا الرمز مع أي شخص غير الإدارة المختصة.<br>
            MindFitBro لن تطلب منك أبداً رمز التحقق عبر الهاتف أو واتساب.
        </p>

    </div>

    {{-- ── Footer ── --}}
    <div style="background:#F4F7FF;padding:24px 36px;text-align:center;border-top:1px solid #E5EAF3;">
        <p style="font-size:11px;color:#9CA3AF;line-height:1.7;margin:0;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            MindFitBro<br>
            هذا البريد أُرسل تلقائياً للتحقق من ملكية البريد الإلكتروني.<br>
            © {{ date('Y') }} MindFitBro. جميع الحقوق محفوظة.
        </p>
    </div>

</div>
</div>
</body>
</html>

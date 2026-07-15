<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>اشتراكك ينتهي قريباً — MindFitBro</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #F0F4FB; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; color: #1C1C1C; }
    .wrapper { max-width: 600px; margin: 0 auto; padding: 32px 16px; }
    .card { background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 24px rgba(23,77,173,0.08); }
    .header { background: linear-gradient(135deg, #D97706 0%, #92400E 100%); padding: 44px 36px; text-align: center; }
    .header-icon { font-size: 48px; margin-bottom: 14px; }
    .header-badge { display: inline-block; background: #FEF3C7; color: #92400E; font-size: 12px; font-weight: 900; padding: 4px 14px; border-radius: 20px; margin-bottom: 14px; }
    .header-title { color: #ffffff; font-size: 24px; font-weight: 900; margin-bottom: 8px; }
    .header-sub { color: rgba(255,255,255,0.75); font-size: 13px; }
    .body { padding: 36px; }
    .greeting { font-size: 15px; color: #374151; margin-bottom: 28px; line-height: 1.7; }
    .greeting strong { color: #D97706; }
    .countdown-box { background: #FFFBEB; border: 2px solid #FDE68A; border-radius: 16px; padding: 20px; text-align: center; margin-bottom: 28px; }
    .countdown-num { font-size: 52px; font-weight: 900; color: #D97706; line-height: 1; }
    .countdown-label { font-size: 13px; color: #92400E; font-weight: 700; margin-top: 6px; }
    .info-chips { display: flex; gap: 12px; margin-bottom: 28px; flex-wrap: wrap; }
    .chip { flex: 1; min-width: 110px; background: #F4F7FF; border-radius: 14px; padding: 14px; text-align: center; }
    .chip-label { font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.5px; }
    .chip-val { font-size: 14px; font-weight: 900; color: #174DAD; }
    .benefit-box { background: #F0FDF4; border: 1.5px solid #BBF7D0; border-radius: 16px; padding: 20px 22px; margin-bottom: 28px; }
    .benefit-title { font-size: 13px; font-weight: 900; color: #15803D; margin-bottom: 10px; }
    .benefit-item { font-size: 13px; color: #166534; line-height: 1.7; margin-bottom: 4px; }
    .cta-section { text-align: center; margin-bottom: 24px; }
    .cta-btn { display: inline-block; background: #D97706; color: #fff; font-size: 15px; font-weight: 900; padding: 16px 40px; border-radius: 14px; text-decoration: none; }
    .cta-sub { font-size: 12px; color: #9CA3AF; margin-top: 8px; }
    .footer { background: #F4F7FF; padding: 24px 36px; text-align: center; border-top: 1px solid #E5EAF3; }
    .footer-text { font-size: 11px; color: #9CA3AF; line-height: 1.7; }
</style>
</head>
<body>
<div class="wrapper">
<div class="card">

    <div class="header">
        <div class="header-icon">⏳</div>
        <div class="header-badge">تذكير مهم</div>
        <div class="header-title">اشتراكك ينتهي قريباً!</div>
        <div class="header-sub">لا تدع رحلتك تتوقف — جدد الآن واستمر</div>
    </div>

    <div class="body">
        <p class="greeting">
            أهلاً <strong>{{ $customerName }}</strong>،<br>
            نذكرك بأن اشتراكك في MindFitBro سينتهي خلال
            <strong>5 أيام</strong> فقط.
            جدد اشتراكك الآن لتستمر في رحلتك دون انقطاع.
        </p>

        <div class="countdown-box">
            <div class="countdown-num">5</div>
            <div class="countdown-label">أيام تبقت على انتهاء اشتراكك</div>
        </div>

        <div class="info-chips">
            <div class="chip">
                <div class="chip-label">رقم الطلب</div>
                <div class="chip-val">#{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="chip">
                <div class="chip-label">الباقة</div>
                <div class="chip-val">{{ $subscription->plan->name ?? '—' }}</div>
            </div>
            @if($subscription->duration_months)
            <div class="chip">
                <div class="chip-label">المدة</div>
                <div class="chip-val">{{ $subscription->duration_months }} شهر</div>
            </div>
            @endif
            <div class="chip">
                <div class="chip-label">ينتهي في</div>
                <div class="chip-val" dir="ltr">{{ $subscription->end_date?->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="benefit-box">
            <div class="benefit-title">لماذا تجدد الآن؟</div>
            <div class="benefit-item">استمر في تلقي متابعة كوتشك بدون انقطاع</div>
            <div class="benefit-item">حافظ على زخمك وتقدمك الذي حققته</div>
            <div class="benefit-item">إجراءات التجديد سريعة ولا تأخذ دقيقتين</div>
        </div>

        <div class="cta-section">
            <a href="{{ $renewalUrl }}" class="cta-btn">
                جدد اشتراكك الآن
            </a>
            <p class="cta-sub">اضغط للاختيار بين خطة 3 أشهر أو 6 أشهر</p>
        </div>

    </div>

    <div class="footer">
        <p class="footer-text">
            MindFitBro<br>
            © {{ date('Y') }} MindFitBro. جميع الحقوق محفوظة.
        </p>
    </div>

</div>
</div>
</body>
</html>

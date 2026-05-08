<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تأكيد الشراء — MindFitBro</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #F0F4FB; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; color: #1C1C1C; }
    .wrapper { max-width: 600px; margin: 0 auto; padding: 32px 16px; }

    .card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(23,77,173,0.08);
    }

    /* ── Header ── */
    .header {
        background: linear-gradient(135deg, #174DAD 0%, #0f3a87 100%);
        padding: 40px 36px 36px;
        text-align: center;
        position: relative;
    }
    .header-logo { width: 140px; margin-bottom: 20px; }
    .header-badge {
        display: inline-block;
        background: #D4ED57;
        color: #1C1C1C;
        font-size: 12px;
        font-weight: 900;
        padding: 4px 14px;
        border-radius: 20px;
        margin-bottom: 14px;
    }
    .header-title {
        color: #ffffff;
        font-size: 26px;
        font-weight: 900;
        line-height: 1.3;
        margin-bottom: 8px;
    }
    .header-sub { color: rgba(255,255,255,0.7); font-size: 14px; }

    /* ── Body ── */
    .body { padding: 36px; }

    .greeting { font-size: 16px; margin-bottom: 24px; color: #374151; }
    .greeting strong { color: #174DAD; }

    /* ── Invoice Table ── */
    .invoice-title {
        font-size: 15px;
        font-weight: 700;
        color: #6B7280;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #E5EAF3;
    }
    .invoice-table th {
        background: #F4F7FF;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 700;
        color: #6B7280;
        text-align: right;
    }
    .invoice-table td {
        padding: 14px 16px;
        font-size: 14px;
        border-top: 1px solid #F0F4FB;
        color: #374151;
        text-align: right;
    }
    .invoice-table .plan-name { font-weight: 700; color: #1C1C1C; }
    .invoice-table .price-cell { font-weight: 700; color: #174DAD; direction: ltr; text-align: left; }

    /* ── Pricing Summary ── */
    .summary-box {
        background: #F4F7FF;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 28px;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 13px;
        color: #6B7280;
    }
    .summary-row .val { font-weight: 600; color: #374151; direction: ltr; }
    .summary-row.discount .val { color: #16a34a; }
    .divider { height: 1px; background: #E5EAF3; margin: 10px 0; }
    .summary-row.total {
        font-size: 16px;
        font-weight: 900;
        color: #1C1C1C;
        padding-top: 12px;
    }
    .summary-row.total .val { color: #174DAD; font-size: 20px; }

    /* ── Status Box ── */
    .status-box {
        background: #fffbe6;
        border: 1.5px solid #fde68a;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 28px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .status-icon { font-size: 22px; flex-shrink: 0; }
    .status-text { font-size: 13px; color: #92400e; line-height: 1.6; }
    .status-text strong { color: #78350f; }

    /* ── CTA Button ── */
    .cta-section { text-align: center; margin-bottom: 28px; }
    .cta-btn {
        display: inline-block;
        background: #D4ED57;
        color: #1C1C1C;
        font-size: 15px;
        font-weight: 900;
        padding: 16px 40px;
        border-radius: 14px;
        text-decoration: none;
        letter-spacing: 0.3px;
    }
    .cta-sub {
        font-size: 12px;
        color: #9CA3AF;
        margin-top: 10px;
        line-height: 1.6;
    }

    /* ── Order Info ── */
    .order-info {
        display: flex;
        gap: 16px;
        margin-bottom: 28px;
    }
    .info-chip {
        flex: 1;
        background: #F4F7FF;
        border-radius: 12px;
        padding: 14px 16px;
        text-align: center;
    }
    .info-chip-label { font-size: 11px; color: #9CA3AF; font-weight: 600; margin-bottom: 4px; }
    .info-chip-val { font-size: 14px; font-weight: 700; color: #174DAD; }

    /* ── Footer ── */
    .footer {
        background: #F4F7FF;
        padding: 24px 36px;
        text-align: center;
        border-top: 1px solid #E5EAF3;
    }
    .footer-links { margin-bottom: 12px; }
    .footer-links a { color: #174DAD; font-size: 12px; text-decoration: none; margin: 0 8px; }
    .footer-text { font-size: 11px; color: #9CA3AF; line-height: 1.7; }
</style>
</head>
<body>
<div class="wrapper">
<div class="card">

    {{-- ── Header ── --}}
    <div class="header">
        <img src="{{ asset('assets/logo/mindfitbro.png') }}" alt="MindFitBro" class="header-logo">
        <div>
            <div class="header-badge">✓ تم تأكيد الطلب</div>
        </div>
        <div class="header-title">شكراً على ثقتك! 🎉</div>
        <div class="header-sub">تم استلام طلبك بنجاح وسيتم التواصل معك قريباً</div>
    </div>

    {{-- ── Body ── --}}
    <div class="body">

        {{-- Greeting --}}
        <p class="greeting">
            أهلاً <strong>{{ $subscription->guest_name }}</strong>،<br>
            تم تأكيد شراء باقتك على MindFitBro بنجاح. فيما يلي تفاصيل طلبك:
        </p>

        {{-- Order Info Chips --}}
        <div class="order-info">
            <div class="info-chip">
                <div class="info-chip-label">رقم الطلب</div>
                <div class="info-chip-val">#{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="info-chip">
                <div class="info-chip-label">تاريخ الطلب</div>
                <div class="info-chip-val">{{ $subscription->created_at->format('d/m/Y') }}</div>
            </div>
            <div class="info-chip">
                <div class="info-chip-label">نوع الدفع</div>
                <div class="info-chip-val">{{ $subscription->is_yearly ? 'سنوي' : 'شهري' }}</div>
            </div>
        </div>

        {{-- Invoice Table --}}
        <div class="invoice-title">تفاصيل الباقة</div>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>الباقة</th>
                    <th>الكمية</th>
                    <th style="text-align:left; direction:ltr">السعر</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscription->plans_snapshot as $item)
                <tr>
                    <td class="plan-name">{{ $item['plan_name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td class="price-cell">
                        {{ number_format($item['final_price'], 0) }}
                        <span style="font-size:11px;color:#9CA3AF"> ر.س</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pricing Summary --}}
        <div class="summary-box">
            <div class="summary-row">
                <span>الإجمالي الفرعي</span>
                <span class="val">{{ number_format($subscription->subtotal, 0) }} ر.س</span>
            </div>
            @if($subscription->yearly_discount > 0)
            <div class="summary-row discount">
                <span>خصم سنوي</span>
                <span class="val">- {{ number_format($subscription->yearly_discount, 0) }} ر.س</span>
            </div>
            @endif
            @if($subscription->coupon_discount > 0)
            <div class="summary-row discount">
                <span>خصم الكوبون</span>
                <span class="val">- {{ number_format($subscription->coupon_discount, 0) }} ر.س</span>
            </div>
            @endif
            <div class="divider"></div>
            <div class="summary-row total">
                <span>الإجمالي الكلي</span>
                <span class="val">{{ number_format($subscription->total, 0) }} ر.س</span>
            </div>
        </div>

        {{-- Status Notice --}}
        <div class="status-box">
            <div class="status-icon">⏳</div>
            <div class="status-text">
                <strong>الخطوة التالية:</strong> باقتك في انتظار التفعيل.
                سيتواصل معك الكوتش لتحديد موعد الجلسة التعريفية المجانية
                وبعدها تبدأ رحلتك رسمياً! يمكنك أيضاً إنشاء حسابك الآن
                لتتابع كل شيء من لوحة تحكمك.
            </div>
        </div>

        {{-- CTA --}}
        <div class="cta-section">
            <a href="{{ url('/complete-account/' . $subscription->guest_token) }}" class="cta-btn">
                🚀 أكمل بيانات حسابك
            </a>
            <p class="cta-sub">
                اضغط الزر عشان تنشئ حسابك وتتابع رحلتك من لوحة التحكم<br>
                (اسمك وإيميلك هيبقوا جاهزين تلقائياً)
            </p>
        </div>

    </div>

    {{-- ── Footer ── --}}
    <div class="footer">
        <div class="footer-links">
            <a href="{{ url('/') }}">الموقع الرئيسي</a>
            <a href="{{ url('/privacy-policy') }}">سياسة الخصوصية</a>
            <a href="{{ url('/terms-of-service') }}">الشروط والأحكام</a>
        </div>
        <p class="footer-text">
            MindFitBro — الرياض، المملكة العربية السعودية<br>
            هذا البريد أُرسل تلقائياً لـ {{ $subscription->guest_email }}<br>
            © {{ date('Y') }} MindFitBro. جميع الحقوق محفوظة.
        </p>
    </div>

</div>
</div>
</body>
</html>

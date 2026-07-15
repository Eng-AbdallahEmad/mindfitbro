<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>دعوة من {{ $inviterName }} — MindFitBro</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');
</style>
</head>
<body style="margin:0;padding:0;background-color:#F0F4FB;font-family:'Cairo',Arial,sans-serif;direction:rtl;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F0F4FB;">
<tr><td align="center" style="padding:32px 16px;">

<table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(23,77,173,0.08);">

    {{-- Header --}}
    <tr>
        <td style="background:linear-gradient(135deg,#174DAD 0%,#0f3a87 100%);padding:40px 36px 36px;text-align:center;">
            <div style="display:inline-block;background:#D4ED57;color:#1C1C1C;font-size:12px;font-weight:900;padding:4px 16px;border-radius:20px;margin-bottom:16px;font-family:'Cairo',Arial,sans-serif;">
                هدية خاصة لك
            </div>
            <div style="font-size:44px;margin-bottom:8px;">🎁</div>
            <div style="color:#ffffff;font-size:24px;font-weight:900;line-height:1.4;margin-bottom:6px;font-family:'Cairo',Arial,sans-serif;">
                {{ $inviterName }} يدعوك للانضمام
            </div>
            <div style="color:rgba(255,255,255,0.8);font-size:14px;font-family:'Cairo',Arial,sans-serif;">
                انضم إلى MindFitBro مع خصم حصري
            </div>
        </td>
    </tr>

    {{-- Body --}}
    <tr>
        <td style="padding:36px;">

            {{-- Greeting --}}
            <p style="margin:0 0 20px;color:#374151;font-size:15px;font-weight:600;font-family:'Cairo',Arial,sans-serif;line-height:1.7;">
                مرحباً{{ $invitation->invitee_name ? ' ' . $invitation->invitee_name : '' }}،
            </p>
            <p style="margin:0 0 28px;color:#374151;font-size:15px;font-family:'Cairo',Arial,sans-serif;line-height:1.8;">
                صديقك <strong>{{ $inviterName }}</strong> يريد مشاركتك رحلته الصحية على منصة MindFitBro،
                ولهذا أرسل لك خصماً خاصاً على اشتراكك.
            </p>

            {{-- Discount box --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                <tr>
                    <td style="background:linear-gradient(135deg,#fefce8 0%,#fef9c3 100%);border:2px solid #fbbf24;border-radius:16px;padding:28px;text-align:center;">
                        <div style="color:#92400e;font-size:13px;font-weight:700;margin-bottom:8px;font-family:'Cairo',Arial,sans-serif;">
                            خصمك الخاص
                        </div>
                        <div style="color:#1C1C1C;font-size:52px;font-weight:900;line-height:1;margin-bottom:4px;font-family:'Cairo',Arial,sans-serif;">
                            {{ $discountPercent }}%
                        </div>
                        <div style="color:#92400e;font-size:13px;font-weight:600;font-family:'Cairo',Arial,sans-serif;">
                            خصم على أي باقة
                        </div>
                    </td>
                </tr>
            </table>

            {{-- Coupon code --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                <tr>
                    <td style="background:#F8FAFF;border:2px dashed #174DAD;border-radius:12px;padding:20px;text-align:center;">
                        <div style="color:#6b7280;font-size:12px;font-weight:700;margin-bottom:8px;font-family:'Cairo',Arial,sans-serif;letter-spacing:0.5px;">
                            كود الخصم
                        </div>
                        <div style="color:#174DAD;font-size:28px;font-weight:900;letter-spacing:4px;font-family:monospace,Arial,sans-serif;">
                            {{ $coupon->code }}
                        </div>
                        <div style="color:#9ca3af;font-size:11px;font-weight:600;margin-top:8px;font-family:'Cairo',Arial,sans-serif;">
                            صالح لاستخدام واحد فقط · ينتهي خلال 30 يوماً
                        </div>
                    </td>
                </tr>
            </table>

            {{-- CTA --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                <tr>
                    <td align="center">
                        <a href="{{ route('home') }}#programs"
                           style="display:inline-block;background:linear-gradient(135deg,#174DAD 0%,#0f3a87 100%);color:#ffffff;font-size:16px;font-weight:900;padding:16px 40px;border-radius:14px;text-decoration:none;font-family:'Cairo',Arial,sans-serif;">
                            احجز اشتراكك الآن
                        </a>
                    </td>
                </tr>
            </table>

            {{-- How to use --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                <tr>
                    <td style="background:#F8FAFF;border-radius:12px;padding:20px;">
                        <div style="color:#374151;font-size:13px;font-weight:700;margin-bottom:12px;font-family:'Cairo',Arial,sans-serif;">
                            كيفية استخدام الكود:
                        </div>
                        @foreach(['اختر الباقة التي تناسبك من صفحة البرامج', 'أدخل كود الخصم في حقل الكوبون أثناء الاشتراك', 'استمتع بخصم ' . $discountPercent . '% على إجمالي طلبك'] as $step => $text)
                        <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:{{ $loop->last ? '0' : '8px' }};">
                            <div style="display:inline-block;min-width:22px;height:22px;background:#174DAD;color:#fff;font-size:11px;font-weight:900;border-radius:50%;text-align:center;line-height:22px;font-family:'Cairo',Arial,sans-serif;margin-left:8px;">
                                {{ $step + 1 }}
                            </div>
                            <div style="color:#4b5563;font-size:13px;font-family:'Cairo',Arial,sans-serif;line-height:1.6;">{{ $text }}</div>
                        </div>
                        @endforeach
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background:#F8FAFF;padding:24px 36px;text-align:center;border-top:1px solid #e5e7eb;">
            <div style="color:#9ca3af;font-size:12px;font-family:'Cairo',Arial,sans-serif;line-height:1.8;">
                هذه الدعوة صالحة لاستخدام واحد فقط وستنتهي بعد 30 يوماً من تاريخ الإرسال.<br>
                إذا لم تكن تتوقع هذا البريد يمكنك تجاهله بأمان.
            </div>
            <div style="margin-top:16px;color:#174DAD;font-size:13px;font-weight:700;font-family:'Cairo',Arial,sans-serif;">
                MindFitBro — رحلتك الصحية تبدأ هنا
            </div>
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>

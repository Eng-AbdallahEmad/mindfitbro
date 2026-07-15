<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تم استلام إيصالك — MindFitBro</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap');
</style>
</head>
<body style="margin:0;padding:0;background-color:#F0F4FB;font-family:'Cairo',Arial,sans-serif;direction:rtl;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F0F4FB;">
<tr><td align="center" style="padding:32px 16px;">

<table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(23,77,173,0.08);">

    {{-- ── Header ── --}}
    <tr>
        <td style="background:linear-gradient(135deg,#174DAD 0%,#0f3a87 100%);padding:40px 36px 36px;text-align:center;">
            <div style="display:inline-block;background:#D4ED57;color:#1C1C1C;font-size:12px;font-weight:900;padding:4px 16px;border-radius:20px;margin-bottom:14px;font-family:'Cairo',Arial,sans-serif;">
                تم الاستلام
            </div>
            <div style="color:#ffffff;font-size:26px;font-weight:900;line-height:1.4;margin-bottom:6px;font-family:'Cairo',Arial,sans-serif;">
                وصلنا إيصالك!
            </div>
            <div style="color:rgba(255,255,255,0.8);font-size:14px;font-family:'Cairo',Arial,sans-serif;">
                طلبك الآن بانتظار المراجعة من فريق MindFitBro
            </div>
        </td>
    </tr>

    {{-- ── Body ── --}}
    <tr>
        <td style="padding:36px;">
            @php
                $currency = $subscription->currency ?? 'SAR';
                $symbol   = \App\Services\Web\CurrencyService::META[$currency]['symbol'] ?? 'ر.س';
                $dec      = \App\Services\Web\CurrencyService::META[$currency]['decimals'] ?? 0;
            @endphp

            {{-- Greeting --}}
            <p style="font-size:16px;color:#374151;line-height:1.8;margin:0 0 28px;font-family:'Cairo',Arial,sans-serif;">
                أهلاً <strong style="color:#174DAD;">{{ $customerName }}</strong>،<br>
                تم استلام إيصال الدفع الخاص بك. سيقوم فريقنا بمراجعة الطلب
                والتواصل معك في أقرب وقت لتأكيد وتفعيل اشتراكك.
            </p>

            {{-- Info chips: 3 cols --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
                <tr>
                    <td width="33%" style="padding:0 6px 0 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background:#F4F7FF;border-radius:12px;padding:14px 16px;text-align:center;">
                                    <div style="font-size:11px;color:#9CA3AF;font-weight:600;margin-bottom:5px;font-family:'Cairo',Arial,sans-serif;">رقم الطلب</div>
                                    <div style="font-size:14px;font-weight:700;color:#174DAD;font-family:'Cairo',Arial,sans-serif;">
                                        #{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="33%" style="padding:0 3px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background:#F4F7FF;border-radius:12px;padding:14px 16px;text-align:center;">
                                    <div style="font-size:11px;color:#9CA3AF;font-weight:600;margin-bottom:5px;font-family:'Cairo',Arial,sans-serif;">الباقة</div>
                                    <div style="font-size:14px;font-weight:700;color:#174DAD;font-family:'Cairo',Arial,sans-serif;">
                                        {{ $subscription->plan->name ?? '—' }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="33%" style="padding:0 0 0 6px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background:#F4F7FF;border-radius:12px;padding:14px 16px;text-align:center;">
                                    <div style="font-size:11px;color:#9CA3AF;font-weight:600;margin-bottom:5px;font-family:'Cairo',Arial,sans-serif;">المدة</div>
                                    <div style="font-size:14px;font-weight:700;color:#174DAD;font-family:'Cairo',Arial,sans-serif;">
                                        {{ $subscription->duration_months }} شهر
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- Total chip --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                <tr>
                    <td style="background:#F4F7FF;border-radius:12px;padding:16px;text-align:center;">
                        <div style="font-size:11px;color:#9CA3AF;font-weight:600;margin-bottom:6px;font-family:'Cairo',Arial,sans-serif;">المبلغ الإجمالي</div>
                        <div style="font-size:22px;font-weight:900;color:#174DAD;font-family:'Cairo',Arial,sans-serif;">
                            {{ number_format($subscription->total, $dec) }} {{ $symbol }}
                        </div>
                        @if($subscription->coupon_code)
                        <div style="font-size:12px;color:#10B981;font-weight:600;margin-top:4px;font-family:'Cairo',Arial,sans-serif;">
                            كوبون خصم مطبّق: {{ $subscription->coupon_code }}
                        </div>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- Steps box --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#F4F7FF;border-radius:16px;margin-bottom:28px;">
                <tr>
                    <td style="padding:24px;">
                        <div style="font-size:14px;font-weight:700;color:#374151;margin-bottom:18px;font-family:'Cairo',Arial,sans-serif;">
                            ماذا سيحدث بعد ذلك؟
                        </div>

                        {{-- Step 1 --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:14px;">
                            <tr>
                                <td width="36" valign="top">
                                    <div style="width:28px;height:28px;background:#174DAD;border-radius:50%;text-align:center;line-height:28px;font-size:13px;font-weight:700;color:#fff;font-family:'Cairo',Arial,sans-serif;">١</div>
                                </td>
                                <td style="font-size:13px;color:#374151;line-height:1.7;padding-top:3px;font-family:'Cairo',Arial,sans-serif;">
                                    يراجع فريقنا إيصال الدفع ويتحقق منه.
                                </td>
                            </tr>
                        </table>

                        {{-- Step 2 --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:14px;">
                            <tr>
                                <td width="36" valign="top">
                                    <div style="width:28px;height:28px;background:#174DAD;border-radius:50%;text-align:center;line-height:28px;font-size:13px;font-weight:700;color:#fff;font-family:'Cairo',Arial,sans-serif;">٢</div>
                                </td>
                                <td style="font-size:13px;color:#374151;line-height:1.7;padding-top:3px;font-family:'Cairo',Arial,sans-serif;">
                                    يتواصل معك الكوتش لتحديد موعد الجلسة التعريفية المجانية.
                                </td>
                            </tr>
                        </table>

                        {{-- Step 3 --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="36" valign="top">
                                    <div style="width:28px;height:28px;background:#174DAD;border-radius:50%;text-align:center;line-height:28px;font-size:13px;font-weight:700;color:#fff;font-family:'Cairo',Arial,sans-serif;">٣</div>
                                </td>
                                <td style="font-size:13px;color:#374151;line-height:1.7;padding-top:3px;font-family:'Cairo',Arial,sans-serif;">
                                    يُفعَّل اشتراكك ويبدأ عداد المدة من يوم التفعيل.
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>

            {{-- Notice --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="background:#fffbe6;border:1.5px solid #fde68a;border-radius:14px;padding:16px 20px;font-size:13px;color:#92400E;line-height:1.7;font-family:'Cairo',Arial,sans-serif;">
                        <strong>المدة المتوقعة للمراجعة:</strong> خلال 24 ساعة في أيام العمل.
                        إذا لم تتلقَّ ردًا خلال هذه المدة، تواصل معنا عبر واتساب.
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    {{-- ── Footer ── --}}
    <tr>
        <td style="background:#F4F7FF;padding:24px 36px;text-align:center;border-top:1px solid #E5EAF3;">
            <p style="font-size:11px;color:#9CA3AF;line-height:1.8;margin:0;font-family:'Cairo',Arial,sans-serif;">
                MindFitBro<br>
                هذا البريد أُرسل تلقائياً تأكيداً لاستلام طلبك.<br>
                © {{ date('Y') }} MindFitBro. جميع الحقوق محفوظة.
            </p>
        </td>
    </tr>

</table>

</td></tr>
</table>

</body>
</html>

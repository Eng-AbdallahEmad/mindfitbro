<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>طلب شراء جديد — MindFitBro</title>
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
        <td style="background:linear-gradient(135deg,#D97706 0%,#B45309 100%);padding:36px;text-align:center;">
            <div style="display:inline-block;background:#FEF3C7;color:#92400E;font-size:12px;font-weight:900;padding:4px 16px;border-radius:20px;margin-bottom:14px;font-family:'Cairo',Arial,sans-serif;">
                طلب جديد
            </div>
            <div style="color:#ffffff;font-size:24px;font-weight:900;line-height:1.4;margin-bottom:6px;font-family:'Cairo',Arial,sans-serif;">
                طلب شراء بانتظار المراجعة
            </div>
            <div style="color:rgba(255,255,255,0.85);font-size:14px;font-family:'Cairo',Arial,sans-serif;">
                يرجى مراجعة إيصال الدفع والتواصل مع العميل
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

            {{-- Section: Customer info --}}
            <div style="font-size:11px;font-weight:700;color:#6B7280;letter-spacing:0.5px;margin-bottom:12px;font-family:'Cairo',Arial,sans-serif;">
                بيانات العميل
            </div>

            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#F4F7FF;border-radius:14px;overflow:hidden;margin-bottom:28px;">
                <tr>
                    <td style="padding:13px 20px;border-bottom:1px solid #E5EAF3;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#6B7280;font-size:13px;font-family:'Cairo',Arial,sans-serif;">الاسم</td>
                                <td align="left" style="color:#1C1C1C;font-size:13px;font-weight:700;font-family:'Cairo',Arial,sans-serif;">{{ $customerName }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:13px 20px;border-bottom:1px solid #E5EAF3;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#6B7280;font-size:13px;font-family:'Cairo',Arial,sans-serif;">البريد الإلكتروني</td>
                                <td align="left" style="color:#1C1C1C;font-size:13px;font-weight:700;font-family:'Cairo',Arial,sans-serif;direction:ltr;">{{ $customerEmail }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:13px 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#6B7280;font-size:13px;font-family:'Cairo',Arial,sans-serif;">نوع الحساب</td>
                                <td align="left" style="font-size:13px;font-weight:700;font-family:'Cairo',Arial,sans-serif;">
                                    @if($subscription->user_id)
                                        <span style="color:#174DAD;">مسجّل (ID: {{ $subscription->user_id }})</span>
                                    @else
                                        <span style="color:#6B7280;">زائر (Guest)</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- Section: Order details --}}
            <div style="font-size:11px;font-weight:700;color:#6B7280;letter-spacing:0.5px;margin-bottom:12px;font-family:'Cairo',Arial,sans-serif;">
                تفاصيل الطلب
            </div>

            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#F4F7FF;border-radius:14px;overflow:hidden;margin-bottom:28px;">
                <tr>
                    <td style="padding:13px 20px;border-bottom:1px solid #E5EAF3;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#6B7280;font-size:13px;font-family:'Cairo',Arial,sans-serif;">رقم الطلب</td>
                                <td align="left" style="color:#1C1C1C;font-size:13px;font-weight:700;font-family:'Cairo',Arial,sans-serif;">
                                    #{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:13px 20px;border-bottom:1px solid #E5EAF3;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#6B7280;font-size:13px;font-family:'Cairo',Arial,sans-serif;">الباقة</td>
                                <td align="left" style="color:#1C1C1C;font-size:13px;font-weight:700;font-family:'Cairo',Arial,sans-serif;">
                                    {{ $subscription->plan->name ?? '—' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:13px 20px;border-bottom:1px solid #E5EAF3;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#6B7280;font-size:13px;font-family:'Cairo',Arial,sans-serif;">المدة</td>
                                <td align="left" style="color:#1C1C1C;font-size:13px;font-weight:700;font-family:'Cairo',Arial,sans-serif;">
                                    {{ $subscription->duration_months }} شهر
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:13px 20px;border-bottom:1px solid #E5EAF3;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#6B7280;font-size:13px;font-family:'Cairo',Arial,sans-serif;">العملة</td>
                                <td align="left" style="color:#1C1C1C;font-size:13px;font-weight:700;font-family:'Cairo',Arial,sans-serif;">
                                    {{ $currency }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @if($subscription->coupon_code)
                <tr>
                    <td style="padding:13px 20px;border-bottom:1px solid #E5EAF3;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#6B7280;font-size:13px;font-family:'Cairo',Arial,sans-serif;">كوبون الخصم</td>
                                <td align="left" style="color:#10B981;font-size:13px;font-weight:700;font-family:'Cairo',Arial,sans-serif;direction:ltr;">
                                    {{ $subscription->coupon_code }}
                                    (−{{ number_format($subscription->coupon_discount, $dec) }} {{ $symbol }})
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding:13px 20px;border-bottom:1px solid #E5EAF3;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#6B7280;font-size:13px;font-family:'Cairo',Arial,sans-serif;">الإجمالي</td>
                                <td align="left" style="color:#D97706;font-size:17px;font-weight:900;font-family:'Cairo',Arial,sans-serif;">
                                    {{ number_format($subscription->total, $dec) }} {{ $symbol }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:13px 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#6B7280;font-size:13px;font-family:'Cairo',Arial,sans-serif;">تاريخ الطلب</td>
                                <td align="left" style="color:#1C1C1C;font-size:13px;font-weight:700;font-family:'Cairo',Arial,sans-serif;direction:ltr;">
                                    {{ $subscription->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- CTA Button --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="center" style="padding:4px 0 8px;">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center"
                                    style="background:#174DAD;border-radius:14px;">
                                    <a href="{{ url('/admin/subscriptions/' . $subscription->id) }}"
                                       target="_blank"
                                       style="display:inline-block;color:#ffffff;font-size:15px;font-weight:700;padding:16px 40px;text-decoration:none;font-family:'Cairo',Arial,sans-serif;">
                                        مراجعة الطلب والإيصال
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    {{-- ── Footer ── --}}
    <tr>
        <td style="background:#F4F7FF;padding:24px 36px;text-align:center;border-top:1px solid #E5EAF3;">
            <p style="font-size:11px;color:#9CA3AF;line-height:1.8;margin:0;font-family:'Cairo',Arial,sans-serif;">
                MindFitBro — Internal Notification<br>
                هذا البريد أُرسل تلقائياً لجميع الكوتشات والمديرين.<br>
                © {{ date('Y') }} MindFitBro.
            </p>
        </td>
    </tr>

</table>

</td></tr>
</table>

</body>
</html>

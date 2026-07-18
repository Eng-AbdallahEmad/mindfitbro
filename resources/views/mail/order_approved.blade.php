<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تم الموافقة على اشتراكك — MindFitBro</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
<style>
body { margin:0; padding:0; background:#F0F4FB; font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif; }
</style>
</head>
<body style="margin:0;padding:0;background:#F0F4FB;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;direction:rtl;">
@php
    $currency = $subscription->currency ?? 'SAR';
    $symbol   = \App\Services\Web\CurrencyService::META[$currency]['symbol'] ?? 'ر.س';
    $dec      = \App\Services\Web\CurrencyService::META[$currency]['decimals'] ?? 0;
@endphp

<div style="max-width:600px;margin:0 auto;padding:32px 16px;">
<div style="background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(23,77,173,0.10);">

    {{-- ── Header ── --}}
    <div style="background:linear-gradient(135deg,#16a34a 0%,#14532d 100%);padding:44px 36px;text-align:center;">
        <div style="font-size:48px;margin-bottom:14px;line-height:1;"></div>
        <div style="display:inline-block;background:#D4ED57;color:#1C1C1C;font-size:12px;font-weight:900;padding:5px 18px;border-radius:20px;margin-bottom:16px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">موافق عليه</div>
        <div style="color:#ffffff;font-size:26px;font-weight:900;margin-bottom:8px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">تم تأكيد اشتراكك!</div>
        <div style="color:rgba(255,255,255,0.75);font-size:13px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">دفعتك تم التحقق منها ورحلتك تبدأ الآن</div>
    </div>

    {{-- ── Body ── --}}
    <div style="padding:36px;">

        {{-- Greeting ── --}}
        <p style="font-size:15px;color:#374151;margin-bottom:28px;line-height:1.8;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            أهلاً <strong style="color:#16a34a;">{{ $customerName }}</strong>،<br>
            يسعدنا إبلاغك بأن دفعتك تم التحقق منها بنجاح وتم تفعيل اشتراكك في MindFitBro.
            فريقنا مستعد للبدء معك!
        </p>

        {{-- Info chips (table layout — email-safe) ── --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
            <tr>
                <td style="padding:0 6px 0 0;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="background:#F4F7FF;border-radius:14px;padding:14px;text-align:center;width:50%;">
                                <div style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">رقم الطلب</div>
                                <div style="font-size:14px;font-weight:900;color:#174DAD;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">#{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td style="width:8px;"></td>
                            <td style="background:#F4F7FF;border-radius:14px;padding:14px;text-align:center;width:50%;">
                                <div style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">الباقة</div>
                                <div style="font-size:14px;font-weight:900;color:#174DAD;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $subscription->plan->name ?? '—' }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr><td style="height:8px;"></td></tr>
            <tr>
                <td>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="background:#F4F7FF;border-radius:14px;padding:14px;text-align:center;width:33%;">
                                <div style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">المدة</div>
                                <div style="font-size:14px;font-weight:900;color:#174DAD;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $subscription->duration_months }} شهر</div>
                            </td>
                            @if($subscription->start_date)
                            <td style="width:8px;"></td>
                            <td style="background:#F4F7FF;border-radius:14px;padding:14px;text-align:center;width:33%;">
                                <div style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">تاريخ البدء</div>
                                <div style="font-size:14px;font-weight:900;color:#174DAD;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;" dir="ltr">{{ $subscription->start_date->format('d/m/Y') }}</div>
                            </td>
                            @endif
                            @if($subscription->end_date)
                            <td style="width:8px;"></td>
                            <td style="background:#F4F7FF;border-radius:14px;padding:14px;text-align:center;width:33%;">
                                <div style="font-size:10px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">تاريخ الانتهاء</div>
                                <div style="font-size:14px;font-weight:900;color:#174DAD;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;" dir="ltr">{{ $subscription->end_date->format('d/m/Y') }}</div>
                            </td>
                            @endif
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Next steps ── --}}
        <div style="background:#F0FDF4;border:1.5px solid #BBF7D0;border-radius:16px;padding:22px;margin-bottom:28px;">
            <div style="font-size:14px;font-weight:900;color:#15803D;margin-bottom:16px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">ما الذي سيحدث بعد ذلك؟</div>

            {{-- Step 1 ── --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
                <tr>
                    <td style="vertical-align:top;width:32px;">
                        <div style="width:26px;height:26px;background:#16a34a;color:#fff;border-radius:50%;text-align:center;font-size:12px;font-weight:900;line-height:26px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">١</div>
                    </td>
                    <td style="padding-right:10px;vertical-align:top;">
                        <div style="font-size:13px;color:#166534;line-height:1.7;padding-top:3px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">سيتواصل معك الكوتش قريباً لتحديد موعد الجلسة التعريفية المجانية.</div>
                    </td>
                </tr>
            </table>
            {{-- Step 2 ── --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
                <tr>
                    <td style="vertical-align:top;width:32px;">
                        <div style="width:26px;height:26px;background:#16a34a;color:#fff;border-radius:50%;text-align:center;font-size:12px;font-weight:900;line-height:26px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">٢</div>
                    </td>
                    <td style="padding-right:10px;vertical-align:top;">
                        <div style="font-size:13px;color:#166534;line-height:1.7;padding-top:3px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">في الجلسة التعريفية ستضع أهدافك وتبدأ رحلتك رسمياً.</div>
                    </td>
                </tr>
            </table>
            {{-- Step 3 ── --}}
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="vertical-align:top;width:32px;">
                        <div style="width:26px;height:26px;background:#16a34a;color:#fff;border-radius:50%;text-align:center;font-size:12px;font-weight:900;line-height:26px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">٣</div>
                    </td>
                    <td style="padding-right:10px;vertical-align:top;">
                        <div style="font-size:13px;color:#166534;line-height:1.7;padding-top:3px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">تابع تقدمك واطّلع على جلساتك من لوحة تحكمك الشخصية.</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- CTA ── --}}
        @if($isGuest && $accountAutoCreated && $passwordSetUrl)
        {{-- Guest with new auto-created account: must set password to access dashboard --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
            <tr>
                <td align="center">
                    <a href="{{ $passwordSetUrl }}"
                       style="display:inline-block;background:#D4ED57;color:#1C1C1C;font-size:15px;font-weight:900;padding:16px 40px;border-radius:14px;text-decoration:none;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
                        أكمل بياناتك
                    </a>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding-top:10px;">
                    <p style="font-size:12px;color:#9CA3AF;line-height:1.7;margin:0;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
                        تم إنشاء حسابك تلقائياً على MindFitBro.<br>
                        اضغط الزر لتعيين كلمة المرور وإكمال ملفك الشخصي.
                    </p>
                </td>
            </tr>
        </table>
        @elseif($isGuest)
        {{-- Guest whose email already had an existing account: just login --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
            <tr>
                <td align="center">
                    <a href="{{ url('/login') }}"
                       style="display:inline-block;background:#D4ED57;color:#1C1C1C;font-size:15px;font-weight:900;padding:16px 40px;border-radius:14px;text-decoration:none;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
                        أكمل بياناتك
                    </a>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding-top:10px;">
                    <p style="font-size:12px;color:#9CA3AF;line-height:1.7;margin:0;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
                        سجّل دخولك للوصول إلى لوحة التحكم وإكمال ملفك الشخصي.
                    </p>
                </td>
            </tr>
        </table>
        @else
        {{-- Logged-in user at time of purchase: no profile completion needed --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
            <tr>
                <td align="center">
                    <a href="{{ url('/dashboard') }}"
                       style="display:inline-block;background:#174DAD;color:#ffffff;font-size:15px;font-weight:900;padding:16px 40px;border-radius:14px;text-decoration:none;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
                        لوحة التحكم
                    </a>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding-top:10px;">
                    <p style="font-size:12px;color:#9CA3AF;margin:0;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">تابع اشتراكك وجلساتك من لوحة التحكم.</p>
                </td>
            </tr>
        </table>
        @endif

    </div>

    {{-- ── Footer ── --}}
    <div style="background:#F4F7FF;padding:24px 36px;text-align:center;border-top:1px solid #E5EAF3;">
        <p style="font-size:11px;color:#9CA3AF;line-height:1.7;margin:0;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            MindFitBro<br>
            © {{ date('Y') }} MindFitBro. جميع الحقوق محفوظة.
        </p>
    </div>

</div>
</div>
</body>
</html>

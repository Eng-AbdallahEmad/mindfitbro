<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>بخصوص طلب اشتراكك — MindFitBro</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
<style>
body { margin:0; padding:0; background:#F0F4FB; font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif; }
</style>
</head>
<body style="margin:0;padding:0;background:#F0F4FB;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;direction:rtl;">

<div style="max-width:600px;margin:0 auto;padding:32px 16px;">
<div style="background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 4px 24px rgba(23,77,173,0.10);">

    {{-- ── Header ── --}}
    <div style="background:linear-gradient(135deg,#374151 0%,#1f2937 100%);padding:44px 36px;text-align:center;">
        <div style="display:inline-block;background:#FEE2E2;color:#991B1B;font-size:12px;font-weight:900;padding:5px 18px;border-radius:20px;margin-bottom:16px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">لم يتم القبول</div>
        <div style="color:#ffffff;font-size:26px;font-weight:900;margin-bottom:8px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">بخصوص طلب اشتراكك</div>
        <div style="color:rgba(255,255,255,0.65);font-size:13px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">نأسف لإبلاغك بأنه لم يتم تأكيد طلبك</div>
    </div>

    {{-- ── Body ── --}}
    <div style="padding:36px;">

        {{-- Greeting ── --}}
        <p style="font-size:15px;color:#374151;margin-bottom:24px;line-height:1.8;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
            أهلاً <strong style="color:#174DAD;">{{ $customerName }}</strong>،<br>
            شكراً لتواصلك مع MindFitBro. للأسف، لم نتمكن من تأكيد طلب الاشتراك رقم
            <strong style="color:#174DAD;">#{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</strong>
            للسبب التالي:
        </p>

        {{-- Reason box ── --}}
        <div style="background:#FFF5F5;border:1.5px solid #FECACA;border-radius:16px;padding:20px 24px;margin-bottom:28px;">
            <div style="font-size:11px;font-weight:900;color:#EF4444;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">سبب الرفض</div>
            <div style="font-size:14px;color:#374151;line-height:1.8;font-weight:600;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $subscription->rejection_reason ?? 'لم يتم تحديد سبب.' }}</div>
        </div>

        {{-- Order chip (table layout — email-safe) ── --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F7FF;border-radius:16px;margin-bottom:28px;overflow:hidden;">
            <tr>
                <td style="padding:16px 20px;border-left:1px solid #E5EAF3;vertical-align:top;width:33%;">
                    <div style="font-size:11px;color:#9CA3AF;font-weight:700;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">تاريخ الطلب</div>
                    <div style="font-size:14px;font-weight:900;color:#1C1C1C;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;" dir="ltr">{{ $subscription->created_at->format('d/m/Y') }}</div>
                </td>
                <td style="padding:16px 20px;border-left:1px solid #E5EAF3;vertical-align:top;width:33%;text-align:center;">
                    <div style="font-size:11px;color:#9CA3AF;font-weight:700;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">المدة</div>
                    <div style="font-size:14px;font-weight:900;color:#1C1C1C;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $subscription->duration_months }} شهر</div>
                </td>
                <td style="padding:16px 20px;vertical-align:top;width:33%;text-align:left;">
                    <div style="font-size:11px;color:#9CA3AF;font-weight:700;margin-bottom:5px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">الباقة</div>
                    <div style="font-size:14px;font-weight:900;color:#174DAD;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">{{ $subscription->plan->name ?? '—' }}</div>
                </td>
            </tr>
        </table>

        {{-- What to do ── --}}
        <div style="background:#F4F7FF;border-radius:16px;padding:20px 24px;margin-bottom:28px;">
            <div style="font-size:13px;font-weight:900;color:#374151;margin-bottom:10px;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">ماذا يمكنك فعله؟</div>
            <div style="font-size:13px;color:#6B7280;line-height:1.8;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;">
                يمكنك العودة لصفحة طلبك وإعادة المحاولة — إما بالدفع مباشرة بالبطاقة،
                أو برفع إيصال تحويل جديد وصحيح. لا داعي لبدء طلب جديد من الصفر.
                يمكنك أيضاً التواصل معنا مباشرة عبر واتساب لو احتجت مساعدة.
            </div>
        </div>

        {{-- CTA buttons ── --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:14px;">
            <tr>
                <td align="center">
                    <a href="{{ route('paymob.callback', array_filter(['sid' => $subscription->id, 'guest_token' => $subscription->guest_token])) }}"
                       style="display:inline-block;background:#174DAD;color:#ffffff;font-size:15px;font-weight:900;padding:15px 40px;border-radius:14px;text-decoration:none;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;letter-spacing:0.3px;">
                        العودة لطلبي وإعادة المحاولة
                    </a>
                </td>
            </tr>
        </table>

        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
            <tr>
                <td align="center">
                    <a href="https://wa.me/{{ ltrim(config('app.whatsapp', '966593035979'), '+') }}"
                       style="display:inline-block;background:#25D366;color:#ffffff;font-size:15px;font-weight:900;padding:15px 40px;border-radius:14px;text-decoration:none;font-family:'Cairo','Segoe UI',Tahoma,Arial,sans-serif;letter-spacing:0.3px;">
                        تواصل معنا على واتساب
                    </a>
                </td>
            </tr>
        </table>

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

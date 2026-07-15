<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تأكيد الشراء — MindFitBro</title>
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
                تم تأكيد الطلب
            </div>
            <div style="color:#ffffff;font-size:26px;font-weight:900;line-height:1.4;margin-bottom:6px;font-family:'Cairo',Arial,sans-serif;">
                شكراً على ثقتك!
            </div>
            <div style="color:rgba(255,255,255,0.8);font-size:14px;font-family:'Cairo',Arial,sans-serif;">
                تم استلام طلبك بنجاح وسيتم التواصل معك قريباً
            </div>
        </td>
    </tr>

    {{-- ── Body ── --}}
    <tr>
        <td style="padding:36px;">

            @php
                $mailCurrency = $subscription->currency ?? 'SAR';
                $mailSymbol   = \App\Services\Web\CurrencyService::META[$mailCurrency]['symbol'] ?? 'ر.س';
                $mailDec      = \App\Services\Web\CurrencyService::META[$mailCurrency]['decimals'] ?? 0;
                $mailMethod   = config('payment.methods.' . ($subscription->payment_method_key ?? config('payment.currency_to_method.' . $mailCurrency, 'sa_world')), []);
            @endphp

            {{-- Greeting --}}
            <p style="font-size:16px;color:#374151;line-height:1.8;margin:0 0 28px;font-family:'Cairo',Arial,sans-serif;">
                أهلاً <strong style="color:#174DAD;">{{ $subscription->guest_name }}</strong>،<br>
                تم تأكيد شراء باقتك على MindFitBro بنجاح. فيما يلي تفاصيل طلبك:
            </p>

            {{-- Info chips: 3 cols --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                <tr>
                    <td width="33%" style="padding:0 6px 0 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background:#F4F7FF;border-radius:12px;padding:14px 12px;text-align:center;">
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
                                <td style="background:#F4F7FF;border-radius:12px;padding:14px 12px;text-align:center;">
                                    <div style="font-size:11px;color:#9CA3AF;font-weight:600;margin-bottom:5px;font-family:'Cairo',Arial,sans-serif;">تاريخ الطلب</div>
                                    <div style="font-size:14px;font-weight:700;color:#174DAD;font-family:'Cairo',Arial,sans-serif;direction:ltr;">
                                        {{ $subscription->created_at->format('d/m/Y') }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="33%" style="padding:0 0 0 6px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="background:#F4F7FF;border-radius:12px;padding:14px 12px;text-align:center;">
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

            {{-- Invoice Table --}}
            <div style="font-size:11px;font-weight:700;color:#6B7280;letter-spacing:0.5px;margin-bottom:12px;font-family:'Cairo',Arial,sans-serif;">
                تفاصيل الباقة
            </div>
            <table width="100%" cellpadding="0" cellspacing="0" border="1" style="border-collapse:collapse;border:1px solid #E5EAF3;border-radius:14px;overflow:hidden;margin-bottom:20px;">
                <thead>
                    <tr>
                        <th style="background:#F4F7FF;padding:12px 16px;font-size:12px;font-weight:700;color:#6B7280;text-align:right;font-family:'Cairo',Arial,sans-serif;border-bottom:1px solid #E5EAF3;">الباقة</th>
                        <th style="background:#F4F7FF;padding:12px 16px;font-size:12px;font-weight:700;color:#6B7280;text-align:right;font-family:'Cairo',Arial,sans-serif;border-bottom:1px solid #E5EAF3;">الكمية</th>
                        <th style="background:#F4F7FF;padding:12px 16px;font-size:12px;font-weight:700;color:#6B7280;text-align:left;direction:ltr;font-family:'Cairo',Arial,sans-serif;border-bottom:1px solid #E5EAF3;">السعر</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscription->plans_snapshot as $item)
                    <tr>
                        <td style="padding:14px 16px;font-size:14px;color:#1C1C1C;font-weight:700;border-top:1px solid #F0F4FB;font-family:'Cairo',Arial,sans-serif;">{{ $item['plan_name'] }}</td>
                        <td style="padding:14px 16px;font-size:14px;color:#374151;border-top:1px solid #F0F4FB;font-family:'Cairo',Arial,sans-serif;">{{ $item['quantity'] }}</td>
                        <td style="padding:14px 16px;font-size:14px;font-weight:700;color:#174DAD;direction:ltr;text-align:left;border-top:1px solid #F0F4FB;font-family:'Cairo',Arial,sans-serif;">
                            {{ number_format($item['final_price'], $mailDec) }}
                            <span style="font-size:11px;color:#9CA3AF;"> {{ $mailSymbol }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pricing Summary --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#F4F7FF;border-radius:16px;overflow:hidden;margin-bottom:28px;">
                <tr>
                    <td style="padding:20px;">

                        {{-- Subtotal --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:6px;">
                            <tr>
                                <td style="font-size:13px;color:#6B7280;font-family:'Cairo',Arial,sans-serif;">الإجمالي الفرعي</td>
                                <td align="left" style="font-size:13px;font-weight:600;color:#374151;direction:ltr;font-family:'Cairo',Arial,sans-serif;">
                                    {{ number_format($subscription->subtotal, $mailDec) }} {{ $mailSymbol }}
                                </td>
                            </tr>
                        </table>

                        @if($subscription->yearly_discount > 0)
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:6px;">
                            <tr>
                                <td style="font-size:13px;color:#6B7280;font-family:'Cairo',Arial,sans-serif;">خصم سنوي</td>
                                <td align="left" style="font-size:13px;font-weight:600;color:#16a34a;direction:ltr;font-family:'Cairo',Arial,sans-serif;">
                                    - {{ number_format($subscription->yearly_discount, $mailDec) }} {{ $mailSymbol }}
                                </td>
                            </tr>
                        </table>
                        @endif

                        @if($subscription->coupon_discount > 0)
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:6px;">
                            <tr>
                                <td style="font-size:13px;color:#6B7280;font-family:'Cairo',Arial,sans-serif;">خصم الكوبون</td>
                                <td align="left" style="font-size:13px;font-weight:600;color:#16a34a;direction:ltr;font-family:'Cairo',Arial,sans-serif;">
                                    - {{ number_format($subscription->coupon_discount, $mailDec) }} {{ $mailSymbol }}
                                </td>
                            </tr>
                        </table>
                        @endif

                        {{-- Divider --}}
                        <div style="height:1px;background:#E5EAF3;margin:10px 0;"></div>

                        {{-- Total --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="font-size:16px;font-weight:900;color:#1C1C1C;font-family:'Cairo',Arial,sans-serif;">الإجمالي الكلي</td>
                                <td align="left" style="font-size:20px;font-weight:900;color:#174DAD;direction:ltr;font-family:'Cairo',Arial,sans-serif;">
                                    {{ number_format($subscription->total, $mailDec) }} {{ $mailSymbol }}
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>

            {{-- Payment Instructions --}}
            @if(!empty($mailMethod))
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                <tr>
                    <td style="background:#F4F7FF;border:1.5px solid #E0E8FF;border-radius:16px;padding:20px;">
                        <div style="font-size:11px;font-weight:700;color:#6B7280;letter-spacing:0.5px;margin-bottom:14px;font-family:'Cairo',Arial,sans-serif;">
                            تعليمات الدفع
                        </div>
                        @if(($mailMethod['type'] ?? '') === 'instapay')
                        <p style="font-size:13px;color:#374151;margin:0 0 8px;font-family:'Cairo',Arial,sans-serif;">
                            ادفع عبر <strong>InstaPay</strong>
                        </p>
                        @isset($mailMethod['instapay_id'])
                        <p style="font-size:13px;color:#374151;margin:0 0 6px;font-family:'Cairo',Arial,sans-serif;direction:ltr;text-align:right;">
                            InstaPay ID: <strong>{{ $mailMethod['instapay_id'] }}</strong>
                        </p>
                        @endisset
                        @isset($mailMethod['phone'])
                        <p style="font-size:13px;color:#374151;margin:0 0 6px;font-family:'Cairo',Arial,sans-serif;">
                            رقم الهاتف: <strong>{{ $mailMethod['phone'] }}</strong>
                        </p>
                        @endisset
                        @isset($mailMethod['link'])
                        <p style="font-size:13px;color:#374151;margin:6px 0 0;font-family:'Cairo',Arial,sans-serif;">
                            أو <a href="{{ $mailMethod['link'] }}" style="color:#174DAD;font-weight:700;text-decoration:none;">اضغط هنا للدفع مباشرة</a>
                        </p>
                        @endisset
                        @else
                        @isset($mailMethod['bank_name'])
                        <p style="font-size:13px;color:#374151;margin:0 0 6px;font-family:'Cairo',Arial,sans-serif;">البنك: <strong>{{ $mailMethod['bank_name'] }}</strong></p>
                        @endisset
                        @isset($mailMethod['account_name'])
                        <p style="font-size:13px;color:#374151;margin:0 0 6px;font-family:'Cairo',Arial,sans-serif;">اسم الحساب: <strong>{{ $mailMethod['account_name'] }}</strong></p>
                        @endisset
                        @isset($mailMethod['account_number'])
                        <p style="font-size:13px;color:#374151;margin:0 0 6px;font-family:'Cairo',Arial,sans-serif;direction:ltr;text-align:right;">رقم الحساب: <strong>{{ $mailMethod['account_number'] }}</strong></p>
                        @endisset
                        @isset($mailMethod['iban'])
                        <p style="font-size:13px;color:#374151;margin:0 0 6px;font-family:'Cairo',Arial,sans-serif;direction:ltr;text-align:right;">IBAN: <strong>{{ $mailMethod['iban'] }}</strong></p>
                        @endisset
                        @isset($mailMethod['rib'])
                        <p style="font-size:13px;color:#374151;margin:0 0 6px;font-family:'Cairo',Arial,sans-serif;direction:ltr;text-align:right;">RIB: <strong>{{ $mailMethod['rib'] }}</strong></p>
                        @endisset
                        @endif
                        <p style="font-size:13px;font-weight:900;color:#174DAD;margin:10px 0 0;font-family:'Cairo',Arial,sans-serif;">
                            المبلغ: {{ number_format($subscription->total, $mailDec) }} {{ $mailSymbol }}
                        </p>
                    </td>
                </tr>
            </table>
            @endif

            {{-- Status Notice --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                <tr>
                    <td style="background:#fffbe6;border:1.5px solid #fde68a;border-radius:14px;padding:16px 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="32" valign="top" style="font-size:22px;padding-top:2px;"></td>
                                <td style="font-size:13px;color:#92400E;line-height:1.7;font-family:'Cairo',Arial,sans-serif;">
                                    <strong style="color:#78350F;">الخطوة التالية:</strong> باقتك في انتظار التفعيل.
                                    سيتواصل معك الكوتش لتحديد موعد الجلسة التعريفية المجانية
                                    وبعدها تبدأ رحلتك رسمياً! يمكنك أيضاً إنشاء حسابك الآن
                                    لتتابع كل شيء من لوحة تحكمك.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- CTA Button --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
                <tr>
                    <td align="center">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center" style="background:#D4ED57;border-radius:14px;">
                                    <a href="{{ url('/complete-account/' . $subscription->guest_token) }}"
                                       target="_blank"
                                       style="display:inline-block;color:#1C1C1C;font-size:15px;font-weight:900;padding:16px 40px;text-decoration:none;font-family:'Cairo',Arial,sans-serif;">
                                        أكمل بيانات حسابك
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <p style="font-size:12px;color:#9CA3AF;margin:10px 0 0;line-height:1.7;font-family:'Cairo',Arial,sans-serif;">
                            اضغط الزر عشان تنشئ حسابك وتتابع رحلتك من لوحة التحكم<br>
                            (اسمك وإيميلك هيبقوا جاهزين تلقائياً)
                        </p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    {{-- ── Footer ── --}}
    <tr>
        <td style="background:#F4F7FF;padding:24px 36px;text-align:center;border-top:1px solid #E5EAF3;">
            <p style="margin:0 0 10px;">
                <a href="{{ url('/') }}" style="color:#174DAD;font-size:12px;text-decoration:none;margin:0 8px;font-family:'Cairo',Arial,sans-serif;">الموقع الرئيسي</a>
                <a href="{{ url('/privacy-policy') }}" style="color:#174DAD;font-size:12px;text-decoration:none;margin:0 8px;font-family:'Cairo',Arial,sans-serif;">سياسة الخصوصية</a>
                <a href="{{ url('/terms-of-service') }}" style="color:#174DAD;font-size:12px;text-decoration:none;margin:0 8px;font-family:'Cairo',Arial,sans-serif;">الشروط والأحكام</a>
            </p>
            <p style="font-size:11px;color:#9CA3AF;line-height:1.8;margin:0;font-family:'Cairo',Arial,sans-serif;">
                MindFitBro<br>
                هذا البريد أُرسل تلقائياً لـ {{ $subscription->guest_email }}<br>
                © {{ date('Y') }} MindFitBro. جميع الحقوق محفوظة.
            </p>
        </td>
    </tr>

</table>

</td></tr>
</table>

</body>
</html>

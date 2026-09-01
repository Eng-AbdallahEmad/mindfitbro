{{--
    Takes the eligibility method array directly (App\Services\Web\
    PaymentEligibilityService::manualMethodFor()'s return value) plus the
    currency/amount to display — deliberately NOT a $subscription prop.
    Pre-submission on the purchase form there IS no subscription yet; this
    component needs to render off a computed-but-unsaved price. Once a
    subscription exists, the caller passes its own currency/total instead.
--}}
{{--
    liveAmountExpr (optional): a raw Alpine JS expression string evaluated in
    the parent's x-data scope, e.g. "manualFinalPriceFormatted" — when given,
    the amount is x-text-bound to it so it stays correct as duration/coupon
    change, with the server-rendered $total as the pre-hydration/no-JS value.
--}}
@props(['method', 'currency', 'total', 'liveAmountExpr' => null])
@php
    $symbol   = \App\Services\Web\CurrencyService::META[$currency]['symbol'] ?? '';
    $decimals = \App\Services\Web\CurrencyService::META[$currency]['decimals'] ?? 0;
    $totalFormatted = number_format((float) $total, $decimals);
@endphp

@if(!empty($method))
<div class="payment-instructions-block" style="background:#f4f7ff;border-radius:16px;padding:20px 20px 16px;margin-top:20px;margin-bottom: 20px;border:1.5px solid #e0e8ff;">
    <p style="font-size:13px;font-weight:900;color:#6b7280;margin-bottom:14px;text-transform:uppercase;letter-spacing:.5px">
        تعليمات الدفع
    </p>

    @if(($method['type'] ?? '') === 'instapay')
    {{-- InstaPay (Egypt) --}}
    <div style="text-align:center;margin-bottom:12px;">
        <a href="{{ $method['link'] ?? '#' }}"
           target="_blank"
           style="display:inline-block;background:#174DAD;color:#fff;font-weight:900;font-size:14px;padding:14px 32px;border-radius:12px;text-decoration:none;">
            <span class="material-symbols-rounded align-middle" style="font-size:18px;font-variation-settings:'FILL' 1">credit_card</span> ادفع عبر InstaPay
        </a>
    </div>
    <div style="background:#fff;border-radius:12px;padding:14px 16px;font-size:13px;display:flex;flex-direction:column;gap:8px;">
        <div style="display:flex;justify-content:space-between;">
            <span style="color:#6b7280;font-weight:600">InstaPay ID</span>
            <span style="font-weight:800;color:#1c1c1c;direction:ltr">{{ $method['instapay_id'] ?? '' }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;">
            <span style="color:#6b7280;font-weight:600">رقم الهاتف</span>
            <span style="font-weight:800;color:#1c1c1c;direction:ltr">{{ $method['phone'] ?? '' }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;">
            <span style="color:#6b7280;font-weight:600">المبلغ</span>
            <span style="font-weight:900;color:#174DAD;direction:ltr"
                  @if($liveAmountExpr) x-text="{{ $liveAmountExpr }} + ' {{ $symbol }}'" @endif
            >{{ $totalFormatted }} {{ $symbol }}</span>
        </div>
    </div>

    @else
    {{-- Bank Transfer (SA / TN) --}}
    <div style="background:#fff;border-radius:12px;padding:14px 16px;font-size:13px;display:flex;flex-direction:column;gap:8px;">
        @isset($method['bank_name'])
        <div style="display:flex;justify-content:space-between;">
            <span style="color:#6b7280;font-weight:600">البنك</span>
            <span style="font-weight:800;color:#1c1c1c">{{ $method['bank_name'] }}</span>
        </div>
        @endisset
        @isset($method['account_name'])
        <div style="display:flex;justify-content:space-between;">
            <span style="color:#6b7280;font-weight:600">اسم الحساب</span>
            <span style="font-weight:800;color:#1c1c1c">{{ $method['account_name'] }}</span>
        </div>
        @endisset
        @isset($method['account_number'])
        <div style="display:flex;justify-content:space-between;">
            <span style="color:#6b7280;font-weight:600">رقم الحساب</span>
            <span style="font-weight:800;color:#1c1c1c;direction:ltr">{{ $method['account_number'] }}</span>
        </div>
        @endisset
        @isset($method['iban'])
        <div style="display:flex;justify-content:space-between;">
            <span style="color:#6b7280;font-weight:600">IBAN</span>
            <span style="font-weight:800;color:#1c1c1c;direction:ltr;font-size:12px">{{ $method['iban'] }}</span>
        </div>
        @endisset
        @isset($method['rib'])
        <div style="display:flex;justify-content:space-between;">
            <span style="color:#6b7280;font-weight:600">RIB</span>
            <span style="font-weight:800;color:#1c1c1c;direction:ltr">{{ $method['rib'] }}</span>
        </div>
        @endisset
        @isset($method['swift'])
        <div style="display:flex;justify-content:space-between;">
            <span style="color:#6b7280;font-weight:600">SWIFT</span>
            <span style="font-weight:800;color:#1c1c1c;direction:ltr">{{ $method['swift'] }}</span>
        </div>
        @endisset
        <div style="border-top:1px solid #e5eaf3;margin-top:4px;padding-top:8px;display:flex;justify-content:space-between;">
            <span style="color:#6b7280;font-weight:700">المبلغ المطلوب</span>
            <span style="font-weight:900;color:#174DAD;direction:ltr"
                  @if($liveAmountExpr) x-text="{{ $liveAmountExpr }} + ' {{ $symbol }}'" @endif
            >{{ $totalFormatted }} {{ $symbol }}</span>
        </div>
    </div>
    @endif

    <p style="font-size:11px;color:#9ca3af;margin-top:10px;line-height:1.6;text-align:center">
        بعد التحويل، أرسل صورة الإيصال للكوتش وسيتم تفعيل اشتراكك خلال 24 ساعة
    </p>
</div>
@endif

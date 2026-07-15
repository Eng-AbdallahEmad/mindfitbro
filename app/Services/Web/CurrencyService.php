<?php

namespace App\Services\Web;

class CurrencyService
{
    const CURRENCIES = ['SAR', 'EGP', 'TND', 'USD'];

    const META = [
        'SAR' => ['symbol' => 'ر.س', 'decimals' => 0, 'locale' => 'ar-SA'],
        'EGP' => ['symbol' => 'ج.م', 'decimals' => 0, 'locale' => 'ar-EG'],
        'TND' => ['symbol' => 'د.ت', 'decimals' => 3, 'locale' => 'ar-TN'],
        'USD' => ['symbol' => '$',   'decimals' => 2, 'locale' => 'en-US'],
    ];

    // ISO country code → currency
    const COUNTRY_CURRENCY = [
        'SA' => 'SAR',
        'EG' => 'EGP',
        'TN' => 'TND',
    ];

    public function current(): string
    {
        $c = session('currency', 'SAR');
        return in_array($c, self::CURRENCIES) ? $c : 'SAR';
    }

    public function set(string $currency): void
    {
        if (in_array($currency, self::CURRENCIES)) {
            session(['currency' => $currency]);
        }
    }

    public function meta(?string $currency = null): array
    {
        return self::META[$currency ?? $this->current()] ?? self::META['SAR'];
    }

    public function symbol(?string $currency = null): string
    {
        return $this->meta($currency)['symbol'];
    }

    public function decimals(?string $currency = null): int
    {
        return $this->meta($currency)['decimals'];
    }

    public function format(float|string $amount, ?string $currency = null): string
    {
        return number_format((float) $amount, $this->decimals($currency));
    }

    public function fromCountryCode(string $code): string
    {
        return self::COUNTRY_CURRENCY[strtoupper($code)] ?? 'USD';
    }

    public function paymentMethodKey(?string $currency = null): string
    {
        return config('payment.currency_to_method.' . ($currency ?? $this->current()), 'sa_world');
    }

    public function paymentInstructions(?string $currency = null): array
    {
        return config('payment.methods.' . $this->paymentMethodKey($currency), []);
    }

    /** Array consumed by window.MFB_CURRENCY in JS. */
    public function jsConfig(?string $currency = null): array
    {
        $currency = $currency ?? $this->current();
        $meta     = $this->meta($currency);
        return [
            'code'     => $currency,
            'symbol'   => $meta['symbol'],
            'decimals' => $meta['decimals'],
            'locale'   => $meta['locale'],
        ];
    }
}

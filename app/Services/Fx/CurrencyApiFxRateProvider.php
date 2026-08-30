<?php

namespace App\Services\Fx;

use App\Exceptions\FxProviderException;
use Illuminate\Support\Facades\Http;

/**
 * @fawazahmed0/currency-api served via jsDelivr CDN — free, keyless, no
 * attribution required (public-domain/Unlicense project), and genuinely
 * independent infrastructure from ErApiFxRateProvider (different project,
 * different host/CDN) — a real fallback, not the same vendor twice.
 * Confirmed (Batch 5.5) to return EGP for usd/sar/tnd:
 *   usd→egp 50.03118649, sar→egp 13.3592677, tnd→egp 17.15022098
 */
class CurrencyApiFxRateProvider implements FxRateProvider
{
    public function __construct(private readonly int $timeout = 5) {}

    public function name(): string
    {
        return 'currency-api';
    }

    public function fetch(string $currency): float
    {
        $lower = strtolower($currency);

        try {
            $response = Http::timeout($this->timeout)->get(
                "https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/{$lower}.json"
            );
        } catch (\Throwable $e) {
            throw new FxProviderException("currency-api request failed: {$e->getMessage()}", 0, $e);
        }

        if (!$response->ok()) {
            throw new FxProviderException("currency-api HTTP {$response->status()}");
        }

        $rate = $response->json("{$lower}.egp");

        if (!is_numeric($rate)) {
            throw new FxProviderException('currency-api response missing a numeric egp rate');
        }

        return (float) $rate;
    }
}

<?php

namespace App\Services\Fx;

use App\Exceptions\FxProviderException;
use Illuminate\Support\Facades\Http;

/**
 * open.er-api.com — exchangerate-api.com's free, keyless, no-signup endpoint.
 * Confirmed (Batch 5.5) to return EGP for USD/SAR/TND bases:
 *   USD→EGP 50.250989, SAR→EGP 13.400263, TND→EGP 17.310787
 * Requires visible attribution to exchangerate-api.com — see the site footer.
 */
class ErApiFxRateProvider implements FxRateProvider
{
    public function __construct(private readonly int $timeout = 5) {}

    public function name(): string
    {
        return 'er-api';
    }

    public function fetch(string $currency): float
    {
        try {
            $response = Http::timeout($this->timeout)->get("https://open.er-api.com/v6/latest/{$currency}");
        } catch (\Throwable $e) {
            throw new FxProviderException("er-api request failed: {$e->getMessage()}", 0, $e);
        }

        if (!$response->ok()) {
            throw new FxProviderException("er-api HTTP {$response->status()}");
        }

        $data = $response->json();

        if (($data['result'] ?? null) !== 'success') {
            throw new FxProviderException('er-api response result was not "success"');
        }

        $rate = $data['rates']['EGP'] ?? null;

        if (!is_numeric($rate)) {
            throw new FxProviderException('er-api response missing a numeric EGP rate');
        }

        return (float) $rate;
    }
}

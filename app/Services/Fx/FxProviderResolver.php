<?php

namespace App\Services\Fx;

/**
 * Maps the config-driven provider name (services.fx.primary /
 * services.fx.fallback) to a concrete FxRateProvider — this is the "config
 * change, not a rewrite" seam.
 */
class FxProviderResolver
{
    public function primary(): FxRateProvider
    {
        return $this->make((string) config('services.fx.primary', 'er_api'));
    }

    public function fallback(): FxRateProvider
    {
        return $this->make((string) config('services.fx.fallback', 'currency_api'));
    }

    private function make(string $key): FxRateProvider
    {
        $timeout = (int) config('services.fx.http_timeout', 5);

        return match ($key) {
            'er_api' => new ErApiFxRateProvider($timeout),
            'currency_api' => new CurrencyApiFxRateProvider($timeout),
            default => throw new \InvalidArgumentException("Unknown FX provider: '{$key}'."),
        };
    }
}

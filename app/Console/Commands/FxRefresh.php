<?php

namespace App\Console\Commands;

use App\Models\FxRate;
use App\Services\Fx\FxProviderResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FxRefresh extends Command
{
    protected $signature = 'fx:refresh';

    protected $description = 'Fetch SAR/TND/USD → EGP rates (primary provider, falling back on failure) and store them for FxConverter to read. Never called from the checkout path.';

    private const CURRENCIES = ['SAR', 'TND', 'USD'];

    public function handle(FxProviderResolver $resolver): int
    {
        $deviationLimit = (float) config('payment.fx.sanity_deviation_percent', 15);

        foreach (self::CURRENCIES as $currency) {
            $this->refreshOne($currency, $resolver, $deviationLimit);
        }

        return self::SUCCESS;
    }

    private function refreshOne(string $currency, FxProviderResolver $resolver, float $deviationLimit): void
    {
        $primary = $resolver->primary();
        $fallback = $resolver->fallback();

        $rate = null;
        $source = null;

        try {
            $rate = $primary->fetch($currency);
            $source = $primary->name();
        } catch (\Throwable $primaryError) {
            Log::warning('FX primary provider failed, trying fallback', [
                'currency' => $currency,
                'provider' => $primary->name(),
                'error' => $primaryError->getMessage(),
            ]);

            try {
                $rate = $fallback->fetch($currency);
                $source = $fallback->name();
            } catch (\Throwable $fallbackError) {
                Log::error('FX both providers failed — leaving stored rate untouched', [
                    'currency' => $currency,
                    'primary_error' => $primaryError->getMessage(),
                    'fallback_error' => $fallbackError->getMessage(),
                ]);
                $this->error("{$currency}: both providers failed — rate left untouched.");

                return;
            }
        }

        if (!is_numeric($rate) || $rate <= 0) {
            Log::error('FX fetched rate rejected: not a positive number', [
                'currency' => $currency,
                'rate' => $rate,
                'source' => $source,
            ]);
            $this->error("{$currency}: rejected invalid rate ({$rate}).");

            return;
        }

        $existing = FxRate::where('currency', $currency)->first();

        if ($existing) {
            $deviationPercent = abs($rate - (float) $existing->rate_to_egp) / (float) $existing->rate_to_egp * 100;

            if ($deviationPercent > $deviationLimit) {
                Log::critical('FX sanity guard: fetched rate deviates too far from stored rate — rejecting fetch, keeping old value', [
                    'currency' => $currency,
                    'stored_rate' => (float) $existing->rate_to_egp,
                    'fetched_rate' => $rate,
                    'deviation_percent' => round($deviationPercent, 2),
                    'limit_percent' => $deviationLimit,
                    'fetched_source' => $source,
                ]);
                $this->error("{$currency}: fetched rate {$rate} deviates " . round($deviationPercent, 1) . "% from stored {$existing->rate_to_egp} (limit {$deviationLimit}%) — rejected.");

                return;
            }
        }

        FxRate::updateOrCreate(
            ['currency' => $currency],
            ['rate_to_egp' => $rate, 'source' => $source, 'fetched_at' => now()]
        );

        $this->info("{$currency}: {$rate} (source: {$source})");
    }
}

<?php

namespace App\Services;

use App\Exceptions\FxRateNotConfiguredException;
use App\Models\FxRate;
use Illuminate\Support\Facades\Log;

/**
 * Converts an already-decided price into the EGP amount Paymob actually
 * charges (decision D4). Deliberately separate from
 * App\Services\Web\CurrencyService: that class detects/maps a VISITOR's
 * display currency (IP → country → currency), a presentation concern with
 * no notion of markup/rounding/failure. This class does the opposite
 * direction — turning a final price into a charge — and lives flat under
 * App\Services (not App\Services\Web) matching OrderApprovalService /
 * OrderRejectionService: payment-domain logic, not web-presentation logic.
 *
 * NON-NEGOTIABLE (Batch 5.5): this class NEVER makes an HTTP call. The base
 * rate is fetched on a schedule by `php artisan fx:refresh`
 * (App\Services\Fx\*) into the fx_rates table; this class only ever reads
 * that table (plus config for markup/rounding/thresholds/fallback) — so
 * checkout never waits on, or fails because of, an external API.
 */
class FxConverter
{
    /**
     * @return array{cents: int, rate: float, source: string}
     */
    public function toEgpCents(int|float $amount, string $currency): array
    {
        $currency = strtoupper($currency);

        if ($currency === 'EGP') {
            // Pass through untouched — no markup, no rounding change.
            return [
                'cents' => (int) round($amount * 100),
                'rate' => 1.0,
                'source' => 'identity',
            ];
        }

        [$baseRate, $sourceLabel] = $this->resolveBaseRate($currency);

        $markupPercent = (float) config('payment.fx.markup_percent', 0);
        $effectiveRate = $baseRate * (1 + $markupPercent / 100);

        $egpAmount = $this->applyRounding(
            $amount * $effectiveRate,
            (string) config('payment.fx.rounding', 'none')
        );

        return [
            'cents' => (int) round($egpAmount * 100),
            'rate' => $effectiveRate,
            'source' => $sourceLabel,
        ];
    }

    /**
     * Three tiers, all config-driven (payment.fx.stale_after_hours /
     * max_age_hours): fresh → use silently; stale → use, but log a warning
     * on every conversion so a frozen scheduler (audit Risk D-7 — the
     * cPanel cron is unverified) becomes visible instead of invisible;
     * expired → fall back to the manual config rate if one is set, else
     * throw exactly like Batch 5's original null-rate case. Never defaults
     * to 1.0 and never treats a foreign amount as already being EGP.
     *
     * @return array{0: float, 1: string} [baseRate, sourceLabel]
     */
    private function resolveBaseRate(string $currency): array
    {
        $staleAfterHours = (int) config('payment.fx.stale_after_hours', 48);
        $maxAgeHours = (int) config('payment.fx.max_age_hours', 168);

        $fxRate = FxRate::where('currency', $currency)->first();

        if ($fxRate) {
            $ageHours = $fxRate->ageInHours();

            if ($ageHours < $staleAfterHours) {
                return [(float) $fxRate->rate_to_egp, "{$fxRate->source}:fresh"];
            }

            if ($ageHours < $maxAgeHours) {
                Log::warning('Using a stale FX rate — scheduled fx:refresh may not be running (audit Risk D-7)', [
                    'currency' => $currency,
                    'age_hours' => round($ageHours, 1),
                    'source' => $fxRate->source,
                ]);

                return [(float) $fxRate->rate_to_egp, "{$fxRate->source}:stale"];
            }

            // Expired (>= max_age_hours) — fall through to the config fallback.
        }

        $fallbackRate = config("payment.fx.egp_rates.{$currency}");

        if (is_null($fallbackRate)) {
            Log::error('FX rate expired or never fetched, and no config fallback is set — refusing to convert to EGP', [
                'currency' => $currency,
            ]);

            throw new FxRateNotConfiguredException($currency);
        }

        Log::warning('FX rate expired or never fetched — using manual config fallback', [
            'currency' => $currency,
        ]);

        return [(float) $fallbackRate, 'config-fallback'];
    }

    private function applyRounding(float $amount, string $mode): float
    {
        return match ($mode) {
            'none' => $amount,
            'up_to_nearest_5' => ceil($amount / 5) * 5,
            'up_to_nearest_10' => ceil($amount / 10) * 10,
            default => throw new \InvalidArgumentException("Unknown FX rounding mode: '{$mode}'."),
        };
    }
}

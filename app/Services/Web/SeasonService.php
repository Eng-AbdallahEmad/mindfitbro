<?php

namespace App\Services\Web;

use App\Models\Season;
use Illuminate\Support\Facades\Cache;

class SeasonService
{
    const CACHE_KEY = 'active_season_id';
    const CACHE_TTL = 300; // 5 minutes — invalidated immediately on admin action

    // ── Public API ─────────────────────────────────────────────────

    public function getActive(): ?Season
    {
        $id = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Season::active()->value('id');
        });

        return $id ? Season::find($id) : null;
    }

    /**
     * Apply the season discount to a base price.
     * Rounds to nearest integer so the displayed/charged price is always whole.
     */
    public function applyToPrice(float $basePrice, Season $season): float
    {
        return (float) round($basePrice * (1 - (float)$season->discount_percentage / 100));
    }

    /**
     * Season discount amount = original − rounded discounted price.
     * Always an integer difference matching what the customer actually saves.
     */
    public function discountAmount(float $basePrice, Season $season): float
    {
        return $basePrice - $this->applyToPrice($basePrice, $season);
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}

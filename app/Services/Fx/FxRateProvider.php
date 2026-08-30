<?php

namespace App\Services\Fx;

use App\Exceptions\FxProviderException;

/**
 * One implementation per source (ErApiFxRateProvider, CurrencyApiFxRateProvider)
 * so switching primary/fallback is a config change (services.fx.primary /
 * services.fx.fallback), not a rewrite. Only fx:refresh ever calls these —
 * FxConverter itself never makes an HTTP call (non-negotiable, Batch 5.5).
 */
interface FxRateProvider
{
    public function name(): string;

    /**
     * @throws FxProviderException on any transport/parse/data failure —
     *         callers (fx:refresh) decide what to do next (try fallback,
     *         leave stored rate untouched, etc.), this never returns a
     *         guessed or partial value.
     */
    public function fetch(string $currency): float;
}

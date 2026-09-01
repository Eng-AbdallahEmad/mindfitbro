<?php

namespace App\Services\Web;

/**
 * Single source of truth for "is manual bank transfer available to this
 * visitor, and if so, which account." Deliberately gated on the visitor's
 * IP-DETECTED country (session('detected_country'), set once by
 * DetectCurrency, never customer-changeable) — NOT on the display currency
 * (session('currency'), freely switchable via POST /currency/switch with no
 * verification at all). Eligibility must not be self-service; see
 * docs/dual-payment-plan.md A5.
 *
 * Pure function of its input, no session/facade reads inside — callers pass
 * the detected country explicitly, matching the constructor-scalar pattern
 * used by PaymobClient (testable without mocking session state).
 */
class PaymentEligibilityService
{
    /**
     * The manual-transfer method config for this country, or null when
     * manual transfer isn't available. Fails closed: a null/empty/unmapped
     * country, or a currency explicitly disabled in config, both return
     * null — never a fallback to "some" method.
     *
     * @return array{currency: string, enabled: bool}|null
     */
    public function manualMethodFor(?string $detectedCountry): ?array
    {
        if (!$detectedCountry) {
            return null;
        }

        $currency = CurrencyService::COUNTRY_CURRENCY[strtoupper($detectedCountry)] ?? null;

        if (!$currency) {
            return null;
        }

        $config = config("payment.manual.{$currency}");

        if (!is_array($config) || empty($config['enabled'])) {
            return null;
        }

        return array_merge($config, ['currency' => $currency]);
    }

    public function manualAllowedFor(?string $detectedCountry): bool
    {
        return $this->manualMethodFor($detectedCountry) !== null;
    }
}

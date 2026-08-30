<?php

namespace App\Services\Paymob;

use App\Models\Subscription;

/**
 * Batch 5 C2: re-initiating payment overwrites Subscription::paymob_order_id,
 * so a customer who completes payment in an old browser tab sends a callback
 * carrying a superseded order id. That is a legitimate paid order, not a
 * correlation failure — the outcome distinguishes the two so the caller
 * (Batch 6's webhook handler) never confuses "stale but valid" with "wrong".
 */
final readonly class PaymobResolution
{
    public const OUTCOME_CURRENT = 'current';
    public const OUTCOME_STALE_ORDER_ID = 'stale_order_id';
    public const OUTCOME_MERCHANT_REFERENCE_ONLY = 'merchant_reference_only';
    public const OUTCOME_MISMATCH = 'mismatch';
    public const OUTCOME_UNRESOLVED = 'unresolved';

    public function __construct(
        public ?Subscription $subscription,
        public string $outcome,
    ) {}

    /**
     * True for the three outcomes where it's safe to proceed (current,
     * stale-but-valid, or merchant-reference-only). False for mismatch/
     * unresolved, which must never be treated as paid.
     */
    public function isValid(): bool
    {
        return in_array($this->outcome, [
            self::OUTCOME_CURRENT,
            self::OUTCOME_STALE_ORDER_ID,
            self::OUTCOME_MERCHANT_REFERENCE_ONLY,
        ], true);
    }
}

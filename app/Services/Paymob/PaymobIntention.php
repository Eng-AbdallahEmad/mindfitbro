<?php

namespace App\Services\Paymob;

final readonly class PaymobIntention
{
    public function __construct(
        public string $intentionId,
        public string $paymobOrderId,
        public string $clientSecret,
        public string $checkoutUrl,
    ) {}
}

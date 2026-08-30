<?php

namespace App\Services;

final readonly class RejectionResult
{
    public function __construct(
        public string $customerName,
        public ?string $customerEmail,
        public bool $isGuest,
    ) {}
}

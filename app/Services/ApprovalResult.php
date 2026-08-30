<?php

namespace App\Services;

/**
 * Replaces the by-reference closure variables the controller used to mutate
 * (&$accountAutoCreated etc.) — OrderApprovalService::approve() returns one
 * of these instead.
 */
final readonly class ApprovalResult
{
    public function __construct(
        public bool $accountAutoCreated,
        public ?string $passwordSetUrl,
        public string $customerName,
        public ?string $customerEmail,
        public bool $isGuest,
    ) {}
}

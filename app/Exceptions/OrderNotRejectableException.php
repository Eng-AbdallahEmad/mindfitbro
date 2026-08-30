<?php

namespace App\Exceptions;

class OrderNotRejectableException extends \RuntimeException
{
    public function __construct(
        public readonly int $subscriptionId,
        public readonly string $actualStatus,
    ) {
        parent::__construct("Subscription {$subscriptionId} is not rejectable (status: {$actualStatus}).");
    }
}

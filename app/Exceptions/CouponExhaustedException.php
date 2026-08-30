<?php

namespace App\Exceptions;

class CouponExhaustedException extends \RuntimeException
{
    public function __construct(public readonly string $couponCode)
    {
        parent::__construct("Coupon '{$couponCode}' has reached its max_uses limit.");
    }
}

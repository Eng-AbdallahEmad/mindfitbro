<?php

namespace App\Exceptions;

class FxRateNotConfiguredException extends \RuntimeException
{
    public function __construct(public readonly string $currency)
    {
        parent::__construct("No FX rate configured for currency '{$currency}' — refusing to convert to EGP.");
    }
}

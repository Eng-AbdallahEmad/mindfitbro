<?php

namespace App\Services\Paymob;

class PaymobRequestException extends \RuntimeException
{
    /**
     * @param array<string, mixed> $sanitizedResponseBody Never contains the
     *        secret key, HMAC, client secret, or billing/card data — callers
     *        must pass already-sanitized data (see PaymobClient::sanitize()).
     */
    public function __construct(
        string $message,
        public readonly ?int $httpStatus = null,
        public readonly array $sanitizedResponseBody = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}

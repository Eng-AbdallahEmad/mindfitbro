<?php

namespace App\Services\Paymob;

use App\Models\Subscription;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Thin wrapper around Paymob's Intention API (v1/intention/, secret-key
 * auth) — the modern entry point Paymob's current docs lead with, over the
 * legacy 3-step auth-token/order/payment-key flow (audit + Batch 4 Part B,
 * confirmed against developers.paymob.com/paymob-docs/developers/intention-apis/create-intention).
 *
 * Plain injectable class by design: no facade over it, no static state, no
 * config() reads inside methods — every credential/setting is a constructor
 * scalar, read once by the AppServiceProvider binding, so a test can
 * construct one directly with fake values.
 */
class PaymobClient
{
    /**
     * Documented order for the transaction-processed callback HMAC
     * (Batch 4 Part B, official worked example — verified byte-for-byte
     * against the docs' own concatenated-string example). Do NOT sort this
     * at runtime and do NOT alphabetize it — it is not alphabetical
     * (e.g. "id" precedes "integration_id" here only because "id" is the
     * documented position, not because of string sort order).
     */
    private const HMAC_FIELD_ORDER = [
        'amount_cents',
        'created_at',
        'currency',
        'error_occured', // Paymob's actual spelling — not a typo to "fix"
        'has_parent_transaction',
        'id',
        'integration_id',
        'is_3d_secure',
        'is_auth',
        'is_capture',
        'is_refunded',
        'is_standalone_payment',
        'is_voided',
        'order.id',
        'owner',
        'pending',
        'source_data.pan',
        'source_data.sub_type',
        'source_data.type',
        'success',
    ];

    /** special_reference format we generate: sub-{subscriptionId}-{unixTime}-{4 random chars}. */
    private const SPECIAL_REFERENCE_PATTERN = '/^sub-(\d+)-/';

    /** Keys never allowed into a log line, at any nesting depth. */
    private const REDACTED_LOG_KEYS = [
        'secret_key', 'api_key', 'hmac', 'client_secret', 'token',
        'billing_data', 'source_data', 'card_number', 'pan', 'cvn', 'authorization',
    ];

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $secretKey,
        private readonly string $publicKey,
        private readonly string $hmacSecret,
        private readonly ?string $integrationIdCard,
        private readonly int $timeout,
    ) {}

    /**
     * Reads the amount to charge from the ALREADY-PERSISTED
     * charged_amount_cents — never computes or converts a price itself.
     * A null amount is a programming error (the caller skipped FX
     * conversion at order-creation time), not a payment failure, so it
     * throws immediately rather than reaching the network.
     */
    public function createIntention(Subscription $subscription, array $billing): PaymobIntention
    {
        if (is_null($subscription->charged_amount_cents)) {
            throw new \LogicException(
                "Subscription {$subscription->id} has no charged_amount_cents — ".
                'cannot create a Paymob intention for an order that was never priced in EGP.'
            );
        }

        if (empty($this->integrationIdCard)) {
            throw new \LogicException(
                'Paymob card integration id is not configured (services.paymob.integrations.card / PAYMOB_INTEGRATION_ID_CARD).'
            );
        }

        $specialReference = $this->buildSpecialReference($subscription);

        $payload = [
            'amount' => (int) $subscription->charged_amount_cents,
            'currency' => $subscription->charged_currency,
            'payment_methods' => [(int) $this->integrationIdCard],
            'billing_data' => $this->buildBillingData($billing),
            'special_reference' => $specialReference,
            // Per-intention, not dashboard-configured, per Batch 4 instructions.
            'notification_url' => route('paymob.webhook'),
            'redirection_url' => route('paymob.callback'),
        ];

        $response = $this->post('/v1/intention/', $payload, $subscription);
        $body = $response->json();

        $intention = new PaymobIntention(
            intentionId: (string) ($body['id'] ?? ''),
            paymobOrderId: (string) ($body['intention_order_id'] ?? ''),
            clientSecret: (string) ($body['client_secret'] ?? ''),
            checkoutUrl: $this->buildCheckoutUrl((string) ($body['client_secret'] ?? '')),
        );

        Log::info('Paymob intention created', [
            'subscription_id' => $subscription->id,
            'special_reference' => $specialReference,
            'paymob_order_id' => $intention->paymobOrderId,
            'paymob_intention_id' => $intention->intentionId,
        ]);

        return $intention;
    }

    /**
     * $obj is the transaction callback's inner "obj" payload (already
     * unwrapped from the {"type": "TRANSACTION", "obj": {...}} envelope by
     * the caller) — every field in HMAC_FIELD_ORDER is read relative to it.
     */
    public function verifyHmac(array $obj, string $receivedHmac): bool
    {
        $concatenated = $this->buildHmacConcatenation($obj);

        if ($concatenated === null) {
            return false;
        }

        $computed = hash_hmac('sha512', $concatenated, $this->hmacSecret);

        return hash_equals($computed, strtolower(trim($receivedHmac)));
    }

    /**
     * Exposed separately from verifyHmac() so a test can assert the
     * string-builder reproduces Paymob's own documented worked example
     * byte-for-byte, independent of the (unpublished-for-that-example) HMAC
     * secret — see PaymobClientTest.
     */
    public function buildHmacConcatenation(array $obj): ?string
    {
        $parts = [];

        foreach (self::HMAC_FIELD_ORDER as $field) {
            if (!Arr::has($obj, $field)) {
                Log::warning('Paymob HMAC verification: missing field in callback payload', [
                    'field' => $field,
                ]);

                return null;
            }

            $parts[] = $this->stringifyHmacValue(Arr::get($obj, $field));
        }

        return implode('', $parts);
    }

    /**
     * Cross-checks the callback's amount_cents/currency against what we
     * actually persisted at order-creation time. A mismatch must never be
     * treated as a paid order — Batch 6's webhook handler calls this before
     * approving anything.
     */
    public function verifyChargedAmount(Subscription $subscription, array $obj): bool
    {
        $receivedAmount = Arr::get($obj, 'amount_cents');
        $receivedCurrency = Arr::get($obj, 'currency');

        $amountMatches = !is_null($receivedAmount)
            && (int) $receivedAmount === (int) $subscription->charged_amount_cents;
        $currencyMatches = is_string($receivedCurrency)
            && strtoupper($receivedCurrency) === strtoupper((string) $subscription->charged_currency);

        if (!$amountMatches || !$currencyMatches) {
            Log::critical('Paymob callback amount/currency mismatch — refusing to treat as paid', [
                'subscription_id' => $subscription->id,
                'expected_amount_cents' => $subscription->charged_amount_cents,
                'received_amount_cents' => $receivedAmount,
                'expected_currency' => $subscription->charged_currency,
                'received_currency' => $receivedCurrency,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Correlates a callback to our subscription via order.id (checked
     * against BOTH the current paymob_order_id and every previous attempt in
     * paymob_order_ids — Batch 5 C2) and, independently, via
     * merchant_order_id (our special_reference, which embeds the
     * subscription id by construction — see buildSpecialReference()).
     * Prefers the order.id match; distinguishes a stale-but-valid retry from
     * a genuine mismatch instead of collapsing both into one outcome.
     */
    public function resolveSubscription(array $obj): PaymobResolution
    {
        $paymobOrderId = (string) (Arr::get($obj, 'order.id') ?? '');
        $merchantOrderId = (string) (Arr::get($obj, 'merchant_order_id') ?? '');

        $byOrderId = $paymobOrderId !== ''
            ? Subscription::where('paymob_order_id', $paymobOrderId)
                ->orWhereJsonContains('paymob_order_ids', $paymobOrderId)
                ->first()
            : null;

        $bySpecialReference = null;
        if ($merchantOrderId !== '' && preg_match(self::SPECIAL_REFERENCE_PATTERN, $merchantOrderId, $m)) {
            $bySpecialReference = Subscription::find((int) $m[1]);
        }

        if (!$byOrderId && !$bySpecialReference) {
            Log::critical('Paymob callback correlation failed: neither order.id nor merchant_order_id resolved to a subscription', [
                'paymob_order_id' => $paymobOrderId,
                'merchant_order_id' => $merchantOrderId,
            ]);

            return new PaymobResolution(null, PaymobResolution::OUTCOME_UNRESOLVED);
        }

        if ($byOrderId && $bySpecialReference && $byOrderId->id !== $bySpecialReference->id) {
            Log::critical('Paymob callback correlation mismatch: order.id and merchant_order_id resolve to different subscriptions', [
                'paymob_order_id' => $paymobOrderId,
                'merchant_order_id' => $merchantOrderId,
                'resolved_by_order_id' => $byOrderId->id,
                'resolved_by_special_reference' => $bySpecialReference->id,
            ]);

            return new PaymobResolution(null, PaymobResolution::OUTCOME_MISMATCH);
        }

        if ($byOrderId) {
            if ($byOrderId->paymob_order_id === $paymobOrderId) {
                return new PaymobResolution($byOrderId, PaymobResolution::OUTCOME_CURRENT);
            }

            Log::warning('Paymob callback matched a previous (stale) intention attempt — still valid, proceeding', [
                'subscription_id' => $byOrderId->id,
                'callback_paymob_order_id' => $paymobOrderId,
                'current_paymob_order_id' => $byOrderId->paymob_order_id,
            ]);

            return new PaymobResolution($byOrderId, PaymobResolution::OUTCOME_STALE_ORDER_ID);
        }

        // Only merchant_order_id resolved it — order.id was never recognized.
        Log::warning('Paymob callback resolved only via merchant_order_id — order.id not recognized', [
            'subscription_id' => $bySpecialReference->id,
            'paymob_order_id' => $paymobOrderId,
            'merchant_order_id' => $merchantOrderId,
        ]);

        return new PaymobResolution($bySpecialReference, PaymobResolution::OUTCOME_MERCHANT_REFERENCE_ONLY);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sanitize(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), self::REDACTED_LOG_KEYS, true)) {
                $result[$key] = '[REDACTED]';
                continue;
            }

            $result[$key] = is_array($value) ? $this->sanitize($value) : $value;
        }

        return $result;
    }

    private function post(string $path, array $payload, Subscription $subscription): Response
    {
        $maxAttempts = 2;
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $startedAt = microtime(true);

            try {
                $response = Http::baseUrl($this->baseUrl)
                    ->withHeaders(['Authorization' => "Token {$this->secretKey}"])
                    ->timeout($this->timeout)
                    ->post($path, $payload);
            } catch (ConnectionException $e) {
                $lastException = $e;

                Log::warning('Paymob request connection/timeout error', [
                    'subscription_id' => $subscription->id,
                    'path' => $path,
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);

                if ($attempt < $maxAttempts) {
                    continue; // retry once on connection/timeout errors only
                }

                throw new PaymobRequestException(
                    "Paymob request failed: connection/timeout error after {$attempt} attempt(s).",
                    null,
                    [],
                    $e
                );
            }

            $durationMs = (int) ((microtime(true) - $startedAt) * 1000);

            Log::info('Paymob request completed', [
                'subscription_id' => $subscription->id,
                'path' => $path,
                'attempt' => $attempt,
                'status' => $response->status(),
                'duration_ms' => $durationMs,
            ]);

            // Retry once on 5xx: an ambiguous server-side failure creating an
            // intention is recoverable (the special_reference makes a retry
            // traceable, and Paymob rejects a literal duplicate reference
            // rather than silently double-charging). A 4xx is never retried:
            // the server has already told us the request itself is wrong —
            // blind retries on that are exactly how duplicate-charge bugs
            // happen, so it fails immediately instead.
            if ($response->serverError() && $attempt < $maxAttempts) {
                Log::warning('Paymob request server error, retrying once', [
                    'subscription_id' => $subscription->id,
                    'path' => $path,
                    'status' => $response->status(),
                ]);

                continue;
            }

            if ($response->failed()) {
                throw new PaymobRequestException(
                    "Paymob request failed with HTTP {$response->status()}.",
                    $response->status(),
                    $this->sanitize($response->json() ?? [])
                );
            }

            return $response;
        }

        // Unreachable in practice — the loop above always returns or throws —
        // kept only so static analysis sees every path produce a value.
        throw new PaymobRequestException('Paymob request failed after retries.', null, [], $lastException);
    }

    private function stringifyHmacValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return '';
        }

        return (string) $value;
    }

    /**
     * Retry-safe merchant order reference: re-initiating payment for the
     * same subscription (a customer retrying after a failed/abandoned
     * attempt) must never collide with a previous attempt's reference, since
     * Paymob rejects a duplicate special_reference outright. The
     * subscription id is embedded as a parseable prefix (sub-{id}-...) so
     * resolveSubscription() can recover it from merchant_order_id with no
     * extra column or DB round trip.
     */
    private function buildSpecialReference(Subscription $subscription): string
    {
        return sprintf('sub-%d-%d-%s', $subscription->id, time(), Str::lower(Str::random(4)));
    }

    /**
     * We now collect full_name + email + phone at checkout (Batch 5 C1 made
     * phone required and removed the 'NA' placeholder that used to stand in
     * for it — always send the real value). Everything else here is either
     * derived from real input or a clearly-marked placeholder — never a
     * fabricated real-looking value. The apartment/floor/street/building/
     * postal_code/state convention ('null' string / 'NA') mirrors a real
     * observed Paymob billing_data payload (community fixture,
     * baklysystems/laravel-paymob), not an invented format. country is 'EG'
     * because this Paymob account only ever charges in EGP (decision D1) —
     * that describes which gateway/country processes the charge, not a
     * (possibly false) claim about the customer's real location.
     */
    private function buildBillingData(array $billing): array
    {
        [$firstName, $lastName] = $this->splitName(trim((string) ($billing['full_name'] ?? '')));

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => (string) ($billing['email'] ?? ''),
            'phone_number' => (string) ($billing['phone_number'] ?? ''),
            'apartment' => 'null',
            'floor' => 'null',
            'street' => 'null',
            'building' => 'null',
            'city' => 'NA',
            'state' => 'NA',
            'country' => 'EG',
            'postal_code' => 'NA',
        ];
    }

    private function splitName(string $fullName): array
    {
        if ($fullName === '') {
            return ['NA', 'NA'];
        }

        $parts = preg_split('/\s+/', $fullName, 2);

        return [$parts[0], $parts[1] ?? $parts[0]];
    }

    private function buildCheckoutUrl(string $clientSecret): string
    {
        return sprintf(
            '%s/unifiedcheckout/?publicKey=%s&clientSecret=%s',
            rtrim($this->baseUrl, '/'),
            urlencode($this->publicKey),
            urlencode($clientSecret)
        );
    }
}

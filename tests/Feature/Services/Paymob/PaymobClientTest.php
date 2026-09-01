<?php

namespace Tests\Feature\Services\Paymob;

use App\Models\Subscription;
use App\Services\Paymob\PaymobClient;
use App\Services\Paymob\PaymobRequestException;
use App\Services\Paymob\PaymobResolution;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PaymobClientTest extends TestCase
{
    private function client(
        ?string $integrationId = '4321',
        ?string $walletId = null,
        ?string $applePayId = null,
    ): PaymobClient
    {
        return new PaymobClient(
            baseUrl: 'https://accept.paymob.com',
            secretKey: 'sk_test_topsecret',
            publicKey: 'pk_test_public',
            hmacSecret: 'hmac_test_secret',
            integrationIdCard: $integrationId,
            timeout: 5,
            integrationIdWallet: $walletId,
            integrationIdApplePay: $applePayId,
        );
    }

    // ── createIntention: happy path ─────────────────────────────────────

    public function test_creates_intention_with_correct_request_and_response_mapping(): void
    {
        Http::fake([
            'accept.paymob.com/v1/intention/' => Http::response([
                'id' => 'int_abc123',
                'intention_order_id' => 555666,
                'client_secret' => 'csecret_xyz',
            ], 201),
        ]);

        $subscription = Subscription::factory()->paidViaPaymob()->create([
            'charged_amount_cents' => 12345,
            'charged_currency' => 'EGP',
        ]);

        $intention = $this->client()->createIntention($subscription, [
            'full_name' => 'Ahmed Ali',
            'email' => 'ahmed@example.com',
        ]);

        $this->assertSame('int_abc123', $intention->intentionId);
        $this->assertSame('555666', $intention->paymobOrderId);
        $this->assertSame('csecret_xyz', $intention->clientSecret);
        $this->assertStringStartsWith('https://accept.paymob.com/unifiedcheckout/?', $intention->checkoutUrl);
        $this->assertStringContainsString('clientSecret=csecret_xyz', $intention->checkoutUrl);
        $this->assertStringContainsString('publicKey=pk_test_public', $intention->checkoutUrl);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://accept.paymob.com/v1/intention/'
                && $request->hasHeader('Authorization', 'Token sk_test_topsecret')
                && $request['amount'] === 12345
                && is_int($request['amount'])
                && $request['currency'] === 'EGP'
                && $request['payment_methods'] === [4321]
                && $request['billing_data']['first_name'] === 'Ahmed'
                && $request['billing_data']['last_name'] === 'Ali'
                && $request['billing_data']['email'] === 'ahmed@example.com'
                && str_starts_with($request['special_reference'], "sub-{$request['special_reference']}") === false // sanity noop
                && str_starts_with($request['special_reference'], 'sub-');
        });

        Http::assertSentCount(1);
    }

    public function test_appends_wallet_and_apple_pay_integration_ids_when_configured(): void
    {
        Http::fake([
            'accept.paymob.com/v1/intention/' => Http::response([
                'id' => 'int_abc123', 'intention_order_id' => 555666, 'client_secret' => 'csecret_xyz',
            ], 201),
        ]);

        $subscription = Subscription::factory()->paidViaPaymob()->create([
            'charged_amount_cents' => 12345,
            'charged_currency' => 'EGP',
        ]);

        $this->client(integrationId: '4321', walletId: '8765', applePayId: '9999')
            ->createIntention($subscription, ['full_name' => 'Ahmed Ali', 'email' => 'ahmed@example.com']);

        Http::assertSent(fn ($request) => $request['payment_methods'] === [4321, 8765, 9999]);
    }

    public function test_omits_wallet_and_apple_pay_from_payment_methods_when_not_configured(): void
    {
        Http::fake([
            'accept.paymob.com/v1/intention/' => Http::response([
                'id' => 'int_abc123', 'intention_order_id' => 555666, 'client_secret' => 'csecret_xyz',
            ], 201),
        ]);

        $subscription = Subscription::factory()->paidViaPaymob()->create([
            'charged_amount_cents' => 12345,
            'charged_currency' => 'EGP',
        ]);

        $this->client(integrationId: '4321')
            ->createIntention($subscription, ['full_name' => 'Ahmed Ali', 'email' => 'ahmed@example.com']);

        Http::assertSent(fn ($request) => $request['payment_methods'] === [4321]);
    }

    // ── createIntention: guard clauses ──────────────────────────────────

    public function test_throws_immediately_without_hitting_network_when_amount_not_persisted(): void
    {
        Http::fake();

        $subscription = Subscription::factory()->create(['charged_amount_cents' => null]);

        $this->expectException(\LogicException::class);

        try {
            $this->client()->createIntention($subscription, ['full_name' => 'X', 'email' => 'x@x.com']);
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_throws_immediately_when_integration_id_not_configured(): void
    {
        Http::fake();

        $subscription = Subscription::factory()->paidViaPaymob()->create();

        $this->expectException(\LogicException::class);

        try {
            $this->client(integrationId: null)->createIntention($subscription, ['full_name' => 'X', 'email' => 'x@x.com']);
        } finally {
            Http::assertNothingSent();
        }
    }

    // ── createIntention: HTTP error handling ────────────────────────────

    public function test_api_400_throws_immediately_and_is_never_retried(): void
    {
        Http::fake([
            'accept.paymob.com/v1/intention/' => Http::response(['message' => 'bad request'], 400),
        ]);

        $subscription = Subscription::factory()->paidViaPaymob()->create();

        try {
            $this->client()->createIntention($subscription, ['full_name' => 'X', 'email' => 'x@x.com']);
            $this->fail('Expected PaymobRequestException');
        } catch (PaymobRequestException $e) {
            $this->assertSame(400, $e->httpStatus);
        }

        Http::assertSentCount(1);
    }

    public function test_api_500_is_retried_once_then_throws(): void
    {
        Http::fake([
            'accept.paymob.com/v1/intention/' => Http::sequence()
                ->push(['message' => 'server error'], 500)
                ->push(['message' => 'server error'], 500),
        ]);

        $subscription = Subscription::factory()->paidViaPaymob()->create();

        try {
            $this->client()->createIntention($subscription, ['full_name' => 'X', 'email' => 'x@x.com']);
            $this->fail('Expected PaymobRequestException');
        } catch (PaymobRequestException $e) {
            $this->assertSame(500, $e->httpStatus);
        }

        Http::assertSentCount(2);
    }

    public function test_connection_timeout_is_retried_once_then_throws(): void
    {
        // Http::fake()'s "recorded" bookkeeping doesn't capture requests that
        // throw via a fake closure (assertSentCount reports 0 either way),
        // so the retry count is proven via the two connection-error warning
        // logs instead — one per attempt — which is a direct assertion on
        // the retry loop actually running twice.
        $captured = [];
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Log\Events\MessageLogged::class,
            function ($event) use (&$captured) {
                if ($event->message === 'Paymob request connection/timeout error') {
                    $captured[] = $event->context;
                }
            }
        );

        Http::fake([
            'accept.paymob.com/v1/intention/' => function () {
                throw new ConnectionException('Connection timed out');
            },
        ]);

        $subscription = Subscription::factory()->paidViaPaymob()->create();

        try {
            $this->client()->createIntention($subscription, ['full_name' => 'X', 'email' => 'x@x.com']);
            $this->fail('Expected PaymobRequestException');
        } catch (PaymobRequestException $e) {
            $this->assertNull($e->httpStatus);
        }

        $this->assertCount(2, $captured, 'expected exactly 2 attempts (1 initial + 1 retry)');
        $this->assertSame(1, $captured[0]['attempt']);
        $this->assertSame(2, $captured[1]['attempt']);
    }

    // ── HMAC: the documented worked example (Batch 4 Part B) ───────────

    /**
     * The official docs page publishes this exact obj payload and its
     * resulting concatenated string, but NOT the HMAC secret used to
     * produce the final hash for that example. So this fixture proves the
     * field order, concatenation, and boolean/null formatting are correct
     * (byte-for-byte against the documented string) — it does NOT prove the
     * final hash matches a real Paymob-issued value. That can only be
     * confirmed against a live sandbox transaction (paymob:ping / a real
     * webhook delivery).
     */
    private function documentedWorkedExampleObj(): array
    {
        return [
            'amount_cents' => 100,
            'created_at' => '2020-03-25T18:39:44.719228',
            'currency' => 'EGP',
            'error_occured' => false,
            'has_parent_transaction' => false,
            'id' => 2556706,
            'integration_id' => 6741,
            'is_3d_secure' => true,
            'is_auth' => false,
            'is_capture' => false,
            'is_refunded' => false,
            'is_standalone_payment' => true,
            'is_voided' => false,
            'order' => ['id' => 4778239],
            'owner' => 4705,
            'pending' => false,
            'source_data' => ['pan' => '2346', 'sub_type' => 'MasterCard', 'type' => 'card'],
            'success' => true,
        ];
    }

    public function test_hmac_concatenation_matches_documented_worked_example_byte_for_byte(): void
    {
        $expected = '1002020-03-25T18:39:44.719228EGPfalsefalse25567066741truefalsefalsefalsetruefalse47782394705false2346MasterCardcardtrue';

        $got = $this->client()->buildHmacConcatenation($this->documentedWorkedExampleObj());

        $this->assertSame($expected, $got);
    }

    public function test_hmac_verify_passes_on_internally_consistent_fixture(): void
    {
        $obj = $this->documentedWorkedExampleObj();
        $client = $this->client();

        $concatenated = $client->buildHmacConcatenation($obj);
        // Ground truth computed independently of PaymobClient, with a secret
        // WE chose (not Paymob's) — see fixture docblock above.
        $selfComputedHmac = hash_hmac('sha512', $concatenated, 'hmac_test_secret');

        $this->assertTrue($client->verifyHmac($obj, $selfComputedHmac));
    }

    public function test_hmac_verify_fails_when_a_field_is_tampered_with(): void
    {
        $obj = $this->documentedWorkedExampleObj();
        $client = $this->client();

        $originalConcatenated = $client->buildHmacConcatenation($obj);
        $validHmac = hash_hmac('sha512', $originalConcatenated, 'hmac_test_secret');

        $tampered = $obj;
        $tampered['amount_cents'] = 999999; // attacker inflates the charged amount

        $this->assertFalse($client->verifyHmac($tampered, $validHmac));
    }

    public function test_hmac_verify_fails_on_missing_field(): void
    {
        Log::spy();

        $obj = $this->documentedWorkedExampleObj();
        unset($obj['source_data']); // whole nested object missing, not just one leaf

        $client = $this->client();
        $concatenated = $client->buildHmacConcatenation($this->documentedWorkedExampleObj());
        $validHmac = hash_hmac('sha512', $concatenated, 'hmac_test_secret');

        $this->assertFalse($client->verifyHmac($obj, $validHmac));

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(fn ($message, $context) => str_contains($message, 'missing field') && isset($context['field']));
    }

    // ── No secret/HMAC leaks into logs ──────────────────────────────────

    public function test_no_log_line_across_the_above_scenarios_contains_the_secret_or_hmac(): void
    {
        $captured = [];
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Log\Events\MessageLogged::class,
            function ($event) use (&$captured) {
                $captured[] = $event->message . ' ' . json_encode($event->context);
            }
        );

        Http::fake([
            'accept.paymob.com/v1/intention/' => Http::response(['message' => 'bad request', 'secret_key' => 'sk_should_never_appear'], 400),
        ]);

        $subscription = Subscription::factory()->paidViaPaymob()->create();

        try {
            $this->client()->createIntention($subscription, ['full_name' => 'X', 'email' => 'x@x.com']);
        } catch (PaymobRequestException) {
            // expected — this scenario is exactly why sanitize() exists:
            // the fake response body above deliberately contains a
            // "secret_key" to prove it gets redacted before logging.
        }

        $obj = $this->documentedWorkedExampleObj();
        $client = $this->client();
        $concatenated = $client->buildHmacConcatenation($obj);
        $validHmac = hash_hmac('sha512', $concatenated, 'hmac_test_secret');
        $client->verifyHmac($obj, $validHmac);

        $this->assertNotEmpty($captured, 'expected at least one log line to have been captured');

        $forbidden = ['sk_test_topsecret', 'hmac_test_secret', $validHmac, 'sk_should_never_appear'];

        foreach ($captured as $line) {
            foreach ($forbidden as $needle) {
                $this->assertStringNotContainsString($needle, $line, "Log line leaked a secret: {$line}");
            }
        }
    }

    // ── resolveSubscription: C2 stale-intention outcomes ────────────────

    public function test_resolve_subscription_current_order_id(): void
    {
        $subscription = Subscription::factory()->paidViaPaymob()->create([
            'paymob_order_id' => '111',
            'paymob_order_ids' => ['111'],
        ]);

        $resolution = $this->client()->resolveSubscription(['order' => ['id' => '111']]);

        $this->assertSame(PaymobResolution::OUTCOME_CURRENT, $resolution->outcome);
        $this->assertTrue($resolution->isValid());
        $this->assertSame($subscription->id, $resolution->subscription->id);
    }

    public function test_resolve_subscription_stale_but_previously_used_order_id_is_still_valid(): void
    {
        $subscription = Subscription::factory()->paidViaPaymob()->create([
            'paymob_order_id' => '222', // current, from a retry
            'paymob_order_ids' => ['111', '222'], // '111' was the first (now stale) attempt
        ]);

        $resolution = $this->client()->resolveSubscription(['order' => ['id' => '111']]);

        $this->assertSame(PaymobResolution::OUTCOME_STALE_ORDER_ID, $resolution->outcome);
        $this->assertTrue($resolution->isValid());
        $this->assertSame($subscription->id, $resolution->subscription->id);
    }

    public function test_resolve_subscription_via_merchant_order_id_only(): void
    {
        $subscription = Subscription::factory()->paidViaPaymob()->create([
            'paymob_order_id' => '999',
            'paymob_order_ids' => ['999'],
        ]);

        // order.id is something we've never seen; merchant_order_id still
        // recovers the subscription via our own special_reference format.
        $resolution = $this->client()->resolveSubscription([
            'order' => ['id' => 'unknown-order-id'],
            'merchant_order_id' => "sub-{$subscription->id}-1700000000-abcd",
        ]);

        $this->assertSame(PaymobResolution::OUTCOME_MERCHANT_REFERENCE_ONLY, $resolution->outcome);
        $this->assertTrue($resolution->isValid());
        $this->assertSame($subscription->id, $resolution->subscription->id);
    }

    public function test_resolve_subscription_mismatch_between_order_id_and_merchant_reference(): void
    {
        $subscriptionA = Subscription::factory()->paidViaPaymob()->create([
            'paymob_order_id' => '333',
            'paymob_order_ids' => ['333'],
        ]);
        $subscriptionB = Subscription::factory()->paidViaPaymob()->create();

        $resolution = $this->client()->resolveSubscription([
            'order' => ['id' => '333'], // resolves to subscriptionA
            'merchant_order_id' => "sub-{$subscriptionB->id}-1700000000-abcd", // resolves to subscriptionB
        ]);

        $this->assertSame(PaymobResolution::OUTCOME_MISMATCH, $resolution->outcome);
        $this->assertFalse($resolution->isValid());
        $this->assertNull($resolution->subscription);
    }

    public function test_resolve_subscription_unresolved_when_nothing_matches(): void
    {
        $resolution = $this->client()->resolveSubscription([
            'order' => ['id' => 'nobody-has-this'],
            'merchant_order_id' => 'not-our-format-at-all',
        ]);

        $this->assertSame(PaymobResolution::OUTCOME_UNRESOLVED, $resolution->outcome);
        $this->assertFalse($resolution->isValid());
        $this->assertNull($resolution->subscription);
    }
}

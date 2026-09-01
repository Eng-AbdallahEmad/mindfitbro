<?php

namespace Tests\Feature\Web;

use App\Mail\OrderApprovedMail;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Paymob\PaymobClient;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaymobWebhookTest extends TestCase
{
    private const HMAC_SECRET = 'test-hmac-secret';

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.paymob.hmac_secret', self::HMAC_SECRET);
        Config::set('services.paymob.base_url', 'https://accept.paymob.com');
        Config::set('services.paymob.secret_key', 'sk_test');
        Config::set('services.paymob.public_key', 'pk_test');
        Config::set('services.paymob.integrations.card', '4321');
    }

    private function makeSubscription(array $overrides = []): Subscription
    {
        return Subscription::factory()->guest()->create(array_merge([
            'status' => Subscription::STATUS_AWAITING_PAYMENT,
            'payment_gateway' => Subscription::GATEWAY_PAYMOB,
            'charged_currency' => 'EGP',
            'charged_amount_cents' => 133000,
            'paymob_order_id' => '555666',
            'paymob_order_ids' => ['555666'],
        ], $overrides));
    }

    private function buildObj(array $overrides = []): array
    {
        return array_merge([
            'amount_cents' => 133000,
            'created_at' => '2020-03-25T18:39:44.719228',
            'currency' => 'EGP',
            'error_occured' => false,
            'has_parent_transaction' => false,
            'id' => '999001',
            'integration_id' => 4321,
            'is_3d_secure' => true,
            'is_auth' => false,
            'is_capture' => false,
            'is_refunded' => false,
            'is_standalone_payment' => true,
            'is_voided' => false,
            'order' => ['id' => '555666'],
            'owner' => 12345,
            'pending' => false,
            'source_data' => ['pan' => '2346', 'sub_type' => 'MasterCard', 'type' => 'card'],
            'success' => true,
        ], $overrides);
    }

    private function signedPayload(array $objOverrides = [], string $type = 'TRANSACTION'): array
    {
        $obj = $this->buildObj($objOverrides);
        $client = new PaymobClient('https://accept.paymob.com', 'sk', 'pk', self::HMAC_SECRET, null, 5);
        $concatenated = $client->buildHmacConcatenation($obj);

        return [
            'body' => ['type' => $type, 'obj' => $obj],
            'hmac' => hash_hmac('sha512', $concatenated, self::HMAC_SECRET),
        ];
    }

    private function postWebhook(array $signed)
    {
        return $this->postJson('/paymob/webhook?hmac=' . $signed['hmac'], $signed['body']);
    }

    // ── Valid webhook: full approval side effects ───────────────────────

    public function test_valid_webhook_approves_exactly_once_with_full_side_effects(): void
    {
        Mail::fake();
        $guestEmail = 'webhook-guest@example.com';
        $subscription = $this->makeSubscription(['guest_email' => $guestEmail]);
        $usersBefore = User::count();

        $response = $this->postWebhook($this->signedPayload());

        $response->assertStatus(200);

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_APPROVED, $subscription->status);
        $this->assertNotNull($subscription->paid_at);
        $this->assertSame('999001', $subscription->paymob_transaction_id);

        // Batch 3 characterization: guest with no matching user -> new account,
        // guest_name/guest_email nulled, guest_token preserved as the
        // setup-account key.
        $this->assertSame($usersBefore + 1, User::count());
        $newUser = User::where('email', $guestEmail)->first();
        $this->assertNotNull($newUser);
        $this->assertSame($newUser->id, $subscription->user_id);
        $this->assertNull($subscription->guest_email);
        $this->assertNotNull($subscription->guest_token);

        Mail::assertSent(OrderApprovedMail::class, function ($mail) use ($subscription) {
            return $mail->subscription->id === $subscription->id && $mail->accountAutoCreated === true;
        });
    }

    // ── Replay: idempotency ──────────────────────────────────────────────

    /**
     * Illuminate\Foundation\Application::terminate() iterates
     * $terminatingCallbacks but never clears the array — harmless in real
     * production (each HTTP request is a fresh PHP process/app boot, no
     * Octane here, so the list always starts empty), but within ONE
     * PHPUnit test process, three simulated requests share the SAME app
     * instance and its callback list, so an afterResponse() callback from
     * request #1 would replay on #2 and #3's terminate() too. This resets
     * that list between simulated requests to match what a real separate
     * process boot naturally provides — otherwise this test would fail for
     * a testing-harness reason, not a real idempotency bug.
     */
    private function resetTerminatingCallbacks(): void
    {
        $prop = new \ReflectionProperty(app(), 'terminatingCallbacks');
        $prop->setAccessible(true);
        $prop->setValue(app(), []);
    }

    public function test_replayed_webhook_three_times_approves_once_creates_one_user_sends_one_mail(): void
    {
        Mail::fake();
        $subscription = $this->makeSubscription();
        $signed = $this->signedPayload();

        $usersBefore = User::count();

        $this->postWebhook($signed)->assertStatus(200);
        $this->resetTerminatingCallbacks();
        $this->postWebhook($signed)->assertStatus(200);
        $this->resetTerminatingCallbacks();
        $this->postWebhook($signed)->assertStatus(200);

        $this->assertSame($usersBefore + 1, User::count(), 'exactly one user must be created across all 3 deliveries');
        Mail::assertSent(OrderApprovedMail::class, 1);

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_APPROVED, $subscription->status);
    }

    // ── Tampered HMAC ────────────────────────────────────────────────────

    public function test_tampered_hmac_returns_401_and_writes_nothing(): void
    {
        $subscription = $this->makeSubscription();
        $signed = $this->signedPayload();
        $signed['body']['obj']['amount_cents'] = 999999; // tamper after signing

        $response = $this->postWebhook($signed);

        $response->assertStatus(401);

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->status);
        $this->assertNull($subscription->paid_at);
        $this->assertNull($subscription->paymob_transaction_id);
    }

    // ── Failed transaction ───────────────────────────────────────────────

    public function test_failed_transaction_payload_marks_payment_failed_not_approved(): void
    {
        $subscription = $this->makeSubscription();

        $response = $this->postWebhook($this->signedPayload(['success' => false]));

        $response->assertStatus(200);

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_PAYMENT_FAILED, $subscription->status);
        $this->assertNotNull($subscription->payment_failure_reason);
        $this->assertNull($subscription->paid_at);
    }

    // ── Amount / currency mismatch ───────────────────────────────────────

    public function test_amount_mismatch_does_not_approve_and_logs_critical(): void
    {
        Log::spy();
        $subscription = $this->makeSubscription(['charged_amount_cents' => 133000]);

        $response = $this->postWebhook($this->signedPayload(['amount_cents' => 100]));

        $response->assertStatus(200);

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->status);
        $this->assertNull($subscription->paid_at);

        Log::shouldHaveReceived('critical')
            ->withArgs(fn ($message) => str_contains($message, 'mismatch'));
    }

    public function test_currency_mismatch_does_not_approve(): void
    {
        Log::spy();
        $subscription = $this->makeSubscription(['charged_currency' => 'EGP']);

        $response = $this->postWebhook($this->signedPayload(['currency' => 'USD']));

        $response->assertStatus(200);

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->status);
        $this->assertNull($subscription->paid_at);
    }

    // ── Unresolvable order ───────────────────────────────────────────────

    public function test_unresolvable_order_id_returns_200_logs_critical_and_writes_nothing(): void
    {
        Log::spy();
        $subscription = $this->makeSubscription();

        $response = $this->postWebhook($this->signedPayload(['order' => ['id' => 'no-such-order']]));

        $response->assertStatus(200);

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->status, 'the unrelated subscription must be untouched');

        Log::shouldHaveReceived('critical')
            ->withArgs(fn ($message) => str_contains($message, 'UNRESOLVED') || str_contains($message, 'unresolved'));
    }

    // ── Stale intention (Batch 5 C2) ─────────────────────────────────────

    public function test_stale_intention_order_id_still_approves_with_a_warning(): void
    {
        Mail::fake();
        Log::spy();

        $subscription = $this->makeSubscription([
            'paymob_order_id' => 'NEW999', // current attempt, from a retry
            'paymob_order_ids' => ['555666', 'NEW999'], // 555666 was the first (now stale) attempt
        ]);

        // The webhook references the STALE (first) order id.
        $response = $this->postWebhook($this->signedPayload(['order' => ['id' => '555666']]));

        $response->assertStatus(200);

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_APPROVED, $subscription->status, 'a stale-but-valid order id must still approve');

        Log::shouldHaveReceived('warning')
            ->withArgs(fn ($message) => str_contains($message, 'stale'));
    }

    // ── Callback page never mutates (D5) ─────────────────────────────────

    public function test_callback_page_never_mutates_even_with_success_true_in_the_query(): void
    {
        $subscription = $this->makeSubscription();

        $response = $this->get('/paymob/callback?' . http_build_query([
            'order' => $subscription->paymob_order_id,
            'success' => 'true',
            'id' => '999999',
            'guest_token' => $subscription->guest_token,
        ]));

        $response->assertOk();

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->status, 'the callback page must never approve anything');
        $this->assertNull($subscription->paid_at);
    }

    // ── Callback page authorization (owner or matching guest_token only) ──

    public function test_callback_page_rejects_wrong_guest_token(): void
    {
        $subscription = $this->makeSubscription(['guest_token' => 'correct-token']);

        $this->get('/paymob/callback?' . http_build_query(['sid' => $subscription->id, 'guest_token' => 'wrong-token']))
            ->assertStatus(403);

        $this->get('/paymob/callback?' . http_build_query(['sid' => $subscription->id]))
            ->assertStatus(403);
    }

    public function test_callback_page_accepts_correct_guest_token(): void
    {
        $subscription = $this->makeSubscription(['guest_token' => 'correct-token']);

        $this->get('/paymob/callback?' . http_build_query(['sid' => $subscription->id, 'guest_token' => 'correct-token']))
            ->assertOk()
            ->assertSee($subscription->invoiceNumber());
    }

    public function test_callback_page_rejects_a_logged_in_user_who_does_not_own_the_subscription(): void
    {
        $owner   = User::factory()->create();
        $someoneElse = User::factory()->create();
        $subscription = $this->makeSubscription(['user_id' => $owner->id, 'guest_email' => null, 'guest_name' => null, 'guest_token' => null]);

        $this->actingAs($someoneElse)
            ->get('/paymob/callback?' . http_build_query(['sid' => $subscription->id]))
            ->assertStatus(403);
    }

    public function test_callback_page_accepts_the_owning_authenticated_user_with_no_token_needed(): void
    {
        $owner = User::factory()->create();
        $subscription = $this->makeSubscription(['user_id' => $owner->id, 'guest_email' => null, 'guest_name' => null, 'guest_token' => null]);

        $this->actingAs($owner)
            ->get('/paymob/callback?' . http_build_query(['sid' => $subscription->id]))
            ->assertOk()
            ->assertSee($subscription->invoiceNumber());
    }

    public function test_callback_page_never_leaks_another_customers_data_via_the_bare_order_id(): void
    {
        $victim = $this->makeSubscription(['guest_token' => 'victim-token', 'guest_name' => 'ضحية سرية']);

        // No sid, no guest_token — only Paymob's own (guessable) order id.
        $this->get('/paymob/callback?' . http_build_query(['order' => $victim->paymob_order_id]))
            ->assertStatus(403)
            ->assertDontSee('ضحية سرية');
    }

    // ── Status endpoint authorization ────────────────────────────────────

    public function test_status_endpoint_rejects_wrong_guest_token(): void
    {
        $subscription = $this->makeSubscription(['guest_token' => 'correct-token']);

        $this->getJson(route('purchase.status', $subscription) . '?guest_token=wrong-token')
            ->assertStatus(403);

        $this->getJson(route('purchase.status', $subscription))
            ->assertStatus(403);

        $this->getJson(route('purchase.status', $subscription) . '?guest_token=correct-token')
            ->assertOk();
    }

    // ── CSRF exemption configuration ─────────────────────────────────────

    /**
     * Laravel's ValidateCsrfToken::handle() short-circuits on
     * runningUnitTests() BEFORE it ever checks the except() list — meaning
     * an HTTP feature test cannot actually prove the exemption rejects/
     * accepts correctly either way (it would pass even without the
     * exemption). This instead asserts the exemption is REGISTERED, by
     * inspecting the static except-list Laravel's middleware config
     * populates at boot. The real end-to-end proof is the live sandbox
     * webhook delivery (outside the test suite, non-testing environment).
     */
    public function test_webhook_route_is_registered_in_the_csrf_exempt_list(): void
    {
        $ref = new \ReflectionClass(ValidateCsrfToken::class);
        $except = $ref->getStaticPropertyValue('neverVerify');

        $this->assertContains('paymob/webhook', $except);
    }

    public function test_webhook_reaches_the_controller_without_any_session_cookie(): void
    {
        $subscription = $this->makeSubscription();

        // No actingAs(), no withSession() — a bare, cookie-less POST, as a
        // real server-to-server caller would send.
        $response = $this->postWebhook($this->signedPayload());

        $response->assertStatus(200);
        $this->assertSame(Subscription::STATUS_APPROVED, $subscription->fresh()->status);
    }
}

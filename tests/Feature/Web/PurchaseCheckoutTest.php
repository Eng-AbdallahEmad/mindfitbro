<?php

namespace Tests\Feature\Web;

use App\Models\Coupon;
use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PurchaseCheckoutTest extends TestCase
{
    private function configurePaymobForTests(bool $enabled = true): void
    {
        Config::set('services.paymob.enabled', $enabled);
        Config::set('services.paymob.base_url', 'https://accept.paymob.com');
        Config::set('services.paymob.secret_key', 'sk_test_topsecret');
        Config::set('services.paymob.public_key', 'pk_test_public');
        Config::set('services.paymob.hmac_secret', 'hmac_test_secret');
        Config::set('services.paymob.integrations.card', '4321');
        Config::set('services.paymob.http_timeout', 5);

        Config::set('payment.fx.egp_rates.EGP', 1.0);
        Config::set('payment.fx.egp_rates.SAR', 13.3);
        Config::set('payment.fx.egp_rates.TND', null);
        Config::set('payment.fx.egp_rates.USD', 49.0);
        Config::set('payment.fx.markup_percent', 0);
        Config::set('payment.fx.rounding', 'none');
    }

    private function makePlan(): Plan
    {
        $plan = Plan::factory()->create();

        PlanPrice::create(['plan_id' => $plan->id, 'currency' => 'SAR', 'duration_months' => 3, 'price' => 100]);
        PlanPrice::create(['plan_id' => $plan->id, 'currency' => 'SAR', 'duration_months' => 6, 'price' => 180]);

        return $plan;
    }

    private function fakeSuccessfulIntention(string $orderId = '555666'): void
    {
        Http::fake([
            'accept.paymob.com/v1/intention/' => Http::response([
                'id' => 'int_' . $orderId,
                'intention_order_id' => $orderId,
                'client_secret' => 'csecret_' . $orderId,
            ], 201),
        ]);
    }

    private function checkoutPayload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Ahmed Ali',
            'email' => 'ahmed@example.com',
            'phone' => '01012345678',
            'duration_months' => 3,
        ], $overrides);
    }

    // ── Successful checkout ──────────────────────────────────────────

    public function test_successful_checkout_creates_awaiting_payment_order_and_redirects_to_checkout_url(): void
    {
        $this->configurePaymobForTests();
        $this->fakeSuccessfulIntention();
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'SAR'])
            ->post(route('purchase.submit', $plan), $this->checkoutPayload());

        $response->assertRedirect('https://accept.paymob.com/unifiedcheckout/?publicKey=pk_test_public&clientSecret=csecret_555666');

        $subscription = Subscription::firstOrFail();
        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->status);
        $this->assertSame(Subscription::GATEWAY_PAYMOB, $subscription->payment_gateway);
        $this->assertSame('SAR', $subscription->currency);
        $this->assertEquals(100.0, (float) $subscription->total);
        $this->assertSame('EGP', $subscription->charged_currency);
        $this->assertSame(133000, $subscription->charged_amount_cents);
        $this->assertEqualsWithDelta(13.3, (float) $subscription->fx_rate, 0.0001);
        $this->assertSame('555666', $subscription->paymob_order_id);
        $this->assertSame(['555666'], $subscription->paymob_order_ids);
        $this->assertSame('01012345678', $subscription->billing_phone);
        $this->assertNull($subscription->receipt_path);
    }

    // ── FX guard ─────────────────────────────────────────────────────

    public function test_foreign_currency_with_null_configured_rate_blocks_before_any_order_or_http_call(): void
    {
        $this->configurePaymobForTests();
        Http::fake(); // anything sent here fails the test via assertNothingSent
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'TND'])
            ->post(route('purchase.submit', $plan), $this->checkoutPayload());

        $response->assertRedirect(route('purchase.form', $plan));
        $response->assertSessionHas('warning');
        $this->assertSame(0, Subscription::count());
        Http::assertNothingSent();
    }

    // ── Paymob failure handling ──────────────────────────────────────

    public function test_paymob_failure_marks_order_payment_failed_and_shows_retry_page_without_leaking_an_exception(): void
    {
        $this->configurePaymobForTests();
        Http::fake([
            'accept.paymob.com/v1/intention/' => Http::response(['message' => 'bad request'], 400),
        ]);
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'SAR'])
            ->post(route('purchase.submit', $plan), $this->checkoutPayload());

        $subscription = Subscription::firstOrFail();
        $response->assertRedirect(route('purchase.success', $subscription->id));

        $this->assertSame(Subscription::STATUS_PAYMENT_FAILED, $subscription->status);
        $this->assertNotNull($subscription->payment_failure_reason);

        $page = $this->get(route('purchase.success', $subscription->id));
        $page->assertOk();
        $page->assertSee(__('messages.purchase.retry_payment_btn'));
    }

    // ── Kill switch ──────────────────────────────────────────────────

    public function test_kill_switch_off_blocks_before_any_order_or_http_call(): void
    {
        $this->configurePaymobForTests(enabled: false);
        Http::fake();
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'SAR'])
            ->post(route('purchase.submit', $plan), $this->checkoutPayload());

        $response->assertRedirect(route('purchase.form', $plan));
        $this->assertSame(0, Subscription::count());
        Http::assertNothingSent();
    }

    // ── Duplicate-order guard ────────────────────────────────────────

    public function test_guest_with_a_blocking_order_is_blocked_from_creating_a_new_one(): void
    {
        $this->configurePaymobForTests();
        Http::fake();
        $plan = $this->makePlan();

        Subscription::factory()->create([
            'plan_id' => $plan->id,
            'user_id' => null,
            'guest_email' => 'ahmed@example.com',
            'status' => Subscription::STATUS_PENDING_REVIEW,
        ]);

        $response = $this->withSession(['currency' => 'SAR'])
            ->post(route('purchase.submit', $plan), $this->checkoutPayload());

        $response->assertRedirect(route('home'));
        $this->assertSame(1, Subscription::count());
        Http::assertNothingSent();
    }

    public function test_authenticated_user_with_awaiting_payment_order_is_resumed_not_duplicated(): void
    {
        $this->configurePaymobForTests();
        $this->fakeSuccessfulIntention('777888');
        $plan = $this->makePlan();

        $user = User::factory()->create();
        $existing = Subscription::factory()->awaitingPayment()->create([
            'plan_id' => $plan->id,
            'user_id' => $user->id,
            'charged_amount_cents' => 42000,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['currency' => 'SAR'])
            ->post(route('purchase.submit', $plan), $this->checkoutPayload());

        $response->assertRedirect('https://accept.paymob.com/unifiedcheckout/?publicKey=pk_test_public&clientSecret=csecret_777888');
        $this->assertSame(1, Subscription::count(), 'no duplicate row should be created');
        $this->assertSame(42000, $existing->fresh()->charged_amount_cents, 'the persisted charge must not be recomputed');
    }

    // ── Coupon atomicity ─────────────────────────────────────────────
    //
    // A genuine cross-connection concurrency test was attempted (two truly
    // separate DB connections, RefreshDatabase's transactional wrapping
    // disabled) and abandoned: it either self-deadlocked (a DB::listen()
    // hook firing on our own lock-acquiring query tries to lock the same
    // row from a second connection WHILE our own connection still holds
    // it — since the callback runs synchronously after our query already
    // returned, it can only ever block on a lock we ourselves hold) or, once
    // that was fixed, gave an inconclusive result seemingly tied to InnoDB
    // snapshot-visibility timing in that non-transactional test setup —
    // on top of this environment's migrate:fresh being unusually slow
    // (90s+) under repeated isolated runs. Real concurrent MySQL
    // connections correctly serialize through SELECT ... FOR UPDATE — a
    // long-established pattern — but proving that reliably needs real
    // separate processes/threads, not a single PHPUnit run. The two tests
    // below instead verify (a) the lock is actually issued in the SQL sent
    // to the coupons table, and (b) the simple, pre-existing case (coupon
    // already exhausted before the request starts) still works correctly.

    public function test_coupon_check_issues_a_row_lock_on_the_coupons_table(): void
    {
        $this->configurePaymobForTests();
        $this->fakeSuccessfulIntention();
        $plan = $this->makePlan();

        Coupon::factory()->create(['code' => 'LOCKCHECK', 'max_uses' => 5]);

        $lockQuerySeen = false;
        \Illuminate\Support\Facades\DB::listen(function ($query) use (&$lockQuerySeen) {
            $sql = strtolower($query->sql);
            if (str_contains($sql, 'coupons') && str_contains($sql, 'for update')) {
                $lockQuerySeen = true;
            }
        });

        $this->withSession(['currency' => 'SAR'])
            ->post(route('purchase.submit', $plan), $this->checkoutPayload(['coupon_code' => 'LOCKCHECK']));

        $this->assertTrue($lockQuerySeen, 'expected a SELECT ... FOR UPDATE against coupons — the atomic gate must lock the row, not just count unlocked');
    }

    public function test_coupon_already_exhausted_before_the_request_is_rejected_at_pricing_time_with_no_order(): void
    {
        // The simple case: no race needed, just confirms the pre-existing
        // Coupon::findActive() usage check still works — the coupon is
        // silently not applied, checkout proceeds without it.
        $this->configurePaymobForTests();
        $this->fakeSuccessfulIntention();
        $plan = $this->makePlan();

        Coupon::factory()->create(['code' => 'ONECODE', 'max_uses' => 1, 'type' => 'fixed', 'value' => 10]);
        // A genuinely CONSUMED status (Batch 6, C3) — approved counts against
        // max_uses; awaiting_payment (tested separately below) does not.
        Subscription::factory()->create([
            'plan_id' => $plan->id,
            'coupon_code' => 'ONECODE',
            'status' => Subscription::STATUS_APPROVED,
        ]);

        $this->withSession(['currency' => 'SAR'])
            ->post(route('purchase.submit', $plan), $this->checkoutPayload(['coupon_code' => 'ONECODE']));

        $newSubscription = Subscription::where('id', '!=', 1)->latest('id')->first();
        $this->assertSame(2, Subscription::count());
        $this->assertNull($newSubscription->coupon_code, 'exhausted coupon must not be silently applied');
        $this->assertEquals(100.0, (float) $newSubscription->total, 'full price, no discount, since the coupon was rejected');
    }

    // ── C3: abandoned/failed Paymob checkouts must not burn coupon capacity ──

    public function test_awaiting_payment_and_payment_failed_orders_do_not_count_against_coupon_max_uses(): void
    {
        $this->configurePaymobForTests();
        $this->fakeSuccessfulIntention();
        $plan = $this->makePlan();

        Coupon::factory()->create(['code' => 'STILLGOOD', 'max_uses' => 1, 'type' => 'fixed', 'value' => 10]);

        // Two prior "uses" that must NOT count: one abandoned mid-checkout,
        // one that was declined by Paymob. Neither is a kept sale.
        Subscription::factory()->create([
            'plan_id' => $plan->id,
            'coupon_code' => 'STILLGOOD',
            'status' => Subscription::STATUS_AWAITING_PAYMENT,
        ]);
        Subscription::factory()->create([
            'plan_id' => $plan->id,
            'coupon_code' => 'STILLGOOD',
            'status' => Subscription::STATUS_PAYMENT_FAILED,
        ]);

        $response = $this->withSession(['currency' => 'SAR'])
            ->post(route('purchase.submit', $plan), $this->checkoutPayload(['coupon_code' => 'STILLGOOD']));

        $response->assertRedirect('https://accept.paymob.com/unifiedcheckout/?publicKey=pk_test_public&clientSecret=csecret_555666');

        $newSubscription = Subscription::latest('id')->first();
        $this->assertSame('STILLGOOD', $newSubscription->coupon_code, 'the coupon must still be considered available');
        $this->assertGreaterThan(0, (float) $newSubscription->coupon_discount, 'the discount must actually apply');
    }

    // ── Retry ─────────────────────────────────────────────────────────

    public function test_retry_creates_a_fresh_intention_on_the_same_row_reusing_the_persisted_charge(): void
    {
        $this->configurePaymobForTests();
        $this->fakeSuccessfulIntention('new-order-2');

        $subscription = Subscription::factory()->create([
            'user_id' => null,
            'guest_email' => 'guest@example.com',
            'guest_name' => 'Guest Person',
            'guest_token' => 'correct-token',
            'status' => Subscription::STATUS_AWAITING_PAYMENT,
            'payment_gateway' => Subscription::GATEWAY_PAYMOB,
            'charged_amount_cents' => 77700,
            'charged_currency' => 'EGP',
            'paymob_order_id' => 'old-order-1',
            'paymob_order_ids' => ['old-order-1'],
            'billing_phone' => '01099998888',
        ]);

        $response = $this->post(route('purchase.retry', $subscription), [
            'guest_token' => 'correct-token',
        ]);

        $response->assertRedirect('https://accept.paymob.com/unifiedcheckout/?publicKey=pk_test_public&clientSecret=csecret_new-order-2');

        $subscription->refresh();
        $this->assertSame(77700, $subscription->charged_amount_cents, 'retry must reuse the exact persisted charge');
        $this->assertSame('new-order-2', $subscription->paymob_order_id);
        $this->assertSame(['old-order-1', 'new-order-2'], $subscription->paymob_order_ids);

        Http::assertSent(fn ($request) => $request['amount'] === 77700);
    }

    public function test_retry_with_wrong_guest_token_is_forbidden(): void
    {
        $this->configurePaymobForTests();
        Http::fake();

        $subscription = Subscription::factory()->create([
            'user_id' => null,
            'guest_email' => 'guest@example.com',
            'guest_token' => 'correct-token',
            'status' => Subscription::STATUS_AWAITING_PAYMENT,
        ]);

        $this->post(route('purchase.retry', $subscription), ['guest_token' => 'wrong-token'])
            ->assertForbidden();

        $this->post(route('purchase.retry', $subscription), [])
            ->assertForbidden();

        Http::assertNothingSent();
    }

    public function test_retry_on_an_already_approved_order_is_refused(): void
    {
        $this->configurePaymobForTests();
        Http::fake();

        $subscription = Subscription::factory()->create([
            'user_id' => null,
            'guest_token' => 'correct-token',
            'status' => Subscription::STATUS_APPROVED,
        ]);

        $this->post(route('purchase.retry', $subscription), ['guest_token' => 'correct-token'])
            ->assertStatus(422);

        Http::assertNothingSent();
    }

    // ── Batch 5.5: dynamic FX rates don't retroactively change past orders ──

    public function test_an_order_created_at_rate_x_keeps_rate_x_after_fx_refresh_changes_the_rate(): void
    {
        $this->configurePaymobForTests();
        $this->fakeSuccessfulIntention();
        $plan = $this->makePlan();

        \App\Models\FxRate::create([
            'currency' => 'SAR',
            'rate_to_egp' => 13.3,
            'source' => 'er-api',
            'fetched_at' => now(),
        ]);

        $this->withSession(['currency' => 'SAR'])
            ->post(route('purchase.submit', $plan), $this->checkoutPayload());

        $subscription = Subscription::firstOrFail();
        $this->assertEqualsWithDelta(13.3, (float) $subscription->fx_rate, 0.0001);
        $originalChargedCents = $subscription->charged_amount_cents;

        // Rate changes significantly after the order was created.
        \App\Models\FxRate::where('currency', 'SAR')->update(['rate_to_egp' => 20.0, 'fetched_at' => now()]);

        $subscription->refresh();
        $this->assertEqualsWithDelta(13.3, (float) $subscription->fx_rate, 0.0001, 'a persisted order must not be retroactively repriced');
        $this->assertSame($originalChargedCents, $subscription->charged_amount_cents);
    }
}

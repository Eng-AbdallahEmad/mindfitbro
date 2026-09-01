<?php

namespace Tests\Feature\Web;

use App\Mail\OrderPendingReviewMail;
use App\Mail\OrderReceivedMail;
use App\Models\Coupon;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SwitchMethodTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Config::set('services.paymob.enabled', true);
        Config::set('services.paymob.base_url', 'https://accept.paymob.com');
        Config::set('services.paymob.secret_key', 'sk_test');
        Config::set('services.paymob.public_key', 'pk_test');
        Config::set('services.paymob.hmac_secret', 'hmac_test');
        Config::set('services.paymob.integrations.card', '4321');
        Config::set('payment.fx.egp_rates.SAR', 13.3);
        Config::set('payment.fx.egp_rates.EGP', 1.0);
    }

    private function fakeIntention(string $orderId = '777888'): void
    {
        Http::fake([
            'accept.paymob.com/v1/intention/' => Http::response([
                'id' => 'int_' . $orderId, 'intention_order_id' => $orderId, 'client_secret' => 'cs_' . $orderId,
            ], 201),
        ]);
    }

    // ── Authorization: identical to retry ──────────────────────────────

    public function test_wrong_guest_token_is_rejected(): void
    {
        $subscription = Subscription::factory()->guest()->awaitingPayment()->create(['guest_token' => 'correct']);

        $this->post(route('purchase.switch-method', $subscription), [
            'to' => 'manual', 'guest_token' => 'wrong',
            'receipt' => UploadedFile::fake()->image('r.jpg')->size(500),
        ])->assertStatus(403);

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->status);
    }

    public function test_a_logged_in_user_cannot_switch_someone_elses_subscription(): void
    {
        $owner = User::factory()->create();
        $someoneElse = User::factory()->create();
        $subscription = Subscription::factory()->awaitingPayment()->create(['user_id' => $owner->id]);

        $this->actingAs($someoneElse)
            ->post(route('purchase.switch-method', $subscription), ['to' => 'card'])
            ->assertStatus(403);
    }

    // ── Allowed transitions ──────────────────────────────────────────

    public function test_awaiting_payment_can_switch_to_manual_with_a_receipt(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $subscription = Subscription::factory()->guest()->awaitingPayment()->create([
            'currency' => 'EGP', 'total' => 900,
        ]);

        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.switch-method', $subscription), [
                'to' => 'manual',
                'guest_token' => $subscription->guest_token,
                'receipt' => UploadedFile::fake()->image('r.jpg')->size(500),
            ]);

        $subscription->refresh();
        $response->assertRedirect(route('paymob.callback', [
            'ref' => $subscription->id, 'guest_token' => $subscription->guest_token,
        ]));
        $this->assertSame(Subscription::STATUS_PENDING_REVIEW, $subscription->status);
        $this->assertSame(Subscription::GATEWAY_MANUAL, $subscription->payment_gateway);
        $this->assertSame('EGP', $subscription->payment_method_key);
        $this->assertNotNull($subscription->receipt_path);
        Storage::disk('local')->assertExists($subscription->receipt_path);

        Mail::assertSent(OrderReceivedMail::class);
        Mail::assertSent(OrderPendingReviewMail::class, fn ($mail) => $mail->hasTo($admin->email));
    }

    public function test_payment_failed_can_switch_to_manual(): void
    {
        Mail::fake();
        $subscription = Subscription::factory()->guest()->paymentFailed()->create([
            'currency' => 'SAR', 'total' => 100,
        ]);

        $this->withSession(['currency' => 'SAR', 'detected_country' => 'SA'])
            ->post(route('purchase.switch-method', $subscription), [
                'to' => 'manual',
                'guest_token' => $subscription->guest_token,
                'receipt' => UploadedFile::fake()->image('r.jpg')->size(500),
            ])->assertRedirect();

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_PENDING_REVIEW, $subscription->status);
        $this->assertSame(Subscription::GATEWAY_MANUAL, $subscription->payment_gateway);
    }

    public function test_awaiting_payment_can_switch_to_card(): void
    {
        $this->fakeIntention();
        $subscription = Subscription::factory()->guest()->awaitingPayment()->create();

        $response = $this->post(route('purchase.switch-method', $subscription), [
            'to' => 'card',
            'guest_token' => $subscription->guest_token,
        ]);

        $response->assertRedirect('https://accept.paymob.com/unifiedcheckout/?publicKey=pk_test&clientSecret=cs_777888');
        $this->assertSame(Subscription::GATEWAY_PAYMOB, $subscription->fresh()->payment_gateway);
    }

    public function test_payment_failed_can_switch_to_card(): void
    {
        $this->fakeIntention();
        $subscription = Subscription::factory()->guest()->paymentFailed()->create();

        $this->post(route('purchase.switch-method', $subscription), [
            'to' => 'card', 'guest_token' => $subscription->guest_token,
        ])->assertRedirect('https://accept.paymob.com/unifiedcheckout/?publicKey=pk_test&clientSecret=cs_777888');
    }

    // ── Blocked statuses ─────────────────────────────────────────────

    public function test_pending_review_cannot_be_switched(): void
    {
        $subscription = Subscription::factory()->guest()->pendingReview()->create();

        $response = $this->post(route('purchase.switch-method', $subscription), [
            'to' => 'card', 'guest_token' => $subscription->guest_token,
        ]);

        $response->assertStatus(422);
    }

    public function test_approved_active_and_terminal_statuses_cannot_be_switched(): void
    {
        foreach ([
            Subscription::STATUS_APPROVED, Subscription::STATUS_ACTIVE, Subscription::STATUS_EXPIRED,
            Subscription::STATUS_CANCELLED, Subscription::STATUS_REFUNDED,
        ] as $status) {
            $subscription = Subscription::factory()->guest()->create(['status' => $status]);

            $this->post(route('purchase.switch-method', $subscription), [
                'to' => 'card', 'guest_token' => $subscription->guest_token,
            ])->assertStatus(422);
        }
    }

    // ── Ineligible target ────────────────────────────────────────────

    public function test_ineligible_target_currency_for_manual_is_refused(): void
    {
        $subscription = Subscription::factory()->guest()->awaitingPayment()->create(['currency' => 'SAR']);

        // Detected in the US — not eligible for manual at all.
        $this->withSession(['currency' => 'USD', 'detected_country' => 'US'])
            ->post(route('purchase.switch-method', $subscription), [
                'to' => 'manual', 'guest_token' => $subscription->guest_token,
                'receipt' => UploadedFile::fake()->image('r.jpg')->size(500),
            ])->assertStatus(403);

        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->fresh()->status);
    }

    public function test_eligible_country_but_mismatched_order_currency_is_refused(): void
    {
        // Order was priced in SAR (e.g. display currency at creation time),
        // but the visitor is now detected in Egypt (EGP) — the two disagree,
        // so there's no correct account to show; refuse rather than improvise.
        $subscription = Subscription::factory()->guest()->awaitingPayment()->create(['currency' => 'SAR']);

        $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.switch-method', $subscription), [
                'to' => 'manual', 'guest_token' => $subscription->guest_token,
                'receipt' => UploadedFile::fake()->image('r.jpg')->size(500),
            ])->assertStatus(403);

        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->fresh()->status);
    }

    // ── Price integrity across a switch ─────────────────────────────

    public function test_price_in_customer_currency_is_unchanged_across_a_switch_to_manual(): void
    {
        Mail::fake();
        $subscription = Subscription::factory()->guest()->awaitingPayment()->create([
            'currency' => 'EGP', 'total' => 1234.5, 'subtotal' => 1234.5,
        ]);

        $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.switch-method', $subscription), [
                'to' => 'manual', 'guest_token' => $subscription->guest_token,
                'receipt' => UploadedFile::fake()->image('r.jpg')->size(500),
            ]);

        $subscription->refresh();
        $this->assertSame('EGP', $subscription->currency);
        $this->assertEquals(1234.5, (float) $subscription->total);
    }

    public function test_manual_to_card_switch_derives_a_fresh_egp_amount_and_logs_both_rates(): void
    {
        Log::spy();
        $this->fakeIntention();

        // A row with no charged_amount_cents yet — the genuine "was manual,
        // now becoming card" shape (manual orders never set FX fields).
        $subscription = Subscription::factory()->guest()->create([
            'status' => Subscription::STATUS_PAYMENT_FAILED,
            'payment_gateway' => Subscription::GATEWAY_MANUAL,
            'currency' => 'SAR',
            'total' => 100,
            'charged_currency' => null,
            'charged_amount_cents' => null,
            'fx_rate' => null,
        ]);

        $this->post(route('purchase.switch-method', $subscription), [
            'to' => 'card', 'guest_token' => $subscription->guest_token,
        ])->assertRedirect();

        $subscription->refresh();
        $this->assertSame('EGP', $subscription->charged_currency);
        $this->assertSame(133000, $subscription->charged_amount_cents); // 100 * 13.3 * 100
        $this->assertEqualsWithDelta(13.3, (float) $subscription->fx_rate, 0.0001);
        $this->assertEquals(100.0, (float) $subscription->total, 'display total in SAR must never change');
        $this->assertSame(Subscription::GATEWAY_PAYMOB, $subscription->payment_gateway);
        $this->assertNull($subscription->receipt_path);
        $this->assertNull($subscription->payment_method_key);

        Log::shouldHaveReceived('info')
            ->withArgs(fn ($message, $context = []) => str_contains($message, "today's rate") && isset($context['new_fx_rate']))
            ->atLeast()->once();
    }

    // ── Atomic: rejected manual switch leaves state untouched ─────────

    public function test_switch_to_manual_without_a_receipt_is_rejected_atomically(): void
    {
        $subscription = Subscription::factory()->guest()->awaitingPayment()->create(['currency' => 'EGP']);

        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.switch-method', $subscription), [
                'to' => 'manual', 'guest_token' => $subscription->guest_token,
            ]);

        $response->assertSessionHasErrors('receipt');

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->status, 'must remain on its original status');
        $this->assertSame(Subscription::GATEWAY_PAYMOB, $subscription->payment_gateway, 'must remain on its original gateway');
        $this->assertNull($subscription->receipt_path);
    }

    // ── Audit log line ───────────────────────────────────────────────

    public function test_every_switch_writes_an_audit_log_line(): void
    {
        Log::spy();
        $this->fakeIntention();
        $subscription = Subscription::factory()->guest()->awaitingPayment()->create();

        $this->post(route('purchase.switch-method', $subscription), [
            'to' => 'card', 'guest_token' => $subscription->guest_token,
        ]);

        Log::shouldHaveReceived('info')
            ->withArgs(function ($message, $context = []) use ($subscription) {
                return $message === 'Subscription payment method switched'
                    && $context['subscription_id'] === $subscription->id
                    && $context['to_method'] === 'card'
                    && isset($context['from_method'], $context['actor'], $context['timestamp']);
            })
            ->atLeast()->once();
    }

    // ── Step 7: rejected → recovery (S7.1-S7.5) ────────────────────────

    private function makeRejected(array $overrides = []): Subscription
    {
        return Subscription::factory()->guest()->create(array_merge([
            'status' => Subscription::STATUS_REJECTED,
            'payment_gateway' => Subscription::GATEWAY_MANUAL,
            'rejection_reason' => 'الإيصال غير واضح',
            'receipt_path' => 'receipts/2026/01/old-rejected-receipt.jpg',
            'currency' => 'EGP',
            'total' => 900,
        ], $overrides));
    }

    public function test_rejected_can_switch_to_card_with_a_fresh_egp_amount_and_unchanged_display_total(): void
    {
        $this->fakeIntention();
        Storage::disk('local')->put('receipts/2026/01/old-rejected-receipt.jpg', 'fake-old-receipt');
        $subscription = $this->makeRejected();

        $response = $this->post(route('purchase.switch-method', $subscription), [
            'to' => 'card', 'guest_token' => $subscription->guest_token,
        ]);

        $response->assertRedirect('https://accept.paymob.com/unifiedcheckout/?publicKey=pk_test&clientSecret=cs_777888');

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_AWAITING_PAYMENT, $subscription->status);
        $this->assertSame(Subscription::GATEWAY_PAYMOB, $subscription->payment_gateway);
        $this->assertSame('EGP', $subscription->charged_currency);
        $this->assertEqualsWithDelta(1.0, (float) $subscription->fx_rate, 0.0001); // EGP -> EGP rate
        $this->assertEquals(900.0, (float) $subscription->total, 'display total (EGP) must never change');
        $this->assertNull($subscription->rejection_reason, 'a fresh attempt supersedes the old rejection decision');
    }

    public function test_rejected_can_switch_to_manual_with_a_new_receipt_and_returns_to_pending_review(): void
    {
        Mail::fake();
        $subscription = $this->makeRejected();

        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.switch-method', $subscription), [
                'to' => 'manual',
                'guest_token' => $subscription->guest_token,
                'receipt' => UploadedFile::fake()->image('new-receipt.jpg')->size(400),
            ]);

        $response->assertRedirect(route('paymob.callback', [
            'ref' => $subscription->id, 'guest_token' => $subscription->guest_token,
        ]));

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_PENDING_REVIEW, $subscription->status);
        $this->assertSame(Subscription::GATEWAY_MANUAL, $subscription->payment_gateway);
        $this->assertNull($subscription->rejection_reason);
        $this->assertNull($subscription->reviewed_at);
        Storage::disk('local')->assertExists($subscription->receipt_path);
    }

    public function test_old_rejected_receipt_file_is_kept_on_disk_not_deleted_when_switching_to_card(): void
    {
        // Deliberate choice, not an oversight: the old file stays as audit
        // evidence for whatever the rejection_reason described — only the
        // DB pointer moves. See switchToCard()'s docblock.
        $this->fakeIntention();
        Storage::disk('local')->put('receipts/2026/01/old-rejected-receipt.jpg', 'fake-old-receipt');
        $subscription = $this->makeRejected();

        $this->post(route('purchase.switch-method', $subscription), [
            'to' => 'card', 'guest_token' => $subscription->guest_token,
        ]);

        Storage::disk('local')->assertExists('receipts/2026/01/old-rejected-receipt.jpg');
        $this->assertNull($subscription->fresh()->receipt_path, 'the DB pointer is cleared even though the file remains');
    }

    public function test_old_rejected_receipt_file_is_kept_on_disk_when_replaced_by_a_new_manual_upload(): void
    {
        Mail::fake();
        Storage::disk('local')->put('receipts/2026/01/old-rejected-receipt.jpg', 'fake-old-receipt');
        $subscription = $this->makeRejected();

        $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.switch-method', $subscription), [
                'to' => 'manual',
                'guest_token' => $subscription->guest_token,
                'receipt' => UploadedFile::fake()->image('new-receipt.jpg')->size(400),
            ]);

        Storage::disk('local')->assertExists('receipts/2026/01/old-rejected-receipt.jpg');
        $newPath = $subscription->fresh()->receipt_path;
        $this->assertNotSame('receipts/2026/01/old-rejected-receipt.jpg', $newPath);
        Storage::disk('local')->assertExists($newPath);
    }

    public function test_rejected_order_does_not_block_a_brand_new_submission(): void
    {
        $this->fakeIntention();
        $user = User::factory()->create();
        $this->makeRejected(['user_id' => $user->id, 'guest_email' => null, 'guest_name' => null, 'guest_token' => null]);

        $plan = \App\Models\Plan::factory()->create();
        \App\Models\PlanPrice::create(['plan_id' => $plan->id, 'currency' => 'EGP', 'duration_months' => 3, 'price' => 900]);

        $response = $this->actingAs($user)
            ->withSession(['currency' => 'EGP'])
            ->post(route('purchase.submit', $plan), [
                'full_name' => $user->name, 'email' => $user->email,
                'phone' => '01012345678', 'duration_months' => 3,
            ]);

        $response->assertRedirect('https://accept.paymob.com/unifiedcheckout/?publicKey=pk_test&clientSecret=cs_777888');
        $this->assertSame(2, Subscription::count(), 'a brand new row alongside the rejected one');
    }

    public function test_released_coupon_capacity_from_a_rejected_order_is_usable_by_someone_else(): void
    {
        // C3 confirmed by BEHAVIOR, not by reading the CONSUMED_STATUSES
        // constant: max_uses=1, the rejected row already "used" it, and a
        // second customer must still be able to redeem it.
        Coupon::create(['code' => 'RELEASE1', 'type' => 'fixed', 'value' => 10, 'is_active' => true, 'max_uses' => 1]);
        $this->makeRejected(['coupon_code' => 'RELEASE1']);

        $coupon = \App\Models\Coupon::findActive('RELEASE1');

        $this->assertNotNull($coupon, 'a rejected order must not hold coupon capacity hostage');
        $this->assertSame(0, $coupon->usageCount());
    }
}

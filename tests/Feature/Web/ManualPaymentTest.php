<?php

namespace Tests\Feature\Web;

use App\Mail\OrderPendingReviewMail;
use App\Mail\OrderReceivedMail;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManualPaymentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Config::set('services.paymob.enabled', true);
    }

    private function makePlan(): Plan
    {
        $plan = Plan::factory()->create();

        foreach (['SAR' => [100, 180], 'EGP' => [900, 1600], 'TND' => [300, 500]] as $currency => [$p3, $p6]) {
            PlanPrice::create(['plan_id' => $plan->id, 'currency' => $currency, 'duration_months' => 3, 'price' => $p3]);
            PlanPrice::create(['plan_id' => $plan->id, 'currency' => $currency, 'duration_months' => 6, 'price' => $p6]);
        }

        return $plan;
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Ahmed Ali',
            'email' => 'ahmed@example.com',
            'phone' => '01012345678',
            'duration_months' => 3,
            'payment_method' => 'manual',
            'receipt' => UploadedFile::fake()->image('receipt.jpg', 100, 100)->size(500),
        ], $overrides);
    }

    // ── Eligible manual submission ────────────────────────────────────

    public function test_eligible_manual_submission_creates_a_pending_review_order(): void
    {
        Mail::fake();
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.submit', $plan), $this->payload());

        $subscription = Subscription::firstOrFail();
        $response->assertRedirect(route('paymob.callback', [
            'sid' => $subscription->id,
            'guest_token' => $subscription->guest_token,
        ]));

        $this->assertSame(Subscription::STATUS_PENDING_REVIEW, $subscription->status);
        $this->assertSame(Subscription::GATEWAY_MANUAL, $subscription->payment_gateway);
        $this->assertSame('EGP', $subscription->currency);
        $this->assertSame('EGP', $subscription->payment_method_key);
        $this->assertEquals(900.0, (float) $subscription->total);
        $this->assertNotNull($subscription->receipt_path);
        $this->assertNull($subscription->charged_currency, 'manual orders never go through FX conversion');
        $this->assertNull($subscription->charged_amount_cents);
        $this->assertNull($subscription->fx_rate);

        Storage::disk('local')->assertExists($subscription->receipt_path);
    }

    public function test_manual_submission_sends_order_received_and_pending_review_mails(): void
    {
        Mail::fake();
        $plan = $this->makePlan();
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->withSession(['currency' => 'SAR', 'detected_country' => 'SA'])
            ->post(route('purchase.submit', $plan), $this->payload());

        $subscription = Subscription::firstOrFail();

        Mail::assertSent(OrderReceivedMail::class, fn ($mail) => $mail->hasTo('ahmed@example.com'));
        Mail::assertSent(OrderPendingReviewMail::class, fn ($mail) => $mail->hasTo($admin->email));
    }

    public function test_card_path_still_sends_no_mail_at_creation(): void
    {
        Mail::fake();
        $plan = $this->makePlan();

        \Illuminate\Support\Facades\Http::fake([
            'accept.paymob.com/v1/intention/' => \Illuminate\Support\Facades\Http::response([
                'id' => 'int_1', 'intention_order_id' => '1', 'client_secret' => 'cs_1',
            ], 201),
        ]);
        Config::set('services.paymob.base_url', 'https://accept.paymob.com');
        Config::set('services.paymob.secret_key', 'sk_test');
        Config::set('services.paymob.public_key', 'pk_test');
        Config::set('services.paymob.hmac_secret', 'hmac_test');
        Config::set('services.paymob.integrations.card', '4321');
        Config::set('payment.fx.egp_rates.SAR', 13.3);

        $this->withSession(['currency' => 'SAR', 'detected_country' => 'SA'])
            ->post(route('purchase.submit', $plan), [
                'full_name' => 'Ahmed Ali', 'email' => 'ahmed@example.com',
                'phone' => '01012345678', 'duration_months' => 3,
                'payment_method' => 'card',
            ]);

        Mail::assertNothingSent();
    }

    // ── Per-currency bank details (must match the visitor's ACTUAL currency) ──

    public function test_saudi_visitor_sees_the_sar_bank_account(): void
    {
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'SAR', 'detected_country' => 'SA'])
            ->get(route('purchase.form', $plan));

        $response->assertSee('STC Bank');
        $response->assertSee('1028992404');
        $response->assertDontSee('mindfitbro@instapay');
        $response->assertDontSee('STBKTNTT');
    }

    public function test_egyptian_visitor_sees_instapay(): void
    {
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->get(route('purchase.form', $plan));

        $response->assertSee('mindfitbro@instapay');
        $response->assertDontSee('STC Bank');
        $response->assertDontSee('STBKTNTT');
    }

    public function test_tunisian_visitor_sees_the_tnd_bank_account(): void
    {
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'TND', 'detected_country' => 'TN'])
            ->get(route('purchase.form', $plan));

        $response->assertSee('STBKTNTT');
        $response->assertDontSee('STC Bank');
        $response->assertDontSee('mindfitbro@instapay');
    }

    public function test_unmapped_country_sees_no_manual_option_at_all(): void
    {
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'USD', 'detected_country' => 'US'])
            ->get(route('purchase.form', $plan));

        $response->assertDontSee('تحويل بنكي محلي');
        $response->assertDontSee('STC Bank');
        $response->assertDontSee('mindfitbro@instapay');
        $response->assertDontSee('STBKTNTT');
    }

    // ── Server-side re-validation: never trust the POST body's claim ──

    public function test_ineligible_visitor_forcing_payment_method_manual_is_rejected(): void
    {
        $plan = $this->makePlan();

        // Session currency switched to EGP (self-service, unverified), but
        // the visitor's actual DETECTED country is US — must still be
        // rejected, since eligibility is never read from session('currency').
        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'US'])
            ->post(route('purchase.submit', $plan), $this->payload());

        $response->assertRedirect(route('purchase.form', $plan));
        $this->assertSame(0, Subscription::count(), 'no order should be created for an ineligible visitor');
    }

    public function test_no_detected_country_fails_closed_to_no_manual_order(): void
    {
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'SAR', 'detected_country' => null])
            ->post(route('purchase.submit', $plan), $this->payload());

        $response->assertRedirect(route('purchase.form', $plan));
        $this->assertSame(0, Subscription::count());
    }

    // ── Receipt validation restored unchanged ─────────────────────────

    public function test_receipt_is_required_for_manual_submission(): void
    {
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.submit', $plan), $this->payload(['receipt' => null]));

        $response->assertSessionHasErrors('receipt');
        $this->assertSame(0, Subscription::count());
    }

    public function test_receipt_must_be_an_accepted_file_type(): void
    {
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.submit', $plan), $this->payload([
                'receipt' => UploadedFile::fake()->create('receipt.exe', 100),
            ]));

        $response->assertSessionHasErrors('receipt');
    }

    public function test_receipt_over_5mb_is_rejected(): void
    {
        $plan = $this->makePlan();

        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.submit', $plan), $this->payload([
                'receipt' => UploadedFile::fake()->image('receipt.jpg')->size(5121),
            ]));

        $response->assertSessionHasErrors('receipt');
    }

    // ── Coupon already exhausted BEFORE the request: silently dropped, order
    // still created without it — matches the pre-existing card-path behavior
    // (Coupon::findActive() usage check), not a rejection. ──

    public function test_coupon_exhausted_before_the_request_is_silently_dropped_receipt_still_attached(): void
    {
        $plan = $this->makePlan();
        Coupon::create(['code' => 'FULL10', 'type' => 'fixed', 'value' => 10, 'is_active' => true, 'max_uses' => 1]);
        Subscription::factory()->create(['coupon_code' => 'FULL10', 'status' => Subscription::STATUS_APPROVED]);

        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.submit', $plan), $this->payload(['coupon_code' => 'FULL10']));

        // The pre-seeded "already consumed the coupon" subscription above
        // also defaults to GATEWAY_MANUAL (factory default) — disambiguate
        // by taking the latest row, not just any manual-gateway match.
        $subscription = Subscription::where('payment_gateway', Subscription::GATEWAY_MANUAL)->latest('id')->firstOrFail();
        $response->assertRedirect(route('paymob.callback', [
            'sid' => $subscription->id,
            'guest_token' => $subscription->guest_token,
        ]));
        $this->assertNull($subscription->coupon_code, 'exhausted coupon must not be silently applied');
        $this->assertEquals(900.0, (float) $subscription->total, 'full price, no discount, since the coupon was rejected');
        Storage::disk('local')->assertExists($subscription->receipt_path);
    }

    /**
     * The genuine mid-transaction race (findActive() passes, then the
     * lockForUpdate() re-check finds it exhausted) needs real concurrency to
     * trigger honestly — this codebase already tried and abandoned a
     * dedicated concurrent test for the identical card-path gate (see git
     * history / prior session notes on CouponRaceConditionTest.php) rather
     * than ship a flaky or self-deadlocking test. The cleanup line itself
     * (Storage::delete($receiptPath) in the CouponExhaustedException catch)
     * is a one-line, clearly-correct defensive fix, not left untested by
     * accident — deliberately not simulated here for the same reason.
     */

    // ── C5: card and manual pricing can never drift — same computePricing()
    // routine, only the currency differs. This asserts the OBSERVABLE outcome
    // (the pricing math, not just "the code is shared") across every
    // supported manual currency × duration × coupon-or-not combination. ──

    public function test_season_and_coupon_math_is_identical_across_currencies_and_durations(): void
    {
        $plan = $this->makePlan();
        Coupon::create(['code' => 'PARITY10', 'type' => 'percentage', 'value' => 10, 'is_active' => true]);

        $expectedPct = ['SAR' => [3 => 100, 6 => 180], 'EGP' => [3 => 900, 6 => 1600], 'TND' => [3 => 300, 6 => 500]];
        $countryFor = ['SAR' => 'SA', 'EGP' => 'EG', 'TND' => 'TN'];

        foreach ($expectedPct as $currency => $durations) {
            foreach ($durations as $duration => $fullPrice) {
                $response = $this->withSession(['currency' => $currency, 'detected_country' => $countryFor[$currency]])
                    ->post(route('purchase.submit', $plan), [
                        'full_name' => 'Test User',
                        'email' => "test-{$currency}-{$duration}@example.com",
                        'phone' => '01012345678',
                        'duration_months' => $duration,
                        'coupon_code' => 'PARITY10',
                        'payment_method' => 'manual',
                        'receipt' => UploadedFile::fake()->image('receipt.jpg')->size(500),
                    ]);

                $subscription = Subscription::where('guest_email', "test-{$currency}-{$duration}@example.com")->firstOrFail();
                $response->assertRedirect(route('paymob.callback', [
                    'sid' => $subscription->id,
                    'guest_token' => $subscription->guest_token,
                ]));

                $expectedTotal = round($fullPrice * 0.9); // 10% off, same rounding rule everywhere
                $this->assertEquals(
                    $expectedTotal,
                    (float) $subscription->total,
                    "manual total mismatch for {$currency}/{$duration}m"
                );
                $this->assertEquals(
                    round($fullPrice * 0.1, 3),
                    (float) $subscription->coupon_discount,
                    "manual coupon_discount mismatch for {$currency}/{$duration}m"
                );
            }
        }
    }

    // ── Duplicate-order guard already covers manual (pending_review) ──

    public function test_pending_review_manual_order_blocks_a_new_submission(): void
    {
        $user = User::factory()->create();
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => Subscription::STATUS_PENDING_REVIEW,
            'payment_gateway' => Subscription::GATEWAY_MANUAL,
        ]);
        $plan = $this->makePlan();

        $response = $this->actingAs($user)
            ->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.submit', $plan), $this->payload());

        $response->assertRedirect(route('home'));
        $this->assertSame(1, Subscription::count());
    }

    // ── Abandoned-payment sweeper invariant (no sweeper exists yet, but this
    // locks in the status-vocabulary separation any future sweeper relies on) ──

    public function test_manual_orders_never_enter_the_awaiting_payment_status(): void
    {
        Mail::fake();
        $plan = $this->makePlan();

        $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->post(route('purchase.submit', $plan), $this->payload());

        $manual = Subscription::where('payment_gateway', Subscription::GATEWAY_MANUAL)->firstOrFail();
        $this->assertNotSame(Subscription::STATUS_AWAITING_PAYMENT, $manual->status);

        // A hypothetical sweeper querying status=awaiting_payment (exactly
        // what a Paymob-timeout sweep would target) must never see this row.
        $this->assertSame(
            0,
            Subscription::where('status', Subscription::STATUS_AWAITING_PAYMENT)
                ->where('id', $manual->id)
                ->count()
        );
    }
}

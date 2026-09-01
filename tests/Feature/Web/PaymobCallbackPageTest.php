<?php

namespace Tests\Feature\Web;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Tests\TestCase;

class PaymobCallbackPageTest extends TestCase
{
    private function visit(Subscription $subscription)
    {
        return $this->get('/paymob/callback?' . http_build_query([
            'sid' => $subscription->id,
            'guest_token' => $subscription->guest_token,
        ]));
    }

    public function test_paid_state_renders_success_content(): void
    {
        $subscription = Subscription::factory()->guest()->paidViaPaymob()->create();

        $this->visit($subscription)
            ->assertOk()
            ->assertSee('تم الدفع بنجاح', false)
            ->assertSee($subscription->invoiceNumber())
            ->assertSee($subscription->paymob_transaction_id);
    }

    public function test_pending_state_renders_polling_content(): void
    {
        $subscription = Subscription::factory()->guest()->awaitingPayment()->create();

        $this->visit($subscription)
            ->assertOk()
            ->assertSee('جاري تأكيد الدفع', false)
            ->assertSee($subscription->invoiceNumber());
    }

    public function test_failed_state_renders_retry_action(): void
    {
        $subscription = Subscription::factory()->guest()->paymentFailed()->create();

        $this->visit($subscription)
            ->assertOk()
            ->assertSee('تعذر إتمام الدفع', false)
            ->assertSee(route('purchase.retry', $subscription), false);
    }

    public function test_rejected_state_renders_reason_and_recovery_actions_when_eligible(): void
    {
        $subscription = Subscription::factory()->guest()->create([
            'status' => Subscription::STATUS_REJECTED,
            'payment_gateway' => Subscription::GATEWAY_MANUAL,
            'rejection_reason' => 'الإيصال غير واضح',
            'currency' => 'EGP',
        ]);

        $response = $this->withSession(['currency' => 'EGP', 'detected_country' => 'EG'])
            ->get('/paymob/callback?' . http_build_query([
                'sid' => $subscription->id, 'guest_token' => $subscription->guest_token,
            ]));

        $response->assertOk();
        $response->assertSee('تم رفض طلبك', false);
        $response->assertSee('الإيصال غير واضح');
        $response->assertSee('إعادة المحاولة بالبطاقة');
        $response->assertSee('رفع الإيصال الجديد');
    }

    public function test_rejected_state_omits_manual_recovery_when_ineligible(): void
    {
        $subscription = Subscription::factory()->guest()->create([
            'status' => Subscription::STATUS_REJECTED,
            'payment_gateway' => Subscription::GATEWAY_MANUAL,
            'currency' => 'USD',
        ]);

        $response = $this->withSession(['currency' => 'USD', 'detected_country' => 'US'])
            ->get('/paymob/callback?' . http_build_query([
                'sid' => $subscription->id, 'guest_token' => $subscription->guest_token,
            ]));

        $response->assertOk();
        $response->assertSee('إعادة المحاولة بالبطاقة');
        $response->assertDontSee('رفع الإيصال الجديد');
    }

    public function test_manual_review_state_renders_review_timeline_not_polling(): void
    {
        $subscription = Subscription::factory()->guest()->pendingReview()->create([
            'payment_gateway' => Subscription::GATEWAY_MANUAL,
        ]);

        $response = $this->visit($subscription);

        $response->assertOk();
        $response->assertSee('بانتظار مراجعة طلبك', false);
        $response->assertSee('24 ساعة', false);
    }

    public function test_egp_conversion_line_only_shows_for_non_egp_paymob_orders(): void
    {
        $sar = Subscription::factory()->guest()->paidViaPaymob()->create([
            'currency' => 'SAR',
            'charged_currency' => 'EGP',
            'fx_rate' => 13.3,
        ]);
        $egp = Subscription::factory()->guest()->paidViaPaymob()->create([
            'currency' => 'EGP',
            'charged_currency' => 'EGP',
        ]);

        $this->visit($sar)->assertSee('سعر الصرف', false);
        $this->visit($egp)->assertDontSee('سعر الصرف', false);
    }

    public function test_invoice_number_is_shown_instead_of_the_raw_database_id(): void
    {
        $subscription = Subscription::factory()->guest()->paidViaPaymob()->create();

        $response = $this->visit($subscription);

        $response->assertSee($subscription->invoiceNumber());
        $response->assertDontSee('subscription-' . $subscription->id, false);
    }

    public function test_plan_name_and_customer_details_are_shown(): void
    {
        $plan = Plan::factory()->create(['name' => 'باقة النخبة']);
        $subscription = Subscription::factory()->guest()->paidViaPaymob()->create([
            'plan_id' => $plan->id,
            'guest_name' => 'أحمد علي',
            'guest_email' => 'ahmed@example.com',
        ]);

        $response = $this->visit($subscription);

        $response->assertSee('باقة النخبة');
        $response->assertSee('أحمد علي');
        $response->assertSee('ahmed@example.com');
    }

    public function test_authenticated_owner_sees_their_own_name_and_email(): void
    {
        $user = User::factory()->create(['name' => 'سارة محمد', 'email' => 'sara@example.com']);
        $subscription = Subscription::factory()->paidViaPaymob()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/paymob/callback?' . http_build_query(['sid' => $subscription->id]));

        $response->assertOk();
        $response->assertSee('سارة محمد');
        $response->assertSee('sara@example.com');
    }
}

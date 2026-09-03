<?php

namespace Tests\Feature\Mail;

use App\Mail\OrderApprovedMail;
use App\Models\Plan;
use App\Models\Subscription;
use Tests\TestCase;

class OrderApprovedMailTest extends TestCase
{
    public function test_renders_the_amount_paid_and_customer_details(): void
    {
        $plan = Plan::factory()->create(['name' => 'باقة النخبة']);
        $subscription = Subscription::factory()->paidViaPaymob()->create([
            'plan_id' => $plan->id,
            'total' => 999,
            'currency' => 'SAR',
            'billing_phone' => '966501234567',
        ]);
        $subscription->load('plan', 'user');

        $html = (new OrderApprovedMail($subscription, 'أحمد علي'))->render();

        $this->assertStringContainsString('999', $html, 'amount paid must be shown');
        $this->assertStringContainsString('أحمد علي', $html);
        $this->assertStringContainsString($subscription->user->email, $html);
        $this->assertStringContainsString('966501234567', $html);
        $this->assertStringContainsString('باقة النخبة', $html);
    }

    public function test_renders_guest_email_when_there_is_no_linked_user(): void
    {
        $subscription = Subscription::factory()->guest()->paidViaPaymob()->create([
            'guest_email' => 'guest-buyer@example.com',
            'billing_phone' => '201098765432',
        ]);
        $subscription->load('plan', 'user');

        $html = (new OrderApprovedMail($subscription, 'ضيف مشتري', isGuest: true))->render();

        $this->assertStringContainsString('guest-buyer@example.com', $html);
        $this->assertStringContainsString('201098765432', $html);
    }
}

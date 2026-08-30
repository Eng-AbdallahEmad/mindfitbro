<?php

namespace Tests\Unit\Models;

use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Subscription;
use Tests\TestCase;

class CouponTest extends TestCase
{
    public function test_usage_count_includes_only_consumed_statuses(): void
    {
        $plan = Plan::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'TESTCODE']);

        $counted = [
            Subscription::STATUS_PENDING_REVIEW,
            Subscription::STATUS_APPROVED,
            Subscription::STATUS_ACTIVE,
            Subscription::STATUS_EXPIRED,
        ];
        $notCounted = [
            Subscription::STATUS_AWAITING_PAYMENT,
            Subscription::STATUS_PAYMENT_FAILED,
            Subscription::STATUS_REJECTED,
            Subscription::STATUS_CANCELLED,
            Subscription::STATUS_REFUNDED,
        ];

        foreach ($counted as $status) {
            Subscription::factory()->create(['plan_id' => $plan->id, 'coupon_code' => 'TESTCODE', 'status' => $status]);
        }
        foreach ($notCounted as $status) {
            Subscription::factory()->create(['plan_id' => $plan->id, 'coupon_code' => 'TESTCODE', 'status' => $status]);
        }

        $this->assertSame(count($counted), $coupon->usageCount());
    }

    public function test_usage_count_is_case_insensitive_on_the_code(): void
    {
        $plan = Plan::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'MixedCase']);

        Subscription::factory()->create([
            'plan_id' => $plan->id,
            'coupon_code' => 'MIXEDCASE',
            'status' => Subscription::STATUS_APPROVED,
        ]);

        $this->assertSame(1, $coupon->usageCount());
    }

    public function test_find_active_returns_null_once_max_uses_is_reached_by_consumed_statuses_only(): void
    {
        $plan = Plan::factory()->create();
        Coupon::factory()->create(['code' => 'LIMITED', 'max_uses' => 1]);

        Subscription::factory()->create([
            'plan_id' => $plan->id,
            'coupon_code' => 'LIMITED',
            'status' => Subscription::STATUS_AWAITING_PAYMENT,
        ]);

        $this->assertNotNull(Coupon::findActive('LIMITED'), 'an awaiting_payment order must not exhaust the coupon');

        Subscription::factory()->create([
            'plan_id' => $plan->id,
            'coupon_code' => 'LIMITED',
            'status' => Subscription::STATUS_APPROVED,
        ]);

        $this->assertNull(Coupon::findActive('LIMITED'), 'an approved order must exhaust a max_uses=1 coupon');
    }
}

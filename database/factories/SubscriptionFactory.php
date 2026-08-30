<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'user_id' => User::factory(),
            'status' => Subscription::STATUS_PENDING_REVIEW,
            'duration_months' => 3,
            'subtotal' => 100,
            'coupon_discount' => 0,
            'season_discount' => 0,
            'total' => 100,
            'currency' => 'SAR',
            'payment_gateway' => Subscription::GATEWAY_MANUAL,
        ];
    }

    /**
     * Same as the default state — named explicitly for readability alongside
     * the other named status states below.
     */
    public function pendingReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Subscription::STATUS_PENDING_REVIEW,
        ]);
    }

    public function awaitingPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Subscription::STATUS_AWAITING_PAYMENT,
            'payment_gateway' => Subscription::GATEWAY_PAYMOB,
            'payment_intended_at' => now(),
        ]);
    }

    public function paidViaPaymob(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Subscription::STATUS_APPROVED,
            'payment_gateway' => Subscription::GATEWAY_PAYMOB,
            'payment_intended_at' => now()->subMinutes(5),
            'paid_at' => now(),
            'paymob_order_id' => (string) fake()->unique()->numberBetween(100000, 999999),
            'paymob_transaction_id' => (string) fake()->unique()->numberBetween(1000000, 9999999),
            'charged_currency' => 'EGP',
            'charged_amount_cents' => 10000,
            'fx_rate' => 1.0,
            'fx_rate_source' => 'manual-config',
        ]);
    }

    public function paymentFailed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Subscription::STATUS_PAYMENT_FAILED,
            'payment_gateway' => Subscription::GATEWAY_PAYMOB,
            'payment_intended_at' => now()->subMinutes(5),
            'payment_failure_reason' => 'Card declined by issuer.',
        ]);
    }

    /**
     * Guest checkout: no linked user yet, matching the pre-approval shape
     * PurchaseController::submit() produces for an unauthenticated purchase.
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'guest_name' => fake()->name(),
            'guest_email' => fake()->unique()->safeEmail(),
            'guest_token' => Str::random(64),
        ]);
    }
}

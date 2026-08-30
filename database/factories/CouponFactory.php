<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('COUPON-####')),
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'expires_at' => null,
            'max_uses' => null,
        ];
    }
}

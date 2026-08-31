<?php

namespace Database\Factories;

use App\Models\MeetingBooking;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MeetingBooking>
 */
class MeetingBookingFactory extends Factory
{
    protected $model = MeetingBooking::class;

    public function definition(): array
    {
        $date = fake()->dateTimeBetween('+1 day', '+2 weeks')->format('Y-m-d');
        $time = fake()->randomElement(['10:00', '12:00', '16:00', '18:00']);

        return [
            'user_id' => User::factory(),
            'subscription_id' => Subscription::factory(),
            'meeting_date' => $date,
            'meeting_time' => $time,
            'slot_lock' => "{$date} {$time}",
            'meet_link' => null,
            'status' => 'pending',
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'confirmed']);
    }
}

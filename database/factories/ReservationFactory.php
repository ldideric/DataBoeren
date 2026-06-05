<?php

namespace Database\Factories;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use App\Models\Campsite;
use App\Models\Coupon;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('+1 month', '+3 months');
        $checkOut = fake()->dateTimeBetween($checkIn, (clone $checkIn)->modify('+14 days'));

        return [
            'customer_id' => User::factory(),
            'campsite_id' => Campsite::factory(),
            'booked_by_user_id' => null,
            'coupon_id' => null,
            'source' => ReservationSource::Online,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'num_adults' => fake()->numberBetween(1, 4),
            'num_children' => fake()->numberBetween(0, 3),
            'status' => ReservationStatus::Confirmed,
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'cancelled_by_user_id' => null,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => fake()->sentence(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(['status' => ReservationStatus::Pending]);
    }

    public function bookedByEmployee(?User $employee): static
    {
        return $this->state([
            'booked_by_user_id' => $employee ?? User::factory()->withRole(UserRole::Employee),
            'source' => ReservationSource::Employee,
        ]);
    }

    public function withCoupon(Coupon $coupon): static
    {
        return $this->state(fn () => ['coupon_id' => $coupon->id]);
    }
}

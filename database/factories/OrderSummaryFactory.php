<?php

namespace Database\Factories;

use App\Models\OrderSummary;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderSummary>
 */
class OrderSummaryFactory extends Factory
{
    public function definition(): array
    {
        $numNights = fake()->numberBetween(1, 14);
        $nightlyRate = fake()->randomFloat(2, 10, 50);
        $perPersonRate = fake()->randomFloat(2, 2, 15);
        $numPeople = fake()->numberBetween(1, 4);
        $extrasTotal = fake()->randomFloat(2, 0, 50);
        $couponDiscount = fake()->optional(0.3)->randomFloat(2, 5, 30);

        $base = ($nightlyRate + $perPersonRate * $numPeople) * $numNights;
        $total = max(0, $base + $extrasTotal - ($couponDiscount ?? 0));

        return [
            'reservation_id' => Reservation::factory(),
            'season_name' => fake()->randomElement(['Zomer 2025', 'Naseizoen 2025', 'Voorjaar 2026']),
            'num_nights' => $numNights,
            'nightly_rate' => $nightlyRate,
            'per_person_rate' => $perPersonRate,
            'last_minute_applied' => false,
            'last_minute_discount' => null,
            'coupon_discount' => $couponDiscount,
            'extras_total' => $extrasTotal,
            'total' => round($total, 2),
        ];
    }
}

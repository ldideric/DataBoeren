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
        $nightlyRate = fake()->numberBetween(1000, 5000);
        $perAdultRate = fake()->numberBetween(200, 1500);
        $perChildRate = fake()->numberBetween(100, 800);
        $numAdults = fake()->numberBetween(1, 4);
        $numChildren = fake()->numberBetween(0, 3);
        $extrasTotal = fake()->numberBetween(0, 5000);
        $couponDiscount = fake()->optional(0.3)->numberBetween(500, 3000);

        $base = ($nightlyRate + $perAdultRate * $numAdults + $perChildRate * $numChildren) * $numNights;
        $total = max(0, $base + $extrasTotal - ($couponDiscount ?? 0));

        return [
            'reservation_id' => Reservation::factory(),
            'season_name' => fake()->randomElement(['Zomer 2025', 'Naseizoen 2025', 'Voorjaar 2026']),
            'num_nights' => $numNights,
            'nightly_rate' => $nightlyRate,
            'per_adult_rate' => $perAdultRate,
            'per_child_rate' => $perChildRate,
            'last_minute_applied' => false,
            'last_minute_discount' => null,
            'coupon_discount' => $couponDiscount,
            'extras_total' => $extrasTotal,
            'total' => $total,
        ];
    }
}

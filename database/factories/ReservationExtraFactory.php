<?php

namespace Database\Factories;

use App\Models\Extra;
use App\Models\Reservation;
use App\Models\ReservationExtra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReservationExtra>
 */
class ReservationExtraFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->numberBetween(200, 2500);

        return [
            'reservation_id' => Reservation::factory(),
            'extra_id' => Extra::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'amount' => fake()->randomFloat(2, 50, 500),
            'status' => PaymentStatus::Paid,
            'method' => fake()->randomElement(['ideal', 'card', 'cash']),
            'paid_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state([
            'status' => PaymentStatus::Pending,
            'paid_at' => null,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(['status' => PaymentStatus::Refunded]);
    }
}

<?php

namespace Database\Factories;

use App\Enums\BillingType;
use App\Enums\StockType;
use App\Models\Extra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Extra>
 */
class ExtraFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Bedlinnen', 'Handdoek', 'Fietsverhuur', 'BBQ', 'Kinderbedje']),
            'description' => fake()->optional()->sentence(),
            'billing_type' => fake()->randomElement(BillingType::cases()),
            'price' => fake()->randomFloat(2, 2, 25),
            'stock' => null,
            'stock_type' => StockType::Rental,
            'max_per_booking' => null,
        ];
    }

    public function limitedStock(int $stock = 5): static
    {
        return $this->state(['stock' => $stock]);
    }

    public function cappedPerBooking(int $max = 3): static
    {
        return $this->state(['max_per_booking' => $max]);
    }
}

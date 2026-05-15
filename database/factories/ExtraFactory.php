<?php

namespace Database\Factories;

use App\Enums\BillingType;
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
            'available' => true,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(['available' => false]);
    }
}

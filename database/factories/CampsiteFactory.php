<?php

namespace Database\Factories;

use App\Enums\CampsiteType;
use App\Models\Campsite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campsite>
 */
class CampsiteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->bothify('Plek ##'),
            'type' => fake()->randomElement(CampsiteType::cases()),
            'has_electricity' => fake()->boolean(40),
            'max_people' => fake()->numberBetween(2, 8),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function withElectricity(): static
    {
        return $this->state(['has_electricity' => true]);
    }

    public function ofType(CampsiteType $type): static
    {
        return $this->state(['type' => $type]);
    }
}

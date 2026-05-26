<?php

namespace Database\Factories;

use App\Models\Campsite;
use App\Models\CampsitePrice;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CampsitePrice>
 */
class CampsitePriceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campsite_id' => Campsite::factory(),
            'season_id' => Season::factory(),
            'nightly_rate' => fake()->numberBetween(1000, 5000),
            'per_adult_rate' => fake()->numberBetween(200, 1500),
            'per_child_rate' => fake()->numberBetween(100, 800),
        ];
    }
}

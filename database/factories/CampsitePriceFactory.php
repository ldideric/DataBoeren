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
            'nightly_rate' => fake()->randomFloat(2, 10, 50),
            'per_person_rate' => fake()->randomFloat(2, 2, 15),
        ];
    }
}

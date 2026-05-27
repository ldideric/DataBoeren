<?php

namespace Database\Factories;

use App\Models\Season;
use App\Models\SeasonPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeasonPeriod>
 */
class SeasonPeriodFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('first day of January', '+5 months');

        return [
            'season_id' => Season::factory(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+2 weeks'),
        ];
    }
}

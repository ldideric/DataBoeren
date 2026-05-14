<?php

namespace Database\Factories;

use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Season>
 */
class SeasonFactory extends Factory
{
    private static array $seasonNames = ['Voorjaar', 'Zomer', 'Naseizoen', 'Winterseizoen'];

    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('first day of January', 'first day of June');
        $endsAt = fake()->dateTimeBetween($startsAt, (clone $startsAt)->modify('+6 months'));

        return [
            'name' => fake()->randomElement(self::$seasonNames) . ' ' . fake()->year(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Enums\CampsiteType;
use App\Models\Campsite;
use App\Models\Season;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class CampsiteSeeder extends Seeder
{
    private const SEASON_BY_KEY = [
        'hoog_seizoen' => 'Hoogseizoen',
        'laag_seizoen' => 'Laagseizoen',
    ];

    public function run(): void
    {
        $seasons = Season::pluck('id', 'name');

        foreach ($this->records() as $record) {
            $campsite = Campsite::updateOrCreate(
                [
                    'name' => $record['name'],
                    'type' => CampsiteType::from(Str::lower($record['type'])),
                ],
                [
                    'has_electricity' => $record['has_electricity'],
                    'max_people' => $record['max_people'],
                    'max_vehicles' => $record['max_vehicles'],
                    'notes' => $record['notes'],
                ],
            );

            foreach (self::SEASON_BY_KEY as $key => $seasonName) {
                $rate = $record['pricing'][$key];

                // The JSON holds euros; money is stored as integer cents.
                $campsite->prices()->updateOrCreate(
                    ['season_id' => $seasons[$seasonName] ?? throw new RuntimeException("Missing season \"{$seasonName}\" — run SeasonSeeder first.")],
                    [
                        'nightly_rate' => (int) round($rate['nightly_rate'] * 100),
                        'per_adult_rate' => (int) round($rate['per_person_rate'] * 100),
                        'per_child_rate' => (int) round($rate['per_child_rate'] * 100),
                    ],
                );
            }
        }

        $this->command->info('Campsites and prices seeded.');
    }

    private function records(): array
    {
        $path = database_path('src/campsites.json');

        $records = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        return $records;
    }
}

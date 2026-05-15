<?php

namespace Database\Seeders;

use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    public function run(): void
    {
        $seasons = [
            ['name' => 'Voorseizoen 2026', 'starts_at' => '2026-04-01', 'ends_at' => '2026-05-22'],
            ['name' => 'Pinkster 2026', 'starts_at' => '2026-05-23', 'ends_at' => '2026-06-02'],
            ['name' => 'Zomer 2026', 'starts_at' => '2026-06-03', 'ends_at' => '2026-08-31'],
            ['name' => 'Naseizoen 2026', 'starts_at' => '2026-09-01', 'ends_at' => '2026-10-26'],
            ['name' => 'Herfstvakantie 2026', 'starts_at' => '2026-10-27', 'ends_at' => '2026-11-02'],
        ];

        foreach ($seasons as $season) {
            Season::firstOrCreate(['name' => $season['name']], $season);
        }

        $this->command->info('Seasons seeded.');
    }
}

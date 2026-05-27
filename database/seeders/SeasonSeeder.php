<?php

namespace Database\Seeders;

use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    private const PERIODS = [
        'Hoogseizoen' => [
            ['2026-02-20', '2026-02-28'],
            ['2026-04-23', '2026-05-07'],
            ['2026-07-09', '2026-08-21'],
            ['2026-10-15', '2026-10-23'],
            ['2026-12-24', '2027-01-08'],
        ],
        'Laagseizoen' => [
            ['2026-01-09', '2026-02-19'],
            ['2026-03-01', '2026-04-22'],
            ['2026-05-08', '2026-07-08'],
            ['2026-08-22', '2026-10-14'],
            ['2026-10-24', '2026-12-23'],
        ],
    ];

    public function run(): void
    {
        foreach (self::PERIODS as $name => $ranges) {
            $season = Season::firstOrCreate(['name' => $name]);

            foreach ($ranges as [$startsAt, $endsAt]) {
                $period = $season->periods()->whereDate('starts_at', $startsAt)->first()
                    ?? $season->periods()->make(['starts_at' => $startsAt]);

                $period->ends_at = $endsAt;
                $period->save();
            }
        }

        $this->command->info('Seasons and periods seeded.');
    }
}

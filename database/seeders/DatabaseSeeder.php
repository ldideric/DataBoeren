<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SeasonSeeder::class,
        ]);

        if (App::isLocal()) {
            $this->call(DevSeeder::class);
        }
    }
}

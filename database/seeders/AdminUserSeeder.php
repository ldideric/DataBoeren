<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate([
            'email' => config('admin.email', 'bertina@degroeneweide.nl')
        ], [
            'first_name' => 'Bertina',
            'last_name' => 'ADMIN',
            'password' => Hash::make(config('admin.password', 'changeme')),
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin user ready.');
    }
}

<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Campsite;
use App\Models\Coupon;
use App\Models\Extra;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        $employee = User::factory()->withRole(UserRole::Employee)->create([
            'first_name' => 'Jan',
            'last_name' => 'Medewerker',
            'email' => 'employee@degroeneweide.nl',
        ]);

        $customers = User::factory()->count(20)->create();
        $campsites = Campsite::all();
        $firepit = Extra::where('name', 'Vuurkorf')->first();

        $active = Coupon::factory()->create();
        Coupon::factory()->expired()->create();
        Coupon::factory()->exhausted()->create();
        Coupon::factory()->freeExtra($firepit)->create(['title' => 'Gratis vuurkorf']);

        $this->seedReservations($customers, $campsites, $employee, $active);
    }

    private function seedReservations(Collection $customers, Collection $campsites, User $employee, Coupon $activeCoupon): void
    {
        $base = fn () => Reservation::factory()->recycle($customers)->recycle($campsites);

        $base()->count(15)->create();
        $base()->count(5)->bookedByEmployee($employee)->create();
        $base()->count(3)->withCoupon($activeCoupon)->create();
        $base()->count(4)->pending()->create();
        $base()->count(3)->cancelled()->create();
    }
}

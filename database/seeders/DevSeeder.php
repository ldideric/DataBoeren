<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Campsite;
use App\Models\Coupon;
use App\Models\Extra;
use App\Models\Reservation;
use App\Models\User;
use App\Pricing\Actions\CalculatePrice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use RuntimeException;

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
        $this->priceReservations();
    }

    private function priceReservations(): void
    {
        $calculatePrice = app(CalculatePrice::class);
        $extras = Extra::all();

        Reservation::with('campsite')->whereDoesntHave('orderSummary')->each(function (Reservation $reservation) use ($calculatePrice, $extras) {
            $selections = $this->randomExtras($extras);

            try {
                $summary = $calculatePrice->calculate($reservation, $selections);
            } catch (RuntimeException) {
                return;
            }

            $this->persistExtras($reservation, $selections);
            $summary->save();
        });
    }

    /**
     * @param  Collection<int, Extra>  $extras
     * @return array<array{extra: Extra, quantity: int}>
     */
    private function randomExtras(Collection $extras): array
    {
        if ($extras->isEmpty() || fake()->boolean(60)) {
            return [];
        }

        $extra = $extras->random();

        return [[
            'extra' => $extra,
            'quantity' => min(fake()->numberBetween(1, 2), $extra->max_per_booking ?? 2),
        ]];
    }

    /**
     * @param  array<array{extra: Extra, quantity: int}>  $selections
     */
    private function persistExtras(Reservation $reservation, array $selections): void
    {
        $nights = (int) $reservation->check_in->diffInDays($reservation->check_out);

        foreach ($selections as $line) {
            $reservation->extras()->create([
                'extra_id' => $line['extra']->id,
                'quantity' => $line['quantity'],
                'unit_price' => $line['extra']->price,
                'subtotal' => CalculatePrice::lineSubtotal($line['extra'], $line['quantity'], $nights),
            ]);
        }
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

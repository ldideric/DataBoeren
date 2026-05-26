<?php

namespace Database\Seeders;

use App\Enums\BillingType;
use App\Enums\StockType;
use App\Models\Extra;
use Illuminate\Database\Seeder;

class ExtraSeeder extends Seeder
{
    private const EXTRAS = [
        [
            'name' => 'Hond',
            'description' => null,
            'billing_type' => BillingType::OneTime,
            'price' => 3.50,
            'stock' => null,
            'stock_type' => StockType::Rental,
            'max_per_booking' => 1,
        ],
        [
            'name' => 'BBQ',
            'description' => 'Zelf schoonmaken',
            'billing_type' => BillingType::PerNight,
            'price' => 7.50,
            'stock' => 5,
            'stock_type' => StockType::Rental,
            'max_per_booking' => null,
        ],
        [
            'name' => 'Vuurkorf',
            'description' => null,
            'billing_type' => BillingType::PerNight,
            'price' => 5.00,
            'stock' => 5,
            'stock_type' => StockType::Rental,
            'max_per_booking' => null,
        ],
        [
            'name' => 'Zak hout',
            'description' => 'Incl. aanmaakblokjes',
            'billing_type' => BillingType::OneTime,
            'price' => 10.00,
            'stock' => 25,
            'stock_type' => StockType::Consumable,
            'max_per_booking' => null,
        ],
    ];

    public function run(): void
    {
        foreach (self::EXTRAS as $extra) {
            Extra::updateOrCreate(['name' => $extra['name']], $extra);
        }

        $this->command->info('Extras seeded.');
    }
}

<?php

use App\Booking\Queries\GetExtraAvailability;
use App\Enums\ReservationStatus;
use App\Enums\StockType;
use App\Models\Extra;
use App\Models\Reservation;
use App\Models\ReservationExtra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/** Attach `quantity` units of `$extra` to a reservation spanning the given nights. */
function claim(Extra $extra, string $checkIn, string $checkOut, int $quantity, ReservationStatus $status = ReservationStatus::Confirmed): void
{
    $reservation = Reservation::factory()->create([
        'check_in' => Carbon::parse($checkIn),
        'check_out' => Carbon::parse($checkOut),
        'status' => $status,
    ]);

    ReservationExtra::factory()->create([
        'reservation_id' => $reservation->id,
        'extra_id' => $extra->id,
        'quantity' => $quantity,
        'unit_price' => $extra->price,
        'subtotal' => $extra->price * $quantity,
    ]);
}

it('returns null (unlimited) when the extra has no stock limit', function () {
    $extra = Extra::factory()->create(['stock' => null, 'stock_type' => StockType::Rental]);

    expect(GetExtraAvailability::for($extra)->remaining(Carbon::parse('2026-07-10'), Carbon::parse('2026-07-12')))
        ->toBeNull();
});

it('counts a rental against the busiest single night, not the whole window', function () {
    $extra = Extra::factory()->create(['stock' => 5, 'stock_type' => StockType::Rental]);

    // A occupies nights 10,11,12; B occupies nights 12,13. They overlap only on night 12.
    claim($extra, '2026-07-10', '2026-07-13', 2);
    claim($extra, '2026-07-12', '2026-07-14', 2);

    // Window 10..14 → peak is night 12 (2 + 2 = 4), so 5 - 4 = 1 left.
    expect(GetExtraAvailability::for($extra)->remaining(Carbon::parse('2026-07-10'), Carbon::parse('2026-07-14')))
        ->toBe(1);
});

it('ignores rental usage outside the requested window', function () {
    $extra = Extra::factory()->create(['stock' => 5, 'stock_type' => StockType::Rental]);

    claim($extra, '2026-07-10', '2026-07-13', 4);

    // A different week is unaffected by the existing booking.
    expect(GetExtraAvailability::for($extra)->remaining(Carbon::parse('2026-07-20'), Carbon::parse('2026-07-22')))
        ->toBe(5);
});

it('counts a consumable for good, regardless of dates', function () {
    $extra = Extra::factory()->create(['stock' => 10, 'stock_type' => StockType::Consumable]);

    claim($extra, '2026-07-10', '2026-07-13', 4);

    // Even a far-away window sees the stock permanently reduced.
    expect(GetExtraAvailability::for($extra)->remaining(Carbon::parse('2026-09-01'), Carbon::parse('2026-09-03')))
        ->toBe(6);
});

it('does not count cancelled reservations against stock', function () {
    $extra = Extra::factory()->create(['stock' => 5, 'stock_type' => StockType::Rental]);

    claim($extra, '2026-07-10', '2026-07-13', 3, ReservationStatus::Cancelled);

    expect(GetExtraAvailability::for($extra)->remaining(Carbon::parse('2026-07-10'), Carbon::parse('2026-07-13')))
        ->toBe(5);
});

it('caps maxSelectable by the smaller of per-booking limit and remaining stock', function () {
    $extra = Extra::factory()->create([
        'stock' => 5,
        'stock_type' => StockType::Rental,
        'max_per_booking' => 2,
    ]);

    // Plenty of stock (5) but the per-booking cap (2) wins.
    expect(GetExtraAvailability::for($extra)->maxSelectable(Carbon::parse('2026-07-10'), Carbon::parse('2026-07-12')))
        ->toBe(2);

    // Now eat into stock so only 1 remains; remaining wins over the cap of 2.
    claim($extra, '2026-07-10', '2026-07-12', 4);

    expect(GetExtraAvailability::for($extra)->maxSelectable(Carbon::parse('2026-07-10'), Carbon::parse('2026-07-12')))
        ->toBe(1);
});

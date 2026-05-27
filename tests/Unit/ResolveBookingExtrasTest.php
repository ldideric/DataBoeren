<?php

use App\Booking\Actions\ResolveBookingExtras;
use App\Enums\ReservationStatus;
use App\Enums\StockType;
use App\Models\Extra;
use App\Models\Reservation;
use App\Models\ReservationExtra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('normalizes requested extras and ignores empty quantities', function () {
    $extra = Extra::factory()->create();
    $ignored = Extra::factory()->create();

    $selections = app(ResolveBookingExtras::class)->resolve([
        $extra->id => '2',
        $ignored->id => 0,
    ], Carbon::parse('2026-06-01'), Carbon::parse('2026-06-04'));

    expect($selections)->toHaveCount(1)
        ->and($selections[0]['extra']->id)->toBe($extra->id)
        ->and($selections[0]['quantity'])->toBe(2);
});

it('rejects extras above the per booking cap', function () {
    $extra = Extra::factory()->cappedPerBooking(2)->create();

    expect(fn () => app(ResolveBookingExtras::class)->resolve([
        $extra->id => 3,
    ], Carbon::parse('2026-06-01'), Carbon::parse('2026-06-04')))->toThrow(
        ValidationException::class,
    );
});

it('rejects extras when stock is exhausted', function () {
    $extra = Extra::factory()->create([
        'stock' => 4,
        'stock_type' => StockType::Consumable,
    ]);

    $reservation = Reservation::factory()->create([
        'status' => ReservationStatus::Confirmed,
    ]);

    ReservationExtra::factory()->create([
        'reservation_id' => $reservation->id,
        'extra_id' => $extra->id,
        'quantity' => 3,
        'unit_price' => $extra->price,
        'subtotal' => $extra->price * 3,
    ]);

    try {
        app(ResolveBookingExtras::class)->resolve([
            $extra->id => 2,
        ], Carbon::parse('2026-06-01'), Carbon::parse('2026-06-04'));

        $this->fail('Expected validation exception was not thrown.');
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey("extras.$extra->id")
            ->and($exception->errors()["extras.$extra->id"][0])->toBe('Nog 1 beschikbaar voor deze data.');
    }
});

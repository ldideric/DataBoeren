<?php

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Mail\AwaitingPayment;
use App\Mail\PreArrival;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

// --- Awaiting-payment reminders ---

it('reminds online bookings still unpaid a day after booking and records the flag', function () {
    Mail::fake();

    $reservation = Reservation::factory()->pending()->create(['created_at' => now()->subDays(2)]);

    $this->artisan('reservations:remind-awaiting-payment')->assertSuccessful();

    Mail::assertQueued(AwaitingPayment::class, fn (AwaitingPayment $mail) => $mail->reservation->is($reservation));
    expect($reservation->refresh()->payment_reminder_sent_at)->not->toBeNull();
});

it('does not send the awaiting-payment reminder twice', function () {
    Mail::fake();

    Reservation::factory()->pending()->create(['created_at' => now()->subDays(2)]);

    $this->artisan('reservations:remind-awaiting-payment')->assertSuccessful();
    $this->artisan('reservations:remind-awaiting-payment')->assertSuccessful();

    Mail::assertQueued(AwaitingPayment::class, 1);
});

it('skips recent bookings and pay-on-site bookings for the awaiting-payment reminder', function () {
    Mail::fake();

    // Too recent to chase.
    Reservation::factory()->pending()->create(['created_at' => now()->subHours(2)]);

    // Pay-on-site: already has a pending cash payment, so it is not "unpaid online".
    $onSite = Reservation::factory()->pending()->create(['created_at' => now()->subDays(2)]);
    Payment::factory()->for($onSite)->create([
        'method' => PaymentMethod::Cash,
        'status' => PaymentStatus::Pending,
    ]);

    $this->artisan('reservations:remind-awaiting-payment')->assertSuccessful();

    Mail::assertNothingQueued();
});

// --- Pre-arrival reminders ---

it('reminds confirmed bookings three days before arrival and records the flag', function () {
    Mail::fake();

    $reservation = Reservation::factory()->create([
        'status'    => ReservationStatus::Confirmed,
        'check_in'  => now()->addDays(3)->toDateString(),
        'check_out' => now()->addDays(5)->toDateString(),
    ]);

    $this->artisan('reservations:remind-arrival')->assertSuccessful();

    Mail::assertQueued(PreArrival::class, fn (PreArrival $mail) => $mail->reservation->is($reservation));
    expect($reservation->refresh()->arrival_reminder_sent_at)->not->toBeNull();
});

it('only reminds for the exact arrival window and never twice', function () {
    Mail::fake();

    // Ten days out — not yet due.
    Reservation::factory()->create([
        'status'    => ReservationStatus::Confirmed,
        'check_in'  => now()->addDays(10)->toDateString(),
        'check_out' => now()->addDays(12)->toDateString(),
    ]);

    // Three days out — due.
    Reservation::factory()->create([
        'status'    => ReservationStatus::Confirmed,
        'check_in'  => now()->addDays(3)->toDateString(),
        'check_out' => now()->addDays(5)->toDateString(),
    ]);

    $this->artisan('reservations:remind-arrival')->assertSuccessful();
    $this->artisan('reservations:remind-arrival')->assertSuccessful();

    Mail::assertQueued(PreArrival::class, 1);
});

<?php

use App\Console\Commands\PurgePaymentDataCommand;
use App\Models\Campsite;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('deletes payment rows older than 7 years', function () {
    $campsite = Campsite::factory()->create();
    $user = User::factory()->create();
    $reservation = Reservation::factory()
        ->for($user, 'customer')
        ->for($campsite)
        ->create([
            'check_in' => now()->subYears(8)->toDateString(),
            'check_out' => now()->subYears(8)->addDays(7)->toDateString(),
        ]);

    $payment = Payment::factory()->for($reservation)->create();
    DB::table('payments')->where('id', $payment->id)->update(['created_at' => now()->subYears(8)]);

    $this->artisan(PurgePaymentDataCommand::class)->assertSuccessful();

    expect(Payment::find($payment->id))->toBeNull();
});

it('retains payment rows within the 7-year window', function () {
    $campsite = Campsite::factory()->create();
    $user = User::factory()->create();
    $reservation = Reservation::factory()
        ->for($user, 'customer')
        ->for($campsite)
        ->create([
            'check_in' => now()->subYears(5)->toDateString(),
            'check_out' => now()->subYears(5)->addDays(7)->toDateString(),
        ]);

    $payment = Payment::factory()->for($reservation)->create();
    DB::table('payments')->where('id', $payment->id)->update(['created_at' => now()->subYears(5)]);

    $this->artisan(PurgePaymentDataCommand::class)->assertSuccessful();

    expect(Payment::find($payment->id))->not->toBeNull();
});

it('redacts stripe fields once all payments for a user are purged', function () {
    $campsite = Campsite::factory()->create();
    $user = User::factory()->create(['stripe_id' => 'cus_test123', 'pm_last_four' => '4242', 'pm_type' => 'card']);
    $reservation = Reservation::factory()
        ->for($user, 'customer')
        ->for($campsite)
        ->create([
            'check_in' => now()->subYears(8)->toDateString(),
            'check_out' => now()->subYears(8)->addDays(7)->toDateString(),
        ]);

    $payment = Payment::factory()->for($reservation)->create();
    DB::table('payments')->where('id', $payment->id)->update(['created_at' => now()->subYears(8)]);

    $this->artisan(PurgePaymentDataCommand::class)->assertSuccessful();

    $user->refresh();
    expect($user->stripe_id)->toBeNull()
        ->and($user->pm_last_four)->toBeNull()
        ->and($user->pm_type)->toBeNull();
});

it('keeps stripe fields when user has an active subscription', function () {
    $campsite = Campsite::factory()->create();
    $user = User::factory()->create(['stripe_id' => 'cus_subscription', 'pm_last_four' => '1111']);
    $reservation = Reservation::factory()
        ->for($user, 'customer')
        ->for($campsite)
        ->create([
            'check_in' => now()->subYears(8)->toDateString(),
            'check_out' => now()->subYears(8)->addDays(7)->toDateString(),
        ]);

    $payment = Payment::factory()->for($reservation)->create();
    DB::table('payments')->where('id', $payment->id)->update(['created_at' => now()->subYears(8)]);
    DB::table('subscriptions')->insert([
        'user_id' => $user->id,
        'type' => 'default',
        'stripe_id' => 'sub_active_payment',
        'stripe_status' => 'active',
        'stripe_price' => null,
        'quantity' => null,
        'trial_ends_at' => null,
        'ends_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->artisan(PurgePaymentDataCommand::class)->assertSuccessful();

    expect($user->fresh()->stripe_id)->toBe('cus_subscription');
});

it('keeps stripe fields when a user still has a payment within the retention window', function () {
    $campsite = Campsite::factory()->create();
    $user = User::factory()->create(['stripe_id' => 'cus_keep', 'pm_last_four' => '9999']);
    $reservation = Reservation::factory()
        ->for($user, 'customer')
        ->for($campsite)
        ->create([
            'check_in' => now()->subYears(5)->toDateString(),
            'check_out' => now()->subYears(5)->addDays(7)->toDateString(),
        ]);

    $payment = Payment::factory()->for($reservation)->create();
    DB::table('payments')->where('id', $payment->id)->update(['created_at' => now()->subYears(5)]);

    $this->artisan(PurgePaymentDataCommand::class)->assertSuccessful();

    expect($user->fresh()->stripe_id)->toBe('cus_keep');
});

it('makes no changes when --dry-run is passed', function () {
    $campsite = Campsite::factory()->create();
    $user = User::factory()->create(['stripe_id' => 'cus_dry']);
    $reservation = Reservation::factory()
        ->for($user, 'customer')
        ->for($campsite)
        ->create([
            'check_in' => now()->subYears(8)->toDateString(),
            'check_out' => now()->subYears(8)->addDays(7)->toDateString(),
        ]);

    $payment = Payment::factory()->for($reservation)->create();
    DB::table('payments')->where('id', $payment->id)->update(['created_at' => now()->subYears(8)]);

    $this->artisan(PurgePaymentDataCommand::class, ['--dry-run' => true])->assertSuccessful();

    expect(Payment::find($payment->id))->not->toBeNull();
    expect($user->fresh()->stripe_id)->toBe('cus_dry');
});

<?php

use App\Console\Commands\PurgePersonalDataCommand;
use App\Enums\UserRole;
use App\Models\Campsite;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('redacts personal data for customer whose latest reservation checked out 37 months ago', function () {
    $user = User::factory()->create();
    $campsite = Campsite::factory()->create();

    Reservation::factory()
        ->for($user, 'customer')
        ->for($campsite)
        ->create([
            'check_in' => now()->subMonths(38)->toDateString(),
            'check_out' => now()->subMonths(37)->toDateString(),
        ]);

    $this->artisan(PurgePersonalDataCommand::class)->assertSuccessful();

    $user->refresh();
    expect($user->first_name)->toBeNull()
        ->and($user->last_name)->toBeNull()
        ->and($user->email)->toBeNull()
        ->and($user->phone)->toBeNull()
        ->and($user->password)->toBeNull()
        ->and($user->purged_at)->not->toBeNull();
});

it('purges customer with no reservations when account is older than 36 months', function () {
    $user = User::factory()->create();
    DB::table('users')->where('id', $user->id)->update(['created_at' => now()->subMonths(40)]);

    $this->artisan(PurgePersonalDataCommand::class)->assertSuccessful();

    $user->refresh();
    expect($user->purged_at)->not->toBeNull();
});

it('does not purge customer with a future reservation', function () {
    $user = User::factory()->create();
    $campsite = Campsite::factory()->create();

    Reservation::factory()
        ->for($user, 'customer')
        ->for($campsite)
        ->create([
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(14)->toDateString(),
        ]);

    $this->artisan(PurgePersonalDataCommand::class)->assertSuccessful();

    expect($user->fresh()->purged_at)->toBeNull();
});

it('does not purge customer whose reservation checked out within 36 months', function () {
    $user = User::factory()->create();
    $campsite = Campsite::factory()->create();

    Reservation::factory()
        ->for($user, 'customer')
        ->for($campsite)
        ->create([
            'check_in' => now()->subMonths(12)->toDateString(),
            'check_out' => now()->subMonths(11)->toDateString(),
        ]);

    $this->artisan(PurgePersonalDataCommand::class)->assertSuccessful();

    expect($user->fresh()->purged_at)->toBeNull();
});

it('does not purge non-customer users', function () {
    $admin = User::factory()->withRole(UserRole::Admin)->create();
    DB::table('users')->where('id', $admin->id)->update(['created_at' => now()->subMonths(40)]);

    $this->artisan(PurgePersonalDataCommand::class)->assertSuccessful();

    expect($admin->fresh()->purged_at)->toBeNull();
});

it('skips users that have already been purged', function () {
    $user = User::factory()->create();
    $purgetime = now()->subDays(5);
    DB::table('users')->where('id', $user->id)->update([
        'created_at' => now()->subMonths(40),
        'purged_at' => $purgetime,
        'first_name' => null,
    ]);

    $this->artisan(PurgePersonalDataCommand::class)->assertSuccessful();

    // purged_at must not be updated again
    expect($user->fresh()->purged_at->toDateString())->toBe($purgetime->toDateString());
});

it('deletes sessions older than 30 days and keeps recent ones', function () {
    DB::table('sessions')->insert([
        ['id' => 'old-session', 'user_id' => null, 'ip_address' => '127.0.0.1', 'user_agent' => 'test', 'payload' => 'x', 'last_activity' => now()->subDays(31)->timestamp],
        ['id' => 'recent-session', 'user_id' => null, 'ip_address' => '127.0.0.1', 'user_agent' => 'test', 'payload' => 'x', 'last_activity' => now()->subDays(1)->timestamp],
    ]);

    $this->artisan(PurgePersonalDataCommand::class)->assertSuccessful();

    expect(DB::table('sessions')->where('id', 'old-session')->exists())->toBeFalse()
        ->and(DB::table('sessions')->where('id', 'recent-session')->exists())->toBeTrue();
});

it('deletes expired password reset tokens and keeps fresh ones', function () {
    DB::table('password_reset_tokens')->insert([
        ['email' => 'old@example.com', 'token' => 'tok1', 'created_at' => now()->subMinutes(90)],
        ['email' => 'new@example.com', 'token' => 'tok2', 'created_at' => now()->subMinutes(10)],
    ]);

    $this->artisan(PurgePersonalDataCommand::class)->assertSuccessful();

    expect(DB::table('password_reset_tokens')->where('email', 'old@example.com')->exists())->toBeFalse()
        ->and(DB::table('password_reset_tokens')->where('email', 'new@example.com')->exists())->toBeTrue();
});

it('makes no changes when --dry-run is passed', function () {
    $user = User::factory()->create();
    DB::table('users')->where('id', $user->id)->update(['created_at' => now()->subMonths(40)]);

    DB::table('sessions')->insert([
        'id' => 'old-session', 'user_id' => null, 'ip_address' => '127.0.0.1',
        'user_agent' => 'test', 'payload' => 'x',
        'last_activity' => now()->subDays(31)->timestamp,
    ]);

    $this->artisan(PurgePersonalDataCommand::class, ['--dry-run' => true])->assertSuccessful();

    expect($user->fresh()->purged_at)->toBeNull()
        ->and(DB::table('sessions')->where('id', 'old-session')->exists())->toBeTrue();
});

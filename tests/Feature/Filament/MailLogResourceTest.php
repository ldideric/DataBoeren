<?php

declare(strict_types=1);

use App\Enums\MailEvent;
use App\Enums\UserRole;
use App\Filament\Resources\MailLogs\MailLogResource;
use App\Filament\Resources\MailLogs\Pages\ListMailLogs;
use App\Filament\Resources\MailLogs\Pages\ViewMailLog;
use App\Mail\BookingConfirmed;
use App\Models\MailLog;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeMailLog(MailEvent $event = MailEvent::Sent, array $attributes = []): MailLog
{
    return MailLog::create(array_merge([
        'event'      => $event,
        'mailable'   => BookingConfirmed::class,
        'recipient'  => 'guest@example.com',
        'subject'    => 'Uw reservering is bevestigd',
        'created_at' => now(),
    ], $attributes));
}

// Access control

it('lets an admin render the mail log list', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create());

    Livewire::test(ListMailLogs::class)->assertSuccessful();
});

it('shows recorded mail logs to an admin', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create());
    $logs = collect([makeMailLog(), makeMailLog(MailEvent::Failed)]);

    Livewire::test(ListMailLogs::class)->assertCanSeeTableRecords($logs);
});

it('lets an admin open a mail log entry', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create());
    $log = makeMailLog(MailEvent::Failed, ['error' => 'TransportException: boom']);

    Livewire::test(ViewMailLog::class, ['record' => $log->getRouteKey()])->assertSuccessful();
});

it('forbids employees from the mail log', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Employee)->create());

    Livewire::test(ListMailLogs::class)->assertForbidden();
});

it('forbids customers from the mail log', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ListMailLogs::class)->assertForbidden();
});

it('redirects unauthenticated users to login', function () {
    Auth::logout();

    $this->get(MailLogResource::getUrl('index'))->assertRedirect('/admin/login');
});

// Read-only guarantees

it('is view-only: no create ability and no create/edit pages', function () {
    expect(MailLogResource::canCreate())->toBeFalse()
        ->and(array_keys(MailLogResource::getPages()))->toBe(['index', 'view']);
});

// Filtering

it('can filter the log down to failures', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create());
    $sent = makeMailLog(MailEvent::Sent);
    $failed = makeMailLog(MailEvent::Failed);

    Livewire::test(ListMailLogs::class)
        ->filterTable('failures')
        ->assertCanSeeTableRecords([$failed])
        ->assertCanNotSeeTableRecords([$sent]);
});

// Prune action

it('prunes only mail logs older than 30 days', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create());
    $old = makeMailLog(MailEvent::Sent, ['created_at' => now()->subDays(31)]);
    $recent = makeMailLog(MailEvent::Sent, ['created_at' => now()->subDays(2)]);

    Livewire::test(ListMailLogs::class)->callAction('prune');

    expect(MailLog::find($old->id))->toBeNull()
        ->and(MailLog::find($recent->id))->not->toBeNull();
});

// Recording pipeline

it('records the transport lifecycle when a mail is actually sent', function () {
    config(['mail.default' => 'array', 'queue.default' => 'sync']);
    $reservation = Reservation::factory()->create();

    Mail::to($reservation->customer->email)->send(new BookingConfirmed($reservation));

    expect(MailLog::where('event', MailEvent::Sent)->where('mailable', BookingConfirmed::class)->exists())->toBeTrue()
        ->and(MailLog::where('event', MailEvent::Sent)->whereNotNull('message_id')->exists())->toBeTrue();
});

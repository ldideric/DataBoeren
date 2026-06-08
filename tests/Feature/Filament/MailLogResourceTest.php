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
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create(['show_mail_logs' => true]));

    Livewire::test(ListMailLogs::class)->assertSuccessful();
});

it('shows recorded mail logs to an admin', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create(['show_mail_logs' => true]));
    $logs = collect([makeMailLog(), makeMailLog(MailEvent::Failed)]);

    Livewire::test(ListMailLogs::class)->assertCanSeeTableRecords($logs);
});

it('lets an admin open a mail log entry', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create(['show_mail_logs' => true]));
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

it('hides the log from navigation for an admin who has not opted in', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create(['show_mail_logs' => false]));

    // Hidden from the sidebar, but still reachable by URL so toggling off never 403s.
    expect(MailLogResource::shouldRegisterNavigation())->toBeFalse();
    Livewire::test(ListMailLogs::class)->assertSuccessful();
});

it('reveals the log in navigation once the admin opts in via their profile', function () {
    $admin = User::factory()->withRole(UserRole::Admin)->create(['show_mail_logs' => false]);
    $this->actingAs($admin);

    expect(MailLogResource::shouldRegisterNavigation())->toBeFalse();

    $admin->update(['show_mail_logs' => true]);

    expect(MailLogResource::shouldRegisterNavigation())->toBeTrue();
});

// Read-only guarantees

it('is view-only: no create ability and no create/edit pages', function () {
    expect(MailLogResource::canCreate())->toBeFalse()
        ->and(array_keys(MailLogResource::getPages()))->toBe(['index', 'view']);
});

// Filtering

it('can filter the log down to failures', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create(['show_mail_logs' => true]));
    $sent = makeMailLog(MailEvent::Sent);
    $failed = makeMailLog(MailEvent::Failed);

    Livewire::test(ListMailLogs::class)
        ->filterTable('failures')
        ->assertCanSeeTableRecords([$failed])
        ->assertCanNotSeeTableRecords([$sent]);
});

// Prune action

it('prunes only mail logs older than 30 days', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create(['show_mail_logs' => true]));
    $old = makeMailLog(MailEvent::Sent, ['created_at' => now()->subDays(31)]);
    $recent = makeMailLog(MailEvent::Sent, ['created_at' => now()->subDays(2)]);

    Livewire::test(ListMailLogs::class)->callAction('prune');

    expect(MailLog::find($old->id))->toBeNull()
        ->and(MailLog::find($recent->id))->not->toBeNull();
});

it('prunes every mail log regardless of age', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create(['show_mail_logs' => true]));
    makeMailLog(MailEvent::Sent, ['created_at' => now()->subDays(31)]);
    makeMailLog(MailEvent::Sent, ['created_at' => now()]);

    Livewire::test(ListMailLogs::class)->callAction('pruneAll');

    expect(MailLog::count())->toBe(0);
});

// Date/time filtering

it('can filter the log by occurred_at range', function () {
    $this->actingAs(User::factory()->withRole(UserRole::Admin)->create(['show_mail_logs' => true]));
    $old = makeMailLog(MailEvent::Sent, ['created_at' => now()->subDays(10)]);
    $recent = makeMailLog(MailEvent::Sent, ['created_at' => now()->subDay()]);

    Livewire::test(ListMailLogs::class)
        ->filterTable('occurred_at', ['from' => now()->subDays(3)->toDateTimeString()])
        ->assertCanSeeTableRecords([$recent])
        ->assertCanNotSeeTableRecords([$old]);
});

// Recording pipeline

it('records the transport lifecycle when a mail is actually sent', function () {
    config(['mail.default' => 'array', 'queue.default' => 'sync']);
    $reservation = Reservation::factory()->create();

    Mail::to($reservation->customer->email)->send(new BookingConfirmed($reservation));

    expect(MailLog::where('event', MailEvent::Sent)->where('mailable', BookingConfirmed::class)->exists())->toBeTrue()
        ->and(MailLog::where('event', MailEvent::Sent)->whereNotNull('message_id')->exists())->toBeTrue();
});

it('links every lifecycle row of one mail under a single trace', function () {
    config(['mail.default' => 'array', 'queue.default' => 'sync']);
    $reservation = Reservation::factory()->create();

    Mail::to($reservation->customer->email)->send(new BookingConfirmed($reservation));

    $traces = MailLog::whereNotNull('trace_id')->pluck('trace_id')->unique();

    expect($traces)->toHaveCount(1)
        ->and(MailLog::whereNull('trace_id')->count())->toBe(0)
        ->and(MailLog::where('event', MailEvent::Sending)->value('trace_id'))
        ->toBe(MailLog::where('event', MailEvent::Sent)->value('trace_id'));
});

it('gives separate mails separate traces', function () {
    config(['mail.default' => 'array', 'queue.default' => 'sync']);
    $first = Reservation::factory()->create();
    $second = Reservation::factory()->create();

    Mail::to($first->customer->email)->send(new BookingConfirmed($first));
    Mail::to($second->customer->email)->send(new BookingConfirmed($second));

    expect(MailLog::where('event', MailEvent::Sent)->pluck('trace_id')->unique())->toHaveCount(2);
});

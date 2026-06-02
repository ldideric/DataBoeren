<?php

declare(strict_types=1);

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use App\Filament\Pages\NewBooking;
use App\Filament\Resources\Reservations\Pages\EditReservation;
use App\Filament\Resources\Reservations\Pages\ListReservations;
use App\Filament\Resources\Reservations\Pages\ViewReservation;
use App\Filament\Resources\Reservations\RelationManagers\ExtrasRelationManager;
use App\Filament\Resources\Reservations\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Mail\MagicLink;
use App\Models\Extra;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationExtra;
use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->withRole(UserRole::Admin)->create();
    $this->actingAs($this->admin);
});

// List page

it('can render the reservations list page', function () {
    Livewire::test(ListReservations::class)
        ->assertSuccessful();
});

it('can list reservations', function () {
    $reservations = Reservation::factory()->count(3)->create();

    Livewire::test(ListReservations::class)
        ->assertCanSeeTableRecords($reservations);
});

it('can render reservation table columns', function () {
    Reservation::factory()->create();

    Livewire::test(ListReservations::class)
        ->assertCanRenderTableColumn('customer.first_name')
        ->assertCanRenderTableColumn('campsite.name')
        ->assertCanRenderTableColumn('check_in')
        ->assertCanRenderTableColumn('check_out')
        ->assertCanRenderTableColumn('num_adults')
        ->assertCanRenderTableColumn('status')
        ->assertCanRenderTableColumn('source')
        ->assertCanRenderTableColumn('orderSummary.total');
});

it('can search reservations by customer name', function () {
    $customer = User::factory()->create(['first_name' => 'Uniekvoornaam', 'last_name' => 'Testachternaam']);
    $reservation = Reservation::factory()->create(['customer_id' => $customer->id]);
    $other = Reservation::factory()->create();

    Livewire::test(ListReservations::class)
        ->searchTable('Uniekvoornaam')
        ->assertCanSeeTableRecords([$reservation])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can filter reservations by status', function () {
    $confirmed = Reservation::factory()->create(['status' => ReservationStatus::Confirmed]);
    $cancelled = Reservation::factory()->cancelled()->create();

    Livewire::test(ListReservations::class)
        ->filterTable('status', ReservationStatus::Confirmed->value)
        ->assertCanSeeTableRecords([$confirmed])
        ->assertCanNotSeeTableRecords([$cancelled]);
});

it('can filter reservations by source', function () {
    $online = Reservation::factory()->create(['source' => ReservationSource::Online]);
    $byEmployee = Reservation::factory()->create(['source' => ReservationSource::Employee]);

    Livewire::test(ListReservations::class)
        ->filterTable('source', ReservationSource::Online->value)
        ->assertCanSeeTableRecords([$online])
        ->assertCanNotSeeTableRecords([$byEmployee]);
});

it('can sort reservations by check_in ascending', function () {
    $r1 = Reservation::factory()->create(['check_in' => '2026-07-01', 'check_out' => '2026-07-07']);
    $r2 = Reservation::factory()->create(['check_in' => '2026-08-01', 'check_out' => '2026-08-07']);
    $r3 = Reservation::factory()->create(['check_in' => '2026-09-01', 'check_out' => '2026-09-07']);

    Livewire::test(ListReservations::class)
        ->sortTable('check_in')
        ->assertCanSeeTableRecords([$r1, $r2, $r3], inOrder: true);
});

it('can sort reservations by check_in descending', function () {
    $r1 = Reservation::factory()->create(['check_in' => '2026-07-01', 'check_out' => '2026-07-07']);
    $r2 = Reservation::factory()->create(['check_in' => '2026-08-01', 'check_out' => '2026-08-07']);
    $r3 = Reservation::factory()->create(['check_in' => '2026-09-01', 'check_out' => '2026-09-07']);

    Livewire::test(ListReservations::class)
        ->sortTable('check_in', 'desc')
        ->assertCanSeeTableRecords([$r3, $r2, $r1], inOrder: true);
});

it('can bulk delete reservations', function () {
    $reservations = Reservation::factory()->count(3)->create();

    Livewire::test(ListReservations::class)
        ->callTableBulkAction(DeleteBulkAction::class, $reservations);

    $reservations->each(fn ($r) => $this->assertSoftDeleted($r));
});

// New booking page (replaces create page)

it('can render the new booking page', function () {
    Livewire::test(NewBooking::class)
        ->assertSuccessful();
});

// View page

it('can render the view reservation page', function () {
    $reservation = Reservation::factory()->create();

    Livewire::test(ViewReservation::class, ['record' => $reservation->getRouteKey()])
        ->assertSuccessful();
});

// Edit page

it('can render the edit reservation page', function () {
    $reservation = Reservation::factory()->create();

    Livewire::test(EditReservation::class, ['record' => $reservation->getRouteKey()])
        ->assertSuccessful();
});

it('can retrieve reservation data on the edit page', function () {
    $reservation = Reservation::factory()->create([
        'num_adults'   => 2,
        'num_children' => 1,
        'status'       => ReservationStatus::Confirmed,
        'source'       => ReservationSource::Online,
    ]);

    Livewire::test(EditReservation::class, ['record' => $reservation->getRouteKey()])
        ->assertSchemaStateSet([
            'customer_id'  => $reservation->customer_id,
            'campsite_id'  => $reservation->campsite_id,
            'num_adults'   => 2,
            'num_children' => 1,
            'status'       => ReservationStatus::Confirmed,
        ]);
});

it('can update a reservation status', function () {
    $reservation = Reservation::factory()->pending()->create();

    Livewire::test(EditReservation::class, ['record' => $reservation->getRouteKey()])
        ->fillForm(['status' => ReservationStatus::Confirmed->value])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Confirmed);
});

// Staff actions

it('accepts a pending reservation and marks the cash payment paid', function () {
    Mail::fake();

    $reservation = Reservation::factory()->pending()->create();
    $payment = Payment::factory()->for($reservation)->create([
        'method' => PaymentMethod::Cash,
        'status' => PaymentStatus::Pending,
    ]);

    Livewire::test(ViewReservation::class, ['record' => $reservation->getRouteKey()])
        ->callAction('accept');

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Confirmed)
        ->and($payment->refresh()->status)->toBe(PaymentStatus::Paid)
        ->and($payment->paid_at)->not->toBeNull();

    Mail::assertQueued(BookingConfirmed::class, fn (BookingConfirmed $mail) => $mail->reservation->is($reservation));
});

it('hides the accept action for non-pending reservations', function () {
    $reservation = Reservation::factory()->create(['status' => ReservationStatus::Confirmed]);

    Livewire::test(ViewReservation::class, ['record' => $reservation->getRouteKey()])
        ->assertActionHidden('accept');
});

it('cancels a reservation with a reason', function () {
    Mail::fake();

    $reservation = Reservation::factory()->create(['status' => ReservationStatus::Confirmed]);

    Livewire::test(ViewReservation::class, ['record' => $reservation->getRouteKey()])
        ->callAction('cancel', data: ['cancellation_reason' => 'Geannuleerd via de telefoon']);

    $reservation->refresh();

    expect($reservation->status)->toBe(ReservationStatus::Cancelled)
        ->and($reservation->cancellation_reason)->toBe('Geannuleerd via de telefoon')
        ->and($reservation->cancelled_by_user_id)->toBe($this->admin->id);

    Mail::assertQueued(BookingCancelled::class, fn (BookingCancelled $mail) => $mail->reservation->is($reservation));
});

it('sends a login link to the customer', function () {
    Mail::fake();

    $reservation = Reservation::factory()->create();

    Livewire::test(ViewReservation::class, ['record' => $reservation->getRouteKey()])
        ->callAction('sendLoginLink');

    Mail::assertQueued(MagicLink::class, fn (MagicLink $mail) => $mail->hasTo($reservation->customer->email));
});

it('resends the confirmation email for a confirmed reservation', function () {
    Mail::fake();

    $reservation = Reservation::factory()->create(['status' => ReservationStatus::Confirmed]);

    Livewire::test(ViewReservation::class, ['record' => $reservation->getRouteKey()])
        ->callAction('resendConfirmation');

    Mail::assertQueued(BookingConfirmed::class, fn (BookingConfirmed $mail) => $mail->reservation->is($reservation));
});

// Extras relation manager

it('can render the extras relation manager', function () {
    $reservation = Reservation::factory()->create();

    Livewire::test(ExtrasRelationManager::class, [
        'ownerRecord' => $reservation,
        'pageClass'   => EditReservation::class,
    ])
        ->assertSuccessful();
});

it('can list extras in the extras relation manager', function () {
    $reservation = Reservation::factory()->create();
    $extras = ReservationExtra::factory()->count(2)->create(['reservation_id' => $reservation->id]);

    Livewire::test(ExtrasRelationManager::class, [
        'ownerRecord' => $reservation,
        'pageClass'   => EditReservation::class,
    ])
        ->assertCanSeeTableRecords($extras);
});

it('can create an extra via the extras relation manager', function () {
    $reservation = Reservation::factory()->create();
    $extra = Extra::factory()->create();

    Livewire::test(ExtrasRelationManager::class, [
        'ownerRecord' => $reservation,
        'pageClass'   => EditReservation::class,
    ])
        ->callTableAction('create', data: [
            'extra_id' => $extra->id,
            'quantity' => 2,
        ])
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(ReservationExtra::class, [
        'reservation_id' => $reservation->id,
        'extra_id'       => $extra->id,
        'quantity'       => 2,
    ]);
});

// Payments relation manager

it('can render the payments relation manager', function () {
    $reservation = Reservation::factory()->create();

    Livewire::test(PaymentsRelationManager::class, [
        'ownerRecord' => $reservation,
        'pageClass'   => EditReservation::class,
    ])
        ->assertSuccessful();
});

it('can list payments in the payments relation manager', function () {
    $reservation = Reservation::factory()->create();
    $payments = Payment::factory()->count(2)->create(['reservation_id' => $reservation->id]);

    Livewire::test(PaymentsRelationManager::class, [
        'ownerRecord' => $reservation,
        'pageClass'   => EditReservation::class,
    ])
        ->assertCanSeeTableRecords($payments);
});

// Authorization

it('prevents customers from accessing the reservations list', function () {
    $customer = User::factory()->create();
    $this->actingAs($customer);

    Livewire::test(ListReservations::class)
        ->assertForbidden();
});

// HTTP routes

it('admin can load reservations index via HTTP', function () {
    $this->get(ReservationResource::getUrl('index'))->assertOk();
});

it('admin can load new booking page via HTTP', function () {
    $this->get(NewBooking::getUrl())->assertOk();
});

it('admin can load view reservation page via HTTP', function () {
    $reservation = Reservation::factory()->create();
    $this->get(ReservationResource::getUrl('view', ['record' => $reservation]))->assertOk();
});

it('admin can load edit reservation page via HTTP', function () {
    $reservation = Reservation::factory()->create();
    $this->get(ReservationResource::getUrl('edit', ['record' => $reservation]))->assertOk();
});

it('unauthenticated user is redirected to login from reservations', function () {
    Auth::logout();
    $this->get(ReservationResource::getUrl('index'))->assertRedirect('/admin/login');
});

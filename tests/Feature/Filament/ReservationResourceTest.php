<?php

declare(strict_types=1);

use App\Enums\CheckoutMethod;
use App\Enums\CouponScope;
use App\Enums\DiscountType;
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
use App\Mail\AwaitingPayment;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Mail\BookingReceived;
use App\Mail\MagicLink;
use App\Models\Campsite;
use App\Models\CampsitePrice;
use App\Models\Coupon;
use App\Models\Extra;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationExtra;
use App\Models\Season;
use App\Models\SeasonPeriod;
use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $admin = User::factory()->withRole(UserRole::Admin)->create();
    $this->actingAs($admin);
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

/**
 * A priced, fitting campsite plus the dates for a valid stay, so the wizard's
 * save() resolves availability and pricing. Returns [campsite, checkIn, checkOut].
 */
function newBookingFixture(): array
{
    $checkIn = now()->addDays(10)->startOfDay();
    $checkOut = $checkIn->copy()->addDays(2);

    $season = Season::factory()->create();
    SeasonPeriod::factory()->for($season)->create([
        'starts_at' => $checkIn->copy()->subMonth(),
        'ends_at'   => $checkIn->copy()->addMonth(),
    ]);
    $campsite = Campsite::factory()->create(['max_people' => 6]);
    CampsitePrice::factory()->create(['campsite_id' => $campsite->id, 'season_id' => $season->id]);

    return [$campsite, $checkIn, $checkOut];
}

/**
 * Base wizard form state for a new guest. Override per-test (e.g. payment_method,
 * coupon_id) by array-merging.
 */
function newBookingFormData(Campsite $campsite, $checkIn, $checkOut, array $overrides = []): array
{
    return array_merge([
        'existing_customer' => false,
        'first_name'        => 'Nieuw',
        'last_name'         => 'Gast',
        'email'             => 'nieuw@example.com',
        'phone'             => '0612345678',
        'campsite_id'       => $campsite->id,
        'check_in'          => $checkIn->format('Y-m-d'),
        'check_out'         => $checkOut->format('Y-m-d'),
        'num_adults'        => 2,
        'num_children'      => 1,
        'extras'            => [],
        'payment_method'    => CheckoutMethod::CashPaid->value,
    ], $overrides);
}

it('can render the new booking page', function () {
    Livewire::test(NewBooking::class)
        ->assertSuccessful();
});

it('shows a live price total in the wizard summary once the stay is filled', function () {
    [$campsite, $checkIn, $checkOut] = newBookingFixture();

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut))
        ->assertSee('Prijsoverzicht')
        ->assertSee('Totaal');
});

it('shows the coupon line in the wizard summary when a coupon is selected', function () {
    [$campsite, $checkIn, $checkOut] = newBookingFixture();
    $coupon = Coupon::factory()->flat()->create([
        'discount_value' => 10,
        'uses_count'     => 0,
        'max_uses'       => null,
        'expires_at'     => null,
    ]);

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut, [
            'coupon_id' => $coupon->id,
        ]))
        ->assertSchemaStateSet(['coupon_id' => $coupon->id])
        ->assertSee('Prijsoverzicht')
        ->assertSee('Kortingsbon')
        ->assertSee('Totaal');
});

it('calculates the exact booking total when a coupon is applied', function () {
    Mail::fake();

    [$campsite, $checkIn, $checkOut] = newBookingFixture();
    CampsitePrice::query()
        ->where('campsite_id', $campsite->id)
        ->update([
            'nightly_rate' => 2000,
            'per_adult_rate' => 500,
            'per_child_rate' => 200,
        ]);

    $coupon = Coupon::factory()->create([
        'scope' => CouponScope::Accommodation,
        'discount_type' => DiscountType::Percent,
        'discount_value' => 25,
        'uses_count' => 0,
        'max_uses' => null,
        'expires_at' => null,
    ]);

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut, [
            'coupon_id' => $coupon->id,
            'payment_method' => CheckoutMethod::CashPaid->value,
        ]))
        ->call('save')
        ->assertHasNoFormErrors();

    $reservation = Reservation::query()->firstOrFail();

    expect($reservation->coupon_id)->toBe($coupon->id)
        ->and($reservation->orderSummary->nightly_rate)->toBe(2000)
        ->and($reservation->orderSummary->per_adult_rate)->toBe(500)
        ->and($reservation->orderSummary->per_child_rate)->toBe(200)
        ->and($reservation->orderSummary->coupon_discount)->toBe(1600)
        ->and($reservation->orderSummary->total)->toBe(4800)
        ->and($reservation->payments()->firstOrFail()->amount)->toBe(4800)
        ->and($coupon->refresh()->uses_count)->toBe(1);
});

it('reuses an existing customer when the booking is created for one', function () {
    [$campsite, $checkIn, $checkOut] = newBookingFixture();
    $customer = User::factory()->create(['email' => 'bestaand@example.com']);
    $userCount = User::query()->count();

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut, [
            'existing_customer' => true,
            'customer_id' => $customer->id,
            'email' => 'wordt-niet-gebruikt@example.com',
        ]))
        ->call('save')
        ->assertHasNoFormErrors();

    $reservation = Reservation::query()->firstOrFail();

    expect($reservation->customer_id)->toBe($customer->id)
        ->and(User::query()->count())->toBe($userCount);
});

it('rejects a campsite that is already booked for the selected dates', function () {
    [$campsite, $checkIn, $checkOut] = newBookingFixture();

    Reservation::factory()->create([
        'campsite_id' => $campsite->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'status' => ReservationStatus::Confirmed,
    ]);

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut))
        ->call('save')
        ->assertHasFormErrors(['campsite_id']);

    expect(Reservation::query()->count())->toBe(1);
});

it('rejects a tampered coupon id when the booking is submitted', function () {
    [$campsite, $checkIn, $checkOut] = newBookingFixture();
    $coupon = Coupon::factory()->expired()->create();

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut, [
            'coupon_id' => $coupon->id,
        ]))
        ->call('save')
        ->assertHasFormErrors(['coupon_id']);

    expect(Reservation::query()->count())->toBe(0);
});

it('rejects a booking when no price is configured for the chosen dates', function () {
    $checkIn = now()->addDays(10)->startOfDay();
    $checkOut = $checkIn->copy()->addDays(2);

    $campsite = Campsite::factory()->create([
        'max_people' => 6,
    ]);

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut))
        ->call('save')
        ->assertHasFormErrors(['check_in']);

    expect(Reservation::query()->count())->toBe(0);
});

it('confirms the booking and records a paid cash payment when cash is taken now', function () {
    Mail::fake();
    [$campsite, $checkIn, $checkOut] = newBookingFixture();

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut))
        ->call('save')
        ->assertHasNoFormErrors();

    $reservation = Reservation::query()->firstOrFail();

    expect($reservation->status)->toBe(ReservationStatus::Confirmed)
        ->and($reservation->source)->toBe(ReservationSource::Employee);

    $payment = $reservation->payments()->firstOrFail();
    expect($payment->method)->toBe(PaymentMethod::Cash)
        ->and($payment->status)->toBe(PaymentStatus::Paid)
        ->and($payment->amount)->toBe($reservation->orderSummary->total);

    Mail::assertQueued(BookingConfirmed::class, fn (BookingConfirmed $m) => $m->reservation->is($reservation));
});

it('leaves the booking pending with a cash payment due on arrival', function () {
    Mail::fake();
    [$campsite, $checkIn, $checkOut] = newBookingFixture();

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut, [
            'payment_method' => CheckoutMethod::PayOnArrival->value,
        ]))
        ->call('save')
        ->assertHasNoFormErrors();

    $reservation = Reservation::query()->firstOrFail();

    expect($reservation->status)->toBe(ReservationStatus::Pending)
        ->and($reservation->payments()->firstOrFail()->status)->toBe(PaymentStatus::Pending);

    Mail::assertQueued(BookingReceived::class, fn (BookingReceived $m) => $m->reservation->is($reservation));
});

it('emails a payment link and stays pending when sending a link', function () {
    Mail::fake();
    [$campsite, $checkIn, $checkOut] = newBookingFixture();

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut, [
            'payment_method' => CheckoutMethod::SendLink->value,
        ]))
        ->call('save')
        ->assertHasNoFormErrors();

    $reservation = Reservation::query()->firstOrFail();

    expect($reservation->status)->toBe(ReservationStatus::Pending)
        ->and($reservation->payments()->count())->toBe(0);

    Mail::assertQueued(AwaitingPayment::class, fn (AwaitingPayment $m) => $m->reservation->is($reservation));
});

it('redirects to the stripe payment page when taking a card now', function () {
    Mail::fake();
    [$campsite, $checkIn, $checkOut] = newBookingFixture();

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut, [
            'payment_method' => CheckoutMethod::CardNow->value,
        ]))
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $reservation = Reservation::query()->firstOrFail();

    expect($reservation->status)->toBe(ReservationStatus::Pending)
        ->and($reservation->payments()->count())->toBe(0);
});

it('rejects a party that exceeds the campsite capacity', function () {
    [$campsite, $checkIn, $checkOut] = newBookingFixture(); // max_people 6

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut, [
            'num_adults'   => 2,
            'num_children' => 100,
        ]))
        ->call('save')
        ->assertHasFormErrors(['num_children']);

    expect(Reservation::query()->count())->toBe(0);
});

it('rejects a zero-night stay where check-out equals check-in', function () {
    [$campsite, $checkIn] = newBookingFixture();

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkIn, [
            'check_out' => $checkIn->format('Y-m-d'),
        ]))
        ->call('save')
        ->assertHasFormErrors(['check_out']);

    expect(Reservation::query()->count())->toBe(0);
});

it('applies a coupon, increments its usage, and discounts the total', function () {
    Mail::fake();
    [$campsite, $checkIn, $checkOut] = newBookingFixture();
    $coupon = Coupon::factory()->flat()->create(['discount_value' => 10, 'uses_count' => 0, 'max_uses' => 5]);

    Livewire::test(NewBooking::class)
        ->fillForm(newBookingFormData($campsite, $checkIn, $checkOut, [
            'coupon_id' => $coupon->id,
        ]))
        ->call('save')
        ->assertHasNoFormErrors();

    $reservation = Reservation::query()->firstOrFail();

    expect($reservation->coupon_id)->toBe($coupon->id)
        ->and($coupon->refresh()->uses_count)->toBe(1)
        ->and($reservation->orderSummary->coupon_discount)->not->toBeNull();
});

it('does not list an expired coupon in the wizard', function () {
    $expired = Coupon::factory()->expired()->create();
    $valid = Coupon::factory()->create(['expires_at' => null, 'max_uses' => null]);

    expect(Coupon::query()->redeemable()->pluck('id')->all())
        ->toContain($valid->id)
        ->not->toContain($expired->id);
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
        ->and($reservation->cancelled_by_user_id)->toBe(Auth::id());

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
        ->assertHasNoTableActionErrors();

    $this->assertDatabaseHas(ReservationExtra::class, [
        'reservation_id' => $reservation->id,
        'extra_id'       => $extra->id,
        'quantity'       => 2,
    ]);
});

it('can create an extra from the reservation view page', function () {
    $reservation = Reservation::factory()->create();
    $extra = Extra::factory()->create();

    Livewire::test(ExtrasRelationManager::class, [
        'ownerRecord' => $reservation,
        'pageClass'   => ViewReservation::class,
    ])
        ->callTableAction('create', data: [
            'extra_id' => $extra->id,
            'quantity' => 3,
        ])
        ->assertHasNoTableActionErrors();

    $this->assertDatabaseHas(ReservationExtra::class, [
        'reservation_id' => $reservation->id,
        'extra_id'       => $extra->id,
        'quantity'       => 3,
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

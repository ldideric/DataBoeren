<?php

use App\Booking\Actions\CreateReservation;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Mail\BookingConfirmed;
use App\Mail\BookingReceived;
use App\Mail\MagicLink;
use App\Models\Campsite;
use App\Models\CampsitePrice;
use App\Models\Extra;
use App\Models\OrderSummary;
use App\Models\Reservation;
use App\Models\Season;
use App\Models\SeasonPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

/**
 * Build a campsite that fits the party, priced for a season that covers the
 * stay. Returns the campsite plus the dates so each test can post a valid booking.
 */
function bookableCampsite(): array
{
    $checkIn = now()->addDays(10)->startOfDay();
    $checkOut = $checkIn->copy()->addDays(2);

    $season = Season::factory()->create();

    SeasonPeriod::factory()->for($season)->create([
        'starts_at' => $checkIn->copy()->subMonth(),
        'ends_at' => $checkIn->copy()->addMonth(),
    ]);

    $campsite = Campsite::factory()->create([
        'max_people' => 6,
        'max_vehicles' => 2,
    ]);

    CampsitePrice::factory()->create([
        'campsite_id' => $campsite->id,
        'season_id' => $season->id,
    ]);

    return [$campsite, $checkIn, $checkOut];
}

// Regression guard: the whole booking pipeline must be resolvable through the
// container. CreateReservation pulls in CalculatePrice, FindAvailableCampsite and
// ResolveBookingExtras; if any dependency can't be autowired, store() 500s.
it('resolves the booking pipeline through the container', function () {
    expect(app(CreateReservation::class))->toBeInstanceOf(CreateReservation::class);
});

it('shows the booking form with a price preview and the available extras', function () {
    [$campsite, $checkIn, $checkOut] = bookableCampsite();
    Extra::factory()->create();

    $response = $this->get(route('bookings.create', [
        'campsite' => $campsite->id,
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
        'adults' => 2,
        'children' => 1,
        'vehicles' => 1,
    ]));

    $response->assertOk()
        ->assertViewHas('order', fn ($order) => $order instanceof OrderSummary)
        ->assertViewHas('extras', fn ($extras) => $extras->count() === 1);
});

it('redirects away from the booking form when the campsite does not fit the party', function () {
    [$campsite, $checkIn, $checkOut] = bookableCampsite();
    $campsite->update(['max_people' => 1]);

    $this->get(route('bookings.create', [
        'campsite' => $campsite->id,
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
        'adults' => 4,
        'children' => 0,
        'vehicles' => 1,
    ]))->assertRedirect(route('campsites.index', [
        'datestart' => $checkIn->format('Y-m-d'),
        'dateend' => $checkOut->format('Y-m-d'),
        'adults' => 4,
        'children' => 0,
        'vehicles' => 1,
    ]));
});

it('stores a pending reservation from a valid booking request', function () {
    Mail::fake();

    [$campsite, $checkIn, $checkOut] = bookableCampsite();

    $response = $this->post(route('bookings.store'), [
        'first_name' => 'Jan',
        'last_name' => 'Jansen',
        'phone' => '0612345678',
        'email' => 'jan@example.com',
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
        'campsite_id' => $campsite->id,
        'num_adults' => 2,
        'num_children' => 1,
        'num_vehicles' => 1,
        'pay_method' => 'in_person',
        'adult_confirmation' => '1',
        'house_rules' => '1',
    ]);

    $response->assertRedirect(route('login.sent'));

    $reservation = Reservation::query()->firstOrFail();

    expect($reservation->campsite_id)->toBe($campsite->id)
        ->and($reservation->status)->toBe(ReservationStatus::Pending)
        ->and($reservation->num_adults)->toBe(2)
        ->and($reservation->num_children)->toBe(1);

    expect($reservation->orderSummary()->exists())->toBeTrue();
});

// Pay-on-site: the amount owed is recorded as a pending cash payment and the
// customer gets a "we received your booking" email carrying the management link.
it('records a pending cash payment and emails a received notice for pay-on-site bookings', function () {
    Mail::fake();

    [$campsite, $checkIn, $checkOut] = bookableCampsite();

    $this->post(route('bookings.store'), [
        'first_name' => 'Jan',
        'last_name' => 'Jansen',
        'phone' => '0612345678',
        'email' => 'jan@example.com',
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
        'campsite_id' => $campsite->id,
        'num_adults' => 2,
        'num_children' => 1,
        'num_vehicles' => 1,
        'pay_method' => 'in_person',
        'adult_confirmation' => '1',
        'house_rules' => '1',
    ])->assertRedirect(route('login.sent'));

    $reservation = Reservation::query()->firstOrFail();
    $payment = $reservation->payments()->firstOrFail();

    expect($payment->method)->toBe(PaymentMethod::Cash)
        ->and($payment->status)->toBe(PaymentStatus::Pending)
        ->and($payment->amount)->toBe($reservation->orderSummary->total);

    Mail::assertQueued(
        BookingReceived::class,
        fn (BookingReceived $mail) => $mail->reservation->is($reservation) && $mail->hasTo('jan@example.com'),
    );
    Mail::assertNotQueued(MagicLink::class);
});

// destroy() takes a SignedUrlGenerator to build the redirect target. It was missing
// from the signature, so cancelling any reservation 500'd on an undefined variable.
it('lets a customer cancel their own reservation', function () {
    Mail::fake();

    $user = User::factory()->create();
    $reservation = Reservation::factory()->for($user, 'customer')->create();

    $url = URL::temporarySignedRoute('bookings.destroy', now()->addHour(), [
        'user' => $user->id,
        'reservation' => $reservation->id,
    ]);

    $this->delete($url)->assertRedirect();

    $reservation->refresh();

    expect($reservation->status)->toBe(ReservationStatus::Cancelled)
        ->and($reservation->cancelled_at)->not->toBeNull()
        ->and($reservation->cancelled_by_user_id)->toBe($user->id);
});

// An employee booking is created straight as Confirmed from the admin panel, so
// the status never "changes" and updated() never fires — the created() hook on
// ReservationObserver is what mails the customer their confirmation.
it('emails the customer a confirmation when an employee creates a confirmed booking', function () {
    Mail::fake();

    $customer = User::factory()->create(['email' => 'gast@example.com']);

    $reservation = Reservation::factory()
        ->for($customer, 'customer')
        ->bookedByEmployee(null)
        ->create(['status' => ReservationStatus::Confirmed]);

    Mail::assertQueued(
        BookingConfirmed::class,
        fn (BookingConfirmed $mail) => $mail->reservation->is($reservation) && $mail->hasTo('gast@example.com'),
    );
});

// Online bookings are born Pending; their confirmation must wait for the status
// to change after payment, so creating one must not fire a confirmation mail.
it('does not email a confirmation when an online booking is created pending', function () {
    Mail::fake();

    Reservation::factory()->pending()->create();

    Mail::assertNothingQueued();
});

it('forbids cancelling a reservation that belongs to someone else', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();
    $reservation = Reservation::factory()->for($owner, 'customer')->create();

    // Sign the link for the attacker, pointing at the owner's reservation.
    $url = URL::temporarySignedRoute('bookings.destroy', now()->addHour(), [
        'user' => $attacker->id,
        'reservation' => $reservation->id,
    ]);

    $this->delete($url)->assertForbidden();

    expect($reservation->fresh()->status)->toBe(ReservationStatus::Confirmed);
});

<?php

use App\Booking\Actions\CreateReservation;
use App\Enums\CouponScope;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Mail\BookingConfirmed;
use App\Mail\BookingReceived;
use App\Mail\MagicLink;
use App\Models\Campsite;
use App\Models\Coupon;
use App\Models\CampsitePrice;
use App\Models\Extra;
use App\Models\Reservation;
use App\Models\Season;
use App\Models\SeasonPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

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

/** Mount the booking-form component for a stay, with valid customer details filled in. */
function bookingForm(Campsite $campsite, $checkIn, $checkOut, array $params = [])
{
    return Livewire::test('booking-form', array_merge([
        'campsite' => $campsite,
        'checkIn' => $checkIn->format('Y-m-d'),
        'checkOut' => $checkOut->format('Y-m-d'),
        'adults' => 2,
        'children' => 1,
        'vehicles' => 1,
    ], $params))
        ->set('firstName', 'Jan')
        ->set('lastName', 'Jansen')
        ->set('phone', '0612345678')
        ->set('email', 'jan@example.com')
        ->set('payMethod', 'in_person')
        ->set('adultConfirmation', true)
        ->set('houseRules', true);
}

it('shows the booking form with the live price and the available extras', function () {
    [$campsite, $checkIn, $checkOut] = bookableCampsite();
    $extra = Extra::factory()->create();

    $this->get(route('bookings.create', [
        'campsite' => $campsite->id,
        'check_in' => $checkIn->format('Y-m-d'),
        'check_out' => $checkOut->format('Y-m-d'),
        'adults' => 2,
        'children' => 1,
        'vehicles' => 1,
    ]))
        ->assertOk()
        ->assertSeeLivewire('booking-form')
        ->assertSee('Prijsoverzicht')
        ->assertSee($extra->name);
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

it('stores a pending reservation from a valid booking', function () {
    Mail::fake();
    [$campsite, $checkIn, $checkOut] = bookableCampsite();

    bookingForm($campsite, $checkIn, $checkOut)
        ->call('submit')
        ->assertRedirect(route('login.sent'));

    $reservation = Reservation::query()->firstOrFail();

    expect($reservation->campsite_id)->toBe($campsite->id)
        ->and($reservation->status)->toBe(ReservationStatus::Pending)
        ->and($reservation->num_adults)->toBe(2)
        ->and($reservation->num_children)->toBe(1)
        ->and($reservation->orderSummary()->exists())->toBeTrue();
});

// Pay-on-site: the amount owed is recorded as a pending cash payment and the
// customer gets a "we received your booking" email carrying the management link.
it('records a pending cash payment and emails a received notice for pay-on-site bookings', function () {
    Mail::fake();
    [$campsite, $checkIn, $checkOut] = bookableCampsite();

    bookingForm($campsite, $checkIn, $checkOut)
        ->call('submit')
        ->assertRedirect(route('login.sent'));

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

it('blocks submission when the chosen party exceeds the campsite capacity', function () {
    Mail::fake();
    [$campsite, $checkIn, $checkOut] = bookableCampsite(); // max_people 6

    bookingForm($campsite, $checkIn, $checkOut, ['children' => 100])
        ->call('submit')
        ->assertHasErrors('num_children');

    expect(Reservation::query()->count())->toBe(0);
});

it('applies a coupon to a public booking and tracks its usage', function () {
    Mail::fake();
    [$campsite, $checkIn, $checkOut] = bookableCampsite();
    $coupon = Coupon::factory()->flat()->create([
        'discount_value' => 10,
        'uses_count'     => 0,
        'max_uses'       => null,
        'expires_at'     => null,
    ]);

    bookingForm($campsite, $checkIn, $checkOut)
        ->set('couponCode', $coupon->code)
        ->call('submit')
        ->assertRedirect(route('login.sent'));

    $reservation = Reservation::query()->firstOrFail();

    expect($reservation->coupon_id)->toBe($coupon->id)
        ->and($coupon->refresh()->uses_count)->toBe(1)
        ->and($reservation->orderSummary->coupon_discount)->not->toBeNull();
});

it('rejects an expired coupon code on a public booking', function () {
    Mail::fake();
    [$campsite, $checkIn, $checkOut] = bookableCampsite();
    $coupon = Coupon::factory()->expired()->create();

    bookingForm($campsite, $checkIn, $checkOut)
        ->set('couponCode', $coupon->code)
        ->call('submit')
        ->assertHasErrors('coupon_code');

    expect(Reservation::query()->count())->toBe(0);
});

it('updates the live price when an extra quantity changes', function () {
    [$campsite, $checkIn, $checkOut] = bookableCampsite();
    $extra = Extra::factory()->create([
        'name' => 'Brandhout',
        'price' => 500, // € 5,00
        'billing_type' => \App\Enums\BillingType::OneTime,
    ]);

    bookingForm($campsite, $checkIn, $checkOut)
        ->assertDontSee('€ 15,00')
        ->set("extras.{$extra->id}", 3)
        ->assertSee('€ 15,00'); // 3 × € 5,00 shows in the breakdown's extras line
});

it('shows the live discount when a valid total-scope coupon is applied', function () {
    [$campsite, $checkIn, $checkOut] = bookableCampsite();
    $coupon = Coupon::factory()->percent()->create([
        'scope'          => CouponScope::Total,
        'discount_value' => 50,
        'uses_count'     => 0,
        'max_uses'       => null,
        'expires_at'     => null,
    ]);

    bookingForm($campsite, $checkIn, $checkOut)
        ->set('couponCode', $coupon->code)
        ->call('applyCoupon')
        ->assertSee('Couponkorting')
        ->assertSee($coupon->title)
        ->assertSee($coupon->formatted_discount);
});

it('does not apply an unknown coupon code in the live preview', function () {
    [$campsite, $checkIn, $checkOut] = bookableCampsite();

    bookingForm($campsite, $checkIn, $checkOut)
        ->set('couponCode', 'NOPE404')
        ->call('applyCoupon')
        ->assertSee('ongeldig of niet meer geldig')
        ->assertDontSee('Couponkorting');
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

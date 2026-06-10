<?php

use App\Enums\ReservationStatus;
use App\Mail\AwaitingPayment;
use App\Mail\BookingConfirmed;
use App\Mail\PaymentReceipt;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;

uses(RefreshDatabase::class);

function fireCheckoutWebhook(
    Reservation $reservation,
    string $type = 'checkout.session.completed',
    string $paymentStatus = 'paid',
    string $sessionId = 'cs_test_1',
): void {
    event(new WebhookReceived([
        'type' => $type,
        'data' => ['object' => [
            'id'                  => $sessionId,
            'client_reference_id' => (string) $reservation->id,
            'payment_status'      => $paymentStatus,
            'amount_total'        => 12345,
        ]],
    ]));
}

it('confirms a pending booking and mails the customer when the checkout webhook reports payment', function () {
    Mail::fake();
    $reservation = Reservation::factory()->pending()->create();

    fireCheckoutWebhook($reservation);

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Confirmed)
        ->and($reservation->payments()->where('stripe_session_id', 'cs_test_1')->count())->toBe(1);

    Mail::assertQueued(PaymentReceipt::class);
    Mail::assertQueued(BookingConfirmed::class, fn (BookingConfirmed $mail) => $mail->reservation->is($reservation));
});

it('records the amount Stripe actually charged', function () {
    Mail::fake();
    $reservation = Reservation::factory()->pending()->create();

    fireCheckoutWebhook($reservation);

    expect($reservation->payments()->where('stripe_session_id', 'cs_test_1')->value('amount'))->toBe(12345);
});

it('is idempotent when the redirect and the webhook both confirm the same checkout', function () {
    Mail::fake();
    $reservation = Reservation::factory()->pending()->create();

    fireCheckoutWebhook($reservation);
    fireCheckoutWebhook($reservation); // Stripe retry, or the success redirect racing the webhook.

    expect($reservation->payments()->where('stripe_session_id', 'cs_test_1')->count())->toBe(1);
    Mail::assertQueued(PaymentReceipt::class, 1);
    Mail::assertQueued(BookingConfirmed::class, 1);
});

it('ignores a delayed checkout that has not settled yet', function () {
    Mail::fake();
    $reservation = Reservation::factory()->pending()->create();

    // iDEAL & friends fire checkout.session.completed while still "unpaid".
    fireCheckoutWebhook($reservation, paymentStatus: 'unpaid');

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Pending);
    Mail::assertNothingQueued();
});

it('confirms once the delayed payment finally succeeds', function () {
    Mail::fake();
    $reservation = Reservation::factory()->pending()->create();

    fireCheckoutWebhook($reservation, paymentStatus: 'unpaid');
    fireCheckoutWebhook($reservation, type: 'checkout.session.async_payment_succeeded');

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Confirmed);
    Mail::assertQueued(BookingConfirmed::class, 1);
});

it('emails a retry link when a delayed payment fails on a still-pending booking', function () {
    Mail::fake();
    $reservation = Reservation::factory()->pending()->create();

    fireCheckoutWebhook($reservation, type: 'checkout.session.async_payment_failed', paymentStatus: 'unpaid');

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Pending);
    Mail::assertQueued(AwaitingPayment::class, fn (AwaitingPayment $mail) => $mail->reservation->is($reservation));
});

it('does not nudge a booking that was already confirmed when a later failed event arrives', function () {
    Mail::fake();
    $reservation = Reservation::factory()->pending()->create();

    fireCheckoutWebhook($reservation); // settles first
    fireCheckoutWebhook($reservation, type: 'checkout.session.async_payment_failed', paymentStatus: 'unpaid');

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Confirmed);
    Mail::assertNotQueued(AwaitingPayment::class);
});

it('ignores webhook events it does not handle', function () {
    Mail::fake();
    $reservation = Reservation::factory()->pending()->create();

    event(new WebhookReceived(['type' => 'customer.subscription.updated', 'data' => ['object' => []]]));

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Pending);
    Mail::assertNothingQueued();
});

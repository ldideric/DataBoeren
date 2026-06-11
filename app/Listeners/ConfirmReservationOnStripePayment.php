<?php

namespace App\Listeners;

use App\Auth\Services\SignedUrlGenerator;
use App\Enums\ReservationStatus;
use App\Mail\AwaitingPayment;
use App\Models\Reservation;
use App\Payments\Actions\ConfirmStripeCheckout;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;

/**
 * Drives reservation fulfilment from Stripe webhooks, so it no longer depends on
 * the customer's browser making it back to the success_url:
 *
 *  - checkout.session.completed              → card / instant methods (already paid)
 *  - checkout.session.async_payment_succeeded → iDEAL & other delayed methods,
 *    which return to success_url still "unpaid" and only settle later → confirm.
 *  - checkout.session.async_payment_failed    → a delayed payment that was started
 *    but ultimately failed → the booking stays Pending; email the customer a fresh
 *    retry link so a silent failure doesn't strand them.
 *
 * Card declines never reach here — they happen on the Stripe-hosted page and the
 * customer simply retries there. Cashier verifies the webhook signature before
 * dispatching this event, and ConfirmStripeCheckout is idempotent, so this is
 * safe to run alongside the success redirect or for a retried delivery.
 */
class ConfirmReservationOnStripePayment
{
    private const PAID_EVENTS = [
        'checkout.session.completed',
        'checkout.session.async_payment_succeeded',
    ];

    private const FAILED_EVENTS = [
        'checkout.session.async_payment_failed',
    ];

    public function __construct(
        private readonly ConfirmStripeCheckout $confirm,
        private readonly SignedUrlGenerator $urls,
    ) {
    }

    public function handle(WebhookReceived $event): void
    {
        $type = $event->payload['type'] ?? null;
        $session = $event->payload['data']['object'] ?? [];

        $reservation = $this->resolveReservation($session);

        if (! $reservation) {
            return;
        }

        if (in_array($type, self::PAID_EVENTS, true)) {
            $this->onPaid($reservation, $session);

            return;
        }

        if (in_array($type, self::FAILED_EVENTS, true)) {
            $this->onFailed($reservation);
        }
    }

    /**
     * @param  array<string, mixed>  $session
     */
    private function onPaid(Reservation $reservation, array $session): void
    {
        // A delayed-notification method fires checkout.session.completed while
        // still "unpaid"; ignore it and wait for async_payment_succeeded.
        if (($session['payment_status'] ?? null) !== 'paid') {
            return;
        }

        if (! isset($session['id'])) {
            return;
        }

        $this->confirm->handle($reservation, $session['id'], $session['amount_total'] ?? null);
    }

    private function onFailed(Reservation $reservation): void
    {
        // Only nudge bookings that are genuinely still open — never one that was
        // already paid (e.g. a retry that finally succeeded) or cancelled.
        if ($reservation->status !== ReservationStatus::Pending) {
            return;
        }

        Mail::to($reservation->customer->email)
            ->send(new AwaitingPayment($reservation, $this->urls->payment($reservation)));
    }

    /**
     * @param  array<string, mixed>  $session
     */
    private function resolveReservation(array $session): ?Reservation
    {
        $reservationId = $session['client_reference_id'] ?? null;

        return $reservationId ? Reservation::find($reservationId) : null;
    }
}

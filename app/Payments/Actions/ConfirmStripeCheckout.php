<?php

namespace App\Payments\Actions;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Mail\PaymentReceipt;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Records a paid Stripe checkout against a reservation and confirms it.
 *
 * This is the single source of truth for fulfilling an online payment, used by
 * both the browser success redirect (PaymentController::success) and the Stripe
 * webhook listener. It is idempotent: the payments.stripe_session_id unique
 * index plus a row lock on the reservation mean the redirect and the webhook
 * can both fire for the same checkout without double-charging, double-mailing,
 * or double-confirming — whichever arrives first wins, the other is a no-op.
 */
class ConfirmStripeCheckout
{
    public function handle(Reservation $reservation, string $sessionId, ?int $amountInCents = null): Payment
    {
        return DB::transaction(function () use ($reservation, $sessionId, $amountInCents): Payment {
            // Serialise the redirect and the webhook racing on the same booking.
            $reservation = Reservation::query()
                ->lockForUpdate()
                ->with(['orderSummary', 'customer'])
                ->findOrFail($reservation->getKey());

            $payment = Payment::firstOrCreate(
                ['stripe_session_id' => $sessionId],
                [
                    'reservation_id' => $reservation->id,
                    'amount'         => $amountInCents ?? $reservation->orderSummary?->total ?? 0,
                    'status'         => PaymentStatus::Paid,
                    'method'         => PaymentMethod::Stripe,
                    'paid_at'        => now(),
                ],
            );

            if ($payment->wasRecentlyCreated) {
                Mail::to($reservation->customer->email)
                    ->send((new PaymentReceipt($reservation, $payment))->afterCommit());
            }

            // A pay-on-arrival booking may still carry a Pending cash payment;
            // paying online supersedes it, so void it rather than leave it counting
            // as cash owed. Idempotent: a re-entrant call finds nothing to void.
            $reservation->payments()
                ->whereKeyNot($payment->getKey())
                ->where('status', PaymentStatus::Pending)
                ->update(['status' => PaymentStatus::Cancelled]);

            // Flipping the status fires ReservationObserver::updated(), which sends
            // the BookingConfirmed mail. Guard so a second call stays a no-op.
            if ($reservation->status !== ReservationStatus::Confirmed) {
                $reservation->update(['status' => ReservationStatus::Confirmed]);
            }

            return $payment;
        });
    }
}

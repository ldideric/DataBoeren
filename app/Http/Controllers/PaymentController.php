<?php

namespace App\Http\Controllers;

use App\Auth\Services\SignedUrlGenerator;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Payments\Actions\ConfirmStripeCheckout;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Checkout;

class PaymentController extends Controller
{
    public function show(Reservation $reservation, SignedUrlGenerator $urls): View|RedirectResponse
    {
        $reservation->loadMissing('campsite', 'customer', 'orderSummary', 'extras.extra');

        abort_if($reservation->orderSummary === null, 409, 'Deze reservering heeft geen prijsoverzicht.');
        abort_if($reservation->status === ReservationStatus::Cancelled, 409, 'Deze reservering is geannuleerd.');

        if ($reservation->status === ReservationStatus::Confirmed) {
            return redirect()->to($urls->bookings($reservation->customer))
                ->with('status', 'Deze reservering is al betaald.');
        }

        return view('payments.show', [
            'reservation' => $reservation,
            'order' => $reservation->orderSummary,
            'checkoutUrl' => $urls->checkout($reservation),
            'bookingsUrl' => $urls->bookings($reservation->customer),
        ]);
    }

    public function checkout(Reservation $reservation): Checkout
    {
        $reservation->loadMissing('campsite', 'orderSummary');

        abort_if($reservation->orderSummary === null, 409, 'Deze reservering heeft geen prijsoverzicht.');
        abort_if($reservation->status === ReservationStatus::Confirmed, 409, 'Deze reservering is al betaald.');
        abort_if($reservation->status === ReservationStatus::Cancelled, 409, 'Deze reservering is geannuleerd.');

        $amountInCents = $reservation->orderSummary->total;

        return $reservation->customer->checkoutCharge($amountInCents, "Reservering {$reservation->campsite->name}", 1, [
            'success_url'         => route('payments.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'          => route('payments.cancel').'?reservation_id='.$reservation->id,
            'client_reference_id' => $reservation->id,
        ]);
    }

    public function success(Request $request, ConfirmStripeCheckout $confirm): View
    {
        $sessionId = $request->query('session_id');
        abort_if(! $sessionId, 400);

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        $reservation = Reservation::findOrFail($session->client_reference_id);

        // Card/instant methods are already 'paid' the moment we land here, so
        // confirm inline for instant feedback (idempotent — the webhook may have
        // beaten us to it). Delayed methods such as iDEAL arrive 'unpaid' and are
        // confirmed by the webhook once Stripe settles; we show a "processing"
        // notice instead of treating the successful payment as cancelled.
        if ($session->payment_status === 'paid') {
            $confirm->handle($reservation, $sessionId, $session->amount_total);
        }

        $reservation->refresh()->loadMissing('campsite', 'orderSummary');

        return view('checkout.success', [
            'reservation' => $reservation,
            'confirmed'   => $reservation->status === ReservationStatus::Confirmed,
        ]);
    }

    public function cancel(Request $request, SignedUrlGenerator $urls): View
    {
        $reservation = Reservation::find($request->query('reservation_id'));

        $retryUrl = $reservation ? $urls->payment($reservation) : null;

        return view('checkout.cancel', compact('retryUrl'));
    }
}

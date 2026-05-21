<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Support\SignedLink;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Laravel\Cashier\Checkout;

class PaymentController extends Controller
{
    public function show(Reservation $reservation): View
    {
        // Access is authorised by the signed URL; no session/owner check needed.
        return view('payments.show', [
            'reservation' => $reservation,
            'checkoutUrl' => SignedLink::checkout($reservation),
            'bookingsUrl' => SignedLink::bookings($reservation->customer),
        ]);
    }

    public function checkout(Reservation $reservation): Checkout
    {
        /**
         * @todo Real total must come from $reservation->orderSummary->total (in cents).
         *       OrderSummary is not yet created in the booking flow, so we charge a
         *       placeholder amount to prove the round-trip. See todo.md > Payments.
         */
        $amount = 100;

        return $reservation->customer->checkoutCharge($amount, "Reservering {$reservation->id}", 1, [
            'success_url' => route('payments.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payments.cancel'),
        ]);
    }

    public function success(Request $request): View
    {
        /**
         * @todo Verify the Stripe session before trusting success:
         *         $session = Cashier::stripe()->checkout->sessions->retrieve($request->get('session_id'));
         *         if ($session->payment_status === 'paid') { create Payment row, set Reservation Confirmed }
         * @todo No Payment row is created and the Stripe session id is not stored — data lost here.
         * @todo Production: confirm payment via the Stripe webhook, not just this redirect.
         */
        return view('checkout.success');
    }

    public function cancel(): View
    {
        // @todo No record is kept that the user abandoned payment.
        return view('checkout.cancel');
    }
}

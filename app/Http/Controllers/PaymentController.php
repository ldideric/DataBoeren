<?php

namespace App\Http\Controllers;

use App\Auth\Services\SignedUrlGenerator;
use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Laravel\Cashier\Checkout;

class PaymentController extends Controller
{
    public function show(Reservation $reservation, SignedUrlGenerator $urls): View
    {
        $reservation->loadMissing('campsite', 'orderSummary');

        abort_if($reservation->orderSummary === null, 409, 'Deze reservering heeft geen prijsoverzicht.');

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

        $amountInCents = $reservation->orderSummary->total;

        return $reservation->customer->checkoutCharge($amountInCents, "Reservering {$reservation->campsite->name}", 1, [
            'success_url' => route('payments.success').'?session_id={CHECKOUT_SESSION_ID}',
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

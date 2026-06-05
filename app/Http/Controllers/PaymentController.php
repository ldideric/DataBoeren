<?php

namespace App\Http\Controllers;

use App\Auth\Services\SignedUrlGenerator;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Mail\PaymentReceipt;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

    public function success(Request $request): View|RedirectResponse
    {
        $sessionId = $request->query('session_id');
        abort_if(! $sessionId, 400);

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect()->route('payments.cancel', [
                'reservation_id' => $session->client_reference_id,
            ]);
        }

        $reservation = Reservation::findOrFail($session->client_reference_id);
        $reservation->loadMissing('campsite', 'orderSummary');

        $payment = Payment::firstOrCreate(
            ['stripe_session_id' => $sessionId],
            [
                'reservation_id' => $reservation->id,
                'amount'         => $reservation->orderSummary->total,
                'status'         => PaymentStatus::Paid,
                'method'         => PaymentMethod::Stripe,
                'paid_at'        => now(),
            ]
        );

        if ($payment->wasRecentlyCreated) {
            $reservation->loadMissing('customer');
            Mail::to($reservation->customer->email)->send(new PaymentReceipt($reservation, $payment));
        }

        if ($reservation->status !== ReservationStatus::Confirmed) {
            $reservation->update(['status' => ReservationStatus::Confirmed]);
        }

        return view('checkout.success', compact('reservation'));
    }

    public function cancel(Request $request, SignedUrlGenerator $urls): View
    {
        $reservation = Reservation::find($request->query('reservation_id'));

        $retryUrl = $reservation ? $urls->payment($reservation) : null;

        return view('checkout.cancel', compact('retryUrl'));
    }
}

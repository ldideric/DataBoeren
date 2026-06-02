<?php

namespace App\Booking\Actions;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Reservation;

readonly class RecordCashPayment
{
    /**
     * Record the amount owed for a pay-on-site reservation as a pending cash
     * Payment. It is marked Paid later, when an employee accepts the booking
     * after the guest has settled up at the camping.
     */
    public function handle(Reservation $reservation): Payment
    {
        return $reservation->payments()->create([
            'amount' => $reservation->orderSummary->total,
            'status' => PaymentStatus::Pending,
            'method' => PaymentMethod::Cash,
        ]);
    }
}

<?php

namespace App\Observers;

use App\Enums\ReservationStatus;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;

class ReservationObserver
{
    public function updated(Reservation $reservation): void
    {
        if (! $reservation->wasChanged('status')) {
            return;
        }

        $mailable = match ($reservation->status) {
            ReservationStatus::Confirmed => new BookingConfirmed($reservation),
            ReservationStatus::Cancelled => new BookingCancelled($reservation),
            default => null,
        };

        if ($mailable) {
            Mail::to($reservation->customer->email)->send($mailable);
        }
    }
}

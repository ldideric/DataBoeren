<?php

namespace App\Observers;

use App\Enums\PaymentStatus;
use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmed;
use App\Models\Reservation;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class ReservationObserver
{
    /**
     * Online bookings are born Pending and only earn their confirmation once the
     * status later changes (handled by updated()). Employee bookings, however,
     * are created straight as Confirmed from the admin panel, so the status never
     * "changes" and updated() never fires — catch that here so the customer still
     * gets a confirmation mail for a booking made for them on-site or by phone.
     */
    public function created(Reservation $reservation): void
    {
        if ($reservation->source !== ReservationSource::Employee) {
            return;
        }

        $this->sendStatusMail($reservation);
    }

    public function updated(Reservation $reservation): void
    {
        if (! $reservation->wasChanged('status')) {
            return;
        }

        // A cancelled booking can never collect its outstanding cash, so void any
        // still-pending payment rather than leaving an orphan that keeps counting
        // toward "cash to collect". Catches both the admin and public cancel paths.
        if ($reservation->status === ReservationStatus::Cancelled) {
            $reservation->payments()
                ->where('status', PaymentStatus::Pending)
                ->update(['status' => PaymentStatus::Cancelled]);
        }

        $this->sendStatusMail($reservation);
    }

    private function sendStatusMail(Reservation $reservation): void
    {
        $mailable = match ($reservation->status) {
            ReservationStatus::Confirmed => new BookingConfirmed($reservation),
            ReservationStatus::Cancelled => new BookingCancelled($reservation),
            default => null,
        };

        if (! $mailable instanceof Mailable) {
            return;
        }

        // created() fires inside the admin panel's DB::transaction while the queue
        // runs with after_commit => false, so defer the send until the row is
        // actually committed — otherwise a rolled-back booking would still mail.
        Mail::to($reservation->customer->email)->send($mailable->afterCommit());
    }
}

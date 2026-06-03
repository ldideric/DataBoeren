<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AwaitingPayment extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Reservation $reservation, public string $paymentUrl)
    {
        $this->locale('nl');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Rond uw reservering af — betaling nog open');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.bookings.awaiting-payment');
    }
}

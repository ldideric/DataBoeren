<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreArrival extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Reservation $reservation)
    {
        $this->locale('nl');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Tot binnenkort op Camping De Groene Weide');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.bookings.pre-arrival');
    }
}

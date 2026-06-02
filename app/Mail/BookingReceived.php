<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReceived extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Reservation $reservation, public string $bookingsUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'We hebben uw reservering ontvangen');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.bookings.received');
    }
}

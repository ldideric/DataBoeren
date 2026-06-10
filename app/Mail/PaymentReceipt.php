<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceipt extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Reservation $reservation, public Payment $payment)
    {
        $this->locale('nl');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Betaling ontvangen — uw betalingsbewijs');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.payments.receipt');
    }
}

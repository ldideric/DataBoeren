<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MagicLink extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public User $user, public string $signedUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Uw link naar uw boekingen — De Groene Weide');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.auth.magic-link');
    }
}

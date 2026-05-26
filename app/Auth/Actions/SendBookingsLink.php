<?php

namespace App\Auth\Actions;

use App\Auth\Services\SignedUrlGenerator;
use App\Mail\MagicLink;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendBookingsLink
{
    public function __construct(private readonly SignedUrlGenerator $urls) {}

    public function handle(User $user): void
    {
        Mail::to($user->email)->send(new MagicLink($user, $this->urls->bookings($user)));
    }
}

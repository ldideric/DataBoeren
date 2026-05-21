<?php

namespace App\Actions;

use App\Mail\MagicLink;
use App\Models\User;
use App\Support\SignedLink;
use Illuminate\Support\Facades\Mail;

class SendBookingsLink
{
    public function handle(User $user): void
    {
        Mail::to($user->email)->send(new MagicLink($user, SignedLink::bookings($user)));
    }
}

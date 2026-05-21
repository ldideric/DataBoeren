<?php

namespace App\Support;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class SignedLink
{
    /** Amount of minutes a signed url is valid */
    public const int TTL_MINUTES = 60;

    public static function bookings(User $user): string
    {
        return URL::temporarySignedRoute(
            'bookings.index',
            now()->addMinutes(self::TTL_MINUTES),
            ['user' => $user->id],
        );
    }

    public static function cancelReservation(User $user, Reservation $reservation): string
    {
        return URL::temporarySignedRoute(
            'bookings.destroy',
            now()->addMinutes(self::TTL_MINUTES),
            ['user' => $user->id, 'reservation' => $reservation->id],
        );
    }

    public static function payment(Reservation $reservation): string
    {
        return URL::temporarySignedRoute(
            'payments.show',
            now()->addMinutes(self::TTL_MINUTES),
            ['reservation' => $reservation->id],
        );
    }

    public static function checkout(Reservation $reservation): string
    {
        return URL::temporarySignedRoute(
            'payments.checkout',
            now()->addMinutes(self::TTL_MINUTES),
            ['reservation' => $reservation->id],
        );
    }
}

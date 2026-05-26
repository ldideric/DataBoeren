<?php

namespace App\Auth\Services;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Contracts\Routing\UrlGenerator;

class SignedUrlGenerator
{
    public const TTL_MINUTES = 60;

    public function __construct(private readonly UrlGenerator $url) {}

    public function bookings(User $user): string
    {
        return $this->url->temporarySignedRoute(
            'bookings.index',
            now()->addMinutes(self::TTL_MINUTES),
            ['user' => $user->id],
        );
    }

    public function cancelReservation(User $user, Reservation $reservation): string
    {
        return $this->url->temporarySignedRoute(
            'bookings.destroy',
            now()->addMinutes(self::TTL_MINUTES),
            ['user' => $user->id, 'reservation' => $reservation->id],
        );
    }

    public function payment(Reservation $reservation): string
    {
        return $this->url->temporarySignedRoute(
            'payments.show',
            now()->addMinutes(self::TTL_MINUTES),
            ['reservation' => $reservation->id],
        );
    }

    public function checkout(Reservation $reservation): string
    {
        return $this->url->temporarySignedRoute(
            'payments.checkout',
            now()->addMinutes(self::TTL_MINUTES),
            ['reservation' => $reservation->id],
        );
    }
}

<?php

namespace App\Booking\Queries;

use App\Models\Campsite;
use Illuminate\Support\Carbon;

class CheckAvailability
{
    public function handle(
        Campsite $campsite,
        int $partySize,
        Carbon $checkIn,
        Carbon $checkOut,
    ): bool {
        return Campsite::query()
            ->whereKey($campsite->id)
            ->whereFitsParty($partySize)
            ->whereAvailableBetween($checkIn, $checkOut)
            ->exists();
    }
}

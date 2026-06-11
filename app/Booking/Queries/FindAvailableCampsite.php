<?php

namespace App\Booking\Queries;

use App\Models\Campsite;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class FindAvailableCampsite
{
    /**
     * Lock and return a campsite that still fits the party and is free for the
     * dates. The row lock stops two concurrent bookings from grabbing the same
     * spot; if it was taken in the meantime, fail validation.
     *
     * @throws ValidationException
     */
    public function handle(
        string $campsiteId,
        int $partySize,
        Carbon $checkIn,
        Carbon $checkOut,
    ): Campsite {
        return Campsite::query()
            ->whereKey($campsiteId)
            ->whereFitsParty($partySize)
            ->whereAvailableBetween($checkIn, $checkOut)
            ->lockForUpdate()
            ->first()
            ?? throw ValidationException::withMessages([
                'campsite_id' => 'De gekozen plek is niet (meer) beschikbaar voor deze data.',
            ]);
    }
}

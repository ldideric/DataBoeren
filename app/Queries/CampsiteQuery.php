<?php

namespace App\Queries;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class CampsiteQuery extends Builder
{
    public function whereFitsParty(int $people): self
    {
        return $this->where('max_people', '>=', $people);
    }

    public function whereAvailableBetween(Carbon $checkIn, Carbon $checkOut): self
    {
        return $this->whereDoesntHave(
            'reservations',
            fn (Builder $query) => $query
                ->whereIn('status', [ReservationStatus::Pending, ReservationStatus::Confirmed])
                ->where('check_in', '<', $checkOut)
                ->where('check_out', '>', $checkIn)
        );
    }
}

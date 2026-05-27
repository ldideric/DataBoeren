<?php

namespace App\Booking\Queries;

use App\Models\Extra;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GetAvailableExtras
{
    public function handle(Carbon $checkIn, Carbon $checkOut): Collection
    {
        return Extra::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Extra $extra) => [
                'model' => $extra,
                'cap' => GetExtraAvailability::for($extra)->maxSelectable($checkIn, $checkOut),
            ]);
    }
}

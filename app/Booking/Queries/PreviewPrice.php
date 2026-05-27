<?php

namespace App\Booking\Queries;

use App\Models\Campsite;
use App\Models\OrderSummary;
use App\Models\Reservation;
use App\Pricing\Actions\CalculatePrice;
use Illuminate\Support\Carbon;
use RuntimeException;

readonly class PreviewPrice
{
    public function __construct(
        private CalculatePrice $calculator,
    ) {
    }

    public function handle(Campsite $campsite, Carbon $checkIn, Carbon $checkOut, int $adults, int $children): ?OrderSummary
    {
        $preview = new Reservation([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'num_adults' => $adults,
            'num_children' => $children,
        ]);
        $preview->setRelation('campsite', $campsite);

        try {
            return $this->calculator->calculate($preview);
        } catch (RuntimeException) {
            return null;
        }
    }
}

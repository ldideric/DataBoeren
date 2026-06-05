<?php

namespace App\Booking\Queries;

use App\Models\Campsite;
use App\Models\Coupon;
use App\Models\Extra;
use App\Models\OrderSummary;
use App\Models\Reservation;
use App\Pricing\Actions\CalculatePrice;
use Illuminate\Support\Carbon;
use RuntimeException;

readonly class PreviewBookingPrice
{
    public function __construct(private CalculatePrice $calculator)
    {
    }

    public function fromFormData(array $data): ?OrderSummary
    {
        $campsite = ! empty($data['campsite_id']) ? Campsite::find($data['campsite_id']) : null;

        if (! $campsite instanceof Campsite || empty($data['check_in']) || empty($data['check_out'])) {
            return null;
        }

        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            return null;
        }

        $reservation = new Reservation([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'num_adults' => (int) ($data['num_adults'] ?? 0),
            'num_children' => (int) ($data['num_children'] ?? 0),
        ]);
        $reservation->setRelation('campsite', $campsite);
        $reservation->setRelation('coupon', ! empty($data['coupon_id']) ? Coupon::find($data['coupon_id']) : null);

        try {
            return $this->calculator->calculate($reservation, $this->extraSelections($data['extras'] ?? []));
        } catch (RuntimeException) {
            return null;
        }
    }

    private function extraSelections(array $rows): array
    {
        $quantities = collect($rows)
            ->filter(fn ($row) => is_array($row) && ! empty($row['extra_id']) && (int) ($row['quantity'] ?? 0) > 0)
            ->mapWithKeys(fn ($row) => [(string) $row['extra_id'] => (int) $row['quantity']]);

        if ($quantities->isEmpty()) {
            return [];
        }

        $extras = Extra::query()->whereKey($quantities->keys())->get()->keyBy('id');

        return $quantities
            ->map(fn (int $quantity, string $id) => ['extra' => $extras->get($id), 'quantity' => $quantity])
            ->filter(fn (array $line) => $line['extra'] instanceof Extra)
            ->values()
            ->all();
    }
}

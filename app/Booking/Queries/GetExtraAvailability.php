<?php

namespace App\Booking\Queries;

use App\Enums\ReservationStatus;
use App\Enums\StockType;
use App\Models\Extra;
use App\Models\ReservationExtra;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Answers "how many of this extra can still be booked for these nights?".
 *
 * Rentals (firepits, BBQs) are a pool of units checked out and returned, so the
 * limit is the peak number in use on any single night. Consumables (firewood)
 * are used up for good, so every order ever placed counts against stock,
 * regardless of dates. Extras without a stock limit are unlimited.
 */
final readonly class GetExtraAvailability
{
    private const ACTIVE_STATUSES = [ReservationStatus::Pending, ReservationStatus::Confirmed];

    public function __construct(private Extra $extra)
    {
    }

    public static function for(Extra $extra): self
    {
        return new self($extra);
    }

    public function remaining(Carbon $checkIn, Carbon $checkOut): ?int
    {
        if ($this->extra->stock === null) {
            return null;
        }

        $inUse = $this->extra->stock_type === StockType::Consumable
            ? $this->totalClaimed()
            : $this->peakNightlyUsage($checkIn, $checkOut);

        return max(0, $this->extra->stock - $inUse);
    }

    public function maxSelectable(Carbon $checkIn, Carbon $checkOut): ?int
    {
        return collect([$this->extra->max_per_booking, $this->remaining($checkIn, $checkOut)])
            ->reject(fn (?int $cap) => $cap === null)
            ->min();
    }

    private function totalClaimed(): int
    {
        return (int) $this->claimedLines()->sum('quantity');
    }

    private function peakNightlyUsage(Carbon $checkIn, Carbon $checkOut): int
    {
        $lines = $this->claimedLines(fn (Builder $reservation) => $reservation
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn))
            ->with('reservation:id,check_in,check_out')
            ->get();

        return $this->nights($checkIn, $checkOut)
            ->map(fn (Carbon $night) => $lines
                ->filter(fn (ReservationExtra $line) => $night->gte($line->reservation->check_in)
                    && $night->lt($line->reservation->check_out))
                ->sum('quantity'))
            ->max() ?? 0;
    }

    private function claimedLines(?callable $whereReservation = null): Builder
    {
        return ReservationExtra::query()
            ->where('extra_id', $this->extra->id)
            ->whereHas('reservation', function (Builder $reservation) use ($whereReservation) {
                $reservation->whereIn('status', self::ACTIVE_STATUSES);

                if ($whereReservation) {
                    $whereReservation($reservation);
                }
            });
    }

    /** @return Collection<int, Carbon> */
    private function nights(Carbon $checkIn, Carbon $checkOut): Collection
    {
        return collect(CarbonPeriod::create($checkIn, $checkOut->copy()->subDay())->toArray());
    }
}

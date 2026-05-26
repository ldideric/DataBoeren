<?php

namespace App\Support;

use App\Enums\ReservationStatus;
use App\Models\Extra;
use App\Models\ReservationExtra;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Answers "can we rent N of this extra over these nights?".
 *
 * An extra with a `stock` limit (e.g. 5 firepits) behaves like a small pool of
 * identical units that are checked out for a date range and returned. The
 * constraint is per-night: on no single night within the stay may the units in
 * use exceed `stock`. We therefore compute the *peak* concurrent usage across
 * the requested nights rather than a naive sum over the range — two bookings
 * that both overlap the query window but not each other never compete for a
 * unit, so summing them would reject valid combinations.
 *
 * Extras with `stock = null` are unlimited (e.g. dogs) and always available;
 * the per-booking cap (`max_per_booking`) is enforced separately at validation.
 *
 * @todo When wired into the booking flow, run remaining()/canRent() inside the
 *       same locked transaction as CreateReservation (lockForUpdate on the
 *       overlapping rows) so two concurrent requests can't both claim the last
 *       firepit. The read below is correct but not concurrency-safe on its own.
 */
final readonly class ExtraAvailability
{
    public function __construct(private Extra $extra) {}

    public static function for(Extra $extra): self
    {
        return new self($extra);
    }

    /**
     * Units still rentable on the tightest night of the range.
     * Returns null when the extra has no stock limit (unlimited supply).
     */
    public function remaining(Carbon $checkIn, Carbon $checkOut): ?int
    {
        if ($this->extra->stock === null) {
            return null;
        }

        return max(0, $this->extra->stock - $this->peakUsage($checkIn, $checkOut));
    }

    public function canRent(int $quantity, Carbon $checkIn, Carbon $checkOut): bool
    {
        $remaining = $this->remaining($checkIn, $checkOut);

        return $remaining === null || $quantity <= $remaining;
    }

    /**
     * Highest number of units in use on any single night in [checkIn, checkOut).
     */
    private function peakUsage(Carbon $checkIn, Carbon $checkOut): int
    {
        $overlapping = ReservationExtra::query()
            ->where('extra_id', $this->extra->id)
            ->whereHas('reservation', fn (Builder $query) => $query
                ->whereIn('status', [ReservationStatus::Pending, ReservationStatus::Confirmed])
                ->where('check_in', '<', $checkOut)
                ->where('check_out', '>', $checkIn))
            ->with('reservation:id,check_in,check_out')
            ->get();

        $peak = 0;

        // One pass per night of the requested stay; stays are short, so this
        // is cheap. (For longer horizons, sweep booking boundaries instead.)
        for ($night = $checkIn->copy(); $night->lt($checkOut); $night->addDay()) {
            $usage = $overlapping
                ->filter(fn (ReservationExtra $line) => $night->gte($line->reservation->check_in)
                    && $night->lt($line->reservation->check_out))
                ->sum('quantity');

            $peak = max($peak, $usage);
        }

        return $peak;
    }
}

<?php

namespace App\Pricing\Actions;

use App\Enums\BillingType;
use App\Enums\CouponScope;
use App\Enums\DiscountType;
use App\Models\Coupon;
use App\Models\Extra;
use App\Models\OrderSummary;
use App\Models\Reservation;
use App\Models\Season;
use App\Models\SeasonPeriod;
use RuntimeException;

/**
 * Turns a reservation (+ chosen extras) into an OrderSummary: the frozen,
 * itemised invoice that becomes the amount owed. The returned model is NOT
 * saved — the caller persists it alongside the reservation_extras lines inside
 * one transaction.
 *
 * Pricing assumptions (documented in docs/pricing.md):
 *   - The whole stay is priced in the season that contains the check-in date.
 *   - Discounts apply in order: applied by each strategy.
 *
 * @todo Increment Coupon::uses_count once a coupon is attached in the booking flow.
 */
class CalculatePrice
{
    /**
     * @param  array<array{extra: Extra, quantity: int}>  $extraSelections
     */
    public function calculate(Reservation $reservation, array $extraSelections = []): OrderSummary
    {
        $season = $this->seasonFor($reservation);
        $rate = $reservation->campsite->prices()->where('season_id', $season->id)->firstOrFail();

        $nights = (int) $reservation->check_in->diffInDays($reservation->check_out);

        $accommodation = ($rate->nightly_rate
            + $rate->per_adult_rate * $reservation->num_adults
            + $rate->per_child_rate * $reservation->num_children) * $nights;

        $extrasTotal = collect($extraSelections)
            ->sum(fn (array $line) => self::lineSubtotal($line['extra'], $line['quantity'], $nights));

        [$lastMinuteDiscount, $couponDiscount] = $this->calculateDiscounts($reservation, $accommodation, $extraSelections, $nights);
        $totalDiscount = $lastMinuteDiscount + $couponDiscount;

        $total = max(0, $accommodation - $totalDiscount + $extrasTotal);

        return new OrderSummary([
            'reservation_id' => $reservation->id,
            'season_name' => $season->name,
            'num_nights' => $nights,
            'nightly_rate' => $rate->nightly_rate,
            'per_adult_rate' => $rate->per_adult_rate,
            'per_child_rate' => $rate->per_child_rate,
            'last_minute_applied' => $lastMinuteDiscount > 0,
            'last_minute_discount' => $lastMinuteDiscount > 0 ? round($lastMinuteDiscount, 2) : null,
            'coupon_discount' => $couponDiscount > 0 ? round($couponDiscount, 2) : null,
            'extras_total' => round($extrasTotal, 2),
            'total' => round($total, 2),
        ]);
    }

    /**
     * @param  array<array{extra: Extra, quantity: int}>  $extraSelections
     */
    private function calculateDiscounts(Reservation $reservation, float $accommodation, array $extraSelections, int $nights): array
    {
        $lastMinute = $this->lastMinuteDiscount($reservation, $accommodation);
        $coupon = $this->couponDiscount($reservation, max(0, $accommodation - $lastMinute), $extraSelections, $nights);

        return [$lastMinute, $coupon];
    }

    private function seasonFor(Reservation $reservation): Season
    {
        return SeasonPeriod::query()
            ->where('starts_at', '<=', $reservation->check_in)
            ->where('ends_at', '>=', $reservation->check_in)
            ->first()?->season
            ?? throw new RuntimeException("No season covers check-in date {$reservation->check_in->toDateString()}.");
    }

    public static function lineSubtotal(Extra $extra, int $quantity, int $nights): float
    {
        $units = $extra->billing_type === BillingType::PerNight ? $quantity * $nights : $quantity;

        return round($extra->price * $units, 2);
    }

    private function lastMinuteDiscount(Reservation $reservation, float $baseAmount): float
    {
        $config = config('pricing.last_minute');

        if (! $config['enabled'] || now()->diffInDays($reservation->check_in, false) > $config['threshold_days']) {
            return 0.0;
        }

        return round($baseAmount * $config['discount_percent'] / 100, 2);
    }

    /**
     * @param  array<array{extra: Extra, quantity: int}>  $extraSelections
     */
    private function couponDiscount(Reservation $reservation, float $baseAmount, array $extraSelections, int $nights): float
    {
        $coupon = $reservation->coupon;

        if (! $coupon instanceof Coupon) {
            return 0.0;
        }

        $base = match ($coupon->scope) {
            CouponScope::Accommodation => $baseAmount,
            CouponScope::Extra => collect($extraSelections)
                ->filter(fn (array $line) => $line['extra']->id === $coupon->extra_id)
                ->sum(fn (array $line) => self::lineSubtotal($line['extra'], $line['quantity'], $nights)),
        };

        $discount = match ($coupon->discount_type) {
            DiscountType::Flat => min($coupon->discount_value, $base),
            DiscountType::Percent => $base * $coupon->discount_value / 100,
        };

        return round($discount, 2);
    }
}

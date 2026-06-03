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

class CalculatePrice
{
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
            'last_minute_discount' => $lastMinuteDiscount > 0 ? $lastMinuteDiscount : null,
            'coupon_discount' => $couponDiscount > 0 ? $couponDiscount : null,
            'extras_total' => $extrasTotal,
            'total' => $total,
        ]);
    }

    private function calculateDiscounts(Reservation $reservation, int $accommodation, array $extraSelections, int $nights): array
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

    public static function lineSubtotal(Extra $extra, int $quantity, int $nights): int
    {
        $units = $extra->billing_type === BillingType::PerNight ? $quantity * $nights : $quantity;

        return $extra->price * $units;
    }

    private function lastMinuteDiscount(Reservation $reservation, int $baseAmount): int
    {
        $config = config('pricing.last_minute');

        if (! $config['enabled'] || now()->diffInDays($reservation->check_in) > $config['threshold_days']) {
            return 0;
        }

        return (int) round($baseAmount * $config['discount_percent'] / 100);
    }
    private function couponDiscount(Reservation $reservation, int $baseAmount, array $extraSelections, int $nights): int
    {
        $coupon = $reservation->coupon;

        if (! $coupon instanceof Coupon) {
            return 0;
        }

        $extrasTotal = fn (): int => collect($extraSelections)
            ->sum(fn (array $line) => self::lineSubtotal($line['extra'], $line['quantity'], $nights));

        $base = match ($coupon->scope) {
            CouponScope::Total => $baseAmount + $extrasTotal(),
            CouponScope::Accommodation => $baseAmount,
            CouponScope::Extra => collect($extraSelections)
                ->filter(fn (array $line) => $line['extra']->id === $coupon->extra_id)
                ->sum(fn (array $line) => self::lineSubtotal($line['extra'], $line['quantity'], $nights)),
        };

        return match ($coupon->discount_type) {
            DiscountType::Flat => min((int) round($coupon->discount_value * 100), $base),
            DiscountType::Percent => (int) round($base * $coupon->discount_value / 100),
        };
    }
}

<?php

namespace App\Models;

use App\Enums\BillingType;
use App\Enums\ReservationStatus;
use App\Enums\StockType;
use Database\Factories\ExtraFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property BillingType $billing_type
 * @property float $price
 * @property int|null $stock
 * @property StockType $stock_type
 * @property int|null $max_per_booking
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read Collection<ReservationExtra> $reservationExtras
 */
#[Fillable(['name', 'description', 'billing_type', 'price', 'stock', 'stock_type', 'max_per_booking'])]
class Extra extends Model
{
    /** @use HasFactory<ExtraFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'billing_type' => BillingType::class,
            'stock_type' => StockType::class,
        ];
    }

    public function reservationExtras(): HasMany
    {
        return $this->hasMany(ReservationExtra::class);
    }

    public function reservedQuantityBetween(Carbon $checkIn, Carbon $checkOut): int
    {
        return (int) $this->reservationExtras()
            ->whereHas('reservation', function (Builder $query) use ($checkIn, $checkOut) {
                $query->whereIn('status', [ReservationStatus::Pending, ReservationStatus::Confirmed]);

                if ($this->stock_type === StockType::Rental) {
                    $query->where('check_in', '<', $checkOut)->where('check_out', '>', $checkIn);
                }
            })
            ->sum('quantity');
    }

    public function availableStockBetween(Carbon $checkIn, Carbon $checkOut): ?int
    {
        return $this->stock === null
            ? null
            : max(0, $this->stock - $this->reservedQuantityBetween($checkIn, $checkOut));
    }

    public function maxSelectableBetween(Carbon $checkIn, Carbon $checkOut): ?int
    {
        $caps = array_filter(
            [$this->max_per_booking, $this->availableStockBetween($checkIn, $checkOut)],
            fn (?int $cap) => $cap !== null,
        );

        return $caps === [] ? null : (int) min($caps);
    }
}

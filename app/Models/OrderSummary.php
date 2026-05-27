<?php

namespace App\Models;

use Database\Factories\OrderSummaryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $reservation_id
 * @property string $season_name
 * @property int $num_nights
 * @property int $nightly_rate
 * @property int $per_adult_rate
 * @property int $per_child_rate
 * @property bool $last_minute_applied
 * @property int|null $last_minute_discount
 * @property int|null $coupon_discount
 * @property int|null $extras_total
 * @property int $total
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Reservation $reservation
 */
#[Fillable(['reservation_id', 'season_name', 'num_nights', 'nightly_rate', 'per_adult_rate', 'per_child_rate', 'last_minute_applied', 'last_minute_discount', 'coupon_discount', 'extras_total', 'total'])]
class OrderSummary extends Model
{
    /** @use HasFactory<OrderSummaryFactory> */
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'last_minute_applied' => 'boolean',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}

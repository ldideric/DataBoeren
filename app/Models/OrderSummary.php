<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $reservation_id
 * @property string $season_name
 * @property int $num_nights
 * @property float $nightly_rate
 * @property float $per_person_rate
 * @property bool $last_minute_applied
 * @property float|null $last_minute_discount
 * @property float|null $coupon_discount
 * @property float|null $extras_total
 * @property float $total
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Reservation $reservation
 */
#[Fillable(['reservation_id', 'season_name', 'num_nights', 'nightly_rate', 'per_person_rate', 'last_minute_applied', 'last_minute_discount', 'coupon_discount', 'extras_total', 'total'])]
class OrderSummary extends Model
{
    use HasUuids;

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}

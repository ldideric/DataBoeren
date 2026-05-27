<?php

namespace App\Models;

use Database\Factories\ReservationExtraFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $reservation_id
 * @property string $extra_id
 * @property int $quantity
 * @property float $unit_price
 * @property float $subtotal
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Reservation $reservation
 * @property-read Extra $extra
 */
#[Fillable(['reservation_id', 'extra_id', 'quantity', 'unit_price', 'subtotal'])]
class ReservationExtra extends Model
{
    /** @use HasFactory<ReservationExtraFactory> */
    use HasFactory, HasUuids;

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function extra(): BelongsTo
    {
        return $this->belongsTo(Extra::class);
    }
}

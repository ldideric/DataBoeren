<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $reservation_id
 * @property float $amount
 * @property PaymentStatus $status
 * @property string $method
 * @property Carbon|null $paid_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Reservation $reservation
 */
#[Fillable(['reservation_id', 'amount', 'status', 'method', 'paid_at'])]
class Payment extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}

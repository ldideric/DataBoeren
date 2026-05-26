<?php

namespace App\Models;

use App\Enums\BillingType;
use Database\Factories\ExtraFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
 * @property int|null $max_per_booking
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read Collection<ReservationExtra> $reservationExtras
 */
#[Fillable(['name', 'description', 'billing_type', 'price', 'stock', 'max_per_booking'])]
class Extra extends Model
{
    /** @use HasFactory<ExtraFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'billing_type' => BillingType::class,
        ];
    }

    public function reservationExtras(): HasMany
    {
        return $this->hasMany(ReservationExtra::class);
    }
}

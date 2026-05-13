<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $code
 * @property DiscountType $discount_type
 * @property float $discount_value
 * @property Carbon|null $expires_at
 * @property int|null $max_uses
 * @property int $uses_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Collection<Reservation> $reservations
 */
#[Fillable(['code', 'discount_type', 'discount_value', 'expires_at', 'max_uses', 'uses_count'])]
class Coupon extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}

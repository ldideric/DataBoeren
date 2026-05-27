<?php

namespace App\Models;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use Database\Factories\CouponFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $title
 * @property string $code
 * @property CouponScope $scope
 * @property string|null $extra_id
 * @property DiscountType $discount_type
 * @property float $discount_value
 * @property Carbon|null $expires_at
 * @property int|null $max_uses
 * @property int $uses_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<Reservation> $reservations
 * @property-read Extra|null $extra
 */
#[Fillable(['title', 'code', 'scope', 'extra_id', 'discount_type', 'discount_value', 'expires_at', 'max_uses', 'uses_count'])]
class Coupon extends Model
{
    /** @use HasFactory<CouponFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'scope' => CouponScope::class,
            'discount_type' => DiscountType::class,
            'expires_at' => 'date',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function extra(): BelongsTo
    {
        return $this->belongsTo(Extra::class);
    }
}

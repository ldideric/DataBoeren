<?php

namespace App\Models;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $customer_id
 * @property string $campsite_id
 * @property string|null $booked_by_user_id
 * @property string|null $coupon_id
 * @property ReservationSource $source
 * @property Carbon $check_in
 * @property Carbon $check_out
 * @property int $num_people
 * @property int $num_vehicles
 * @property ReservationStatus $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $customer
 * @property-read User|null $bookedBy
 * @property-read Campsite $campsite
 * @property-read Coupon|null $coupon
 * @property-read OrderSummary|null $orderSummary
 * @property-read Collection<ReservationExtra> $extras
 * @property-read Collection<Payment> $payments
 */
#[Fillable(['customer_id', 'campsite_id', 'booked_by_user_id', 'coupon_id', 'source', 'check_in', 'check_out', 'num_people', 'num_vehicles', 'status'])]
class Reservation extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'source' => ReservationSource::class,
            'status' => ReservationStatus::class,
            'check_in' => 'date',
            'check_out' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function bookedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by_user_id');
    }

    public function campsite(): BelongsTo
    {
        return $this->belongsTo(Campsite::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function orderSummary(): HasOne
    {
        return $this->hasOne(OrderSummary::class);
    }

    public function extras(): HasMany
    {
        return $this->hasMany(ReservationExtra::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}

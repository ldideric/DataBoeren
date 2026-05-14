<?php

namespace App\Models;

use App\Enums\CampsiteType;
use Database\Factories\CampsiteFactory;
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
 * @property CampsiteType $type
 * @property bool $has_electricity
 * @property int $max_people
 * @property int $max_vehicles
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read Collection<CampsitePrice> $prices
 * @property-read Collection<Reservation> $reservations
 */
#[Fillable(['name', 'type', 'has_electricity', 'max_people', 'max_vehicles', 'notes'])]
class Campsite extends Model
{
    /** @use HasFactory<CampsiteFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'type' => CampsiteType::class,
        ];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(CampsitePrice::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}

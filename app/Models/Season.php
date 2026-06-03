<?php

namespace App\Models;

use Database\Factories\SeasonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<SeasonPeriod> $periods
 * @property-read Collection<CampsitePrice> $campsitePrices
 */
#[Fillable(['name'])]
class Season extends Model
{
    /** @use HasFactory<SeasonFactory> */
    use HasFactory;
    use HasUuids;

    public function periods(): HasMany
    {
        return $this->hasMany(SeasonPeriod::class);
    }

    public function campsitePrices(): HasMany
    {
        return $this->hasMany(CampsitePrice::class);
    }
}

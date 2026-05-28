<?php

namespace App\Models;

use Database\Factories\CampsitePriceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $campsite_id
 * @property string $season_id
 * @property int $nightly_rate
 * @property int $per_adult_rate
 * @property int $per_child_rate
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Campsite $campsite
 * @property-read Season $season
 */
#[Fillable(['campsite_id', 'season_id', 'nightly_rate', 'per_adult_rate', 'per_child_rate'])]
class CampsitePrice extends Model
{
    /** @use HasFactory<CampsitePriceFactory> */
    use HasFactory;
    use HasUuids;

    public function campsite(): BelongsTo
    {
        return $this->belongsTo(Campsite::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}

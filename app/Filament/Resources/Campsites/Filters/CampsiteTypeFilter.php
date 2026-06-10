<?php

namespace App\Filament\Resources\Campsites\Filters;

use App\Enums\CampsiteType;
use Filament\Tables\Filters\SelectFilter;

class CampsiteTypeFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'type');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('campsite.fields.type'))
            ->options(CampsiteType::class);
    }
}

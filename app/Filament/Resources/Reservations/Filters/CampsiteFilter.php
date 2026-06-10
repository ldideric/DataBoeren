<?php

namespace App\Filament\Resources\Reservations\Filters;

use Filament\Tables\Filters\SelectFilter;

class CampsiteFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'campsite');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('reservation.filters.campsite'))
            ->relationship('campsite', 'name')
            ->searchable()
            ->preload();
    }
}

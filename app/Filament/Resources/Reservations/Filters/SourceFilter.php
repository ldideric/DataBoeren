<?php

namespace App\Filament\Resources\Reservations\Filters;

use App\Enums\ReservationSource;
use Filament\Tables\Filters\SelectFilter;

class SourceFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'source');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(ReservationSource::class);
    }
}

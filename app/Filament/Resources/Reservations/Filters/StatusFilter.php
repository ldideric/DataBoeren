<?php

namespace App\Filament\Resources\Reservations\Filters;

use App\Enums\ReservationStatus;
use Filament\Tables\Filters\SelectFilter;

class StatusFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'status');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('common.status'))
            ->options(ReservationStatus::class);
    }
}

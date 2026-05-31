<?php

namespace App\Filament\Resources\Reservations\Filters;

use Filament\Tables\Filters\TernaryFilter;

class BookedByStaffFilter extends TernaryFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'booked_by_user_id');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Booked by staff')
            ->nullable();
    }
}

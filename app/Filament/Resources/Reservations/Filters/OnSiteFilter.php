<?php

namespace App\Filament\Resources\Reservations\Filters;

use App\Enums\ReservationStatus;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class OnSiteFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'on_site');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Currently on site')
            ->toggle()
            ->query(
                fn (Builder $query) => $query
                ->where('check_in', '<=', today())
                ->where('check_out', '>=', today())
                ->where('status', ReservationStatus::Confirmed)
            );
    }
}

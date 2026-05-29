<?php

namespace App\Filament\Resources\Reservations\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ArrivalPeriodFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'check_in');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Arrival period')
            ->schema([
                DatePicker::make('from')->label('From'),
                DatePicker::make('until')->label('Until'),
            ])
            ->query(
                fn (Builder $query, array $data) => $query
                ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('check_in', '>=', $date))
                ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('check_in', '<=', $date))
            )
            ->indicateUsing(
                fn (array $data) => collect()
                ->when($data['from'] ?? null, fn ($collection) => $collection->push('Arriving from: '.$data['from']))
                ->when($data['until'] ?? null, fn ($collection) => $collection->push('Arriving until: '.$data['until']))
                ->toArray()
            );
    }
}

<?php

namespace App\Filament\Resources\Reservations\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
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
            ->label(__('reservation.filters.arrival_period'))
            ->columnSpanFull()
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('from')->label(__('common.from')),
                        DatePicker::make('until')->label(__('common.until')),
                    ])
                    ->columns(2),
            ])
            ->query(
                fn (Builder $query, array $data) => $query
                ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('check_in', '>=', $date))
                ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('check_in', '<=', $date))
            )
            ->indicateUsing(
                fn (array $data) => collect()
                ->when($data['from'] ?? null, fn ($collection) => $collection->push(__('reservation.filters.arriving_from', ['date' => $data['from']])))
                ->when($data['until'] ?? null, fn ($collection) => $collection->push(__('reservation.filters.arriving_until', ['date' => $data['until']])))
                ->toArray()
            );
    }
}

<?php

namespace App\Filament\Resources\Reservations\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class DeparturePeriodFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'check_out');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Departure period')
            ->columnSpanFull()
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->columns(2),
            ])
            ->query(
                fn (Builder $query, array $data) => $query
                ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('check_out', '>=', $date))
                ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('check_out', '<=', $date))
            )
            ->indicateUsing(
                fn (array $data) => collect()
                ->when($data['from'] ?? null, fn ($collection) => $collection->push('Departing from: '.$data['from']))
                ->when($data['until'] ?? null, fn ($collection) => $collection->push('Departing until: '.$data['until']))
                ->toArray()
            );
    }
}

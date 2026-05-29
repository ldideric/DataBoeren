<?php

namespace App\Filament\Resources\Payments\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class PaidAtFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'paid_at');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Payment date')
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
                ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('paid_at', '>=', $date))
                ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('paid_at', '<=', $date))
            )
            ->indicateUsing(
                fn (array $data) => collect()
                ->when($data['from'] ?? null, fn ($collection) => $collection->push('Paid from: '.$data['from']))
                ->when($data['until'] ?? null, fn ($collection) => $collection->push('Paid until: '.$data['until']))
                ->toArray()
            );
    }
}

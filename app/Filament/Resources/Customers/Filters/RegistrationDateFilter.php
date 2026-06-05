<?php

namespace App\Filament\Resources\Customers\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class RegistrationDateFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'registered');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('customer.filters.registration_date'))
            ->schema([
                DatePicker::make('from')->label(__('common.from')),
                DatePicker::make('until')->label(__('common.until')),
            ])
            ->query(
                fn (Builder $query, array $data) => $query
                ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            )
            ->indicateUsing(
                fn (array $data) => collect()
                ->when($data['from'] ?? null, fn ($collection) => $collection->push(__('customer.filters.registered_from', ['date' => $data['from']])))
                ->when($data['until'] ?? null, fn ($collection) => $collection->push(__('customer.filters.registered_until', ['date' => $data['until']])))
                ->toArray()
            );
    }
}

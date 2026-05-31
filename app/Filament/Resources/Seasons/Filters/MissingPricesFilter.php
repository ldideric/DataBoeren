<?php

namespace App\Filament\Resources\Seasons\Filters;

use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class MissingPricesFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'missing_prices');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Missing campsite prices')
            ->toggle()
            ->query(fn (Builder $query, array $data) => $query
                ->when($data['isActive'] ?? false, fn ($q) => $q->whereDoesntHave('campsitePrices')));
    }
}

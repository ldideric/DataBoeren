<?php

namespace App\Filament\Resources\Extras\Filters;

use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class LowStockFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'low_stock');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('extra.filters.low_stock'))
            ->toggle()
            ->query(fn (Builder $query, array $data) => $query
                ->when($data['isActive'] ?? false, fn ($q) => $q->whereNotNull('stock')->where('stock', '<=', 3)));
    }
}

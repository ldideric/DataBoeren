<?php

namespace App\Filament\Resources\Extras\Filters;

use App\Enums\StockType;
use Filament\Tables\Filters\SelectFilter;

class StockTypeFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'stock_type');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(StockType::class);
    }
}

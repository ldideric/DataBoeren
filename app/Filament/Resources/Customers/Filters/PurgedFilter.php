<?php

namespace App\Filament\Resources\Customers\Filters;

use Filament\Tables\Filters\TernaryFilter;

class PurgedFilter extends TernaryFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'purged_at');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('customer.filters.gdpr_purged'))
            ->nullable();
    }
}

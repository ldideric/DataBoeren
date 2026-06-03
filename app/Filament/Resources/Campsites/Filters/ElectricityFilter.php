<?php

namespace App\Filament\Resources\Campsites\Filters;

use Filament\Tables\Filters\TernaryFilter;

class ElectricityFilter extends TernaryFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'has_electricity');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('campsite.filters.electricity'));
    }
}

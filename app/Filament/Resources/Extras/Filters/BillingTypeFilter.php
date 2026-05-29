<?php

namespace App\Filament\Resources\Extras\Filters;

use App\Enums\BillingType;
use Filament\Tables\Filters\SelectFilter;

class BillingTypeFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'billing_type');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(BillingType::class);
    }
}

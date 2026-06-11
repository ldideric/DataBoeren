<?php

namespace App\Filament\Resources\Payments\Filters;

use App\Enums\PaymentMethod;
use Filament\Tables\Filters\SelectFilter;

class MethodFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'method');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('payment.filters.method'))
            ->options(PaymentMethod::class);
    }
}

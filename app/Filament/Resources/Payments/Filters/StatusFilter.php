<?php

namespace App\Filament\Resources\Payments\Filters;

use App\Enums\PaymentStatus;
use Filament\Tables\Filters\SelectFilter;

class StatusFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'status');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(PaymentStatus::class);
    }
}

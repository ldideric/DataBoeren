<?php

namespace App\Filament\Resources\Customers\Filters;

use Filament\Tables\Filters\TernaryFilter;

class EmailVerifiedFilter extends TernaryFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'email_verified_at');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Email verified')
            ->nullable();
    }
}

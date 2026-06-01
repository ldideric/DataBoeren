<?php

namespace App\Filament\Resources\Coupons\Filters;

use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class ExpiredFilter extends TernaryFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'expired');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Expiry status')
            ->trueLabel('Expired')
            ->falseLabel('Active')
            ->queries(
                true: fn (Builder $query) => $query->where('expires_at', '<', now()),
                false: fn (Builder $query) => $query->where(
                    fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now())
                ),
            );
    }
}

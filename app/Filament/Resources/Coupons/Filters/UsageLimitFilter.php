<?php

namespace App\Filament\Resources\Coupons\Filters;

use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class UsageLimitFilter extends TernaryFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'has_usage_limit');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('coupon.filters.usage_limit'))
            ->trueLabel(__('coupon.filters.has_limit'))
            ->falseLabel(__('coupon.filters.unlimited'))
            ->queries(
                true: fn (Builder $query) => $query->whereNotNull('max_uses'),
                false: fn (Builder $query) => $query->whereNull('max_uses'),
            );
    }
}

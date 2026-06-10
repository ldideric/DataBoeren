<?php

namespace App\Filament\Resources\Coupons\Filters;

use App\Enums\DiscountType;
use Filament\Tables\Filters\SelectFilter;

class DiscountTypeFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'discount_type');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('coupon.fields.discount_type'))
            ->options(DiscountType::class);
    }
}

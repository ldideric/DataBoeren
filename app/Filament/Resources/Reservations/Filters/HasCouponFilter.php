<?php

namespace App\Filament\Resources\Reservations\Filters;

use Filament\Tables\Filters\TernaryFilter;

class HasCouponFilter extends TernaryFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'coupon_id');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('reservation.filters.has_coupon'))
            ->nullable();
    }
}

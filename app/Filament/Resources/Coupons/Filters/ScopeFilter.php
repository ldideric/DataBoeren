<?php

namespace App\Filament\Resources\Coupons\Filters;

use App\Enums\CouponScope;
use Filament\Tables\Filters\SelectFilter;

class ScopeFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'scope');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(CouponScope::class);
    }
}

<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CouponScope: string implements HasColor, HasLabel
{
    case Total = 'total';
    case Accommodation = 'accommodation';
    case Extra = 'extra';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Total         => __('enums.coupon_scope.total'),
            self::Accommodation => __('enums.coupon_scope.accommodation'),
            self::Extra         => __('enums.coupon_scope.extra'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Total         => 'success',
            self::Accommodation => 'primary',
            self::Extra         => 'info',
        };
    }
}

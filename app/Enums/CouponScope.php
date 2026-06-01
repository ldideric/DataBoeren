<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CouponScope: string implements HasColor, HasLabel
{
    case Accommodation = 'accommodation';
    case Extra = 'extra';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Accommodation => 'Accommodation',
            self::Extra         => 'Extra',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Accommodation => 'primary',
            self::Extra         => 'info',
        };
    }
}

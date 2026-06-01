<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DiscountType: string implements HasColor, HasLabel
{
    case Flat = 'flat';
    case Percent = 'percent';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Flat    => 'Flat',
            self::Percent => 'Percent',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Flat    => 'success',
            self::Percent => 'warning',
        };
    }
}

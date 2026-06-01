<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StockType: string implements HasColor, HasLabel
{
    case Rental = 'rental';
    case Consumable = 'consumable';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Rental     => 'Rental',
            self::Consumable => 'Consumable',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Rental     => 'warning',
            self::Consumable => 'gray',
        };
    }
}

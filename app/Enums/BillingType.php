<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BillingType: string implements HasColor, HasLabel
{
    case OneTime = 'one_time';
    case PerNight = 'per_night';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OneTime  => 'One Time',
            self::PerNight => 'Per Night',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::OneTime  => 'info',
            self::PerNight => 'primary',
        };
    }
}

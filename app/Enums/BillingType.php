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
            self::OneTime  => __('enums.billing_type.one_time'),
            self::PerNight => __('enums.billing_type.per_night'),
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

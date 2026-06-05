<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ReservationSource: string implements HasColor, HasLabel
{
    case Online = 'online';
    case Employee = 'employee';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Online   => __('enums.reservation_source.online'),
            self::Employee => __('enums.reservation_source.employee'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Online   => 'info',
            self::Employee => 'gray',
        };
    }
}

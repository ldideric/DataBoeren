<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasLabel
{
    case Customer = 'customer';
    case Employee = 'employee';
    case Admin = 'admin';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Customer => __('enums.user_role.customer'),
            self::Employee => __('enums.user_role.employee'),
            self::Admin    => __('enums.user_role.admin'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Customer => 'gray',
            self::Employee => 'info',
            self::Admin    => 'danger',
        };
    }
}

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
            self::Customer => 'Customer',
            self::Employee => 'Employee',
            self::Admin    => 'Admin',
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

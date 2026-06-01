<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasColor, HasLabel
{
    case Stripe = 'stripe';
    case Cash = 'cash';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Stripe => 'Stripe',
            self::Cash   => 'Cash',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Stripe => 'info',
            self::Cash   => 'success',
        };
    }
}

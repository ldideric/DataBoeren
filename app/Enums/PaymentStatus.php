<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending   => __('enums.payment_status.pending'),
            self::Paid      => __('enums.payment_status.paid'),
            self::Cancelled => __('enums.payment_status.cancelled'),
            self::Refunded  => __('enums.payment_status.refunded'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending   => 'warning',
            self::Paid      => 'success',
            self::Cancelled => 'danger',
            self::Refunded  => 'info',
        };
    }
}

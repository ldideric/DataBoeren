<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CheckoutMethod: string implements HasLabel
{
    case CashPaid = 'cash_paid';
    case CardNow = 'card_now';
    case SendLink = 'send_link';
    case PayOnArrival = 'pay_on_arrival';

    public function getLabel(): string
    {
        return match ($this) {
            self::CashPaid     => __('enums.checkout_method.label.cash_paid'),
            self::CardNow      => __('enums.checkout_method.label.card_now'),
            self::SendLink     => __('enums.checkout_method.label.send_link'),
            self::PayOnArrival => __('enums.checkout_method.label.pay_on_arrival'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CashPaid     => __('enums.checkout_method.description.cash_paid'),
            self::CardNow      => __('enums.checkout_method.description.card_now'),
            self::SendLink     => __('enums.checkout_method.description.send_link'),
            self::PayOnArrival => __('enums.checkout_method.description.pay_on_arrival'),
        };
    }
}

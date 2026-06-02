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
            self::CashPaid     => 'Cash — paid now',
            self::CardNow      => 'Card now (Stripe)',
            self::SendLink     => 'Send payment link',
            self::PayOnArrival => 'Pay on arrival',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CashPaid     => 'Guest pays cash at the desk. Booking is confirmed and the payment is recorded as paid.',
            self::CardNow      => 'Take a card payment now — you are sent to the Stripe payment page to complete it.',
            self::SendLink     => 'Email the customer a payment link. The booking stays pending until they pay online.',
            self::PayOnArrival => 'Reserve now, settle on arrival. A pending cash payment is recorded.',
        };
    }
}

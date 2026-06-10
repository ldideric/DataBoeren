<?php

return [
    'reservation_status' => [
        'pending'   => 'Pending',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
    ],

    'payment_status' => [
        'pending'   => 'Pending',
        'paid'      => 'Paid',
        'cancelled' => 'Cancelled',
        'refunded'  => 'Refunded',
    ],

    'payment_method' => [
        'stripe' => 'Stripe',
        'cash'   => 'Cash',
    ],

    'reservation_source' => [
        'online'   => 'Online',
        'employee' => 'Employee',
    ],

    'user_role' => [
        'customer' => 'Customer',
        'employee' => 'Employee',
        'admin'    => 'Admin',
    ],

    'mail_event' => [
        'queued'     => 'Queued',
        'processing' => 'Processing',
        'sending'    => 'Sending',
        'sent'       => 'Sent',
        'processed'  => 'Processed',
        'retrying'   => 'Retrying',
        'failed'     => 'Failed',
    ],

    'discount_type' => [
        'flat'    => 'Flat',
        'percent' => 'Percent',
    ],

    'billing_type' => [
        'one_time'  => 'One time',
        'per_night' => 'Per night',
    ],

    'stock_type' => [
        'rental'     => 'Rental',
        'consumable' => 'Consumable',
    ],

    'coupon_scope' => [
        'total'         => 'Total price',
        'accommodation' => 'Accommodation',
        'extra'         => 'Extra',
        'all_extras'    => 'All extras',
    ],

    'checkout_method' => [
        'label' => [
            'cash_paid'      => 'Cash — paid now',
            'card_now'       => 'Card now (Stripe)',
            'send_link'      => 'Send payment link',
            'pay_on_arrival' => 'Pay on arrival',
        ],
        'description' => [
            'cash_paid'      => 'Guest pays cash at the desk. Booking is confirmed and the payment is recorded as paid.',
            'card_now'       => 'Take a card payment now — you are sent to the Stripe payment page to complete it.',
            'send_link'      => 'Email the customer a payment link. The booking stays pending until they pay online.',
            'pay_on_arrival' => 'Reserve now, settle on arrival. A pending cash payment is recorded.',
        ],
    ],
];

<?php

return [
    'fields' => [
        'customer'          => 'Customer',
        'check_in'          => 'Check-in',
        'amount'            => 'Amount',
        'method'            => 'Method',
        'paid_at'           => 'Paid at',
        'stripe_session_id' => 'Stripe session ID',
        'reservation'       => 'Reservation',
    ],

    'filters' => [
        'method'       => 'Method',
        'payment_date' => 'Payment date',
        'paid_from'    => 'Paid from: :date',
        'paid_until'   => 'Paid until: :date',
    ],
];

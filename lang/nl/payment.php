<?php

return [
    'fields' => [
        'customer'          => 'Klant',
        'check_in'          => 'Inchecken',
        'amount'            => 'Bedrag',
        'method'            => 'Methode',
        'paid_at'           => 'Betaald op',
        'stripe_session_id' => 'Stripe-sessie-ID',
        'reservation'       => 'Reservering',
    ],

    'filters' => [
        'method'       => 'Methode',
        'payment_date' => 'Betaaldatum',
        'paid_from'    => 'Betaald vanaf: :date',
        'paid_until'   => 'Betaald tot: :date',
    ],
];

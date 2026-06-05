<?php

return [
    'fields' => [
        'email_address'     => 'Email address',
        'email_verified_at' => 'Email verified at',
        'verified'          => 'Verified',
        'purged'            => 'Purged',
        'purged_at'         => 'Purged at',
        'stripe_id'         => 'Stripe ID',
        'pm_type'           => 'Payment method type',
        'pm_last_four'      => 'Card last four',
        'trial_ends_at'     => 'Trial ends at',
    ],

    'filters' => [
        'email_verified'    => 'Email verified',
        'gdpr_purged'       => 'GDPR purged',
        'registration_date' => 'Registration date',
        'registered_from'   => 'Registered from: :date',
        'registered_until'  => 'Registered until: :date',
    ],

    'placeholders' => [
        'no_phone' => 'None',
    ],

    'reservations' => [
        'campsite'  => 'Campsite',
        'check_in'  => 'Check-in',
        'check_out' => 'Check-out',
        'total'     => 'Total',
    ],
];

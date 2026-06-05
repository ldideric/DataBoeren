<?php

return [
    'fields' => [
        'email_address'     => 'E-mailadres',
        'email_verified_at' => 'E-mail geverifieerd op',
        'verified'          => 'Geverifieerd',
        'purged'            => 'Gewist',
        'purged_at'         => 'Gewist op',
        'stripe_id'         => 'Stripe-ID',
        'pm_type'           => 'Type betaalmethode',
        'pm_last_four'      => 'Laatste 4 cijfers kaart',
        'trial_ends_at'     => 'Proefperiode eindigt op',
    ],

    'filters' => [
        'email_verified'    => 'E-mail geverifieerd',
        'gdpr_purged'       => 'AVG gewist',
        'registration_date' => 'Registratiedatum',
        'registered_from'   => 'Geregistreerd vanaf: :date',
        'registered_until'  => 'Geregistreerd tot: :date',
    ],

    'placeholders' => [
        'no_phone' => 'Geen',
    ],

    'reservations' => [
        'campsite'  => 'Kampeerplaats',
        'check_in'  => 'Inchecken',
        'check_out' => 'Uitchecken',
        'total'     => 'Totaal',
    ],
];

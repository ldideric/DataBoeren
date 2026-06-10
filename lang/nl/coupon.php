<?php

return [
    'discount_on' => ':value op :target',

    'fields' => [
        'title'         => 'Titel',
        'code'          => 'Code',
        'scope'         => 'Bereik',
        'extra'         => 'Extra',
        'discount_type' => 'Kortingstype',
        'discount_value' => 'Korting',
        'discount'      => 'Korting',
        'expires_at'    => 'Vervaldatum',
        'max_uses'      => 'Max. gebruik',
        'uses'          => 'Gebruik',
        'uses_count'    => 'Gebruik',
    ],

    'sections' => [
        'basic'      => 'Basisgegevens',
        'scope'      => 'Bereik van de kortingsbon',
        'discount'   => 'Kortingsdetails',
        'additional' => 'Aanvullende informatie',
    ],

    'filters' => [
        'expiry_status' => 'Vervalstatus',
        'expired'       => 'Verlopen',
        'active'        => 'Actief',
        'usage_limit'   => 'Gebruikslimiet',
        'has_limit'     => 'Heeft limiet',
        'unlimited'     => 'Onbeperkt',
    ],

    'placeholders' => [
        'no_expiry' => 'Geen vervaldatum',
    ],

    'reservations' => [
        'customer'  => 'Klant',
        'check_in'  => 'Inchecken',
        'check_out' => 'Uitchecken',
        'total'     => 'Totaal',
    ],
];

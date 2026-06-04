<?php

return [
    'fields' => [
        'type'           => 'Type',
        'has_electricity' => 'Elektriciteit',
        'max_people'     => 'Max. personen',
    ],

    'suffix' => [
        'people'   => ' pers.',
    ],

    'filters' => [
        'electricity'            => 'Elektriciteit',
        'min_capacity'           => 'Min. capaciteit',
        'min_people'             => 'Min. personen',
        'min_capacity_indicator' => 'Min. capaciteit: :count',
    ],

    'prices' => [
        'season'         => 'Seizoen',
        'nightly_rate'   => 'Tarief per nacht',
        'per_adult_rate' => 'Tarief per volwassene',
        'per_child_rate' => 'Tarief per kind',
    ],

    'reservations' => [
        'customer'  => 'Klant',
        'check_in'  => 'Inchecken',
        'check_out' => 'Uitchecken',
        'total'     => 'Totaal',
    ],
];

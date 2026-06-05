<?php

return [
    'fields' => [
        'type'           => 'Type',
        'has_electricity' => 'Electricity',
        'max_people'     => 'Max. people',
    ],

    'suffix' => [
        'people'   => ' ppl.',
    ],

    'filters' => [
        'electricity'            => 'Electricity',
        'min_capacity'           => 'Min. capacity',
        'min_people'             => 'Min. people',
        'min_capacity_indicator' => 'Min. capacity: :count',
    ],

    'prices' => [
        'season'         => 'Season',
        'nightly_rate'   => 'Nightly rate',
        'per_adult_rate' => 'Rate per adult',
        'per_child_rate' => 'Rate per child',
    ],

    'reservations' => [
        'customer'  => 'Customer',
        'check_in'  => 'Check-in',
        'check_out' => 'Check-out',
        'total'     => 'Total',
    ],
];

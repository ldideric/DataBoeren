<?php

return [
    'fields' => [
        'email_address'     => 'Email address',
        'email_verified_at' => 'Email verified at',
    ],

    'filters' => [
        'role' => 'Role',
    ],

    'relations' => [
        'booked_reservations' => 'Booked reservations',
    ],
];

<?php

return [
    'fields' => [
        'email_address'     => 'E-mailadres',
        'email_verified_at' => 'E-mail geverifieerd op',
    ],

    'filters' => [
        'role' => 'Rol',
    ],

    'relations' => [
        'booked_reservations' => 'Geboekte reserveringen',
    ],
];

<?php

return [
    'fields' => [
        'description'     => 'Beschrijving',
        'billing_type'    => 'Facturatietype',
        'price'           => 'Prijs',
        'stock_type'      => 'Voorraadtype',
        'stock'           => 'Voorraad',
        'max_per_booking' => 'Max. per boeking',
    ],

    'hints' => [
        'price' => 'Opslaan in centen, bijv. 500 = € 5,00',
        'stock' => 'Laat leeg voor onbeperkt',
    ],

    'placeholders' => [
        'no_description' => 'Geen',
        'unlimited'      => 'Onbeperkt',
    ],

    'filters' => [
        'billing_type' => 'Facturatietype',
        'stock_type'   => 'Voorraadtype',
        'low_stock'    => 'Lage voorraad (≤ 3)',
    ],
];

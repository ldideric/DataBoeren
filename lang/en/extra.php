<?php

return [
    'fields' => [
        'description'     => 'Description',
        'billing_type'    => 'Billing type',
        'price'           => 'Price',
        'stock_type'      => 'Stock type',
        'stock'           => 'Stock',
        'max_per_booking' => 'Max. per booking',
    ],

    'hints' => [
        'price' => 'Store as cents, e.g. 500 = €5.00',
        'stock' => 'Leave empty for unlimited',
    ],

    'placeholders' => [
        'no_description' => 'None',
        'unlimited'      => 'Unlimited',
    ],

    'filters' => [
        'billing_type' => 'Billing type',
        'stock_type'   => 'Stock type',
        'low_stock'    => 'Low stock (≤ 3)',
    ],
];

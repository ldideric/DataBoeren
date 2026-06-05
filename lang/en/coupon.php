<?php

return [
    'discount_on' => ':value on :target',

    'fields' => [
        'title'         => 'Title',
        'code'          => 'Code',
        'scope'         => 'Scope',
        'extra'         => 'Extra',
        'discount_type' => 'Discount type',
        'discount_value' => 'Discount',
        'discount'      => 'Discount',
        'expires_at'    => 'Expiry date',
        'max_uses'      => 'Max. uses',
        'uses'          => 'Uses',
        'uses_count'    => 'Uses',
    ],

    'sections' => [
        'basic'      => 'Basic information',
        'scope'      => 'Scope of the coupon',
        'discount'   => 'Discount details',
        'additional' => 'Additional information',
    ],

    'filters' => [
        'expiry_status' => 'Expiry status',
        'expired'       => 'Expired',
        'active'        => 'Active',
        'usage_limit'   => 'Usage limit',
        'has_limit'     => 'Has limit',
        'unlimited'     => 'Unlimited',
    ],

    'placeholders' => [
        'no_expiry' => 'No expiry',
    ],

    'reservations' => [
        'customer'  => 'Customer',
        'check_in'  => 'Check-in',
        'check_out' => 'Check-out',
        'total'     => 'Total',
    ],
];

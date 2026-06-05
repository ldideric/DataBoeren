<?php

return [
    'page' => [
        'title'   => 'New booking',
        'created' => 'Booking created successfully',
        'submit'  => 'Create booking',
    ],

    'steps' => [
        'customer' => 'Customer',
        'stay'     => 'Stay',
        'extras'   => 'Extras',
        'summary'  => 'Summary & payment',
    ],

    'fields' => [
        'existing_customer' => 'Existing customer',
        'customer'          => 'Customer',
        'campsite'          => 'Campsite',
        'check_in'          => 'Check-in',
        'check_out'         => 'Check-out',
        'adults'            => 'Adults',
        'children'          => 'Children',
        'coupon'            => 'Coupon',
        'coupon_helper'     => 'Only currently valid coupons are listed.',
        'payment'           => 'Payment',
        'extras'            => 'Extras',
        'add_extra'         => 'Add extra',
        'extra'             => 'Extra',
        'quantity'          => 'Quantity',
    ],

    'summary' => [
        'heading'     => 'Price overview',
        'empty'       => 'Fill in the stay details to calculate the price.',
        'stay'        => 'Stay (:count :unit)',
        'night'       => 'night',
        'nights'      => 'nights',
        'last_minute' => 'Last-minute discount',
        'coupon'      => 'Coupon',
        'extras'      => 'Extras',
        'total'       => 'Total',
    ],

    'errors' => [
        'campsite_unavailable' => 'This spot is no longer available for these dates.',
        'coupon_invalid'       => 'This coupon is expired or has reached its usage limit.',
        'no_pricing'           => 'No pricing set for these dates.',
    ],
];

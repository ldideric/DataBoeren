<?php

return [
    'fields' => [
        'customer'            => 'Customer',
        'campsite'            => 'Campsite',
        'check_in'            => 'Check-in',
        'check_out'           => 'Check-out',
        'num_adults'          => 'Adults',
        'num_children'        => 'Children',
        'num_vehicles'        => 'Vehicles',
        'guests'              => 'Guests',
        'guests_summary'      => ':adults adult(s), :children child(ren)',
        'coupon'              => 'Coupon',
        'source'              => 'Source',
        'cancellation_reason' => 'Cancellation reason',
        'total'               => 'Total',
    ],

    'sections' => [
        'reservation'   => 'Reservation',
        'order_summary' => 'Order summary',
    ],

    'order_summary' => [
        'season_name'          => 'Season',
        'num_nights'           => 'Nights',
        'nightly_rate'         => 'Nightly rate',
        'per_adult_rate'       => 'Rate per adult',
        'per_child_rate'       => 'Rate per child',
        'last_minute_discount' => 'Last-minute discount',
        'coupon_discount'      => 'Coupon discount',
        'extras_total'         => 'Extras total',
        'total'                => 'Total',
    ],

    'filters' => [
        'campsite'         => 'Campsite',
        'arrival_period'   => 'Arrival period',
        'departure_period' => 'Departure period',
        'on_site'          => 'Currently on site',
        'has_coupon'       => 'Has coupon',
        'booked_by_staff'  => 'Booked by staff',
        'source'           => 'Source',
        'arriving_from'    => 'Arriving from: :date',
        'arriving_until'   => 'Arriving until: :date',
        'departing_from'   => 'Departing from: :date',
        'departing_until'  => 'Departing until: :date',
    ],

    'actions' => [
        'accept' => [
            'label'             => 'Accept',
            'modal_heading'     => 'Accept pending reservation',
            'modal_description' => 'Confirm this reservation and mark any pending on-site payment as paid. The customer receives a confirmation email.',
            'success'           => 'Reservation accepted',
        ],
        'cancel' => [
            'label'   => 'Cancel',
            'reason'  => 'Reason',
            'success' => 'Reservation cancelled',
        ],
        'resend_confirmation' => [
            'label'   => 'Resend confirmation',
            'success' => 'Confirmation email re-sent',
        ],
        'send_login_link' => [
            'label'             => 'Send login link',
            'modal_heading'     => 'Send login link',
            'modal_description' => 'Email a sign-in link to :email so they can view or cancel their bookings.',
            'success'           => 'Login link sent',
        ],
    ],

    'extras' => [
        'extra'      => 'Extra',
        'quantity'   => 'Quantity',
        'unit_price' => 'Unit price',
        'subtotal'   => 'Subtotal',
    ],

    'payments' => [
        'amount'            => 'Amount',
        'method'            => 'Method',
        'paid_at'           => 'Paid at',
        'stripe_session_id' => 'Stripe session ID',
    ],
];

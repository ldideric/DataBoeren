<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Last-minute discount
    |--------------------------------------------------------------------------
    |
    | Applied automatically to the accommodation subtotal when a booking is
    | made within `threshold_days` of check-in. There is no admin UI / table
    | for this — it is a single global rule, so it lives in config. The
    | resolved amount is snapshotted onto order_summaries.last_minute_discount
    | at booking time, so changing these values never alters past bookings.
    |
    */
    'last_minute' => [
        'enabled' => true,
        'threshold_days' => 7,
        'discount_percent' => 10,
    ],
];

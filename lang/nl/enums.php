<?php

return [
    'reservation_status' => [
        'pending'   => 'In afwachting',
        'confirmed' => 'Bevestigd',
        'cancelled' => 'Geannuleerd',
    ],

    'payment_status' => [
        'pending'   => 'In afwachting',
        'paid'      => 'Betaald',
        'cancelled' => 'Geannuleerd',
        'refunded'  => 'Terugbetaald',
    ],

    'payment_method' => [
        'stripe' => 'Stripe',
        'cash'   => 'Contant',
    ],

    'reservation_source' => [
        'online'   => 'Online',
        'employee' => 'Medewerker',
    ],

    'user_role' => [
        'customer' => 'Klant',
        'employee' => 'Medewerker',
        'admin'    => 'Beheerder',
    ],

    'mail_event' => [
        'queued'     => 'In wachtrij',
        'processing' => 'In verwerking',
        'sending'    => 'Versturen',
        'sent'       => 'Verzonden',
        'processed'  => 'Afgerond',
        'retrying'   => 'Opnieuw proberen',
        'failed'     => 'Mislukt',
    ],

    'discount_type' => [
        'flat'    => 'Vast bedrag',
        'percent' => 'Percentage',
    ],

    'billing_type' => [
        'one_time'  => 'Eenmalig',
        'per_night' => 'Per nacht',
    ],

    'stock_type' => [
        'rental'     => 'Verhuur',
        'consumable' => 'Verbruiksartikel',
    ],

    'coupon_scope' => [
        'total'         => 'Totaalprijs',
        'accommodation' => 'Accommodatie',
        'extra'         => 'Extra',
        'all_extras'    => 'Alle extra’s',
    ],

    'checkout_method' => [
        'label' => [
            'cash_paid'      => 'Contant — nu betaald',
            'card_now'       => 'Nu met kaart (Stripe)',
            'send_link'      => 'Betaallink versturen',
            'pay_on_arrival' => 'Bij aankomst betalen',
        ],
        'description' => [
            'cash_paid'      => 'Gast betaalt contant aan de balie. De boeking wordt bevestigd en de betaling wordt als betaald geregistreerd.',
            'card_now'       => 'Nu een kaartbetaling doen — je wordt naar de Stripe-betaalpagina gestuurd om deze af te ronden.',
            'send_link'      => 'Mail de klant een betaallink. De boeking blijft in afwachting totdat zij online betalen.',
            'pay_on_arrival' => 'Reserveer nu, reken af bij aankomst. Er wordt een openstaande contante betaling geregistreerd.',
        ],
    ],
];

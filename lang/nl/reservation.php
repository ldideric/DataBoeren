<?php

return [
    'fields' => [
        'customer'            => 'Klant',
        'campsite'            => 'Kampeerplaats',
        'check_in'            => 'Inchecken',
        'check_out'           => 'Uitchecken',
        'num_adults'          => 'Volwassenen',
        'num_children'        => 'Kinderen',
        'guests'              => 'Gasten',
        'guests_summary'      => ':adults volwassene(n), :children kind(eren)',
        'coupon'              => 'Kortingsbon',
        'source'              => 'Bron',
        'booked_by'           => 'Geboekt door',
        'cancellation_reason' => 'Annuleringsreden',
        'total'               => 'Totaal',
    ],

    'sections' => [
        'reservation'   => 'Reservering',
        'order_summary' => 'Besteloverzicht',
    ],

    'order_summary' => [
        'season_name'          => 'Seizoen',
        'num_nights'           => 'Nachten',
        'nightly_rate'         => 'Tarief per nacht',
        'per_adult_rate'       => 'Tarief per volwassene',
        'per_child_rate'       => 'Tarief per kind',
        'last_minute_discount' => 'Last-minutekorting',
        'coupon_discount'      => 'Kortingsbonkorting',
        'extras_total'         => "Totaal extra's",
        'total'                => 'Totaal',
    ],

    'filters' => [
        'campsite'         => 'Kampeerplaats',
        'arrival_period'   => 'Aankomstperiode',
        'departure_period' => 'Vertrekperiode',
        'on_site'          => 'Momenteel op locatie',
        'has_coupon'       => 'Heeft kortingsbon',
        'booked_by_staff'  => 'Geboekt door personeel',
        'source'           => 'Bron',
        'arriving_from'    => 'Aankomst vanaf: :date',
        'arriving_until'   => 'Aankomst tot: :date',
        'departing_from'   => 'Vertrek vanaf: :date',
        'departing_until'  => 'Vertrek tot: :date',
    ],

    'actions' => [
        'accept' => [
            'label'             => 'Accepteren',
            'modal_heading'     => 'Reservering in afwachting accepteren',
            'modal_description' => 'Bevestig deze reservering en markeer eventuele openstaande betaling op locatie als betaald. De klant ontvangt een bevestigingsmail.',
            'success'           => 'Reservering geaccepteerd',
        ],
        'cancel' => [
            'label'   => 'Annuleren',
            'reason'  => 'Reden',
            'success' => 'Reservering geannuleerd',
        ],
        'resend_confirmation' => [
            'label'   => 'Bevestiging opnieuw versturen',
            'success' => 'Bevestigingsmail opnieuw verzonden',
        ],
        'send_login_link' => [
            'label'             => 'Inloglink versturen',
            'modal_heading'     => 'Inloglink versturen',
            'modal_description' => 'Stuur een inloglink naar :email zodat zij hun boekingen kunnen bekijken of annuleren.',
            'success'           => 'Inloglink verzonden',
        ],
    ],

    'extras' => [
        'extra'      => 'Extra',
        'quantity'   => 'Aantal',
        'unit_price' => 'Stukprijs',
        'subtotal'   => 'Subtotaal',
    ],

    'payments' => [
        'amount'            => 'Bedrag',
        'method'            => 'Methode',
        'paid_at'           => 'Betaald op',
        'stripe_session_id' => 'Stripe-sessie-ID',
    ],
];

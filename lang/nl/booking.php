<?php

return [
    'page' => [
        'title'   => 'Nieuwe boeking',
        'created' => 'Boeking succesvol aangemaakt',
        'submit'  => 'Boeking aanmaken',
    ],

    'steps' => [
        'customer' => 'Klant',
        'stay'     => 'Verblijf',
        'extras'   => "Extra's",
        'summary'  => 'Overzicht & betaling',
    ],

    'fields' => [
        'existing_customer' => 'Bestaande klant',
        'customer'          => 'Klant',
        'campsite'          => 'Kampeerplaats',
        'check_in'          => 'Inchecken',
        'check_out'         => 'Uitchecken',
        'adults'            => 'Volwassenen',
        'children'          => 'Kinderen',
        'vehicles'          => 'Voertuigen',
        'coupon'            => 'Kortingsbon',
        'coupon_helper'     => 'Alleen momenteel geldige kortingsbonnen worden getoond.',
        'payment'           => 'Betaling',
        'extras'            => "Extra's",
        'add_extra'         => 'Extra toevoegen',
        'extra'             => 'Extra',
        'quantity'          => 'Aantal',
    ],

    'summary' => [
        'heading'     => 'Prijsoverzicht',
        'empty'       => 'Vul de verblijfsgegevens in om de prijs te berekenen.',
        'stay'        => 'Verblijf (:count :unit)',
        'night'       => 'nacht',
        'nights'      => 'nachten',
        'last_minute' => 'Last-minutekorting',
        'coupon'      => 'Kortingsbon',
        'extras'      => "Extra's",
        'total'       => 'Totaal',
    ],

    'errors' => [
        'campsite_unavailable' => 'Deze plek is niet (meer) beschikbaar voor deze data.',
        'coupon_invalid'       => 'Deze kortingsbon is verlopen of heeft de gebruikslimiet bereikt.',
        'no_pricing'           => 'Geen prijs ingesteld voor deze data.',
    ],
];

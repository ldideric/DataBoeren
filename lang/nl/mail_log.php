<?php

return [
    'fields' => [
        'occurred_at' => 'Tijdstip',
        'event'       => 'Gebeurtenis',
        'mailable'    => 'Mail',
        'recipient'   => 'Ontvanger',
        'subject'     => 'Onderwerp',
        'message_id'  => 'Message-Id (Mailgun)',
        'error'       => 'Fout',
        'job_id'      => 'Job-Id',
        'attempt'     => 'Poging',
        'queue'       => 'Wachtrij',
        'connection'  => 'Connectie',
        'context'     => 'Context',
    ],

    'filters' => [
        'failures' => 'Alleen mislukt / opnieuw',
    ],

    'actions' => [
        'prune'         => 'Oude logs opruimen',
        'prune_confirm' => 'Hiermee verwijder je alle maillogs ouder dan 30 dagen. Dit kan niet ongedaan worden gemaakt.',
        'pruned'        => ':count maillog(s) opgeruimd.',
    ],

    'sections' => [
        'event'    => 'Gebeurtenis',
        'delivery' => 'Aflevering & wachtrij',
        'error'    => 'Foutmelding',
        'context'  => 'Context',
    ],
];

<?php

return [
    'fields' => [
        'occurred_at' => 'Tijdstip',
        'event'       => 'Gebeurtenis',
        'mailable'    => 'Mail',
        'recipient'   => 'Ontvanger',
        'subject'     => 'Onderwerp',
        'message_id'  => 'Message-Id (Mailgun)',
        'trace_id'    => 'Trace-ID',
        'error'       => 'Fout',
        'job_id'      => 'Job-Id',
        'attempt'     => 'Poging',
        'queue'       => 'Wachtrij',
        'connection'  => 'Connectie',
        'context'     => 'Context',
    ],

    'filters' => [
        'failures'       => 'Alleen mislukt / opnieuw',
        'occurred_from'  => 'Vanaf :date',
        'occurred_until' => 'Tot :date',
    ],

    'actions' => [
        'prune'             => 'Oude logs opruimen',
        'prune_confirm'     => 'Hiermee verwijder je alle maillogs ouder dan 30 dagen. Dit kan niet ongedaan worden gemaakt.',
        'prune_all'         => 'Alle logs opruimen',
        'prune_all_confirm' => 'Hiermee verwijder je elke maillog, ongeacht de leeftijd. Dit kan niet ongedaan worden gemaakt.',
        'prune_all_submit'  => 'Alles verwijderen',
        'pruned'            => ':count maillog(s) opgeruimd.',
    ],

    'groups' => [
        'mail' => 'Mail',
    ],

    'sections' => [
        'event'    => 'Gebeurtenis',
        'delivery' => 'Aflevering & wachtrij',
        'error'    => 'Foutmelding',
        'context'  => 'Context',
    ],
];

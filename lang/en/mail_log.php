<?php

return [
    'fields' => [
        'occurred_at' => 'Timestamp',
        'event'       => 'Event',
        'mailable'    => 'Mail',
        'recipient'   => 'Recipient',
        'subject'     => 'Subject',
        'message_id'  => 'Message-Id (Mailgun)',
        'error'       => 'Error',
        'job_id'      => 'Job ID',
        'attempt'     => 'Attempt',
        'queue'       => 'Queue',
        'connection'  => 'Connection',
        'context'     => 'Context',
    ],

    'filters' => [
        'failures' => 'Failures / retries only',
    ],

    'actions' => [
        'prune'         => 'Prune old logs',
        'prune_confirm' => 'This permanently deletes all mail logs older than 30 days. This cannot be undone.',
        'pruned'        => 'Pruned :count mail log(s).',
    ],

    'sections' => [
        'event'    => 'Event',
        'delivery' => 'Delivery & queue',
        'error'    => 'Error',
        'context'  => 'Context',
    ],
];

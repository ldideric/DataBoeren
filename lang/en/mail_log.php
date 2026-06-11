<?php

return [
    'fields' => [
        'occurred_at' => 'Timestamp',
        'event'       => 'Event',
        'mailable'    => 'Mail',
        'recipient'   => 'Recipient',
        'subject'     => 'Subject',
        'message_id'  => 'Message-Id (Mailgun)',
        'trace_id'    => 'Trace ID',
        'error'       => 'Error',
        'job_id'      => 'Job ID',
        'attempt'     => 'Attempt',
        'queue'       => 'Queue',
        'connection'  => 'Connection',
        'context'     => 'Context',
    ],

    'filters' => [
        'failures'       => 'Failures / retries only',
        'occurred_from'  => 'From :date',
        'occurred_until' => 'Until :date',
    ],

    'actions' => [
        'prune'             => 'Prune old logs',
        'prune_confirm'     => 'This permanently deletes all mail logs older than 30 days. This cannot be undone.',
        'prune_all'         => 'Prune all logs',
        'prune_all_confirm' => 'This permanently deletes every mail log, regardless of age. This cannot be undone.',
        'prune_all_submit'  => 'Delete everything',
        'pruned'            => 'Pruned :count mail log(s).',
    ],

    'groups' => [
        'mail' => 'Mail',
    ],

    'sections' => [
        'event'    => 'Event',
        'delivery' => 'Delivery & queue',
        'error'    => 'Error',
        'context'  => 'Context',
    ],
];

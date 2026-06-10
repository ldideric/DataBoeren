<?php

namespace App\Models;

use App\Enums\MailEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property MailEvent $event
 * @property string|null $mailable
 * @property string|null $recipient
 * @property string|null $subject
 * @property string|null $message_id
 * @property string|null $job_id
 * @property string|null $connection
 * @property string|null $queue
 * @property int|null $attempt
 * @property string|null $error
 * @property array|null $context
 * @property Carbon|null $created_at
 */
class MailLog extends Model
{
    use MassPrunable;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'event'      => MailEvent::class,
        'context'    => 'array',
        'created_at' => 'datetime',
    ];

    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(30));
    }

    public function mailableLabel(): string
    {
        return $this->mailable ? class_basename($this->mailable) : '—';
    }
}

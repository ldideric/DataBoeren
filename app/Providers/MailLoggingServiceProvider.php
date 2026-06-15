<?php

namespace App\Providers;

use App\Enums\MailEvent;
use App\Models\MailLog;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Email;
use Throwable;

class MailLoggingServiceProvider extends ServiceProvider
{
    private ?string $currentTrace = null;

    private ?string $currentJobId = null;

    private ?string $currentRecipient = null;

    private ?string $currentMailable = null;

    public function boot(): void
    {
        if (! env('MAIL_ACTIVITY_LOG', true)) {
            return;
        }

        // --- A mail was requested and queued (all our mailables are ShouldQueue) ---
        Event::listen(function (JobQueued $event): void {
            $job = $event->job;

            if (! $job instanceof SendQueuedMailable) {
                return;
            }

            $this->record(MailEvent::Queued, [
                'mailable'   => $job->mailable::class,
                'recipient'  => $this->recipientsFromMailable($job->mailable),
                'connection' => $event->connectionName,
                'queue'      => $job->queue ?? 'default',
                'job_id'     => (string) $event->id,
            ]);
        });

        // --- The queue worker lifecycle ---
        Event::listen(function (JobProcessing $event): void {
            if ($this->isMailJob($event->job)) {
                $this->currentJobId = (string) $event->job->getJobId();
                $this->currentTrace = (string) Str::uuid();
                $this->linkQueuedRow();
                $this->record(MailEvent::Processing, $this->jobContext($event->job));
            }
        });

        Event::listen(function (JobProcessed $event): void {
            if ($this->isMailJob($event->job)) {
                $this->record(MailEvent::Processed, $this->jobContext($event->job));
                $this->resetThread();
            }
        });

        Event::listen(function (JobExceptionOccurred $event): void {
            if ($this->isMailJob($event->job)) {
                $this->record(MailEvent::Retrying, $this->jobContext($event->job) + [
                    'error' => $this->describe($event->exception),
                ]);
            }
        });

        Event::listen(function (JobFailed $event): void {
            if ($this->isMailJob($event->job)) {
                $this->record(MailEvent::Failed, $this->jobContext($event->job) + [
                    'error' => $this->describe($event->exception),
                ]);
                $this->resetThread();
            }
        });

        // --- The actual transport call (SMTP / Mailgun) ---
        Event::listen(function (MessageSending $event): void {
            $trace = $this->currentTrace ?? (string) Str::uuid();
            $event->message->getHeaders()->addTextHeader('X-Mail-Trace', $trace);

            $recipient = $this->addresses($event->message);
            $mailable = $event->data['__laravel_mailable'] ?? null;

            // The queue-job rows (processing/queued) are written before the recipient is
            // known, so backfill them now that it is — otherwise their group title differs
            // from the transport rows and Filament splits one mail into several groups.
            $this->currentRecipient = $recipient;
            $this->currentMailable ??= $mailable;
            $this->backfillThreadRecipient($trace);

            $this->record(MailEvent::Sending, [
                'mailable'  => $mailable,
                'recipient' => $recipient,
                'subject'   => $event->message->getSubject(),
                'job_id'    => $this->currentJobId,
                'trace_id'  => $trace,
                'context'   => ['mailer' => $event->data['__laravel_mailer'] ?? config('mail.default')],
            ]);
        });

        Event::listen(function (MessageSent $event): void {
            $this->record(MailEvent::Sent, [
                'mailable'   => $event->data['__laravel_mailable'] ?? null,
                'recipient'  => $this->addresses($event->message),
                'subject'    => $event->message->getSubject(),
                'message_id' => $event->sent->getMessageId(),
                'job_id'     => $this->currentJobId,
                'trace_id'   => $this->currentTrace ?? $this->traceFromMessage($event->message),
            ]);
        });
    }

    private function record(MailEvent $event, array $attributes): void
    {
        try {
            MailLog::create($attributes + [
                'event'      => $event,
                'trace_id'   => $this->currentTrace,
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Never let logging interfere with mail delivery
        }
    }

    private function linkQueuedRow(): void
    {
        if ($this->currentJobId === null || $this->currentJobId === '') {
            return;
        }

        try {
            MailLog::query()
                ->where('job_id', $this->currentJobId)
                ->whereNull('trace_id')
                ->update(['trace_id' => $this->currentTrace]);

            // Adopt the thread's recipient/mailable from the originating queued row.
            // The job-lifecycle events (processing/processed/...) have no recipient of
            // their own, so without this their group title differs from the transport
            // rows and Filament splits one mail into several groups.
            $queued = MailLog::query()
                ->where('job_id', $this->currentJobId)
                ->where('event', MailEvent::Queued)
                ->latest('id')
                ->first();

            $this->currentRecipient = $queued?->recipient;
            $this->currentMailable = $queued?->mailable;
        } catch (Throwable) {
            // Linking is best-effort; never break mail delivery over it
        }
    }

    private function backfillThreadRecipient(string $trace): void
    {
        if ($this->currentRecipient === null) {
            return;
        }

        try {
            MailLog::query()
                ->where('trace_id', $trace)
                ->whereNull('recipient')
                ->update(['recipient' => $this->currentRecipient]);
        } catch (Throwable) {
            // Backfilling is best-effort; never break mail delivery over it
        }
    }

    private function resetThread(): void
    {
        $this->currentTrace = null;
        $this->currentJobId = null;
        $this->currentRecipient = null;
        $this->currentMailable = null;
    }

    private function traceFromMessage(Email $message): ?string
    {
        $header = $message->getHeaders()->get('X-Mail-Trace');

        return $header ? ($header->getBodyAsString() ?: null) : null;
    }

    private function isMailJob(JobContract $job): bool
    {
        $name = $job->resolveName();

        return $name === SendQueuedMailable::class || str_contains($name, '\\Mail\\');
    }

    private function jobContext(JobContract $job): array
    {
        return [
            'mailable'   => $this->mailableFromPayload($job) ?? $this->currentMailable,
            'recipient'  => $this->currentRecipient,
            'connection' => $job->getConnectionName(),
            'queue'      => $job->getQueue(),
            'job_id'     => (string) $job->getJobId(),
            'attempt'    => $job->attempts(),
        ];
    }

    private function mailableFromPayload(JobContract $job): ?string
    {
        $name = $job->resolveName();

        // SendQueuedMailable resolves its display name to the mailable class.
        return $name === SendQueuedMailable::class ? null : $name;
    }

    private function recipientsFromMailable(object $mailable): ?string
    {
        $addresses = collect($mailable->to ?? [])->pluck('address')->filter()->values();

        return $addresses->isEmpty() ? null : $addresses->implode(', ');
    }

    private function addresses(Email $message): ?string
    {
        $addresses = collect($message->getTo())->map(fn ($address) => $address->getAddress());

        return $addresses->isEmpty() ? null : $addresses->implode(', ');
    }

    private function describe(Throwable $e): string
    {
        return sprintf('%s: %s (%s:%d)', $e::class, $e->getMessage(), $e->getFile(), $e->getLine());
    }
}

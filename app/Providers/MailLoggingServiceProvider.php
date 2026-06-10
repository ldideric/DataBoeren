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
use Symfony\Component\Mime\Email;
use Throwable;

/**
 * Records the full lifecycle of every outgoing mail into the mail_logs table so
 * admins can see, per message: when it was queued, what the worker did with it,
 * when the SMTP/Mailgun transport fired, and the Message-Id to cross-reference
 * against the Mailgun dashboard. Read-only surface lives in MailLogResource.
 *
 * This is pure observability — it never changes whether or how a mail is sent,
 * and any failure to write a log row is swallowed so it can't break delivery.
 * Toggle with MAIL_ACTIVITY_LOG=false.
 */
class MailLoggingServiceProvider extends ServiceProvider
{
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
                $this->record(MailEvent::Processing, $this->jobContext($event->job));
            }
        });

        Event::listen(function (JobProcessed $event): void {
            if ($this->isMailJob($event->job)) {
                $this->record(MailEvent::Processed, $this->jobContext($event->job));
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
            }
        });

        // --- The actual transport call (SMTP / Mailgun) ---
        Event::listen(function (MessageSending $event): void {
            $this->record(MailEvent::Sending, [
                'mailable'  => $event->data['__laravel_mailable'] ?? null,
                'recipient' => $this->addresses($event->message),
                'subject'   => $event->message->getSubject(),
                'context'   => ['mailer' => $event->data['__laravel_mailer'] ?? config('mail.default')],
            ]);
        });

        Event::listen(function (MessageSent $event): void {
            $this->record(MailEvent::Sent, [
                'mailable'   => $event->data['__laravel_mailable'] ?? null,
                'recipient'  => $this->addresses($event->message),
                'subject'    => $event->message->getSubject(),
                'message_id' => $event->sent->getMessageId(),
            ]);
        });
    }

    private function record(MailEvent $event, array $attributes): void
    {
        try {
            MailLog::create($attributes + [
                'event'      => $event,
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Never let logging interfere with mail delivery
        }
    }

    private function isMailJob(JobContract $job): bool
    {
        $name = $job->resolveName();

        return $name === SendQueuedMailable::class || str_contains($name, '\\Mail\\');
    }

    private function jobContext(JobContract $job): array
    {
        return [
            'mailable'   => $this->mailableFromPayload($job),
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

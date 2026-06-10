<?php

namespace App\Console\Commands;

use App\Auth\Services\SignedUrlGenerator;
use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Mail\AwaitingPayment;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAwaitingPaymentReminders extends Command
{
    protected $signature = 'reservations:remind-awaiting-payment {--dry-run : Preview affected reservations without sending mail}';

    protected $description = 'Remind customers whose online reservation is still unpaid one day after booking.';

    public function handle(SignedUrlGenerator $urls): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $reminded = 0;

        // Online bookings only create a Payment row once Stripe checkout succeeds,
        // so "no payment row at all" means the customer never paid. Pay-on-site
        // bookings already have a pending cash Payment, so they're excluded here.
        Reservation::query()
            ->with('customer', 'orderSummary')
            ->where('status', ReservationStatus::Pending)
            ->where('source', ReservationSource::Online)
            ->whereNull('payment_reminder_sent_at')
            ->where('created_at', '<', now()->subDay())
            ->whereDoesntHave('payments')
            ->orderBy('created_at')
            ->orderBy('id')
            ->chunkById(100, function ($reservations) use ($dryRun, $urls, &$reminded): void {
                foreach ($reservations as $reservation) {
                    if (! $dryRun) {
                        Mail::to($reservation->customer->email)
                            ->send(new AwaitingPayment($reservation, $urls->payment($reservation)));

                        $reservation->forceFill(['payment_reminder_sent_at' => now()])->save();
                    }

                    $reminded++;
                }
            });

        Log::info('reservations:remind-awaiting-payment completed', [
            'dry_run' => $dryRun,
            'reminders_sent' => $reminded,
        ]);

        $label = $dryRun ? ' (dry run — no mail sent)' : '';
        $this->info("Awaiting-payment reminders: {$reminded}{$label}");

        return self::SUCCESS;
    }
}

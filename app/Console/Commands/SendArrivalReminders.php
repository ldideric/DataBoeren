<?php

namespace App\Console\Commands;

use App\Enums\ReservationStatus;
use App\Mail\PreArrival;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendArrivalReminders extends Command
{
    private const DAYS_BEFORE_ARRIVAL = 3;

    protected $signature = 'reservations:remind-arrival {--dry-run : Preview affected reservations without sending mail}';

    protected $description = 'Remind customers a few days before their confirmed reservation begins.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $reminded = 0;
        $target = now()->addDays(self::DAYS_BEFORE_ARRIVAL)->toDateString();

        Reservation::query()
            ->with('customer', 'campsite')
            ->where('status', ReservationStatus::Confirmed)
            ->whereNull('arrival_reminder_sent_at')
            ->whereDate('check_in', $target)
            ->orderBy('check_in')
            ->orderBy('id')
            ->chunkById(100, function ($reservations) use ($dryRun, &$reminded): void {
                foreach ($reservations as $reservation) {
                    if (! $dryRun) {
                        Mail::to($reservation->customer->email)->send(new PreArrival($reservation));

                        $reservation->forceFill(['arrival_reminder_sent_at' => now()])->save();
                    }

                    $reminded++;
                }
            });

        Log::info('reservations:remind-arrival completed', [
            'dry_run' => $dryRun,
            'reminders_sent' => $reminded,
        ]);

        $label = $dryRun ? ' (dry run — no mail sent)' : '';
        $this->info("Pre-arrival reminders: {$reminded}{$label}");

        return self::SUCCESS;
    }
}

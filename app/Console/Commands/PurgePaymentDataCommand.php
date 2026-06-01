<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurgePaymentDataCommand extends Command
{
    protected $signature = 'purge:payment-data {--dry-run : Preview affected rows without writing changes}';

    protected $description = 'Delete payment records older than 7 years and redact Stripe identifiers for users with no remaining payments.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $cutoff = now()->subYears(7);

        $paymentsDeleted = $dryRun
            ? Payment::where('created_at', '<', $cutoff)->count()
            : Payment::where('created_at', '<', $cutoff)->delete();

        $redactedUsers = 0;

        User::query()
            ->whereNotNull('stripe_id')
            ->whereDoesntHave('subscriptions', fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->whereRaw('NOT EXISTS (SELECT 1 FROM payments INNER JOIN reservations ON payments.reservation_id = reservations.id WHERE reservations.customer_id = users.id)')
            ->orderBy('created_at')
            ->orderBy('id')
            ->chunk(100, function ($users) use ($dryRun, &$redactedUsers) {
                if (! $dryRun) {
                    DB::transaction(function () use ($users) {
                        DB::table('users')
                            ->whereIn('id', $users->pluck('id'))
                            ->update([
                                'stripe_id' => null,
                                'pm_type' => null,
                                'pm_last_four' => null,
                                'updated_at' => now(),
                            ]);
                    });
                }

                $redactedUsers += $users->count();
            });

        Log::info('purge:payment-data completed', [
            'dry_run' => $dryRun,
            'payments_deleted' => $paymentsDeleted,
            'users_stripe_redacted' => $redactedUsers,
        ]);

        $label = $dryRun ? ' (dry run — no changes written)' : '';
        $this->info("Payments deleted: {$paymentsDeleted} | Stripe fields redacted on users: {$redactedUsers}{$label}");

        return self::SUCCESS;
    }
}

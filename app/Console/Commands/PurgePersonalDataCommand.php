<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurgePersonalDataCommand extends Command
{
    protected $signature = 'purge:personal-data {--dry-run : Preview affected rows without writing changes}';

    protected $description = 'Redact personal data from customer accounts inactive for 36+ months and clean up expired session data.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $cutoff = now()->subMonths(36);
        $purgedUsers = 0;

        User::query()
            ->where('role', UserRole::Customer)
            ->whereNull('purged_at')
            ->whereDoesntHave('reservations', fn ($q) => $q->where('check_in', '>', now()))
            ->whereDoesntHave('subscriptions', fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->where(function ($query) use ($cutoff) {
                $query->where(function ($q) use ($cutoff) {
                    $q->doesntHave('reservations')
                        ->where('created_at', '<', $cutoff);
                })->orWhere(function ($q) use ($cutoff) {
                    $q->has('reservations')
                        ->whereRaw(
                            '(SELECT MAX(check_out) FROM reservations WHERE customer_id = users.id) < ?',
                            [$cutoff->toDateString()]
                        );
                });
            })
            ->chunkById(100, function ($users) use ($dryRun, &$purgedUsers) {
                if (! $dryRun) {
                    DB::transaction(function () use ($users) {
                        DB::table('users')
                            ->whereIn('id', $users->pluck('id'))
                            ->update([
                                'first_name' => null,
                                'last_name' => null,
                                'email' => null,
                                'phone' => null,
                                'password' => null,
                                'remember_token' => null,
                                'purged_at' => now(),
                                'updated_at' => now(),
                            ]);
                    });
                }

                $purgedUsers += $users->count();
            });

        $sessionCutoff = now()->subDays(30)->timestamp;
        $sessionsDeleted = $dryRun
            ? DB::table('sessions')->where('last_activity', '<', $sessionCutoff)->count()
            : DB::table('sessions')->where('last_activity', '<', $sessionCutoff)->delete();

        $tokenCutoff = now()->subMinutes(60);
        $tokensDeleted = $dryRun
            ? DB::table('password_reset_tokens')->where('created_at', '<', $tokenCutoff)->count()
            : DB::table('password_reset_tokens')->where('created_at', '<', $tokenCutoff)->delete();

        Log::info('purge:personal-data completed', [
            'dry_run' => $dryRun,
            'users_purged' => $purgedUsers,
            'sessions_deleted' => $sessionsDeleted,
            'tokens_deleted' => $tokensDeleted,
        ]);

        $label = $dryRun ? ' (dry run — no changes written)' : '';
        $this->info("Users purged: {$purgedUsers} | Sessions deleted: {$sessionsDeleted} | Tokens deleted: {$tokensDeleted}{$label}");

        return self::SUCCESS;
    }
}

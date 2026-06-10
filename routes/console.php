<?php

use App\Console\Commands\PurgePaymentDataCommand;
use App\Console\Commands\PurgePersonalDataCommand;
use App\Console\Commands\SendArrivalReminders;
use App\Console\Commands\SendAwaitingPaymentReminders;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command(PurgePersonalDataCommand::class)
    ->name('purge:personal-data')
    ->daily()
    ->onOneServer()
    ->timezone('Europe/Amsterdam')
    ->environments(['production'])
    ->runInBackground()
    ->withoutOverlapping()
    ->onFailure(fn ($output) => Log::error('Purge personal-data job failed', ['output' => (string) $output]));

Schedule::command(PurgePaymentDataCommand::class)
    ->name('purge:payment-data')
    ->daily()
    ->onOneServer()
    ->timezone('Europe/Amsterdam')
    ->environments(['production'])
    ->runInBackground()
    ->withoutOverlapping()
    ->onFailure(fn ($output) => Log::error('Purge payment-data job failed', ['output' => (string) $output]));

Schedule::command(SendAwaitingPaymentReminders::class)
    ->name('reservations:remind-awaiting-payment')
    ->hourly()
    ->onOneServer()
    ->timezone('Europe/Amsterdam')
    ->environments(['production', 'staging'])
    ->runInBackground()
    ->withoutOverlapping()
    ->onFailure(fn ($output) => Log::error('Awaiting-payment reminder job failed', ['output' => (string) $output]));

Schedule::command(SendArrivalReminders::class)
    ->name('reservations:remind-arrival')
    ->dailyAt('09:00')
    ->onOneServer()
    ->timezone('Europe/Amsterdam')
    ->environments(['production', 'staging'])
    ->runInBackground()
    ->withoutOverlapping()
    ->onFailure(fn ($output) => Log::error('Pre-arrival reminder job failed', ['output' => (string) $output]));

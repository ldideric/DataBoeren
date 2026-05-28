<?php

use App\Console\Commands\PurgePaymentDataCommand;
use App\Console\Commands\PurgePersonalDataCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

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

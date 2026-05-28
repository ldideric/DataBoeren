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

Schedule::daily()
    ->onOneServer()
    ->timezone('Europe/Amsterdam')
    ->environments(['production'])
    ->runInBackground()
    ->withoutOverlapping()
    ->onFailure(fn (Stringable $output) => Log::error('Purge job failed', ['output' => (string) $output]))
    ->group(function () {
        Schedule::command(PurgePersonalDataCommand::class)->name('purge:personal-data');
        Schedule::command(PurgePaymentDataCommand::class)->name('purge:payment-data');
    });

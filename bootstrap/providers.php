<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\MailLoggingServiceProvider;

return [
    AppServiceProvider::class,
    MailLoggingServiceProvider::class,
    AdminPanelProvider::class,
];

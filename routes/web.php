<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CampsiteController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/campsites', [CampsiteController::class, 'index'])->name('campsites.index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'requestForm'])->name('login');
    Route::post('/login', [AuthController::class, 'sendLink'])->name('login.send');
    Route::get('/login/sent', [AuthController::class, 'linkSent'])->name('login.sent');
});

Route::get('/auth/link/{user}', [AuthController::class, 'verify'])
    ->middleware('signed')
    ->name('login.verify');

Route::controller(BookingController::class)
    ->prefix('bookings')
    ->name('bookings.')
    ->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
    });

Route::middleware('customer.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::controller(BookingController::class)
        ->prefix('bookings')
        ->name('bookings.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::delete('/{reservation}', 'destroy')->name('destroy');
        });
});

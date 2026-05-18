<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CampsiteController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/campsites', [CampsiteController::class, 'index'])->name('campsites.index');

Route::controller(BookingController::class)
    ->prefix('bookings')
    ->name('bookings.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/cancel', 'cancelForm')->name('cancel.form');
        Route::post('/cancel', 'cancel')->name('cancel');
    });

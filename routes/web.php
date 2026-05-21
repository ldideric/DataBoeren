<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CampsiteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/* Main routes for the application. */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/campsites', [CampsiteController::class, 'index'])->name('campsites.index');

/* Authentication routes. */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'requestForm'])->name('login');
    Route::post('/login', [AuthController::class, 'sendLink'])->name('login.send');
    Route::get('/login/sent', [AuthController::class, 'linkSent'])->name('login.sent');
});

Route::get('/auth/link/{user}', [AuthController::class, 'verify'])
    ->middleware('signed')
    ->name('login.verify');

/* Public booking routes (guests can book; they get a magic link afterwards). */
Route::controller(BookingController::class)
    ->prefix('bookings')
    ->name('bookings.')
    ->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
    });

/* Routes that require an authenticated customer. */
Route::middleware('customer.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /* Booking routes. */
    Route::controller(BookingController::class)
        ->prefix('bookings')
        ->name('bookings.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::delete('/{reservation}', 'destroy')->name('destroy');
        });

    /* Stripe payment routes. */
    Route::controller(PaymentController::class)->group(function () {
        Route::get('/bookings/{reservation}/payment', 'show')->name('payments.show');
        Route::post('/bookings/{reservation}/checkout', 'checkout')->name('payments.checkout');
        Route::get('/checkout/success', 'success')->name('payments.success');
        Route::get('/checkout/cancel', 'cancel')->name('payments.cancel');
    });
});

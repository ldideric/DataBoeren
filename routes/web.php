<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CampsiteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/* Main routes */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/campsites', [CampsiteController::class, 'index'])->name('campsites.index');
Route::get('/map', fn () => view('map.index'))->name('map.index');
Route::get('privacy', fn () => view('extras.privacy'))->name('privacy');

/* Access-link request */
Route::get('/login', [AuthController::class, 'requestForm'])->name('login');
Route::post('/login', [AuthController::class, 'sendLink'])->name('login.send');
Route::get('/login/sent', [AuthController::class, 'linkSent'])->name('login.sent');

/* Public booking routes */
Route::controller(BookingController::class)
    ->prefix('bookings')
    ->name('bookings.')
    ->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
    });

/* Customer self-service */
Route::middleware('signed')->group(function () {
    Route::get('/bookings/{user}', [BookingController::class, 'index'])->name('bookings.index');
    Route::delete('/bookings/{user}/reservations/{reservation}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    Route::get('/bookings/{reservation}/payment', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/bookings/{reservation}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
});

/* Stripe redirect targets */
Route::get('/checkout/success', [PaymentController::class, 'success'])->name('payments.success');
Route::get('/checkout/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');

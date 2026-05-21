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

Route::get('/auth/required', [AuthController::class, 'required'])->name('auth.required');

/* Authentication routes. */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/* Routes that require authentication. */
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /* Booking routes. */
    Route::controller(BookingController::class)
        ->prefix('bookings')
        ->name('bookings.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
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

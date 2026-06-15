<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CampsiteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\RememberCustomer;
use Illuminate\Support\Facades\Route;

/* Main routes */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('about', fn () => view('pages.about'))->name('about');
Route::get('activities', fn () => view('pages.activities'))->name('activities');
Route::get('contact', fn () => view('pages.contact'))->name('contact');
Route::get('/privacy', fn () => view('extras.privacy'))->name('privacy');
Route::get('/houserules', fn () => view('extras.houserules'))->name('houserules');
Route::get('/map', function () {
    $campsites = \App\Models\Campsite::all();
    return view('campsites.map.index', compact('campsites'));
})->name('map.index');

/* Public booking routes */
Route::get('/campsites', [CampsiteController::class, 'index'])->name('campsites.index');
Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');

/* Customer self-service */
Route::middleware(['signed', RememberCustomer::class])->group(function () {
    Route::get('/bookings/{user}', [BookingController::class, 'index'])->name('bookings.index');
    Route::delete('/bookings/{user}/reservations/{reservation}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    Route::get('/bookings/{reservation}/payment', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/bookings/{reservation}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
});

/* Stripe redirect targets */
Route::get('/checkout/success', [PaymentController::class, 'success'])->name('payments.success');
Route::get('/checkout/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');

/* Locale switching */
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

require __DIR__.'/auth.php';

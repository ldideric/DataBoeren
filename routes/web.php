<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('bookings.page');
})->name('home');

Route::get('/annuleren.blade.php', function () {
    return view('bookings.Annuleren');
})->name('annuleren');

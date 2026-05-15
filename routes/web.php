<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('bookings.page');
})->name('home');

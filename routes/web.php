<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('bookings.homepage');
})->name('home');

Route::get('/bookings', function () {
    return view('bookings.page');
})->name('bookings');

Route::get('/boeken', function () {
    return view('bookings.book');
})->name('boeken');

Route::get('/formulier', function () {
    return view('bookings.form');
})->name('invulformulier');

Route::get('/annuleren', function () {
    return view('bookings.cancel');
})->name('annuleren');

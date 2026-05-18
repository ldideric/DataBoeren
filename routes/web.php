<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('bookings.page');
})->name('home');

Route::get('/formulier.blade.php', function () {
    return view('bookings.formulier');
})->name('invulformulier');

Route::get('/annuleren.blade.php', function () {
    return view('bookings.Annuleren');
})->name('annuleren');

Route::get('/boeken.blade.php', function () {
    return view('bookings.boeken');
})->name('boeken');

Route::get('/homepagina.blade.php', function () {
    return view('bookings.homepage');
})->name('homepage');

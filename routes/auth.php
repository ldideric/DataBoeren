<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'requestForm'])->name('login');
Route::post('/login', [AuthController::class, 'sendLink'])->name('login.send');
Route::get('/login/sent', [AuthController::class, 'linkSent'])->name('login.sent');

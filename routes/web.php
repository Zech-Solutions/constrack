<?php

use App\Http\Controllers\PreRegistrationController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SupplierController::class, 'index']);
Route::get('/signup', [PreRegistrationController::class, 'show'])->name('signup.page');
Route::post('/signup', [PreRegistrationController::class, 'store'])->name('signup.store');

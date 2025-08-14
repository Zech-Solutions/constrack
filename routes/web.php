<?php

use App\Http\Controllers\PreRegistrationController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

// Route::get('/signup', [PreRegistrationController::class, 'show'])->name('signup.page');
// Route::post('/signup', [PreRegistrationController::class, 'store'])->name('signup.store');

Route::get('/admin/quotation/{id}/generate-pdf', [QuotationController::class, 'generatePdf']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;

Route::get('/', [SupplierController::class, 'index']);

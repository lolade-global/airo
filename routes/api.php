<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuotationController;

Route::group([ 'middleware' => 'api' ], function () {
    Route::group([ 'prefix' => 'auth' ], function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });
    Route::post('/quotation', [QuotationController::class, 'generateQuotation'])->middleware('auth:api')->name('quotation.generate');
});

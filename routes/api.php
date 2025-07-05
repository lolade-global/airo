<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuotationController;

Route::group([ 'middleware' => 'api' ], function () {
    Route::group([ 'prefix' => 'auth' ], function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/quotation', [QuotationController::class, 'generateQuotation'])->name('quotation.generate');
    });
});

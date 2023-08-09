<?php

use App\Http\Controllers\Google\GoogleCallBackController;
use App\Http\Controllers\Google\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->prefix('google')->name('google.')->group(function () {
    Route::get('auth', GoogleAuthController::class)->name('auth');
    Route::get('callback', GoogleCallBackController::class)->name('callback');
});

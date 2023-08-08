<?php

use App\Http\Controllers\User\UserLoginController;
use App\Http\Controllers\User\UserRegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('user')->name('user.')->group(function () {
    Route::post('register', UserRegisterController::class)->name('register');
    Route::post('login', UserLoginController::class)->name('login');
});

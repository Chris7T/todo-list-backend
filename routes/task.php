<?php

use App\Http\Controllers\Task\TaskCreateController;
use App\Http\Controllers\Task\TaskDeleteController;
use App\Http\Controllers\Task\TaskGetController;
use App\Http\Controllers\Task\TaskListController;
use App\Http\Controllers\Task\TaskSetCompleteController;
use App\Http\Controllers\Task\TaskUpdateController;
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

Route::middleware('auth:api')->prefix('task')->name('task.')->group(function () {
    Route::post('register', TaskCreateController::class)->name('register');
    Route::get('list/{id}', TaskListController::class)->name('list');
    Route::delete('delete/{id}', TaskDeleteController::class)->name('delete');
    Route::get('get/{id}', TaskGetController::class)->name('get');
    Route::patch('complete/{id}', TaskSetCompleteController::class)->name('complete');
    Route::put('update/{id}', TaskUpdateController::class)->name('update');
});

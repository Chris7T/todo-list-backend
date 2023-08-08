<?php

use App\Http\Controllers\TaskList\TaskListCreateController;
use App\Http\Controllers\TaskList\TaskListDeleteController;
use App\Http\Controllers\TaskList\TaskListListController;
use App\Http\Controllers\TaskList\TaskListUpdateController;
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

Route::middleware('auth:api')->prefix('task-list')->name('task-list.')->group(function () {
    Route::post('register', TaskListCreateController::class)->name('register');
    Route::get('list', TaskListListController::class)->name('list');
    Route::delete('delete/{id}', TaskListDeleteController::class)->name('delete');
    Route::put('update/{id}', TaskListUpdateController::class)->name('update');
});

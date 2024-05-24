<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//End User
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/forgot', [AuthController::class, 'forgot']);
    Route::post('/reset', [AuthController::class, 'reset']);
})->middleware('auth:sanctum');

Route::prefix('profile')->group(function () {
    Route::get('/show/{id?}', [ProfileController::class, 'show']);
    Route::put('/update', [ProfileController::class, 'update']);
});


// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function () {
        return auth()->user();
    })->name('users.index');
});

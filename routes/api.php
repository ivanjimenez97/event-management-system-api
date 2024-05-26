<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//End User
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\TicketController;

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

// Authenticated Routes
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


Route::prefix('events')->group(function () {
    //Crud endpoints
    Route::get('/', [EventController::class, 'index']);
    Route::post('/save', [EventController::class, 'store']);
    Route::get('/edit/{id?}', [EventController::class, 'edit']);
    Route::put('/update/{id?}', [EventController::class, 'update']);
    Route::delete('/delete/{id?}', [EventController::class, 'destroy']);

    //Organizer's event endpoints
    Route::get('/organizer/{organizerId?}', [EventController::class, 'getOrganizerEvents']);
    
    //Visitor's event endpoints
    Route::prefix('visitor')->group(function(){
        Route::get('/available-events', [EventController::class, 'getAvailableEvents']);
    });
});


Route::prefix('tickets')->group(function () {
    //Crud endpoints
    Route::get('/', [TicketController::class, 'index']);
    Route::post('/save', [TicketController::class, 'store']);
    Route::get('/show/{id?}', [TicketController::class, 'show']);
    Route::put('/update/{id?}', [TicketController::class, 'update']);
    Route::delete('/delete/{id?}', [TicketController::class, 'destroy']);
});


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function () {
        return auth()->user();
    })->name('users.index');
});

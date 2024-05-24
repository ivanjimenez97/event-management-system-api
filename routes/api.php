<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//End User
use App\Http\Controllers\API\Auth\AuthController;

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

//Authentication
Route::prefix('auth')->group(function () {
    //Registration
    Route::post('registration', [AuthController::class, 'register']);

    //Login
    Route::post('login', [AuthController::class, 'login']);

    //Logout
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {
    Route::prefix('users')->group(function () {
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SpotController;
use App\Http\Controllers\Api\RegistrationController;

// === PUBLIC ===
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);
Route::get('spots',     [SpotController::class, 'index']);
Route::get('spots/{id}', [SpotController::class, 'show']);

// === AUTHENTICATED ===
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me',      [AuthController::class, 'me']);

    Route::post('spots/{id}/register',      [RegistrationController::class, 'register']);
    Route::get('my-registrations',          [RegistrationController::class, 'myRegistrations']);
    Route::patch('spots/{id}/cancel',       [RegistrationController::class, 'cancel']);


    // === ADMIN ONLY ===
    Route::middleware('role:admin')->group(function () {
        Route::get('admin/spots',          [SpotController::class, 'adminIndex']);
        Route::post('spots',               [SpotController::class, 'store']);
        Route::post('spots/{id}',          [SpotController::class, 'update']); // POST + _method=PUT for form-data
        Route::delete('spots/{id}',        [SpotController::class, 'destroy']);
        Route::get('spots/{id}/registrations', [RegistrationController::class, 'spotRegistrations']);
    });
});

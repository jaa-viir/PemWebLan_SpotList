<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SpotController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\GuideController;

// === PUBLIC ===
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

// Spots - public
Route::get('spots',          [SpotController::class, 'index']);
Route::get('spots/{id}',     [SpotController::class, 'show']);

// Guides - public
Route::get('guides',         [GuideController::class, 'index']);
Route::get('guides/{id}',    [GuideController::class, 'show']);

// === AUTHENTICATED ===
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me',      [AuthController::class, 'me']);

    // Member: event registration
    Route::post('spots/{id}/register',  [RegistrationController::class, 'register']);
    Route::get('my-registrations',      [RegistrationController::class, 'myRegistrations']);
    Route::patch('spots/{id}/cancel',   [RegistrationController::class, 'cancel']);

    // === ADMIN ONLY ===
    Route::middleware('role:admin,super_admin')->group(function () {

        // Spots management
        Route::get('admin/spots',              [SpotController::class, 'adminIndex']);
        Route::post('spots',                   [SpotController::class, 'store']);
        Route::post('spots/{id}',              [SpotController::class, 'update']); // POST + _method=PUT
        Route::delete('spots/{id}',            [SpotController::class, 'destroy']);
        Route::get('spots/{id}/registrations', [RegistrationController::class, 'spotRegistrations']);
        Route::put('/registrations/{id}/confirm', [RegistrationController::class, 'confirmRegistration']);

        // Guides management
        Route::post('guides',                  [GuideController::class, 'store']);
        Route::put('guides/{id}',              [GuideController::class, 'update']);
        Route::delete('guides/{id}',           [GuideController::class, 'destroy']);
    });
});

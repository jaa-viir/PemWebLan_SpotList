<?php

use Illuminate\Http\Request;    
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\AuthController;


// default laravel auth route
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

/*
API Routes - SpotList Project
*/

// --- RUTE AUTENTIKASI ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

// --- RUTE PUBLIK (Dapat diakses Member/Tanpa Login) ---
// Menampilkan daftar panduan dan detail isi panduan
Route::get('/guides', [GuideController::class, 'index']);
Route::get('/guides/{id}', [GuideController::class, 'show']);


// --- RUTE PRIVATE (Khusus Admin & Super Admin) ---
// auth:api menggunakan JWT, role:admin adalah middleware RBAC yang sudah kita buat
Route::middleware(['auth:api', 'role:admin,super_admin'])->group(function () {
    
// Rute Terproteksi JWT
// Route::middleware('auth:api')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);

    // Hanya Admin dan Super Admin yang bisa mengelola isi panduan
    Route::post('/guides', [GuideController::class, 'store']);      // Create
    Route::put('/guides/{id}', [GuideController::class, 'update']);   // Update
    Route::delete('/guides/{id}', [GuideController::class, 'destroy']); // Delete
    
});
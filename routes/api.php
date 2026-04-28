<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Api\WisataApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\SubmissionApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint API untuk aplikasi Flutter mobile.
| Semua route di sini otomatis memiliki prefix /api
|
*/

// === PUBLIC ROUTES (tanpa login) ===

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);

// Beranda
Route::get('/home', [HomeApiController::class, 'index']);

// Wisata
Route::get('/wisata', [WisataApiController::class, 'index']);
Route::get('/wisata/{slug}', [WisataApiController::class, 'show']);
Route::get('/fasilitas/{slug}', [WisataApiController::class, 'facilities']);

// Jadwal / Booking
Route::get('/jadwal', [BookingApiController::class, 'locations']);
Route::get('/booking/{slug}', [BookingApiController::class, 'byLocation']);
Route::get('/booking/{slug}/{bulan}', [BookingApiController::class, 'byMonth']);


// === AUTHENTICATED ROUTES (perlu login / token Sanctum) ===

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::post('/profile/update', [ProfileApiController::class, 'update']);

    // Submission
    Route::get('/submission/locations', [SubmissionApiController::class, 'locations']);
    Route::post('/submission', [SubmissionApiController::class, 'store']);
    Route::get('/history', [SubmissionApiController::class, 'history']);
});

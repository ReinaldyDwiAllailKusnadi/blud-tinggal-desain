<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Api\WisataApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\SubmissionApiController;
use App\Http\Controllers\Api\RecommendationController;

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
Route::middleware('throttle:60,1')->group(function () {
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
    Route::get('/jadwal', [BookingApiController::class, 'index']);
    Route::get('/schedules', [BookingApiController::class, 'schedules']);
    Route::get('/booking/{slug}', [BookingApiController::class, 'byLocation']);
    Route::get('/booking/{slug}/{bulan}', [BookingApiController::class, 'byMonth']);
});


// === AUTHENTICATED ROUTES (perlu login / token Sanctum) ===

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
    Route::post('/profile/update', [ProfileApiController::class, 'update']);

    // Submission
    Route::get('/submission/locations', [SubmissionApiController::class, 'locations']);
    Route::post('/submission', [SubmissionApiController::class, 'store']);
    Route::get('/history', [SubmissionApiController::class, 'history']);
    Route::get('/submission/{id}/download/{type}', [SubmissionApiController::class, 'download']);
    Route::post('/recommendation', [RecommendationController::class, 'recommend']);
});

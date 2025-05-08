<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\HewanController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PermohonanAdopsiController;

// === AUTH ===
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // === HEWAN ===
    Route::middleware('role:user')->group(function () {
        Route::get('/favorite', [LikeController::class, 'favoriteHewan']);
        Route::post('/hewan/{id}/like', [LikeController::class, 'toggleLike']);
        Route::get('/jenis/{jenis}', [HewanController::class, 'filterByJenis']);
    });

    Route::middleware('role:user,shelter,admin')->group(function () {
        Route::get('/hewan', [HewanController::class, 'index']);
        Route::get('/hewan/{id}', [HewanController::class, 'show']);
    });

    Route::middleware('role:user,shelter')->group(function () {
        Route::post('/hewan', [HewanController::class, 'store']);
        Route::put('/hewan/{id}', [HewanController::class, 'update']);
        Route::delete('/hewan/{id}', [HewanController::class, 'destroy']);
    });

    //profile
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [ProfileController::class, 'profile']);
        Route::get('/profile/detail', [ProfileController::class, 'detailProfile']);
    });

    // === ARTIKEL ===
    Route::middleware('role:user,shelter,admin')->group(function () {
        Route::get('/artikel', [ArtikelController::class, 'index']);
        Route::get('/artikel/{id}', [ArtikelController::class, 'show']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/artikel', [ArtikelController::class, 'store']);
        Route::put('/artikel/{id}', [ArtikelController::class, 'update']);
        Route::delete('/artikel/{id}', [ArtikelController::class, 'destroy']);
    });

    // === NOTIFIKASI ===
    Route::middleware('role:user,shelter')->group(function () {
        Route::get('/notifikasi', [NotifikasiController::class, 'index']);
        Route::get('/notifikasi/{id}', [NotifikasiController::class, 'show']);
        Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy']);
    });

    // === PERMOHONAN ADOPSI ===
    Route::middleware('role:user')->group(function () {
        Route::post('/permohonan-adopsi', [PermohonanAdopsiController::class, 'store']);
    });

    Route::middleware('role:user,shelter')->group(function () {
        Route::get('/permohonan-adopsi', [PermohonanAdopsiController::class, 'index']);
        Route::get('/permohonan-adopsi/{id}', [PermohonanAdopsiController::class, 'show']);
        Route::put('/permohonan-adopsi/{id}/status', [PermohonanAdopsiController::class, 'updateStatus']);
        Route::delete('/permohonan-adopsi/{id}', [PermohonanAdopsiController::class, 'destroy']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

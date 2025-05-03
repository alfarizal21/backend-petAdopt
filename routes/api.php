<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HewanController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PermohonanAdopsiController;

//auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//hewan
Route::get('/hewan', [HewanController::class, 'index']);
Route::get('/hewan/{id}', [HewanController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/hewan', [HewanController::class, 'store']);
    Route::put('/hewan/{id}', [HewanController::class, 'update']);
    Route::delete('/hewan/{id}', [HewanController::class, 'destroy']);
});

//artikel
Route::get('/artikel', [ArtikelController::class, 'index']);
Route::get('/artikel/{id}', [ArtikelController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/artikel', [ArtikelController::class, 'store']);
    Route::put('/artikel/{id}', [ArtikelController::class, 'update']);
    Route::delete('/artikel/{id}', [ArtikelController::class, 'destroy']);
});

//notifikasi
Route::middleware(['auth:sanctum', 'role:user,shelter'])->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::get('/notifikasi/{id}', [NotifikasiController::class, 'show']);
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy']);
});

// Route::get('/notifikasi', [NotifikasiController::class, 'index']);
// Route::get('/notifikasi/{id}', [NotifikasiController::class, 'show']);
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/notifikasi', [NotifikasiController::class, 'store']);
//     Route::put('/notifikasi/{id}', [NotifikasiController::class, 'updateStatus']);
//     Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy']);
// });

//permohonan adopsi
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::post('/permohonan-adopsi', [PermohonanAdopsiController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'role:user,shelter'])->group(function () {
    Route::get('/permohonan-adopsi', [PermohonanAdopsiController::class, 'index']);
    Route::get('/permohonan-adopsi/{id}', [PermohonanAdopsiController::class, 'show']);
    Route::put('/permohonan-adopsi/{id}/status', [PermohonanAdopsiController::class, 'updateStatus']);
    Route::delete('/permohonan-adopsi/{id}', [PermohonanAdopsiController::class, 'destroy']);
});

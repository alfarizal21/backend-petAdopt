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
    Route::middleware('role:user,shelter,admin')->group(function () {
        Route::get('/profile', [ProfileController::class, 'profile']);
        Route::get('/profile/detail', [ProfileController::class, 'detailProfile']);
        Route::put('/profile/update', [ProfileController::class, 'updateProfile']);
        Route::put('/update-password', [ProfileController::class, 'updatePassword']);
        Route::get('/profile/my-pets', [HewanController::class, 'myPets']);
        Route::post('/profile/photo', [ProfileController::class, 'uploadFotoProfil']);
        Route::get('/profile/photo', [ProfileController::class, 'getFotoProfil']);
        Route::delete('/profile/photo', [ProfileController::class, 'deleteFotoProfil']);
    });

    // === NOTIFIKASI ===
    Route::middleware('role:user')->group(function () {
        Route::get('/notifikasi', [NotifikasiController::class, 'getUserNotifications']);
        Route::patch('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead']);
    });

    // === PERMOHONAN ADOPSI ===
    Route::middleware('role:user')->group(function () {
        Route::post('/permohonan-adopsi', [PermohonanAdopsiController::class, 'store']); //halaman formulir pengajuan adopsi
    });

    Route::middleware('role:user,shelter')->group(function () {
        Route::get('/permohonan/daftar-permohonan/list-hewan', [PermohonanAdopsiController::class, 'listUserPermohonanHewan']); //mengembalikan daftar hewan yang telah diajukan adopsi oleh user
        Route::get('/permohonan/{id}', [PermohonanAdopsiController::class, 'showDetailPermohonan']); //mengembalikan data permohonan adopsi berdasarkan id
        Route::get('/permohonan/hewan/{id}/pemohon', [PermohonanAdopsiController::class, 'listPemohonByHewan']); //mengembalikan daftar pemohon adopsi berdasarkan id hewan
        Route::get('/permohonan/hewan/{hewanId}/user/{userId}', [PermohonanAdopsiController::class, 'showDetailByHewanAndUser']); //mengembalikan detail permohonan adopsi berdasarkan id hewan dan id user
        Route::put('/permohonan/{id}/status', [PermohonanAdopsiController::class, 'updateStatus']); //mengupdate status permohonan adopsi sekaligus mengirim notifikasi ke user yang mengajukan permohonan adopsi
        Route::put('/permohonan/{id}', [PermohonanAdopsiController::class, 'update']);
        Route::delete('/permohonan/{id}', [PermohonanAdopsiController::class, 'destroy']);
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
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

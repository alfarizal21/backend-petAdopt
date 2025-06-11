<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HewanController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PermohonanAdopsiController;

// === ROUTE PUBLIC===
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// === ROUTE DENGAN AUTH ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // === USER ===
    Route::middleware('role:user')->prefix('user')->group(function () {
        // favorite hewan
        Route::get('/hewan/favorite', [LikeController::class, 'favoriteHewan']);
        Route::post('/hewan/{id}/like', [LikeController::class, 'toggleLike']);
        Route::get('/hewan/jenis/{jenis}', [HewanController::class, 'filterByJenis']);

        // hewan
        Route::get('/hewan', [HewanController::class, 'index']);
        Route::get('/hewan/{id}', [HewanController::class, 'show']);
        Route::post('/hewan', [HewanController::class, 'store']);
        Route::put('/hewan/{id}', [HewanController::class, 'update']);
        Route::delete('/hewan/{id}', [HewanController::class, 'destroy']);

        // profile
        Route::get('/profile', [ProfileController::class, 'profile']);
        Route::get('/profile/detail', [ProfileController::class, 'detailProfile']);
        Route::put('/profile/update', [ProfileController::class, 'updateProfile']);
        Route::put('/update-password', [ProfileController::class, 'updatePassword']);
        Route::get('/profile/my-pets', [HewanController::class, 'myPets']);
        Route::post('/profile/photo', [ProfileController::class, 'uploadFotoProfil']);
        Route::get('/profile/photo', [ProfileController::class, 'getFotoProfil']);
        Route::delete('/profile/photo', [ProfileController::class, 'deleteFotoProfil']);

        // notifikasi
        Route::get('/notifikasi', [NotifikasiController::class, 'getUserNotifications']);
        Route::patch('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead']);

        // permohonan adopsi
        Route::post('/permohonan-adopsi', [PermohonanAdopsiController::class, 'store']); //halaman formulir pengajuan adopsi
        Route::get('/permohonan/daftar-permohonan/list-hewan', [PermohonanAdopsiController::class, 'listUserPermohonanHewan']); //mengembalikan daftar hewan yang telah diajukan adopsi oleh user
        Route::get('/permohonan/{id}', [PermohonanAdopsiController::class, 'showDetailPermohonan']); //mengembalikan data permohonan adopsi berdasarkan id
        Route::get('/permohonan/hewan/{id}/pemohon', [PermohonanAdopsiController::class, 'listPemohonByHewan']); //mengembalikan daftar pemohon adopsi berdasarkan id hewan
        Route::get('/permohonan/hewan/{hewanId}/user/{userId}', [PermohonanAdopsiController::class, 'showDetailByHewanAndUser']); //mengembalikan detail permohonan adopsi berdasarkan id hewan dan id user
        Route::put('/permohonan/{id}/status', [PermohonanAdopsiController::class, 'updateStatus']); //mengupdate status permohonan adopsi sekaligus mengirim notifikasi ke user yang mengajukan permohonan adopsi
        Route::put('/permohonan/{id}', [PermohonanAdopsiController::class, 'update']);
        Route::delete('/permohonan/{id}', [PermohonanAdopsiController::class, 'destroy']);

        // artikel
        Route::get('/artikel', [ArtikelController::class, 'index']);
        Route::get('/artikel/{id}', [ArtikelController::class, 'show']);
    });

    // === SHELTER ===
    Route::middleware('role:shelter')->prefix('shelter')->group(function () {
        // favorite hewan
        Route::get('/hewan/favorite', [LikeController::class, 'favoriteHewan']);
        Route::post('/hewan/{id}/like', [LikeController::class, 'toggleLike']);
        Route::get('/hewan/jenis/{jenis}', [HewanController::class, 'filterByJenis']);

        // hewan
        Route::get('/hewan', [HewanController::class, 'index']);
        Route::get('/hewan/{id}', [HewanController::class, 'show']);
        Route::post('/hewan', [HewanController::class, 'store']);
        Route::put('/hewan/{id}', [HewanController::class, 'update']);
        Route::delete('/hewan/{id}', [HewanController::class, 'destroy']);

        // profile
        Route::get('/profile', [ProfileController::class, 'profile']);
        Route::get('/profile/detail', [ProfileController::class, 'detailProfile']);
        Route::put('/profile/update', [ProfileController::class, 'updateProfile']);
        Route::put('/update-password', [ProfileController::class, 'updatePassword']);
        Route::get('/profile/my-pets', [HewanController::class, 'myPets']);
        Route::post('/profile/photo', [ProfileController::class, 'uploadFotoProfil']);
        Route::get('/profile/photo', [ProfileController::class, 'getFotoProfil']);
        Route::delete('/profile/photo', [ProfileController::class, 'deleteFotoProfil']);

        // permohonan adopsi
        Route::get('/permohonan/hewan/{id}/pemohon', [PermohonanAdopsiController::class, 'listPemohonByHewan']); //mengembalikan daftar pemohon adopsi berdasarkan id hewan
        Route::get('/permohonan/hewan/{hewanId}/user/{userId}', [PermohonanAdopsiController::class, 'showDetailByHewanAndUser']); //mengembalikan detail permohonan adopsi berdasarkan id hewan dan id user
        Route::put('/permohonan/{id}/status', [PermohonanAdopsiController::class, 'updateStatus']); //mengupdate status permohonan adopsi sekaligus mengirim notifikasi ke user yang mengajukan permohonan adopsi

        // artikel
        Route::get('/artikel', [ArtikelController::class, 'index']);
        Route::get('/artikel/{id}', [ArtikelController::class, 'show']);
    });

    // === ADMIN ===
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // user stats
        Route::get('/dashboard/user-stats', [AdminController::class, 'userStats']);

        // hewan stats
        Route::get('/dashboard/hewan-stats', [AdminController::class, 'hewanStats']);

        // artikel
        Route::post('/artikel', [ArtikelController::class, 'store']);
        Route::put('/artikel/{id}', [ArtikelController::class, 'update']);
        Route::delete('/artikel/{id}', [ArtikelController::class, 'destroy']);
    });
});



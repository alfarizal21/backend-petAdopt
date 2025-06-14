<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hewan;
use Illuminate\Http\Request;
use App\Models\PermohonanShelter;

class AdminController extends Controller
{
    // Statistik user
    public function userStats()
    {
        $totalUsers = User::count();
        $userCount = User::where('role', 'user')->count();
        $shelterCount = User::where('role', 'shelter')->count();
        $adminCount = User::where('role', 'admin')->count();

        return response()->json([
            'message' => 'Get user data successfully.',
            'data' => [
                'total_users' => $totalUsers,
                'user' => $userCount,
                'shelter' => $shelterCount,
                'admin' => $adminCount,
            ]
        ]);
    }

    // Statistik hewan
    public function hewanStats()
    {
        $totalHewan = Hewan::count();
        $kucingCount = Hewan::where('jenis_hewan', 'kucing')->count();
        $anjingCount = Hewan::where('jenis_hewan', 'anjing')->count();

        return response()->json([
            'message' => 'Get animal data successfully.',
            'data' => [
                'total_hewan' => $totalHewan,
                'kucing' => $kucingCount,
                'anjing' => $anjingCount,
            ]
        ]);
    }

    public function listPermohonanShelter()
    {
        $permohonan = \App\Models\PermohonanShelter::with('user')->get();

        $permohonan->transform(function ($item) {
            $item->file = $item->file
                ? asset('storage/' . $item->file)
                : null;
            return $item;
        });

        return response()->json([
            'message' => 'Get successfully',
            'data' => $permohonan
        ]);
    }

    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

        $permohonan = \App\Models\PermohonanShelter::find($id);

        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan'], 404);
        }

        $permohonan->status = $request->status;
        $permohonan->save();

        if ($request->status === 'diterima') {
            $user = $permohonan->user;
            $user->role = 'shelter';
            $user->save();
        }

        $judul = $request->status === 'diterima' ? 'Permohonan Shelter Diterima' : 'Permohonan Shelter Ditolak';
        $pesan = $request->status === 'diterima'
            ? 'Selamat! Permohonan Anda untuk menjadi shelter telah diterima.'
            : 'Maaf, permohonan Anda untuk menjadi shelter ditolak. Silakan periksa kembali file yang Anda unggah.';

        \App\Models\Notifikasi::create([
            'user_id' => $permohonan->user_id,
            'judul' => $judul,
            'pesan' => $pesan,
            'status' => 'belum dibaca',
            'send_at' => now(),
        ]);

        return response()->json([
            'message' => 'Status permohonan berhasil diperbarui',
            'status' => $request->status
        ]);
    }
}

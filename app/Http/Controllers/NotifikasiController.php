<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function getUserNotifications()
    {
        $user = Auth::user();

        $notifikasi = Notifikasi::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($notifikasi->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada notifikasi',
            ], 200);
        }

        return response()->json([
            'message' => 'Daftar notifikasi ditemukan',
            'data' => $notifikasi,
        ]);
    }

    // Menandai notifikasi sebagai dibaca
    public function markAsRead($id)
    {
        $notifikasi = Notifikasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$notifikasi) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        if ($notifikasi->status === 'dibaca') {
            return response()->json(['message' => 'Notifikasi sudah dibaca'], 200);
        }

        $notifikasi->update(['status' => 'dibaca']);

        return response()->json([
            'message' => 'Notifikasi ditandai sebagai dibaca',
            'data' => $notifikasi
        ]);
    }
}

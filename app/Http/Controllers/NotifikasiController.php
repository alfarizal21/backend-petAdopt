<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return response()->json($user->notifikasi);
    }

    public function show($id)
    {
        $notifikasi = Notifikasi::find($id);

        if (!$notifikasi) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($notifikasi);
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'user_id' => 'required|exists:users,user_id',
        //     'judul' => 'required|string|max:255',
        //     'pesan' => 'required|string',
        //     'status' => 'required|in:dibaca,belum dibaca',
        //     'send_at' => 'nullable|date',
        // ]);

        // $notifikasi = Notifikasi::create([
        //     'user_id' => $request->user_id,
        //     'judul' => $request->judul,
        //     'pesan' => $request->pesan,
        //     'status' => $request->status,
        //     'send_at' => $request->send_at,
        // ]);

        // return response()->json(['message' => 'Notifikasi berhasil ditambahkan', 'data' => $notifikasi], 201);
    }

    public function updateStatus($id)
    {
        $notifikasi = Notifikasi::find($id);

        if (!$notifikasi) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $notifikasi->update(['status' => 'dibaca']);

        return response()->json([
            'message' => 'Updated successfully',
            // 'data' => $notifikasi
        ]);
    }

    public function destroy($id)
    {
        $notifikasi = Notifikasi::find($id);

        if (!$notifikasi) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $notifikasi->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

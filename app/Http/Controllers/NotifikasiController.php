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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\PermohonanShelter;
use Illuminate\Support\Facades\Auth;

class PermohonanShelterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $user = Auth::user();
        $path = $request->file('file')->store('permohonan_shelter', 'public');

        PermohonanShelter::create([
            'user_id' => $user->id,
            'file' => $path,
            'status' => 'menunggu'
        ]);

         $adminList = User::where('role', 'admin')->get();

        foreach ($adminList as $admin) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'judul' => 'Permohonan Shelter Baru',
                'pesan' => "User {$user->nama} telah mengajukan permohonan menjadi shelter.",
                'status' => 'belum dibaca',
                'send_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Upload successfully'
        ], 201);
    }
}

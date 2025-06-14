<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        $permohonan = PermohonanShelter::create([
            'user_id' => $user->id,
            'file' => $path,
            'status' => 'menunggu'
        ]);

        return response()->json([
            'message' => 'Upload successfully'
        ], 201);
    }
}

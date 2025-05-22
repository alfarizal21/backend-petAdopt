<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    public function detailProfile()
    {
        $user = Auth::user();

        return response()->json([
            'name' => $user->name,
            'tanggal_lahir' => $user->tanggal_lahir,
            'jenis_kelamin' => $user->jenis_kelamin,
            'no_telp' => $user->no_telp,
            'email' => $user->email
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $validated['name'],
            'tgl_lahir' => $validated['tgl_lahir'] ?? $user->tgl_lahir,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? $user->jenis_kelamin,
            'no_telp' => $validated['no_telp'] ?? $user->no_telp,
            'email' => $validated['email'],
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['old_password'], $user->password)) {
            return response()->json(['message' => 'Old password not match'], 422);
        }

       $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }
}

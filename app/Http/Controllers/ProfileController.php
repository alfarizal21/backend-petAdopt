<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'name' => $user->name,
            'email' => $user->email
        ],200);
    }

    public function detailProfile()
    {
        $user = Auth::user();

        return response()->json([
            'name' => $user->name,
            'tanggal_lahir' => optional($user->tgl_lahir)->format('Y-m-d'),
            'jenis_kelamin' => $user->jenis_kelamin,
            'no_telp' => $user->no_telp,
            'email' => $user->email
        ],200);
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
        ],200);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['old_password'], $user->password)) {
            return response()->json([
                'message' => 'Old password not match'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Password updated successfully'
        ],200);
    }

    public function uploadFotoProfil(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->profile_photo && Storage::exists('public/profile_photos/' . $user->profile_photo)) {
            Storage::delete('public/profile_photos/' . $user->profile_photo);
        }

        $imagePath = $request->file('profile_photo')->store('profile_photos', 'public');
        $filename = basename($imagePath);

        $user->profile_photo = $filename;
        $user->save();

        return response()->json([
            'message' => 'Profile photo uploaded successfully.'
        ],200);
    }

    public function getFotoProfil()
    {
        $user = Auth::user();

        if (!$user->profile_photo) {
            return response()->json(['message' => 'No profile photo set in database'], 404);
        }

        $filePath = 'profile_photos/' . $user->profile_photo;

        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json([
                'message' => 'Profile photo file not found in storage',
            ], 404);
        }

        return response()->json([
            'message' => 'Get photo profile success.',
            'profile_photo' => asset('storage/' . $filePath)
        ],200);
    }

    public function deleteFotoProfil()
    {
        $user = Auth::user();

        $filePath = 'profile_photos/' . $user->profile_photo;

        if (!$user->profile_photo || !Storage::disk('public')->exists($filePath)) {
            return response()->json(['message' => 'No profile photo to delete'], 404);
        }

        // Hapus file dari storage disk 'public'
        Storage::disk('public')->delete($filePath);

        // Hapus referensi dari database
        $user->update(['profile_photo' => null]);

        return response()->json([
            'message' => 'Profile photo deleted.'
        ],200);
    }
}

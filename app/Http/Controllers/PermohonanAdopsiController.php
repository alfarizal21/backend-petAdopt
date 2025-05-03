<?php

namespace App\Http\Controllers;

use App\Models\PermohonanAdopsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermohonanAdopsiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'user') {
            $permohonan = PermohonanAdopsi::with(['user', 'hewan'])
                ->where('user_id', $user->id)
                ->get();
        }

        elseif ($user->role === 'shelter') {
            $permohonan = PermohonanAdopsi::with(['user', 'hewan'])
                ->whereHas('hewan', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->get();
        } else {
            return response()->json(['message' => "Don't have permission"], 403);
        }

        return response()->json($permohonan);
    }

    public function show($id)
    {
        $user = Auth::user();

        $permohonan = PermohonanAdopsi::with(['user', 'hewan'])->find($id);

        if (!$permohonan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($user->role === 'user' && $permohonan->user_id !== $user->id) {
            return response()->json(['message' => "Don't have permission"], 403);
        }

        if (
            $user->role === 'shelter' &&
            $permohonan->hewan &&
            $permohonan->hewan->user_id !== $user->id
        ) {
            return response()->json(['message' => "Don't have permission"], 403);
        }

        return response()->json($permohonan);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'user') {
            return response()->json(['message' => 'Only user can send'], 403);
        }

        $request->validate([
            'hewan_id' => 'required|exists:hewan,id',
            'nama' => 'required',
            'umur' => 'required|integer',
            'no_hp' => 'required|string',
            'email' => 'required|email',
            'nik' => 'required|string',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'tempat_tanggal_lahir' => 'required',
            'pekerjaan' => 'required',
            'alamat' => 'required',
            'riwayat_adopsi' => 'nullable',
        ]);

        $hewan = \App\Models\Hewan::find($request->hewan_id);

        if ($hewan->user_id === $user->id) {
            return response()->json(['message' => 'cannot send'], 403);
        }

        $permohonan = PermohonanAdopsi::create([
            'user_id' => $user->id,
            'hewan_id' => $request->hewan_id,
            'nama' => $request->nama,
            'umur' => $request->umur,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'nik' => $request->nik,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_tanggal_lahir' => $request->tempat_tanggal_lahir,
            'pekerjaan' => $request->pekerjaan,
            'alamat' => $request->alamat,
            'riwayat_adopsi' => $request->riwayat_adopsi,
            'tanggal_permohonan' => now()->toDateString(),
            'status' => 'menunggu'
        ]);

        return response()->json([
            'message' => 'Created successfully',
            // 'data' => $permohonan
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $permohonan = PermohonanAdopsi::with('hewan')->find($id);

        if (!$permohonan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $user = Auth::user();

        if (
            !($user->role === 'shelter' || $permohonan->hewan->user_id === $user->id)
        ) {
            return response()->json(['message' => 'Updated successfully'], 403);
        }

        $request->validate([
            'status' => 'required|in:diterima,menunggu,ditolak'
        ]);

        $permohonan->status = $request->status;
        $permohonan->save();

        return response()->json([
            'message' => 'Status updated successfully',
            // 'data' => $permohonan
        ]);
    }

    public function destroy($id)
    {
        $permohonan = PermohonanAdopsi::with('hewan')->find($id);

        if (!$permohonan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $user = Auth::user();

        if (
            !($user->role === 'shelter' || $permohonan->hewan->user_id === $user->id)
        ) {
            return response()->json(['message' => "Don't have permission"], 403);
        }

        $permohonan->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

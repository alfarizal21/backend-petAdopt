<?php

namespace App\Http\Controllers;

use App\Models\PermohonanAdopsi;
use App\Models\Hewan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermohonanAdopsiController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();

    //     if ($user->role === 'user') {
    //         $permohonan = PermohonanAdopsi::with(['user', 'hewan'])
    //             ->where('user_id', $user->id)
    //             ->get();
    //     }

    //     elseif ($user->role === 'shelter') {
    //         $permohonan = PermohonanAdopsi::with(['user', 'hewan'])
    //             ->whereHas('hewan', function ($query) use ($user) {
    //                 $query->where('user_id', $user->id);
    //             })
    //             ->get();
    //     } else {
    //         return response()->json(['message' => "Don't have permission"], 403);
    //     }

    //     return response()->json($permohonan);
    // }

    // public function show($id)
    // {
    //     $user = Auth::user();

    //     $permohonan = PermohonanAdopsi::with(['user', 'hewan'])->find($id);

    //     if (!$permohonan) {
    //         return response()->json(['message' => 'Not found'], 404);
    //     }

    //     if ($user->role === 'user' && $permohonan->user_id !== $user->id) {
    //         return response()->json(['message' => "Don't have permission"], 403);
    //     }

    //     if (
    //         $user->role === 'shelter' &&
    //         $permohonan->hewan &&
    //         $permohonan->hewan->user_id !== $user->id
    //     ) {
    //         return response()->json(['message' => "Don't have permission"], 403);
    //     }

    //     return response()->json($permohonan);
    // }

    public function store(Request $request) //formulir pengajuan adopsi
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
            'nik' => 'required|string|size:16|regex:/^[0-9]+$/',
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

    // public function updateStatus(Request $request, $id)
    // {
    //     $permohonan = PermohonanAdopsi::with('hewan')->find($id);

    //     if (!$permohonan) {
    //         return response()->json(['message' => 'Not found'], 404);
    //     }

    //     $user = Auth::user();

    //     if (
    //         !($user->role === 'shelter' || $permohonan->hewan->user_id === $user->id)
    //     ) {
    //         return response()->json(['message' => 'Updated successfully'], 403);
    //     }

    //     $request->validate([
    //         'status' => 'required|in:diterima,menunggu,ditolak'
    //     ]);

    //     $permohonan->status = $request->status;
    //     $permohonan->save();

    //     return response()->json([
    //         'message' => 'Status updated successfully',
    //         // 'data' => $permohonan
    //     ]);
    // }

    // public function destroy($id)
    // {
    //     $permohonan = PermohonanAdopsi::with('hewan')->find($id);

    //     if (!$permohonan) {
    //         return response()->json(['message' => 'Not found'], 404);
    //     }

    //     $user = Auth::user();

    //     if (
    //         !($user->role === 'shelter' || $permohonan->hewan->user_id === $user->id)
    //     ) {
    //         return response()->json(['message' => "Don't have permission"], 403);
    //     }

    //     $permohonan->delete();

    //     return response()->json(['message' => 'Deleted successfully']);
    // }

    public function listUserPermohonanHewan() // daftar hewan yang telah diajukan adopsinya oleh user
    {
        $user = Auth::user();

        $permohonan = PermohonanAdopsi::with(['hewan:id,nama,image'])
            ->where('user_id', $user->id)
            ->orderBy('tanggal_permohonan', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'permohonan_id' => $item->id,
                    'nama_hewan' => $item->hewan->nama,
                    'gambar_hewan' => $item->hewan->image,
                ];
            });

        return response()->json([
            'message' => 'Daftar pengajuan adopsi hewan',
            'data' => $permohonan
        ]);
    }

    public function showDetailPermohonan($id) //mengembalikan data permohonan adopsi berdasarkan id permohonan
    {
        $user = Auth::user();

        $permohonan = PermohonanAdopsi::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail permohonan ditemukan',
            'data' => [
                'nama_lengkap' => $permohonan->nama,
                'umur' => $permohonan->umur,
                'no_hp' => $permohonan->no_hp,
                'email' => $permohonan->email,
                'nik' => $permohonan->nik,
                'jenis_kelamin' => $permohonan->jenis_kelamin,
                'tempat_tanggal_lahir' => $permohonan->tempat_tanggal_lahir,
                'pekerjaan' => $permohonan->pekerjaan,
                'alamat' => $permohonan->alamat,
                'riwayat_adopsi' => $permohonan->riwayat_adopsi,
                'status' => $permohonan->status,
                'tanggal_permohonan' => $permohonan->tanggal_permohonan,
            ]
        ]);
    }

    // public function showPermohonanByHewan($hewanId) //mengembalikan data permohonan adopsi berdasarkan id hewan
    // {
    //     $user = Auth::user();

    //     $permohonan = PermohonanAdopsi::where('hewan_id', $hewanId)
    //         ->where('user_id', $user->id)
    //         ->first();

    //     if (!$permohonan) {
    //         return response()->json(['message' => 'Data tidak ditemukan'], 404);
    //     }

    //     return response()->json([
    //         'message' => 'Detail permohonan ditemukan',
    //         'data' => [
    //             'nama_lengkap' => $permohonan->nama,
    //             'umur' => $permohonan->umur,
    //             'no_hp' => $permohonan->no_hp,
    //             'email' => $permohonan->email,
    //             'nik' => $permohonan->nik,
    //             'jenis_kelamin' => $permohonan->jenis_kelamin,
    //             'tempat_tanggal_lahir' => $permohonan->tempat_tanggal_lahir,
    //             'pekerjaan' => $permohonan->pekerjaan,
    //             'alamat' => $permohonan->alamat,
    //             'riwayat_adopsi' => $permohonan->riwayat_adopsi,
    //             'status' => $permohonan->status,
    //             'tanggal_permohonan' => $permohonan->tanggal_permohonan,
    //         ]
    //     ]);
    // }

    public function listPemohonByHewan($hewanId) //mengambil daftar pemohon adopsi berdasarkan id hewan
    {
        $user = Auth::user();

        // Pastikan hewan tersebut milik user yang sedang login
        $hewan = Hewan::where('id', $hewanId)
            ->where('user_id', $user->id)
            ->first();

        if (!$hewan) {
            return response()->json(['message' => 'Hewan tidak ditemukan atau bukan milik Anda'], 404);
        }

        // Ambil daftar permohonan + user pemohon
        $permohonan = PermohonanAdopsi::where('hewan_id', $hewanId)
            ->with('user:id,name,profile_photo') // hanya ambil nama & foto
            ->get();

        // Kembalikan hanya nama dan foto user pemohon
        return response()->json([
            'message' => 'Daftar pemohon ditemukan',
            'data' => $permohonan->map(function ($item) {
                return [
                    'nama' => $item->user->name,
                    'profile_photo' => $item->user->profile_photo ? asset('storage/' . $item->user->profile_photo) : null,
                ];
            }),
        ]);
    }

    public function showDetailByHewanAndUser($hewanId, $userId) //mengambil detail permohonan adopsi berdasarkan id hewan dan id user
    {
        $authUser = Auth::user();

        // Pastikan hewan itu milik shelter yg login
        $hewan = Hewan::where('id', $hewanId)
            ->where('user_id', $authUser->id)
            ->first();

        if (!$hewan) {
            return response()->json(['message' => 'Hewan tidak ditemukan atau bukan milik Anda'], 404);
        }

        // Ambil permohonan user itu ke hewan ini
        $permohonan = PermohonanAdopsi::where('hewan_id', $hewanId)
            ->where('user_id', $userId)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Permohonan tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail permohonan ditemukan',
            'data' => [
                'nama_lengkap' => $permohonan->nama,
                'umur' => $permohonan->umur,
                'no_hp' => $permohonan->no_hp,
                'email' => $permohonan->email,
                'nik' => $permohonan->nik,
                'jenis_kelamin' => $permohonan->jenis_kelamin,
                'tempat_tanggal_lahir' => $permohonan->tempat_tanggal_lahir,
                'pekerjaan' => $permohonan->pekerjaan,
                'alamat' => $permohonan->alamat,
                'riwayat_adopsi' => $permohonan->riwayat_adopsi,
                'status' => $permohonan->status,
                'tanggal_permohonan' => $permohonan->tanggal_permohonan,
            ]
        ]);
    }
}

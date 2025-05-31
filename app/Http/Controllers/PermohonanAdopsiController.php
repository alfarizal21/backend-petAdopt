<?php

namespace App\Http\Controllers;

use App\Models\PermohonanAdopsi;
use App\Models\Hewan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermohonanAdopsiController extends Controller
{
    //formulir pengajuan adopsi
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

    // daftar hewan yang telah diajukan adopsinya oleh user
    public function listUserPermohonanHewan()
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
            'message' => 'Get successfully',
            'data' => $permohonan
        ]);
    }

    //mengembalikan data permohonan adopsi berdasarkan id permohonan
    public function showDetailPermohonan($id)
    {
        $user = Auth::user();

        $permohonan = PermohonanAdopsi::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'message' => 'Get successfully',
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

    //mengambil daftar pemohon adopsi berdasarkan id hewan
    public function listPemohonByHewan($hewanId)
    {
        $user = Auth::user();

        // Pastikan hewan tersebut milik user yang sedang login
        $hewan = Hewan::where('id', $hewanId)
            ->where('user_id', $user->id)
            ->first();

        if (!$hewan) {
            return response()->json(['message' => 'Animal not found or not owned by you.'], 404);
        }

        // Ambil daftar permohonan + user pemohon
        $permohonan = PermohonanAdopsi::where('hewan_id', $hewanId)
            ->with('user:id,name,profile_photo') // hanya ambil nama & foto
            ->get();

        // Kembalikan hanya nama dan foto user pemohon
        return response()->json([
            'message' => 'Get successfully',
            'data' => $permohonan->map(function ($item) {
                return [
                    'nama' => $item->user->name,
                    'profile_photo' => $item->user->profile_photo ? asset('storage/' . $item->user->profile_photo) : null,
                ];
            }),
        ]);
    }

    //mengambil detail permohonan adopsi berdasarkan id hewan dan id user
    public function showDetailByHewanAndUser($hewanId, $userId)
    {
        $authUser = Auth::user();

        // Pastikan hewan itu milik shelter yg login
        $hewan = Hewan::where('id', $hewanId)
            ->where('user_id', $authUser->id)
            ->first();

        if (!$hewan) {
            return response()->json(['message' => 'Animal not found or not owned by you.'], 404);
        }

        // Ambil permohonan user itu ke hewan ini
        $permohonan = PermohonanAdopsi::where('hewan_id', $hewanId)
            ->where('user_id', $userId)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'message' => 'Get successfully',
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

    //mengupdate status permohonan adopsi sekaligus mengirim notifikasi ke user yang mengajukan permohonan adopsi
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

        $permohonan = \App\Models\PermohonanAdopsi::find($id);

        if (!$permohonan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $permohonan->status = $request->status;
        $permohonan->save();

        // Kirim notifikasi ke user yang mengajukan
        $judul = $request->status === 'diterima' ? 'Permohonan Diterima' : 'Permohonan Ditolak';
        $pesan = $request->status === 'diterima'
            ? "Selamat! Permohonan adopsi Anda untuk hewan bernama {$permohonan->hewan->nama} telah diterima."
            : "Maaf, permohonan adopsi Anda untuk hewan bernama {$permohonan->hewan->nama} ditolak.";

        \App\Models\Notifikasi::create([
            'user_id' => $permohonan->user_id,
            'judul' => $judul,
            'pesan' => $pesan,
            'status' => 'belum dibaca',
            'send_at' => now(),
        ]);

        return response()->json([
            'message' => "Permohonan berhasil {$request->status}",
            'status' => $request->status
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $permohonan = PermohonanAdopsi::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($permohonan->status !== 'menunggu') {
            return response()->json(['message' => "Request can't be changed because it's already processed."], 403);
        }

        $request->validate([
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

        $permohonan->update([
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
        ]);

        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $permohonan = PermohonanAdopsi::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$permohonan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $permohonan->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

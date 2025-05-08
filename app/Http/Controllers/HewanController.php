<?php

namespace App\Http\Controllers;

use App\Models\Hewan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HewanController extends Controller
{
    public function index()
    {
        $hewan = Hewan::with('user')->get();
        return response()->json([
            'message' => 'Get successfully',
            'data' => $hewan
        ]);
    }

    public function show($id)
    {
        $hewan = Hewan::with('user')->find($id);

        if (!$hewan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'message' => 'Get successfully',
            'data' => $hewan
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'jenis' => 'required|string',
            'usia' => 'required|integer',
            'status' => 'required|in:tersedia,tidak tersedia',
            'deskripsi' => 'nullable|string'
        ]);

        $hewan = Hewan::create([
            'user_id' => Auth::id(),
            'nama' => $request->nama,
            'jenis' => $request->jenis,
            'usia' => $request->usia,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json([
            'message' => 'Added successfully',
            // 'data' => $hewan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $hewan = Hewan::find($id);

        if (!$hewan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $request->validate([
            'nama' => 'sometimes|required|string',
            'jenis' => 'sometimes|required|string',
            'usia' => 'sometimes|required|integer',
            'status' => 'sometimes|required|in:tersedia,tidak tersedia',
            'deskripsi' => 'nullable|string'
        ]);

        $hewan->update($request->only([
            'nama', 'jenis', 'usia', 'status', 'deskripsi'
        ]));

        return response()->json([
            'message' => 'Updated successfully',
            // 'data' => $hewan
        ]);
    }

    public function destroy($id)
    {
        $hewan = Hewan::find($id);

        if (!$hewan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $hewan->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function filterByJenis(Request $request, $jenis)
    {
        $jenis = strtolower($jenis);

        if (!in_array($jenis, ['kucing', 'anjing'])) {
            return response()->json([
                'message' => 'Invalid animal type. Please use "kucing" or "anjing".'
            ], 400);
        }

        $hewan = Hewan::with('user')->where('jenis', $jenis)->get();

        return response()->json([
            'message' => 'Get successfully',
            'data' => $hewan
        ]);
    }
}

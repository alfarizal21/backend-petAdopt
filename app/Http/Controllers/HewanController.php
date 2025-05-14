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

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'nama' => 'required|string',
    //         'jenis' => 'required|string',
    //         'usia' => 'required|integer',
    //         'status' => 'required|in:tersedia,tidak tersedia',
    //         'deskripsi' => 'nullable|string'
    //     ]);

    //     $hewan = Hewan::create([
    //         'user_id' => Auth::id(),
    //         'nama' => $request->nama,
    //         'jenis' => $request->jenis,
    //         'usia' => $request->usia,
    //         'status' => $request->status,
    //         'deskripsi' => $request->deskripsi
    //     ]);

    //     return response()->json([
    //         'message' => 'Added successfully',
    //         // 'data' => $hewan
    //     ], 201);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'jenis_kelamin' => 'required|in:jantan,betina',
            'warna' => 'required|string',
            'jenis_hewan' => 'required|string',
            'umur' => 'required|integer',
            'status' => 'required|in:tersedia,tidak tersedia',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('hewan_images', 'public');
        }

        $hewan = Hewan::create([
            'user_id' => Auth::id(),
            'image' => $imagePath,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'warna' => $request->warna,
            'jenis_hewan' => $request->jenis_hewan,
            'umur' => $request->umur,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json([
            'message' => 'Hewan added successfully',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $hewan = Hewan::find($id);
        if (!$hewan) {
            return response()->json(['message' => 'Hewan not found'], 404);
        }

        $request->validate([
            'nama' => 'sometimes|required|string',
            'jenis_kelamin' => 'sometimes|required|in:jantan,betina',
            'warna' => 'sometimes|required|string',
            'jenis_hewan' => 'sometimes|required|string',
            'umur' => 'sometimes|required|integer',
            'status' => 'sometimes|required|in:tersedia,tidak tersedia',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('hewan_images', 'public');
            $hewan->image = $imagePath;
        }

        $hewan->update($request->only([
            'nama', 'jenis_kelamin', 'warna', 'jenis_hewan', 'umur', 'status', 'deskripsi'
        ]));

        $hewan->save();

        return response()->json([
            'message' => 'Hewan updated successfully',
        ]);
    }

    // public function update(Request $request, $id)
    // {
    //     $hewan = Hewan::find($id);

    //     if (!$hewan) {
    //         return response()->json(['message' => 'Not found'], 404);
    //     }

    //     $request->validate([
    //         'nama' => 'sometimes|required|string',
    //         'jenis' => 'sometimes|required|string',
    //         'usia' => 'sometimes|required|integer',
    //         'status' => 'sometimes|required|in:tersedia,tidak tersedia',
    //         'deskripsi' => 'nullable|string'
    //     ]);

    //     $hewan->update($request->only([
    //         'nama', 'jenis', 'usia', 'status', 'deskripsi'
    //     ]));

    //     return response()->json([
    //         'message' => 'Updated successfully',
    //         // 'data' => $hewan
    //     ]);
    // }

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

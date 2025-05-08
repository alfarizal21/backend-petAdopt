<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use Illuminate\Http\Request;

class ArtikelController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Get successfully',
            'data' => Artikel::all()
        ]);
    }

    public function show($id)
    {
        $artikel = Artikel::find($id);

        if (!$artikel) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'message' => 'Get successfully',
            'data' => $artikel
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'tanggal_publish' => 'required|date',
        ]);

        $artikel = Artikel::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'tanggal_publish' => $request->tanggal_publish,
        ]);

        return response()->json([
            'message' => 'Created successfully',
            // 'data' => $artikel
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $artikel = Artikel::find($id);

        if (!$artikel) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'konten' => 'sometimes|required|string',
            'tanggal_publish' => 'sometimes|required|date',
        ]);

        $artikel->update($request->all());

        return response()->json([
            'message' => 'Updated successfully',
            // 'data' => $artikel
        ]);
    }

    public function destroy($id)
    {
        $artikel = Artikel::find($id);

        if (!$artikel) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $artikel->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

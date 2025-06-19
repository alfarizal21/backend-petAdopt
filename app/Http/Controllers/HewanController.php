<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hewan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HewanController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $hewan = Hewan::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        //pake asset('') biar url nya jadi lengkap(beserta http..)
        $hewan->transform(function ($item) use ($user) {
            $item->image = $item->image ? asset('storage/' . $item->image) : null;

            // cek like
            $item->liked = $item->likes()->where('user_id', $user->id)->exists();

            return $item;
        });

        return response()->json([
            'message' => 'Get successfully',
            'data' => $hewan
        ], 200);
    }

    public function show($id)
    {
        $hewan = Hewan::with('user')->find($id);

        if (!$hewan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $hewan->image = $hewan->image
            ? asset('storage/' . $hewan->image)
            : null;

        return response()->json([
            'message' => 'Get successfully',
            'data' => $hewan
        ],200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'jenis_kelamin' => 'required|in:jantan,betina',
            'warna' => 'required|string',
            'jenis_hewan' => 'required|in:anjing,kucing',
            'umur' => 'required|integer',
            'status' => 'required|in:tersedia,tidak tersedia',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'lokasi' => 'nullable|string',
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
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi
        ]);

        return response()->json([
            'message' => 'Added successfully',
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
            'jenis_kelamin' => 'sometimes|required|in:jantan,betina',
            'warna' => 'sometimes|required|string',
            'jenis_hewan' => 'sometimes|required|string',
            'umur' => 'sometimes|required|integer',
            'status' => 'sometimes|required|in:tersedia,tidak tersedia',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $hewan->fill($request->only([
            'nama',
            'jenis_kelamin',
            'warna',
            'jenis_hewan',
            'umur',
            'status',
            'deskripsi',
            'lokasi'
        ]));

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('hewan_images', 'public');
            $hewan->image = $imagePath;
        }

        $hewan->save();

        return response()->json([
            'message' => 'Updated successfully',
        ],200);
    }

    public function destroy($id)
    {
        $hewan = Hewan::find($id);

        if (!$hewan) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $hewan->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ],200);
    }

    public function filterByJenis(Request $request, $jenis)
    {
        $jenis = strtolower($jenis);

        if (!in_array($jenis, ['kucing', 'anjing'])) {
            return response()->json([
                'message' => 'Invalid animal type. Please use "kucing" or "anjing".'
            ], 400);
        }

        $hewan = Hewan::with('user')
            ->where('jenis_hewan', $jenis)
            ->get();

        $hewan->transform(function ($item) {
            $item->image = $item->image ? asset('storage/' . $item->image) : null;
            return $item;
        });

        return response()->json([
            'message' => 'Get successfully',
            'data' => $hewan
        ],200);
    }

    public function myPets()
    {
        $user = Auth::user();

        $hewans = $user->hewan()->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $hewans->transform(function ($item) {
            $item->image = $item->image
                ? asset('storage/' . $item->image)
                : null;
            return $item;
        });

        return response()->json([
            'message' => 'Get your pets successfully',
            'data' => $hewans
        ],200);
    }
}

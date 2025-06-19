<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Hewan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    // Toggle like/dislike dengan satu tombol
    public function toggleLike($hewan_id)
    {
        $user = Auth::user();

        $like = Like::where('user_id', $user->id)
                    ->where('hewan_id', $hewan_id)
                    ->first();

        if ($like) {
            $like->delete();
            return response()->json([
                'message' => 'Disliked',
                'liked' => false
            ], 200);
        }

        Like::create([
            'user_id' => $user->id,
            'hewan_id' => $hewan_id,
        ],200);

        return response()->json([
            'message' => 'Liked',
            'liked' => true
        ], 200);
    }

    public function favoriteHewan()
    {
        $hewans = Hewan::withCount('likes')
            ->orderByDesc('likes_count')
            ->take(10)
            ->get()
            ->map(function ($hewan) {
                return [
                    'image' => $hewan->image,
                    'nama' => $hewan->nama,
                    'status' => $hewan->status,
                    'likes_count' => $hewan->likes_count
                ];
            });

        return response()->json([
            'message' => 'Get successfully',
            'data' => $hewans
        ],200);
    }
}

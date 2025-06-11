<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hewan;

class AdminController extends Controller
{
    // Statistik user
    public function userStats()
    {
        $totalUsers = User::count();
        $userCount = User::where('role', 'user')->count();
        $shelterCount = User::where('role', 'shelter')->count();
        $adminCount = User::where('role', 'admin')->count();

        return response()->json([
            'message' => 'Get user data successfully.',
            'data' => [
                'total_users' => $totalUsers,
                'user' => $userCount,
                'shelter' => $shelterCount,
                'admin' => $adminCount,
            ]
        ]);
    }

    // Statistik hewan
    public function hewanStats()
    {
        $totalHewan = Hewan::count();
        $kucingCount = Hewan::where('jenis_hewan', 'kucing')->count();
        $anjingCount = Hewan::where('jenis_hewan', 'anjing')->count();

        return response()->json([
            'message' => 'Get animal data successfully.',
            'data' => [
                'total_hewan' => $totalHewan,
                'kucing' => $kucingCount,
                'anjing' => $anjingCount,
            ]
        ]);
    }
}

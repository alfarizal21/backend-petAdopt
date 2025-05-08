<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    public function detailProfile()
    {
        $user = Auth::user();

        return response()->json([
            'name' => $user->name,
            'tanggal_lahir' => $user->tanggal_lahir,
            'jenis_kelamin' => $user->jenis_kelamin,
            'no_telp' => $user->no_telp,
            'email' => $user->email
        ]);
    }
}

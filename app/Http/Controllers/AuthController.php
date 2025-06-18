<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'role'      => 'user', // default
            'no_telp'   => $request->no_telp,
            'tgl_lahir' => $request->tgl_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return response()->json([
            // 'user'  => $user
            'message' => 'Register successfully',
        ],201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email not found or password incorrect.'],
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successfully',
            'data' => [
                'token' => $token
            ]
        ],200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout Successfully']);
    }
}

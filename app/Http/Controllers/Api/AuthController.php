<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'member',
        ]);

        return response()->json(['success' => true, 'message' => 'Register berhasil', 'data' => $user], 201);
    }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (!$token = Auth::guard('api')->attempt($request->only('email', 'password'))) {
            return response()->json(['success' => false, 'message' => 'Email atau password salah'], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user'    => Auth::guard('api')->user(),
            'token'   => $token,
        ]);
    }

    public function me()
    {
        return response()->json(['success' => true, 'data' => Auth::guard('api')->user()]);
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['success' => true, 'message' => 'Logout berhasil']);
    }
}

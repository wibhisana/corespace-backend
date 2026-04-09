<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input dari Mobile App
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required', // Penting untuk nama token (misal: "Budi's iPhone")
        ]);

        // 2. Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // 3. Cek password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kredensial tidak valid. Silakan periksa Email dan Password Anda.'
            ], 401);
        }

        // 4. Generate Sanctum Token
        $token = $user->createToken($request->device_name)->plainTextToken;

        // 5. Kembalikan response sukses beserta token dan data user
        return response()->json([
            'status' => 'success',
            'message' => 'Login Berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'job_title' => $user->job_title,
                // Anda bisa menambahkan data lain yang dibutuhkan mobile app di sini
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout Berhasil'
        ], 200);
    }
}

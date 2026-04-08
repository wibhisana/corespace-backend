<?php

namespace App\Modules\IAM\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuthLog; // <-- Panggil model CCTV-nya
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // SKENARIO 1: JIKA LOGIN GAGAL (User tidak ada ATAU password salah)
        if (! $user || ! Hash::check($request->password, $user->password)) {

            // Catat CCTV DULU sebelum melempar error
            AuthLog::create([
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'FAILED',
                'message' => 'Kredensial tidak valid (Salah password/email)'
            ]);

            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        // Hapus token lama agar tidak menumpuk (opsional, tergantung kebijakan sesi Anda)
        $user->tokens()->delete();

        // Buat token baru untuk Vue.js
        $token = $user->createToken('vue-client-token')->plainTextToken;

        // SKENARIO 2: JIKA LOGIN SUKSES
        // Catat CCTV DULU sebelum mengirim respons ke Frontend
        AuthLog::create([
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'SUCCESS',
            'message' => 'Login berhasil'
        ]);

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Mengambil nama departemen dan role sekaligus
                'department' => $user->department ? $user->department->name : null,
                'roles' => $user->getRoleNames(),
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}

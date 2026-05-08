<?php

namespace App\Modules\IAM\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\IAM\Models\AuthLog;
use Carbon\Carbon;
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
            'device_name' => 'required|string',
            'remember_me' => 'sometimes|boolean',
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

        // Revoke token lama. Hidrasi → loop → delete() agar event `deleted`
        // ter-fire dan cache Redis ikut dibersihkan; `tokens()->delete()` lewat
        // query builder akan melewati model events.
        $user->tokens->each->delete();

        // Remember me: 30 hari, default 60 menit.
        $ttlMinutes = $request->boolean('remember_me') ? 60 * 24 * 30 : 60;
        $expiresAt = now()->addMinutes($ttlMinutes);

        $token = $user->createToken($request->device_name, ['*'], $expiresAt)->plainTextToken;

        // SKENARIO 2: JIKA LOGIN SUKSES
        // Catat CCTV DULU sebelum mengirim respons ke Frontend
        AuthLog::create([
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'SUCCESS',
            'message' => 'Login berhasil'
        ]);

        // Muat relasi yang sama persis dengan endpoint /me agar frontend
        // bisa fallback ke payload login ketika /me gagal.
        $user->load('department', 'roles', 'employeeFinance');

        $hasClockedIn = $user->attendances()
            ->where('date', Carbon::today()->toDateString())
            ->where('type', 'in')
            ->exists();

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_expires_at' => $expiresAt->toIso8601String(),
            'user' => $user,
            'is_profile_complete' => $user->isProfileComplete(),
            'attendance_status' => [
                'has_clocked_in' => $hasClockedIn,
                'reminder_message' => $hasClockedIn ? null : 'Anda belum melakukan absensi hari ini.',
            ],
            'unread_notifications' => $user->unreadNotifications,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('department', 'roles', 'employeeFinance');

        $hasClockedIn = $user->attendances()
            ->where('date', Carbon::today()->toDateString())
            ->where('type', 'in')
            ->exists();

        return response()->json([
            'user' => $user,
            'is_profile_complete' => $user->isProfileComplete(),
            'attendance_status' => [
                'has_clocked_in' => $hasClockedIn,
                'reminder_message' => $hasClockedIn ? null : 'Anda belum melakukan absensi hari ini.',
            ],
            'unread_notifications' => $user->unreadNotifications,
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

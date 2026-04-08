<?php

namespace App\Modules\IAM\Controllers\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class PasswordResetController extends Controller
{
    // STEP 1: Mengirim Email Link Reset
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(64);

        // Simpan token ke tabel password_reset_tokens bawaan Laravel
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );

        $user = User::where('email', $request->email)->first();
        $user->notify(new ResetPasswordNotification($token));

        return response()->json(['message' => 'Link reset password telah dikirim ke email Anda.']);
    }

    // STEP 2: Eksekusi Ganti Password Baru
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Cek apakah token valid
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Token tidak valid atau sudah kedaluwarsa.'], 400);
        }

        // Update Password
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => $request->password]);

        // Hapus token agar tidak bisa dipakai lagi
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password berhasil diperbarui. Silakan login kembali.']);
    }
}

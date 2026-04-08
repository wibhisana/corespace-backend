<?php

use App\Modules\IAM\Controllers\AuthController;
use App\Modules\HRIS\Controllers\AttendanceApiController;
use App\Modules\HRIS\Controllers\LeaveRequestApiController;
use App\Modules\HRIS\Controllers\PayrollApiController;
use App\Modules\IAM\Controllers\Auth\PasswordResetController;
use App\Modules\HRIS\Controllers\WhatsAppBotController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon; // <-- TAMBAHAN: Wajib ada karena kita pakai Carbon di rute /me

// ==========================================
// RUTE PUBLIK (Tidak Perlu Token)
// ==========================================
Route::post('/login', [AuthController::class, 'login']);

// Lupa & Reset Password
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// Webhook Bot WhatsApp (Ditembak oleh Fonnte/Penyedia WA)
Route::post('/webhook/whatsapp', [WhatsAppBotController::class, 'handleWebhook']);

// Trigger Pengingat Absensi (Ditembak oleh Cron Job / Postman)
Route::post('/attendances/remind', [AttendanceApiController::class, 'checkMissingAttendance']);


// ==========================================
// RUTE TERLINDUNGI (Wajib Bawa Token Sanctum)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Endpoint Profil Karyawan
    Route::get('/me', function (Request $request) {
        // Load relasi department & roles
        $user = $request->user()->load('department', 'roles');

        // Cek status absen hari ini
        $hasClockedIn = $user->attendances()->where('date', Carbon::today()->toDateString())->exists();

        return response()->json([
            'user' => $user,
            'attendance_status' => [
                'has_clocked_in' => $hasClockedIn,
                'reminder_message' => $hasClockedIn ? null : "Anda belum melakukan absensi hari ini."
            ],
            'unread_notifications' => $user->unreadNotifications
        ]);
    });

    // Rute Absensi
    Route::post('/attendances/clock-in', [AttendanceApiController::class, 'clockIn']);
    Route::post('/attendances/clock-out', [AttendanceApiController::class, 'clockOut']);
    Route::get('/attendances/history', [AttendanceApiController::class, 'index']);

    // Rute Cuti
    Route::get('/leaves', [LeaveRequestApiController::class, 'index']);
    Route::post('/leaves', [LeaveRequestApiController::class, 'store']);

    // Rute Slip Gaji
    Route::get('/payrolls', [PayrollApiController::class, 'index']);

    // Rute Manajemen Log
    Route::get('/logs/auth', [\App\Modules\IAM\Controllers\LogApiController::class, 'index']);
    Route::delete('/logs/auth/clear', [\App\Modules\IAM\Controllers\LogApiController::class, 'clearLogs']);
});

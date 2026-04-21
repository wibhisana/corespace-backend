<?php

use App\Modules\IAM\Controllers\AuthController;
use App\Modules\IAM\Controllers\UserManagementController;
use App\Modules\IAM\Controllers\ProfileController;
use App\Modules\HRIS\Controllers\AttendanceApiController;
use App\Modules\HRIS\Controllers\LeaveRequestApiController;
use App\Modules\HRIS\Controllers\PayrollApiController;
use App\Modules\IAM\Controllers\Auth\PasswordResetController;
use App\Modules\HRIS\Controllers\WhatsAppBotController;
use App\Http\Controllers\Api\RoomBookingController;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        $user = $request->user()->load('department', 'roles', 'employeeFinance');

        // Cek status absen hari ini (event-log: hanya baris type='in' yang dihitung sebagai Clock In)
        $hasClockedIn = $user->attendances()
            ->where('date', Carbon::today()->toDateString())
            ->where('type', 'in')
            ->exists();

        return response()->json([
            'user' => $user,
            'is_profile_complete' => $user->isProfileComplete(),
            'attendance_status' => [
                'has_clocked_in' => $hasClockedIn,
                'reminder_message' => $hasClockedIn ? null : "Anda belum melakukan absensi hari ini."
            ],
            'unread_notifications' => $user->unreadNotifications
        ]);
    });

    // Endpoint ESS: Karyawan lengkapi/update profil sendiri
    Route::put('/profile', [ProfileController::class, 'update']);

    // Rute Absensi
    Route::post('/attendances/clock-in', [AttendanceApiController::class, 'clockIn'])
        ->middleware('block.vpn');
    Route::post('/attendances/clock-out', [AttendanceApiController::class, 'clockOut'])
        ->middleware('block.vpn');
    Route::get('/attendances/history', [AttendanceApiController::class, 'index']);

    // Rute Cuti
    Route::get('/leaves', [LeaveRequestApiController::class, 'index']);
    Route::post('/leaves', [LeaveRequestApiController::class, 'store']);

    // Rute Slip Gaji
    Route::get('/payrolls', [PayrollApiController::class, 'index']);

    // ==========================================
    // RUTE MEETING ROOM BOOKING (Karyawan)
    // ==========================================
    // Mendapatkan daftar ruangan (Hanya ruangan yang aktif)
    Route::get('/meeting-rooms', function() {
        return response()->json([
            'data' => \App\Models\MeetingRoom::with('location')->where('is_active', true)->get()
        ]);
    });

    // Rute untuk Submit Booking (Cek bentrok ada di controller ini)
    Route::post('/room-bookings', [RoomBookingController::class, 'store']);

    // Rute untuk melihat riwayat booking milik user yang sedang login
    Route::get('/room-bookings/history', function(Request $request) {
        $bookings = \App\Models\RoomBooking::with(['meetingRoom.location'])
            ->where('user_id', $request->user()->id)
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json(['data' => $bookings]);
    });

    // Rute Manajemen Log
    Route::get('/logs/auth', [\App\Modules\IAM\Controllers\LogApiController::class, 'index']);
    Route::delete('/logs/auth/clear', [\App\Modules\IAM\Controllers\LogApiController::class, 'clearLogs']);

    // ==========================================
    // RUTE USER MANAGEMENT (Admin / Super Admin)
    // ==========================================
    Route::middleware('role:Super Admin|Admin')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::post('/users', [UserManagementController::class, 'store']);
        Route::post('/users/import', [UserManagementController::class, 'import']);
        Route::get('/users/{user}', [UserManagementController::class, 'show']);
        Route::put('/users/{user}', [UserManagementController::class, 'update']);
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);
    });
});

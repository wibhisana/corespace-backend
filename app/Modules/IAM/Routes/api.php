<?php

use App\Modules\IAM\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Modules\HRIS\Controllers\AttendanceApiController;
use App\Modules\HRIS\Controllers\LeaveRequestApiController;
use App\Modules\HRIS\Controllers\PayrollApiController;
use App\Modules\IAM\Controllers\Auth\PasswordResetController;
use App\Modules\HRIS\Controllers\WhatsAppBotController;

// Rute Publik (Tidak perlu token)
Route::post('/login', [AuthController::class, 'login']);

// Rute ini tidak boleh dalam middleware 'auth:sanctum' karena user sedang tidak login
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// Endpoint ini akan "ditembak" oleh Provider WA setiap ada pesan masuk
Route::post('/webhook/whatsapp', [WhatsAppBotController::class, 'handleWebhook']);

// Rute Terlindungi (Wajib bawa token dari Vue.js)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Endpoint untuk mengambil profil karyawan yang sedang login
    Route::get('/me', function (Request $request) {
        $user = $request->user()->load('department', 'roles');
        return response()->json($user);
    });

    // rute Absensi
    Route::post('/attendances/clock-in', [AttendanceApiController::class, 'clockIn']);
    Route::post('/attendances/clock-out', [AttendanceApiController::class, 'clockOut']);
    Route::get('/attendances/history', [AttendanceApiController::class, 'index']);

    // rute Cuti
    Route::get('/leaves', [LeaveRequestApiController::class, 'index']);
    Route::post('/leaves', [LeaveRequestApiController::class, 'store']);

    // rute Slip Gaji
    Route::get('/payrolls', [PayrollApiController::class, 'index']);
});

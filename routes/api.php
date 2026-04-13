<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveRequestApiController;
use App\Modules\HRIS\Controllers\WhatsAppBotController;

# PUBLIC ROUTES (Tidak butuh token)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

// 💡 Rute Webhook WA Bot Fonnte
Route::post('/webhook/whatsapp', [WhatsAppBotController::class, 'handleWebhook']);

# PROTECTED ROUTES (Wajib bawa Bearer Token)
Route::middleware('auth:sanctum')->group(function () {

    // Cek User Profile
    Route::get('/user', function (Request $request) {
        return response()->json(['data' => $request->user()->load(['unit', 'department'])]);
    });

    // Absensi (Clock-in)
    Route::post('/attendance/clock-in', [AttendanceApiController::class, 'clockIn'])->middleware('throttle:clock-in');

    // Leave Request (Mobile)
    Route::post('/leave-request', [LeaveRequestApiController::class, 'store']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

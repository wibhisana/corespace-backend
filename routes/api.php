<?php

/*
|--------------------------------------------------------------------------
| LEGACY API ROUTES — DEPRECATED
|--------------------------------------------------------------------------
| URL di file ini dipertahankan sementara demi backward compatibility
| dengan mobile/web client yang belum migrasi ke rute modular.
|
| Target baru:
|   - /api/iam/*   -> app/Modules/IAM/Routes/api.php
|   - /api/hris/*  -> app/Modules/HRIS/Routes/api.php
|
| Semua rute di bawah ini (kecuali /login & /logout legacy) sudah di-repoint
| ke controller modul. /login & /logout legacy tetap memakai LegacyAuthController
| karena response shape-nya (`token`, `job_title`) berbeda dengan versi modul
| (`access_token`, `department`, `roles`) — menghindari breaking change di mobile.
|
| TODO: hapus file ini setelah seluruh client migrate ke rute modular.
| Target deprecation: <isi tanggal>.
*/

use App\Http\Controllers\Api\AuthController as LegacyAuthController;
use App\Modules\HRIS\Controllers\AttendanceApiController;
use App\Modules\HRIS\Controllers\LeaveRequestApiController;
use App\Modules\HRIS\Controllers\WhatsAppBotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Legacy auth — tetap ke controller legacy untuk preserve response shape
Route::post('/login', [LegacyAuthController::class, 'login'])->middleware('throttle:login');
Route::post('/logout', [LegacyAuthController::class, 'logout'])->middleware('auth:sanctum');

// Legacy webhook — repoint ke modul (behavior sama)
Route::post('/webhook/whatsapp', [WhatsAppBotController::class, 'handleWebhook']);

// Legacy protected routes — repoint ke modul
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json(['data' => $request->user()->load(['unit', 'department'])]);
    });

    Route::post('/attendance/clock-in', [AttendanceApiController::class, 'clockIn'])
        ->middleware('throttle:clock-in');

    Route::post('/leave-request', [LeaveRequestApiController::class, 'store']);
});

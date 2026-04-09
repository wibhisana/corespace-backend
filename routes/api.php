<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\AuthController; // 💡 Import AuthController

# PUBLIC ROUTES (Tidak butuh token)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');


# PROTECTED ROUTES (Wajib bawa Bearer Token)
Route::middleware('auth:sanctum')->group(function () {

    // Cek User Profile
    Route::get('/user', function (Request $request) {
        return response()->json(['data' => $request->user()->load(['unit', 'department'])]);
    });

    // Absensi (Clock-in)
    Route::post('/attendance/clock-in', [AttendanceApiController::class, 'clockIn'])->middleware('throttle:clock-in');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

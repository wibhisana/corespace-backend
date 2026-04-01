<?php

use App\Modules\IAM\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Rute Publik (Tidak perlu token)
Route::post('/login', [AuthController::class, 'login']);

// Rute Terlindungi (Wajib bawa token dari Vue.js)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Endpoint untuk mengambil profil karyawan yang sedang login
    Route::get('/me', function (Request $request) {
        $user = $request->user()->load('department', 'roles');
        return response()->json($user);
    });
});

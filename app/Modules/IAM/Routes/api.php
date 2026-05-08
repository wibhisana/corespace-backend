<?php

use App\Modules\IAM\Controllers\Auth\PasswordResetController;
use App\Modules\IAM\Controllers\AuthController;
use App\Modules\IAM\Controllers\LogApiController;
use App\Modules\IAM\Controllers\ProfileController;
use App\Modules\IAM\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// ==========================================
// PUBLIC
// ==========================================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// ==========================================
// PROTECTED
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profil diri
    Route::get('/me', [AuthController::class, 'me']);

    // ESS: update profil sendiri
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/change-password', [ProfileController::class, 'changePassword']);

    // Audit log — hanya super_admin (security data)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/logs/auth', [LogApiController::class, 'index']);
        Route::delete('/logs/auth/clear', [LogApiController::class, 'clearLogs']);
    });

    // User management — super_admin + hr_manager
    // (Shield grant: hr_manager punya View/Update User; Create/Delete tetap
    // sebaiknya dibatasi level policy/permission jika diperlukan granular.)
    Route::middleware('role:super_admin|hr_manager')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::post('/users', [UserManagementController::class, 'store']);
        Route::post('/users/import', [UserManagementController::class, 'import']);
        Route::get('/users/{user}', [UserManagementController::class, 'show']);
        Route::put('/users/{user}', [UserManagementController::class, 'update']);
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);
    });
});

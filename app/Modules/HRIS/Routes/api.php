<?php

use App\Modules\HRIS\Controllers\AnalyticsController;
use App\Modules\HRIS\Controllers\ApprovalController;
use App\Modules\HRIS\Controllers\AttendanceApiController;
use App\Modules\HRIS\Controllers\EssController;
use App\Modules\HRIS\Controllers\LeaveRequestApiController;
use App\Modules\HRIS\Controllers\PayrollApiController;
use App\Modules\HRIS\Controllers\RoomBookingController;
use App\Modules\HRIS\Controllers\WhatsAppBotController;
use App\Modules\HRIS\Models\MeetingRoom;
use App\Modules\HRIS\Models\RoomBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================================
// PUBLIC (webhook + cron trigger)
// ==========================================
Route::post('/webhook/whatsapp', [WhatsAppBotController::class, 'handleWebhook']);
Route::post('/attendances/remind', [AttendanceApiController::class, 'checkMissingAttendance']);

// ==========================================
// PROTECTED (auth:sanctum)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Attendance ---
    Route::post('/attendances/clock-in', [AttendanceApiController::class, 'clockIn'])
        ->middleware(['block.vpn', 'throttle:clock-in']);
    Route::post('/attendances/clock-out', [AttendanceApiController::class, 'clockOut'])
        ->middleware(['block.vpn', 'throttle:clock-in']);
    Route::get('/attendances/history', [AttendanceApiController::class, 'index']);

    // --- Leave ---
    Route::get('/leaves', [LeaveRequestApiController::class, 'index']);
    Route::post('/leaves', [LeaveRequestApiController::class, 'store']);

    // --- ESS lookup (mobile) ---
    Route::get('/leave-types', [EssController::class, 'leaveTypes']);
    Route::get('/leave-balances/me', [EssController::class, 'myLeaveBalances']);

    // --- Payroll ---
    Route::get('/payrolls', [PayrollApiController::class, 'index']);

    // --- Meeting Rooms / Bookings ---
    Route::get('/meeting-rooms', function () {
        return response()->json([
            'data' => MeetingRoom::with('location')->where('is_active', true)->get(),
        ]);
    });
    Route::post('/room-bookings', [RoomBookingController::class, 'store']);
    Route::get('/room-bookings/history', function (Request $request) {
        $bookings = RoomBooking::with(['meetingRoom.location'])
            ->where('user_id', $request->user()->id)
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json(['data' => $bookings]);
    });

    // --- Analytics (role-scoping dilakukan di controller) ---
    Route::prefix('analytics')->group(function () {
        Route::get('/stats', [AnalyticsController::class, 'getStats']);
        Route::get('/departments', [AnalyticsController::class, 'getDepartmentChart']);
    });

    // --- Approvals (MSS) ---
    // Hanya manajer ke atas yang boleh masuk; scoping per-item di controller.
    Route::middleware('role:super_admin|hr_manager|manager')
        ->prefix('approvals')
        ->group(function () {
            Route::get('/summary', [ApprovalController::class, 'summary']);

            Route::get('/leaves', [ApprovalController::class, 'leavesIndex']);
            Route::post('/leaves/{id}/approve', [ApprovalController::class, 'leavesApprove'])
                ->whereNumber('id');
            Route::post('/leaves/{id}/reject', [ApprovalController::class, 'leavesReject'])
                ->whereNumber('id');

            Route::get('/overtimes', [ApprovalController::class, 'overtimesIndex']);
            Route::post('/overtimes/{id}/approve', [ApprovalController::class, 'overtimesApprove'])
                ->whereNumber('id');
            Route::post('/overtimes/{id}/reject', [ApprovalController::class, 'overtimesReject'])
                ->whereNumber('id');
        });
});

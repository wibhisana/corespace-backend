<?php

namespace App\Modules\HRIS\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\HRIS\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    /**
     * Endpoint untuk Stats Overview (Total Karyawan, Hadir, Cuti)
     */
    public function getStats(Request $request): JsonResponse
    {
        $user = $request->user();
        $isGlobalViewer = $user->hasAnyRole(['super_admin', 'hr_manager']);

        $baseQuery = User::query()->where('employment_status', '!=', 'Resigned');

        if (! $isGlobalViewer) {
            $baseQuery->where('department_id', $user->department_id);
        }

        $totalEmployees = (clone $baseQuery)->count();

        // Simulasi data
        $presentToday = 0;
        $onLeaveToday = 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'scope' => $isGlobalViewer ? 'Seluruh perusahaan' : 'Di departemen Anda',
                'stats' => [
                    'total_active_employees' => $totalEmployees,
                    'present_today' => $presentToday,
                    'on_leave_today' => $onLeaveToday,
                ]
            ]
        ]);
    }

    /**
     * Endpoint untuk Grafik Donat (Persebaran Departemen)
     */
    public function getDepartmentChart(Request $request): JsonResponse
    {
        $user = $request->user();

        // Guard: Jika bukan HR/Admin, kembalikan response 403 (Forbidden)
        if (! $user->hasAnyRole(['super_admin', 'hr_manager'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke grafik ini.'
            ], 403);
        }

        $departments = Department::withCount('users')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'labels' => $departments->pluck('name'),
                'values' => $departments->pluck('users_count'),
            ]
        ]);
    }
}

<?php

namespace App\Modules\HRIS\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Models\LeaveBalance;
use App\Modules\HRIS\Models\LeaveType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EssController extends Controller
{
    /**
     * GET /api/hris/leave-balances/me
     * Semua bucket saldo cuti yang masih AKTIF untuk karyawan yang sedang login.
     * Termasuk carry-forward dari tahun sebelumnya yang belum hangus.
     *
     * Tidak filter per `year` karena bucket carry-forward punya `year` tahun lalu
     * tapi tetap valid dipakai (lihat kolom expires_at).
     */
    public function myLeaveBalances(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = Carbon::today();

        $buckets = LeaveBalance::query()
            ->with('leaveType:id,name,is_unlimited,requires_attachment')
            ->where('user_id', $user->id)
            ->active($today)
            ->orderBy('leave_type_id')
            ->orderByRaw('expires_at IS NULL ASC, expires_at ASC')
            ->get();

        // Detail per-bucket — penting untuk mobile menampilkan urgensi
        // ("3 hari carry-forward kedaluwarsa 31 Mar").
        $details = $buckets->map(function (LeaveBalance $b) {
            return [
                'balance_id'      => (int) $b->id,
                'leave_type_id'   => (int) $b->leave_type_id,
                'leave_type'      => $b->leaveType?->name,
                'is_unlimited'    => (bool) ($b->leaveType?->is_unlimited),
                'year'            => (int) $b->year,
                'source'          => (string) $b->source,
                'total_quota'     => (int) $b->total_quota,
                'used_quota'      => (int) $b->used_quota,
                'remaining_quota' => (int) $b->remaining_quota,
                'expires_at'      => $b->expires_at?->toDateString(),
                'notes'           => $b->notes,
            ];
        });

        // Aggregate per leave_type — biar mobile bisa langsung tampilkan
        // "Sisa cuti tahunan: 12 hari" tanpa loop bucket.
        $summary = $buckets
            ->groupBy('leave_type_id')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'leave_type_id' => (int) $first->leave_type_id,
                    'leave_type'    => $first->leaveType?->name,
                    'is_unlimited'  => (bool) ($first->leaveType?->is_unlimited),
                    'remaining_quota' => (int) $group->sum(fn (LeaveBalance $b) => $b->remaining_quota),
                ];
            })
            ->values();

        return response()->json([
            'as_of'   => $today->toDateString(),
            'summary' => $summary,
            'buckets' => $details,
        ]);
    }

    /**
     * GET /api/hris/leave-types
     * Daftar tipe cuti aktif untuk dropdown form pengajuan.
     */
    public function leaveTypes(): JsonResponse
    {
        $types = LeaveType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'default_quota', 'is_unlimited', 'requires_attachment']);

        return response()->json([
            'data' => $types,
        ]);
    }
}

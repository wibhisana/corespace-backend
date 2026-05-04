<?php

namespace App\Modules\HRIS\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\HRIS\Exceptions\InsufficientLeaveBalanceException;
use App\Modules\HRIS\Models\LeaveRequest;
use App\Modules\HRIS\Models\LeaveType;
use App\Modules\HRIS\Models\OvertimeRequest;
use App\Modules\HRIS\Services\LeaveBalanceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    /**
     * Scope query ke pengajuan yang boleh dilihat/diproses approver.
     * - super_admin / hr_manager: lihat semua.
     * - manager: lihat pengajuan dari karyawan satu departemen ATAU subordinate
     *   langsung (manager_id = approver->id). Pengajuan milik approver sendiri
     *   di-exclude (tidak boleh approve diri sendiri).
     *
     * Dipakai di model LeaveRequest & OvertimeRequest yang keduanya punya
     * relasi `user()`.
     */
    private function scopeForApprover(Builder $query, User $approver): Builder
    {
        if ($approver->hasAnyRole(['super_admin', 'hr_manager'])) {
            return $query;
        }

        return $query
            ->where('user_id', '!=', $approver->id)
            ->whereHas('user', function (Builder $q) use ($approver) {
                $q->where(function (Builder $w) use ($approver) {
                    $w->where('department_id', $approver->department_id)
                      ->orWhere('manager_id', $approver->id);
                });
            });
    }

    private function scopeLabel(User $approver): string
    {
        return $approver->hasAnyRole(['super_admin', 'hr_manager'])
            ? 'global'
            : 'department+subordinates';
    }

    // =========================================================
    // LEAVES
    // =========================================================

    public function leavesIndex(Request $request): JsonResponse
    {
        $approver = $request->user();
        $status = $request->query('status', 'pending');

        $query = LeaveRequest::query()
            ->with(['user:id,name,email,department_id,manager_id', 'leaveType:id,name'])
            ->where('status', $status)
            ->latest('created_at');

        $query = $this->scopeForApprover($query, $approver);

        return response()->json([
            'scope' => $this->scopeLabel($approver),
            'data' => $query->get(),
        ]);
    }

    public function leavesApprove(Request $request, int $id, LeaveBalanceService $balanceService): JsonResponse
    {
        $leave = $this->findScopedLeave($request, $id);
        if (! $leave) {
            return $this->outOfScopeResponse();
        }

        if ($leave->status !== 'pending') {
            return response()->json([
                'message' => "Pengajuan sudah di-{$leave->status} sebelumnya.",
            ], 409);
        }

        // Cuti unlimited (mis. melahirkan, berduka) tidak dipotong saldo.
        $leaveType = LeaveType::find($leave->leave_type_id);
        $shouldDeduct = $leaveType && ! $leaveType->is_unlimited;

        try {
            DB::transaction(function () use ($leave, $request, $balanceService, $shouldDeduct) {
                if ($shouldDeduct) {
                    // FIFO: bucket dengan expires_at terdekat (carry_forward)
                    // dilumat duluan, baru annual.
                    $balanceService->deduct(
                        userId:      (int) $leave->user_id,
                        leaveTypeId: (int) $leave->leave_type_id,
                        days:        (int) $leave->total_days,
                    );
                }

                $leave->status         = 'approved';
                $leave->approved_by    = $request->user()->id;
                $leave->approved_at    = Carbon::now();
                $leave->rejection_note = null;
                $leave->save();
            });
        } catch (InsufficientLeaveBalanceException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Pengajuan cuti disetujui.',
            'data' => $leave->fresh(['user:id,name', 'leaveType:id,name', 'approver:id,name']),
        ]);
    }

    public function leavesReject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $leave = $this->findScopedLeave($request, $id);
        if (! $leave) {
            return $this->outOfScopeResponse();
        }

        if ($leave->status !== 'pending') {
            return response()->json([
                'message' => "Pengajuan sudah di-{$leave->status} sebelumnya.",
            ], 409);
        }

        $leave->status = 'rejected';
        $leave->approved_by = $request->user()->id;
        $leave->approved_at = Carbon::now();
        $leave->rejection_note = $request->input('reason');
        $leave->save();

        return response()->json([
            'message' => 'Pengajuan cuti ditolak.',
            'data' => $leave->fresh(['user:id,name', 'leaveType:id,name', 'approver:id,name']),
        ]);
    }

    // =========================================================
    // OVERTIMES
    // =========================================================

    public function overtimesIndex(Request $request): JsonResponse
    {
        $approver = $request->user();
        $status = $request->query('status', 'pending');

        $query = OvertimeRequest::query()
            ->with(['user:id,name,email,department_id,manager_id'])
            ->where('status', $status)
            ->latest('created_at');

        $query = $this->scopeForApprover($query, $approver);

        return response()->json([
            'scope' => $this->scopeLabel($approver),
            'data' => $query->get(),
        ]);
    }

    public function overtimesApprove(Request $request, int $id): JsonResponse
    {
        $overtime = $this->findScopedOvertime($request, $id);
        if (! $overtime) {
            return $this->outOfScopeResponse();
        }

        if ($overtime->status !== 'pending') {
            return response()->json([
                'message' => "Pengajuan sudah di-{$overtime->status} sebelumnya.",
            ], 409);
        }

        $overtime->status = 'approved';
        $overtime->approved_by = $request->user()->id;
        $overtime->rejection_note = null;
        $overtime->save();

        return response()->json([
            'message' => 'Pengajuan lembur disetujui.',
            'data' => $overtime->fresh(['user:id,name', 'approver:id,name']),
        ]);
    }

    public function overtimesReject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $overtime = $this->findScopedOvertime($request, $id);
        if (! $overtime) {
            return $this->outOfScopeResponse();
        }

        if ($overtime->status !== 'pending') {
            return response()->json([
                'message' => "Pengajuan sudah di-{$overtime->status} sebelumnya.",
            ], 409);
        }

        $overtime->status = 'rejected';
        $overtime->approved_by = $request->user()->id;
        $overtime->rejection_note = $request->input('reason');
        $overtime->save();

        return response()->json([
            'message' => 'Pengajuan lembur ditolak.',
            'data' => $overtime->fresh(['user:id,name', 'approver:id,name']),
        ]);
    }

    // =========================================================
    // SUMMARY (badge counter untuk dashboard manager)
    // =========================================================

    public function summary(Request $request): JsonResponse
    {
        $approver = $request->user();

        $pendingLeaves = $this->scopeForApprover(
            LeaveRequest::query()->where('status', 'pending'),
            $approver
        )->count();

        $pendingOvertimes = $this->scopeForApprover(
            OvertimeRequest::query()->where('status', 'pending'),
            $approver
        )->count();

        return response()->json([
            'scope' => $this->scopeLabel($approver),
            'data' => [
                'pending_leaves' => $pendingLeaves,
                'pending_overtimes' => $pendingOvertimes,
                'total_pending' => $pendingLeaves + $pendingOvertimes,
            ],
        ]);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    private function findScopedLeave(Request $request, int $id): ?LeaveRequest
    {
        return $this->scopeForApprover(
            LeaveRequest::query()->whereKey($id),
            $request->user()
        )->first();
    }

    private function findScopedOvertime(Request $request, int $id): ?OvertimeRequest
    {
        return $this->scopeForApprover(
            OvertimeRequest::query()->whereKey($id),
            $request->user()
        )->first();
    }

    private function outOfScopeResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Pengajuan tidak ditemukan atau di luar lingkup approval Anda.',
        ], 404);
    }
}

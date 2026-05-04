<?php

namespace App\Modules\HRIS\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Models\LeaveRequest;
use App\Modules\HRIS\Models\LeaveType;
use App\Modules\HRIS\Services\LeaveBalanceService;
use App\Notifications\NewLeaveRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveRequestApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $leaves = LeaveRequest::where('user_id', $request->user()->id)
            ->with('leaveType')
            ->latest('created_at')
            ->get();

        return response()->json([
            'message' => 'Riwayat pengajuan cuti berhasil diambil',
            'data' => $leaves,
        ]);
    }

    public function store(Request $request, LeaveBalanceService $balanceService): JsonResponse
    {
        $validated = $request->validate([
            'leave_type_id' => 'required|integer|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = $request->user();

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $totalDays = 0;
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if ($d->isWeekday()) {
                $totalDays++;
            }
        }

        // Pre-check saldo (cepat-fail untuk UX). Decrement sebenarnya
        // terjadi di ApprovalController saat di-approve.
        $leaveType = LeaveType::find($validated['leave_type_id']);
        if ($leaveType && ! $leaveType->is_unlimited) {
            $available = $balanceService->totalRemaining($user->id, $leaveType->id);
            if ($available < $totalDays) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Saldo cuti tidak mencukupi. Tersedia {$available} hari, diminta {$totalDays} hari.",
                ], 422);
            }
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')
                ->store('leave_attachments', 'public');
        }

        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        if ($user->manager_id && $user->manager) {
            $leaveRequest->load('user', 'leaveType');
            $user->manager->notify(new NewLeaveRequestNotification($leaveRequest));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan cuti berhasil dikirim. Menunggu persetujuan manager.',
            'data' => $leaveRequest->load('leaveType'),
        ], 201);
    }
}

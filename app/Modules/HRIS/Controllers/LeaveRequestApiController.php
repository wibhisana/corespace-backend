<?php

namespace App\Modules\HRIS\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestApiController extends Controller
{
    // Mengambil daftar riwayat cuti karyawan yang sedang login
    public function index(Request $request)
    {
        $user = $request->user();

        $leaves = LeaveRequest::where('user_id', $user->id)
                              ->latest('created_at')
                              ->get();

        return response()->json([
            'message' => 'Riwayat pengajuan cuti berhasil diambil',
            'data' => $leaves
        ]);
    }

    // Mengajukan permohonan cuti baru
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $user = $request->user();

        // Hitung jumlah hari cuti (termasuk hari terakhir)
        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $daysRequested = $start->diffInDays($end) + 1;

        // Cek kecukupan kuota
        if ($user->leave_quota < $daysRequested) {
            return response()->json([
                'message' => "Kuota cuti tidak cukup. Sisa kuota Anda: {$user->leave_quota} hari."
            ], 400);
        }

        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending', // Status default adalah menunggu persetujuan
        ]);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim dan menunggu persetujuan HR.',
            'data' => $leaveRequest
        ], 201);
    }
}

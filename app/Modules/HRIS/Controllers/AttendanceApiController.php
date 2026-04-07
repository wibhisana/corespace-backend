<?php

namespace App\Modules\HRIS\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceApiController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        // Ambil riwayat absen milik user yang sedang login, urutkan dari yang terbaru
        $history = Attendance::where('user_id', $user->id)
                            ->latest('date')
                            ->get();

        return response()->json([
            'message' => 'Riwayat absensi berhasil diambil',
            'data' => $history
        ]);
    }

    public function clockIn(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        // Cek apakah sudah absen masuk hari ini
        $attendance = Attendance::where('user_id', $user->id)
                                ->where('date', $today)
                                ->first();

        if ($attendance) {
            return response()->json(['message' => 'Anda sudah melakukan Clock In hari ini.'], 400);
        }

        // Catat absen masuk
        $newAttendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'clock_in' => $now,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Clock In berhasil!',
            'data' => $newAttendance
        ], 201);
    }

    public function clockOut(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        // Cari data absen hari ini
        $attendance = Attendance::where('user_id', $user->id)
                                ->where('date', $today)
                                ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Anda belum melakukan Clock In hari ini.'], 400);
        }

        if ($attendance->clock_out) {
            return response()->json(['message' => 'Anda sudah melakukan Clock Out hari ini.'], 400);
        }

        // Update waktu pulang
        $attendance->update([
            'clock_out' => $now
        ]);

        return response()->json([
            'message' => 'Clock Out berhasil!',
            'data' => $attendance
        ]);
    }
}

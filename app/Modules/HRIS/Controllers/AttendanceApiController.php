<?php

namespace App\Modules\HRIS\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Models\Attendance;
use App\Models\User; // Tambahan: Untuk memanggil data User
use App\Notifications\AttendanceReminderNotification; // Tambahan: Memanggil Notifikasi
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
        $now = Carbon::now();

        $existingIn = Attendance::where('user_id', $user->id)
                                ->where('date', $today)
                                ->where('type', 'in')
                                ->exists();

        if ($existingIn) {
            return response()->json(['message' => 'Anda sudah melakukan Clock In hari ini.'], 400);
        }

        $newAttendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'type' => 'in',
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
        $now = Carbon::now();

        $hasClockedIn = Attendance::where('user_id', $user->id)
                                  ->where('date', $today)
                                  ->where('type', 'in')
                                  ->exists();

        if (!$hasClockedIn) {
            return response()->json(['message' => 'Anda belum melakukan Clock In hari ini.'], 400);
        }

        $existingOut = Attendance::where('user_id', $user->id)
                                 ->where('date', $today)
                                 ->where('type', 'out')
                                 ->exists();

        if ($existingOut) {
            return response()->json(['message' => 'Anda sudah melakukan Clock Out hari ini.'], 400);
        }

        $newAttendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'type' => 'out',
            'clock_out' => $now,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Clock Out berhasil!',
            'data' => $newAttendance
        ], 201);
    }

    /**
     * Trigger Notifikasi Pengingat Absensi
     */
    public function checkMissingAttendance()
    {
        $today = Carbon::today()->toDateString();

        // 1. Ambil ID Karyawan yang SUDAH Clock In hari ini
        $attendedUserIds = Attendance::where('date', $today)
                                     ->where('type', 'in')
                                     ->pluck('user_id');

        // 2. Cari Karyawan yang ID-nya TIDAK ADA di daftar $attendedUserIds
        $usersMissing = User::whereNotIn('id', $attendedUserIds)->get();

        $count = 0;
        foreach ($usersMissing as $user) {
            // 3. Kirim Notifikasi ke masing-masing karyawan (WA & Database)
            $user->notify(new AttendanceReminderNotification());
            $count++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Notifikasi pengingat belum absen berhasil dikirim ke {$count} karyawan."
        ]);
    }
}

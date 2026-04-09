<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\HRIS\Models\Attendance;
use Carbon\Carbon;

class AttendanceApiController extends Controller
{
    /**
     * Rumus Haversine untuk menghitung jarak antara 2 titik koordinat bumi (dalam meter)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Hasil akhir dalam meter
    }

    /**
     * Endpoint untuk Clock-in Karyawan via Mobile
     */
    public function clockIn(Request $request)
    {
        // 1. Validasi request dari HP
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|image|max:2048' // Opsional: Bukti foto selfie
        ]);

        $user = auth()->user();
        $office = $user->unit; // Tarik data unit/kantor tempat user bekerja
        $today = Carbon::today();

        // 2. Cek apakah Unit memiliki data koordinat
        if (!$office || !$office->latitude || !$office->longitude) {
            return response()->json([
                'status' => 'error',
                'message' => 'Koordinat kantor belum diatur oleh HRD. Hubungi Admin.'
            ], 400);
        }

        // 3. Eksekusi Geofencing (Hitung Jarak)
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $office->latitude,
            $office->longitude
        );

        // 4. Validasi Radius
        if ($distance > $office->radius_meters) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda berada di luar radius absen.',
                'distance_meters' => round($distance),
                'allowed_radius' => $office->radius_meters
            ], 403); // HTTP 403: Forbidden
        }

        // 5. Cek apakah sudah absen hari ini
        $existingAttendance = Attendance::where('user_id', $user->id)
                                        ->whereDate('date', $today)
                                        ->first();

        if ($existingAttendance && $existingAttendance->clock_in) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan Clock-in hari ini.'
            ], 400);
        }

        // 6. Jika semua lolos, simpan data absensi
        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'clock_in' => now(),
                'clock_in_location' => $request->latitude . ',' . $request->longitude,
                'status' => 'Present', // Status awal, nanti bisa dihitung Late/Tidak berdasarkan Shift
                'shift_id' => $user->attendanceGroup?->shift_id,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Clock-in. Selamat Bekerja!',
            'data' => [
                'time' => $attendance->clock_in->format('H:i:s'),
                'distance_from_office' => round($distance) . ' meter'
            ]
        ], 200);
    }
}

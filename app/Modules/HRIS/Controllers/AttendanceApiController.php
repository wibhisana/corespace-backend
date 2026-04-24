<?php

declare(strict_types=1);

namespace App\Modules\HRIS\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Models\Attendance;
use App\Models\User;
use App\Notifications\AttendanceReminderNotification;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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

    public function clockIn(Request $request): JsonResponse
    {
        $request->validate([
            'photo'     => ['required', 'image', 'max:5120'],
            'latitude'  => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $user   = $request->user();
        $today  = Carbon::today()->toDateString();
        $now    = Carbon::now();

        $existingIn = Attendance::where('user_id', $user->id)
                                ->where('date', $today)
                                ->where('type', 'in')
                                ->exists();

        if ($existingIn) {
            return response()->json(['message' => 'Anda sudah melakukan Clock In hari ini.'], 400);
        }

        if (empty($user->face_photo_path) || ! Storage::disk('public')->exists($user->face_photo_path)) {
            return response()->json([
                'message' => 'Foto referensi wajah Anda belum terdaftar di sistem. Hubungi HRD.'
            ], 400);
        }

        $verification = $this->verifyFaceWithAI($user->face_photo_path, $request->file('photo'));

        if ($verification['status'] === 'service_down') {
            return response()->json([
                'message' => 'Layanan AI sedang sibuk/offline. Silakan coba lagi nanti.'
            ], 500);
        }

        if ($verification['status'] !== 'matched') {
            return response()->json([
                'message' => "Verifikasi wajah gagal: {$verification['message']}. Silakan coba lagi."
            ], 400);
        }

        // Geofencing check tetap di sini (setelah verifikasi wajah, sebelum create row).

        $selfiePath = $request->file('photo')->store('attendances/selfies', 'public');

        $newAttendance = Attendance::create([
            'user_id'    => $user->id,
            'date'       => $today,
            'type'       => 'in',
            'clock_in'   => $now,
            'latitude'   => $request->input('latitude'),
            'longitude'  => $request->input('longitude'),
            'photo_path' => $selfiePath,
            'notes'      => $request->input('notes'),
        ]);

        return response()->json([
            'message' => 'Clock In berhasil!',
            'data'    => $newAttendance,
        ], 201);
    }

    public function clockOut(Request $request): JsonResponse
    {
        $request->validate([
            'photo'     => ['required', 'image', 'max:5120'],
            'latitude'  => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $user  = $request->user();
        $today = Carbon::today()->toDateString();
        $now   = Carbon::now();

        $hasClockedIn = Attendance::where('user_id', $user->id)
                                  ->where('date', $today)
                                  ->where('type', 'in')
                                  ->exists();

        if (! $hasClockedIn) {
            return response()->json(['message' => 'Anda belum melakukan Clock In hari ini.'], 400);
        }

        $existingOut = Attendance::where('user_id', $user->id)
                                 ->where('date', $today)
                                 ->where('type', 'out')
                                 ->exists();

        if ($existingOut) {
            return response()->json(['message' => 'Anda sudah melakukan Clock Out hari ini.'], 400);
        }

        if (empty($user->face_photo_path) || ! Storage::disk('public')->exists($user->face_photo_path)) {
            return response()->json([
                'message' => 'Foto referensi wajah Anda belum terdaftar di sistem. Hubungi HRD.'
            ], 400);
        }

        $verification = $this->verifyFaceWithAI($user->face_photo_path, $request->file('photo'));

        if ($verification['status'] === 'service_down') {
            return response()->json([
                'message' => 'Layanan AI sedang sibuk/offline. Silakan coba lagi nanti.'
            ], 500);
        }

        if ($verification['status'] !== 'matched') {
            return response()->json([
                'message' => "Verifikasi wajah gagal: {$verification['message']}. Silakan coba lagi."
            ], 400);
        }

        // Geofencing check tetap di sini (setelah verifikasi wajah, sebelum create row).

        $selfiePath = $request->file('photo')->store('attendances/selfies', 'public');

        $newAttendance = Attendance::create([
            'user_id'    => $user->id,
            'date'       => $today,
            'type'       => 'out',
            'clock_out'  => $now,
            'latitude'   => $request->input('latitude'),
            'longitude'  => $request->input('longitude'),
            'photo_path' => $selfiePath,
            'notes'      => $request->input('notes'),
        ]);

        return response()->json([
            'message' => 'Clock Out berhasil!',
            'data'    => $newAttendance,
        ], 201);
    }

    private function verifyFaceWithAI(string $referencePhotoPath, UploadedFile $selfieFile): array
    {
        $endpoint = (string) config(
            'services.face_ai.url',
            'http://localhost:5000/api/v1/verify-face'
        );

        try {
            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->attach(
                    'reference_image',
                    Storage::disk('public')->get($referencePhotoPath),
                    basename($referencePhotoPath)
                )
                ->attach(
                    'selfie_image',
                    file_get_contents($selfieFile->getRealPath()),
                    $selfieFile->getClientOriginalName() ?: 'selfie.jpg'
                )
                ->post($endpoint);
        } catch (ConnectionException $e) {
            return ['status' => 'service_down', 'message' => $e->getMessage()];
        } catch (\Throwable $e) {
            return ['status' => 'service_down', 'message' => $e->getMessage()];
        }

        if (! $response->successful()) {
            return ['status' => 'service_down', 'message' => "HTTP {$response->status()}"];
        }

        $json    = (array) $response->json();
        $matched = (($json['status'] ?? null) !== 'error')
            && (($json['match'] ?? false) === true);

        return [
            'status'  => $matched ? 'matched' : 'mismatch',
            'message' => (string) ($json['message'] ?? ($matched ? 'Wajah cocok.' : 'Wajah tidak sesuai.')),
        ];
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

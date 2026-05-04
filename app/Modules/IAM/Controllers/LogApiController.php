<?php

namespace App\Modules\IAM\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\IAM\Models\AuthLog;
use Illuminate\Http\Request;

class LogApiController extends Controller
{
    public function index(Request $request)
    {
        // Akses dibatasi di route middleware (role:super_admin).
        $query = AuthLog::query();

        // 1. Fitur Filter Berdasarkan Status (SUCCESS / FAILED)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', strtoupper($request->status));
        }

        // 2. Fitur Pencarian (Berdasarkan Email atau Pesan)
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('message', 'LIKE', "%{$searchTerm}%");
            });
        }

        // 3. Urutkan dari yang terbaru dan gunakan Pagination (misal: 15 baris per halaman)
        // Pagination sangat penting agar tabel tidak lag jika log sudah mencapai ribuan baris
        $logs = $query->latest()->paginate(15);

        return response()->json([
            'message' => 'Data log berhasil diambil',
            'data' => $logs
        ]);
    }

    public function clearLogs(Request $request)
    {
        // Akses dibatasi di route middleware (role:super_admin).
        AuthLog::truncate();

        return response()->json([
            'message' => 'Semua riwayat log berhasil dibersihkan.'
        ]);
    }
}

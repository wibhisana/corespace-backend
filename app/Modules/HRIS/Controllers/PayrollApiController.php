<?php

namespace App\Modules\HRIS\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Models\Payroll;
use Illuminate\Http\Request;

class PayrollApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // KARYAWAN HANYA BISA MELIHAT SLIP GAJI YANG SUDAH DIRILIS (is_paid = true)
        $payrolls = Payroll::where('user_id', $user->id)
                    ->where('is_paid', true) // Baris keamanan ini yang terpenting
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get();

        return response()->json([
            'message' => 'Slip gaji berhasil diambil',
            'data' => $payrolls
        ]);
    }
}

<?php

namespace App\Modules\IAM\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRIS\Models\EmployeeFinance;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Karyawan mengisi/update data ESS Kategori 2 (Self-Service).
     * PUT /api/iam/profile
     */
    public function update(Request $request)
    {
        $request->validate([
            'phone_number' => 'nullable|string|max:20',
            'personal_email' => 'nullable|email',
            'current_address' => 'nullable|string',
            'gender' => 'nullable|in:L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'id_card_number' => 'nullable|string|max:30',
            'tax_id' => 'nullable|string|max:30',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
        ]);

        $user = $request->user();

        // Update data ESS di tabel users
        $user->update($request->only([
            'phone_number', 'personal_email', 'current_address',
            'gender', 'birth_place', 'birth_date', 'marital_status',
            'emergency_contact_name', 'emergency_contact_phone',
            'id_card_number', 'tax_id',
        ]));

        // Update data bank di tabel employee_finances
        if ($request->filled('bank_name') || $request->filled('bank_account_number')) {
            EmployeeFinance::updateOrCreate(
                ['user_id' => $user->id],
                array_filter([
                    'basic_salary' => $user->employeeFinance?->basic_salary ?? 0,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->bank_account_number,
                ]),
            );
        }

        $user->load('employeeFinance');

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data' => $user,
            'is_profile_complete' => $user->isProfileComplete(),
        ]);
    }
}

<?php

namespace App\Modules\IAM\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\HRIS\Models\Department;
use App\Modules\HRIS\Models\EmployeeFinance;
use App\Modules\IAM\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    /**
     * Endpoint 1: Manual Add (POST /api/iam/users)
     * Hanya data Kategori 1 (HR). Password auto-generate atau input manual.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nik' => 'required|string|unique:users,nik',
            'department_id' => 'required|exists:departments,id',
            'job_title' => 'required|string|max:255',
            'join_date' => 'required|date',
            'employment_status' => 'required|in:Tetap,Kontrak,Probation',
            'basic_salary' => 'nullable|numeric|min:0',
            'role' => 'required|string',
            'password' => 'nullable|string|min:8',
        ]);

        $plainPassword = $request->password ?? Str::random(8);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $plainPassword, // auto-hashed oleh cast
            'nik' => $request->nik,
            'department_id' => $request->department_id,
            'job_title' => $request->job_title,
            'join_date' => $request->join_date,
            'employment_status' => $request->employment_status,
        ]);

        // Buat data finance jika ada gaji pokok
        if ($request->filled('basic_salary')) {
            EmployeeFinance::create([
                'user_id' => $user->id,
                'basic_salary' => $request->basic_salary,
            ]);
        }

        $user->assignRole($request->role);

        return response()->json([
            'message' => 'Karyawan berhasil ditambahkan.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nik' => $user->nik,
                'role' => $user->getRoleNames(),
                'generated_password' => $request->password ? null : $plainPassword,
            ],
        ], 201);
    }

    /**
     * Endpoint 2: Batch Import (POST /api/iam/users/import)
     * Upload Excel/CSV, baris 1 = header.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:5120',
        ]);

        $import = new UsersImport();
        $import->import($request->file('file')->getRealPath());

        return response()->json([
            'message' => 'Import selesai.',
            'total_created' => count($import->createdUsers),
            'total_failed' => count($import->failedRows),
            'created_users' => collect($import->createdUsers)->map(fn ($u) => [
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => $u['password'],
            ]),
            'failed_rows' => $import->failedRows,
        ]);
    }

    /**
     * Daftar karyawan (GET /api/iam/users)
     */
    public function index(Request $request)
    {
        $query = User::with(['department', 'roles', 'employeeFinance']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('nik', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->employment_status);
        }

        $users = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => 'Data karyawan berhasil diambil.',
            'data' => $users,
        ]);
    }

    /**
     * Detail karyawan (GET /api/iam/users/{user})
     */
    public function show(User $user)
    {
        $user->load(['department', 'roles', 'employeeFinance']);

        return response()->json([
            'message' => 'Detail karyawan.',
            'data' => $user,
        ]);
    }

    /**
     * Update data HR karyawan (PUT /api/iam/users/{user})
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'nik' => 'sometimes|string|unique:users,nik,' . $user->id,
            'department_id' => 'sometimes|exists:departments,id',
            'job_title' => 'sometimes|string|max:255',
            'join_date' => 'sometimes|date',
            'employment_status' => 'sometimes|in:Tetap,Kontrak,Probation',
            'role' => 'sometimes|string',
        ]);

        $user->update($request->only([
            'name', 'email', 'nik', 'department_id',
            'job_title', 'join_date', 'employment_status',
        ]));

        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        return response()->json([
            'message' => 'Data karyawan berhasil diperbarui.',
            'data' => $user->load(['department', 'roles']),
        ]);
    }

    /**
     * Hapus karyawan (DELETE /api/iam/users/{user})
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Karyawan berhasil dihapus.',
        ]);
    }
}

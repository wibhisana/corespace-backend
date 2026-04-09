<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\HRIS\Models\Department;
use App\Modules\HRIS\Models\EmployeeFinance;
use App\Modules\HRIS\Models\Unit; // 💡 Jangan lupa import model Unit
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. BUAT UNIT (Holding / Anak Perusahaan) ---
        $mainUnit = Unit::updateOrCreate(
            ['name' => 'KPN Corp Head Office'],
            ['type' => 'Holding']
        );

        // --- 2. BUAT DEPARTMENT ---
        $itDept = Department::updateOrCreate(['name' => 'IT Department'], ['code' => 'IT']);
        $hrDept = Department::updateOrCreate(['name' => 'Human Resources'], ['code' => 'HR']);

        // --- 3. BUAT ROLE (Spatie) ---
        $adminRole = Role::updateOrCreate(['name' => 'Super Admin']);
        $hrRole    = Role::updateOrCreate(['name' => 'HR Manager']);
        $staffRole = Role::updateOrCreate(['name' => 'Staff']);

        // --- 4. BUAT USER & ASSIGN ROLE ---

        // 👨‍💻 ADMIN
        $admin = User::updateOrCreate(
            ['email' => 'admin@corespace.com'],
            [
                'name' => 'Ary Admin',
                'password' => 'password', // Otomatis di-hash oleh casting model
                'nik' => 'ADM-001',
                'unit_id' => $mainUnit->id, // 💡 Relasi Unit
                'department_id' => $itDept->id,
                'job_title' => 'System Administrator',
                'employment_status' => 'Tetap',
                'join_date' => '2024-01-01',
                'gender' => 'L',
                'phone_number' => '081111222333',
            ]
        );
        $admin->assignRole($adminRole);

        // 👩‍💼 HR MANAGER
        $hr = User::updateOrCreate(
            ['email' => 'hr@corespace.com'],
            [
                'name' => 'Siska HR',
                'password' => 'password',
                'nik' => 'HR-001',
                'unit_id' => $mainUnit->id,
                'department_id' => $hrDept->id,
                'job_title' => 'HR Manager',
                'employment_status' => 'Tetap',
                'join_date' => '2024-01-01',
                'gender' => 'P',
                'phone_number' => '082222333444',
            ]
        );
        $hr->assignRole($hrRole);

        // 👨‍🔧 STAFF (BUDI)
        $staff = User::updateOrCreate(
            ['email' => 'staff@corespace.com'],
            [
                'name' => 'Budi Staff',
                'password' => 'password',
                'nik' => 'STF-001',
                'unit_id' => $mainUnit->id,
                'department_id' => $itDept->id,
                'job_title' => 'Frontend Developer',
                'employment_status' => 'Kontrak',
                'join_date' => '2024-06-01',
                'gender' => 'L',
                'phone_number' => '083333444555',
            ]
        );
        $staff->assignRole($staffRole);

        // --- 5. ISI DATA GAJI (Employee Finance) ---
        EmployeeFinance::updateOrCreate(
            ['user_id' => $staff->id],
            [
                'basic_salary' => 8500000,
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
            ]
        );

        $this->command->info('Semua Akun, Unit, Role, dan Department berhasil dibangkitkan!');
    }
}

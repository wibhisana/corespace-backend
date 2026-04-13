<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\HRIS\Models\Department;
use App\Modules\HRIS\Models\EmployeeFinance;
use App\Modules\HRIS\Models\Unit;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. BUAT UNIT ---
        $mainUnit = Unit::updateOrCreate(
            ['name' => 'KPN Corp Head Office'],
            ['type' => 'Holding']
        );

        // --- 2. BUAT DEPARTMENT ---
        $itDept = Department::updateOrCreate(['name' => 'IT Department'], ['code' => 'IT']);
        $hrDept = Department::updateOrCreate(['name' => 'Human Resources'], ['code' => 'HR']);
        $hseDept = Department::updateOrCreate(['name' => 'HSE / K3'], ['code' => 'HSE']);

        // --- 3. BUAT ROLE (Gunakan snake_case - BEST PRACTICE) ---
        $adminRole = Role::updateOrCreate(['name' => 'super_admin']);
        $hrRole    = Role::updateOrCreate(['name' => 'hr_manager']);
        $k3Role    = Role::updateOrCreate(['name' => 'k3_officer']);
        $staffRole = Role::updateOrCreate(['name' => 'staff']);

        // --- 4. BUAT USER & ASSIGN ROLE ---

        // 👨‍💻 ADMIN
        $admin = User::updateOrCreate(['email' => 'admin@corespace.com'], [
            'name' => 'Ary Admin', 'password' => 'password', 'nik' => 'ADM-001',
            'unit_id' => $mainUnit->id, 'department_id' => $itDept->id,
            'job_title' => 'System Administrator', 'employment_status' => 'Tetap',
            'join_date' => '2024-01-01', 'gender' => 'L', 'phone_number' => '081111222333',
        ]);
        $admin->assignRole($adminRole);

        // 👩‍💼 HR MANAGER
        $hr = User::updateOrCreate(['email' => 'hr@corespace.com'], [
            'name' => 'Siska HR', 'password' => 'password', 'nik' => 'HR-001',
            'unit_id' => $mainUnit->id, 'department_id' => $hrDept->id,
            'job_title' => 'HR Manager', 'employment_status' => 'Tetap',
            'join_date' => '2024-01-01', 'gender' => 'P', 'phone_number' => '082222333444',
        ]);
        $hr->assignRole($hrRole);

        // 👷 K3 OFFICER
        $k3 = User::updateOrCreate(['email' => 'k3@corespace.com'], [
            'name' => 'Bowo K3', 'password' => 'password', 'nik' => 'K3-001',
            'unit_id' => $mainUnit->id, 'department_id' => $hseDept->id,
            'job_title' => 'Safety Officer', 'employment_status' => 'Tetap',
            'join_date' => '2024-01-01', 'gender' => 'L', 'phone_number' => '083333444555',
        ]);
        $k3->assignRole($k3Role);

        // 👨‍🔧 STAFF (MADE)
        $staff = User::updateOrCreate(['email' => 'staff@corespace.com'], [
            'name' => 'Made Staff', 'password' => 'password', 'nik' => 'STF-001',
            'unit_id' => $mainUnit->id, 'department_id' => $itDept->id,
            'job_title' => 'Frontend Developer', 'employment_status' => 'Kontrak',
            'join_date' => '2024-06-01', 'gender' => 'L', 'phone_number' => '087874063434',
        ]);
        $staff->assignRole($staffRole);

        // --- 5. ISI DATA GAJI (Employee Finance) ---
        EmployeeFinance::updateOrCreate(
            ['user_id' => $staff->id],
            ['basic_salary' => 8500000, 'bank_name' => 'BCA', 'account_number' => '1234567890']
        );

        $this->command->info('✅ UserSeeder Berhasil: Akun, Unit, Role (snake_case), dan Dept tersimpan.');
    }
}

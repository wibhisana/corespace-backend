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
        $adminRole   = Role::updateOrCreate(['name' => 'super_admin']);
        $hrRole      = Role::updateOrCreate(['name' => 'hr_manager']);
        $managerRole = Role::updateOrCreate(['name' => 'manager']); // Role baru untuk Head/Manager
        $k3Role      = Role::updateOrCreate(['name' => 'k3_officer']);
        $staffRole   = Role::updateOrCreate(['name' => 'staff']);

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

        // 👔 IT MANAGER (ATASAN)
        $itManager = User::updateOrCreate(['email' => 'manager.it@corespace.com'], [
            'name' => 'Budi IT Manager', 'password' => 'password', 'nik' => 'MGR-IT-001',
            'unit_id' => $mainUnit->id, 'department_id' => $itDept->id,
            'job_title' => 'Head of IT', 'employment_status' => 'Tetap',
            'join_date' => '2024-02-01', 'gender' => 'L', 'phone_number' => '084444555666',
        ]);
        $itManager->assignRole($managerRole);

        // --- 👨‍🔧 STAFF IT (BAWAHAN IT MANAGER) ---

        // 1. IT Operations
        $itOps = User::updateOrCreate(['email' => 'it.ops@corespace.com'], [
            'name' => 'Andi IT Ops', 'password' => 'password', 'nik' => 'STF-IT-001',
            'unit_id' => $mainUnit->id, 'department_id' => $itDept->id,
            'manager_id' => $itManager->id, // Tautkan ke Manager
            'job_title' => 'IT Operations', 'employment_status' => 'Tetap',
            'join_date' => '2024-03-01', 'gender' => 'L', 'phone_number' => '085555666777',
        ]);
        $itOps->assignRole($staffRole);

        // 2. DevOps Engineer
        $devOps = User::updateOrCreate(['email' => 'devops@corespace.com'], [
            'name' => 'Sari DevOps', 'password' => 'password', 'nik' => 'STF-IT-002',
            'unit_id' => $mainUnit->id, 'department_id' => $itDept->id,
            'manager_id' => $itManager->id, // Tautkan ke Manager
            'job_title' => 'DevOps Engineer', 'employment_status' => 'Tetap',
            'join_date' => '2024-04-01', 'gender' => 'P', 'phone_number' => '086666777888',
        ]);
        $devOps->assignRole($staffRole);

        // 3. UI/UX Designer
        $uiux = User::updateOrCreate(['email' => 'uiux@corespace.com'], [
            'name' => 'Joko UI/UX', 'password' => 'password', 'nik' => 'STF-IT-003',
            'unit_id' => $mainUnit->id, 'department_id' => $itDept->id,
            'manager_id' => $itManager->id, // Tautkan ke Manager
            'job_title' => 'UI/UX Designer', 'employment_status' => 'Kontrak',
            'join_date' => '2024-05-01', 'gender' => 'L', 'phone_number' => '087777888999',
        ]);
        $uiux->assignRole($staffRole);

        // 4. Software Engineer (Made)
        $staff = User::updateOrCreate(['email' => 'staff@corespace.com'], [
            'name' => 'Made Software Engineer', 'password' => 'password', 'nik' => 'STF-IT-004',
            'unit_id' => $mainUnit->id, 'department_id' => $itDept->id,
            'manager_id' => $itManager->id, // Tautkan ke Manager
            'job_title' => 'Software Engineer', 'employment_status' => 'Kontrak',
            'join_date' => '2024-06-01', 'gender' => 'L', 'phone_number' => '087874063434',
        ]);
        $staff->assignRole($staffRole);

        // --- 5. ISI DATA GAJI (Employee Finance) ---
        EmployeeFinance::updateOrCreate(
            ['user_id' => $staff->id],
            ['basic_salary' => 8500000, 'bank_name' => 'BCA', 'account_number' => '1234567890']
        );

        $this->command->info('✅ UserSeeder Berhasil: Hierarki Manager IT dan Staff berhasil dibuat!');
    }
}

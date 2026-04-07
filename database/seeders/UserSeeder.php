<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\HRIS\Models\Department;
use App\Modules\HRIS\Models\EmployeeFinance;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. BUAT DEPARTMENT ---
        $itDept = Department::updateOrCreate(['name' => 'IT Department'], ['code' => 'IT']);
        $hrDept = Department::updateOrCreate(['name' => 'Human Resources'], ['code' => 'HR']);

        // --- 2. BUAT ROLE (Spatie) ---
        $adminRole = Role::updateOrCreate(['name' => 'Super Admin']);
        $hrRole    = Role::updateOrCreate(['name' => 'HR Manager']);
        $staffRole = Role::updateOrCreate(['name' => 'Staff']);

        // --- 3. BUAT USER & ASSIGN ROLE ---

        // ADMIN
        $admin = User::updateOrCreate(
            ['email' => 'admin@corespace.com'],
            [
                'name' => 'Ary Admin',
                'password' => Hash::make('password'),
                'department_id' => $itDept->id
            ]
        );
        $admin->assignRole($adminRole);

        // HR MANAGER
        $hr = User::updateOrCreate(
            ['email' => 'hr@corespace.com'],
            [
                'name' => 'Siska HR',
                'password' => Hash::make('password'),
                'department_id' => $hrDept->id
            ]
        );
        $hr->assignRole($hrRole);

        // STAFF (BUDI)
        $staff = User::updateOrCreate(
            ['email' => 'staff@corespace.com'],
            [
                'name' => 'Budi Staff',
                'password' => Hash::make('password'),
                'department_id' => $itDept->id
            ]
        );
        $staff->assignRole($staffRole);

        // --- 4. ISI DATA GAJI (Employee Finance) ---
        // Kita otomatiskan isi gaji Budi agar bisa di-test di Payroll
        EmployeeFinance::updateOrCreate(
            ['user_id' => $staff->id],
            [
                'basic_salary' => 8500000, // Akan terenkripsi otomatis oleh Casting Model
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
            ]
        );

        $this->command->info('Semua Akun, Role, dan Department berhasil dibangkitkan!');
    }
}

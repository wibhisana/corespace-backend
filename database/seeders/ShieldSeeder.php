<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai Shield Permissions Seeder...');

        // 1. SUPER ADMIN
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. HR MANAGER
        $hrManagerPerms = collect()
            ->merge($this->fullCrudWithApproval('LeaveRequest'))
            ->merge($this->fullCrudWithApproval('OvertimeRequest'))
            ->merge($this->fullCrudWithApproval('Attendance'))
            ->merge(['ViewAny:User', 'View:User', 'Update:User'])
            ->merge(['ViewAny:Payroll', 'View:Payroll', 'Update:Payroll'])
            ->merge([
                'ViewAny:Department', 'View:Department',
                'ViewAny:LeaveType', 'View:LeaveType',
                'ViewAny:LeaveBalance', 'View:LeaveBalance', 'Update:LeaveBalance',
                'ViewAny:Shift', 'View:Shift',
                'ViewAny:AttendanceGroup', 'View:AttendanceGroup',
                'ViewAny:OvertimeRule', 'View:OvertimeRule',
            ])->unique();

        $hrManager = Role::firstOrCreate(['name' => 'hr_manager']);
        $hrManager->syncPermissions($this->resolvePermissions($hrManagerPerms));

        // 3. K3 OFFICER (HSE)
        $k3Perms = collect()
            ->merge(['ViewAny:Attendance', 'View:Attendance'])
            ->merge(['ViewAny:User', 'View:User'])
            ->merge(['ViewAny:Department', 'View:Department'])
            ->merge(['ViewAny:Unit', 'View:Unit'])
            ->merge(['Approve:Attendance', 'Reject:Attendance'])
            ->merge(['ViewAny:MeetingRoom', 'View:MeetingRoom', 'Create:MeetingRoom', 'Update:MeetingRoom'])
            ->unique();

        $k3 = Role::firstOrCreate(['name' => 'k3_officer']);
        $k3->syncPermissions($this->resolvePermissions($k3Perms));

        // 4. STAFF
        $staffPerms = collect([
            'ViewAny:Payroll', 'View:Payroll',
            'ViewAny:LeaveRequest', 'View:LeaveRequest', 'Create:LeaveRequest',
            'ViewAny:OvertimeRequest', 'View:OvertimeRequest', 'Create:OvertimeRequest',
            'ViewAny:Attendance', 'View:Attendance',
            'ViewAny:MeetingRoom', 'View:MeetingRoom',
        ]);

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions($this->resolvePermissions($staffPerms));

        $this->command->info('🎉 Shield Permissions Berhasil Di-sync!');
    }

    private function fullCrudWithApproval(string $resource): array
    {
        return [
            "ViewAny:{$resource}", "View:{$resource}", "Create:{$resource}",
            "Update:{$resource}", "Delete:{$resource}", "DeleteAny:{$resource}",
            "Approve:{$resource}", "Reject:{$resource}",
        ];
    }

    private function resolvePermissions($names): \Illuminate\Support\Collection
    {
        return Permission::whereIn('name', collect($names)->unique()->values())->get();
    }
}

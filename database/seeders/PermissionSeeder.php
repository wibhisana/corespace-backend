<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar hak akses untuk CoreSpace
        $permissions = [
            // Hak Akses IAM (Users & Roles)
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Hak Akses HRIS (Departments)
            'view departments',
            'create departments',
            'edit departments',
            'delete departments',
        ];

        // Looping untuk memasukkan data ke database secara otomatis
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}

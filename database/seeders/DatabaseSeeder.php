<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Fondasi hak akses (Role & Permission)
            PermissionSeeder::class,

            // 2. Unit, Department, User, EmployeeFinance
            UserSeeder::class,

            // 3. Koordinat GPS kantor (Geofencing)
            GeofenceSeeder::class,

            // 4. Master HRIS: Shift, Grup Kehadiran, Jenis Cuti, Aturan Lembur, Saldo Cuti
            HrisMasterSeeder::class,

            // 5. Lokasi & Ruang Rapat
            LocationSeeder::class,

            // 6. Sample data absensi 10 hari kerja
            AttendanceSeeder::class,

            // 7. Sample pengajuan lembur
            OvertimeSeeder::class,
        ]);
    }
}

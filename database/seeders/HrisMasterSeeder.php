<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Modules\HRIS\Models\Shift;
use App\Modules\HRIS\Models\AttendanceGroup;
use App\Modules\HRIS\Models\LeaveType;
use App\Modules\HRIS\Models\OvertimeRule;
use App\Modules\HRIS\Models\LeaveBalance;
use Carbon\Carbon;

class HrisMasterSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai injeksi Master Data HRIS...');

        // ==========================================
        // SPRINT 1: MASTER SHIFT & GRUP KEHADIRAN
        // ==========================================
        $this->command->info('1. Membuat Data Sif Kerja...');

        $shiftPagi = Shift::firstOrCreate(
            ['name' => 'Sif Office (Pusat)'],
            [
                'type' => 'Fixed',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'grace_period' => 15,
            ]
        );

        $shiftFleksibel = Shift::firstOrCreate(
            ['name' => 'Sif Fleksibel (Remote/IT)'],
            [
                'type' => 'Free',
                'start_time' => null,
                'end_time' => null,
                'grace_period' => 0,
            ]
        );

        $shiftMalam = Shift::firstOrCreate(
            ['name' => 'Sif Satpam Malam'],
            [
                'type' => 'Scheduled',
                'start_time' => '20:00:00',
                'end_time' => '05:00:00',
                'grace_period' => 10,
            ]
        );

        $this->command->info('2. Membuat Data Grup Kehadiran...');

        $groupPusat = AttendanceGroup::firstOrCreate(
            ['name' => 'Grup Karyawan Pusat (HO)'],
            [
                'description' => 'Karyawan yang bekerja di Head Office KPN Corp.',
                'shift_id' => $shiftPagi->id,
                'is_active' => true,
            ]
        );

        $groupIT = AttendanceGroup::firstOrCreate(
            ['name' => 'Grup IT & Developer'],
            [
                'description' => 'Tim tech dengan jam kerja fleksibel.',
                'shift_id' => $shiftFleksibel->id,
                'is_active' => true,
            ]
        );

        // ==========================================
        // SPRINT 2: MASTER JENIS CUTI
        // ==========================================
        $this->command->info('3. Membuat Data Jenis Cuti...');

        $cutiTahunan = LeaveType::firstOrCreate(
            ['name' => 'Cuti Tahunan'],
            [
                'default_quota' => 12,
                'is_unlimited' => false,
                'requires_attachment' => false,
                'is_active' => true,
            ]
        );

        $cutiSakit = LeaveType::firstOrCreate(
            ['name' => 'Cuti Sakit'],
            [
                'default_quota' => 0,
                'is_unlimited' => true,
                'requires_attachment' => true, // Wajib surat dokter
                'is_active' => true,
            ]
        );

        $cutiMelahirkan = LeaveType::firstOrCreate(
            ['name' => 'Cuti Melahirkan'],
            [
                'default_quota' => 90,
                'is_unlimited' => false,
                'requires_attachment' => true,
                'is_active' => true,
            ]
        );

        // ==========================================
        // SPRINT 3: MASTER ATURAN LEMBUR
        // ==========================================
        $this->command->info('4. Membuat Data Aturan Lembur...');

        OvertimeRule::firstOrCreate(
            ['name' => 'Lembur Standar (Dibayar)'],
            [
                'calculation_method' => 'Attendance_Based',
                'compensation_type' => 'Paid',
                'requires_approval' => true,
                'is_active' => true,
            ]
        );

        OvertimeRule::firstOrCreate(
            ['name' => 'Lembur Ganti Hari (Time-Off)'],
            [
                'calculation_method' => 'Manual',
                'compensation_type' => 'Time_Off',
                'requires_approval' => true,
                'is_active' => true,
            ]
        );

        // ==========================================
        // BONUS: UPDATE USER DUMMY (Jika ada)
        // ==========================================
        $this->command->info('5. Memasukkan Karyawan ke Grup & Generate Saldo Cuti...');

        $users = User::all();
        $currentYear = Carbon::now()->year;

        if ($users->count() > 0) {
            foreach ($users as $index => $user) {
                // Masukkan user ke grup secara bergantian agar datanya variatif
                $user->update([
                    'attendance_group_id' => ($index % 2 == 0) ? $groupPusat->id : $groupIT->id
                ]);

                // Berikan jatah cuti tahunan
                LeaveBalance::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $cutiTahunan->id,
                        'year' => $currentYear,
                    ],
                    [
                        'total_quota' => $cutiTahunan->default_quota,
                        'used_quota' => 0,
                        'notes' => 'Generated by Seeder',
                    ]
                );
            }
            $this->command->info("Berhasil mengupdate {$users->count()} User dengan Grup dan Saldo Cuti.");
        } else {
            $this->command->warn('Tidak ada data User ditemukan. Lewati update relasi User.');
        }

        $this->command->info('🎉 Injeksi Data Master HRIS Selesai!');
    }
}

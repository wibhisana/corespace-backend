<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\HRIS\Models\OvertimeRequest;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OvertimeSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::where('email', 'staff@corespace.com')->first();
        $hr = User::where('email', 'hr@corespace.com')->first();

        if (!$staff || !$hr) {
            $this->command->warn('User staff/hr belum ada. Jalankan UserSeeder dulu.');
            return;
        }

        // Budi mengajukan 3 lembur bulan ini
        OvertimeRequest::firstOrCreate(
            ['user_id' => $staff->id, 'date' => Carbon::today()->subDays(5)->toDateString()],
            [
                'start_time' => '17:00',
                'end_time' => '20:00',
                'duration_minutes' => 180,
                'reason' => 'Deadline deployment sistem baru',
                'status' => 'Approved',
                'approved_by' => $hr->id,
            ]
        );

        OvertimeRequest::firstOrCreate(
            ['user_id' => $staff->id, 'date' => Carbon::today()->subDays(3)->toDateString()],
            [
                'start_time' => '17:00',
                'end_time' => '19:00',
                'duration_minutes' => 120,
                'reason' => 'Fixing bug production',
                'status' => 'Approved',
                'approved_by' => $hr->id,
            ]
        );

        OvertimeRequest::firstOrCreate(
            ['user_id' => $staff->id, 'date' => Carbon::today()->subDay()->toDateString()],
            [
                'start_time' => '17:00',
                'end_time' => '21:00',
                'duration_minutes' => 240,
                'reason' => 'Migrasi database ke server baru',
                'status' => 'Pending',
            ]
        );

        $this->command->info('Pengajuan lembur sample berhasil dibuat!');
    }
}

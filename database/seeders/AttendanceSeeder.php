<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\HRIS\Models\Attendance;
use App\Modules\HRIS\Models\Shift;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $shiftPagi = Shift::where('name', 'Sif Office (Pusat)')->first();

        if ($users->isEmpty() || !$shiftPagi) {
            $this->command->warn('User atau Shift belum ada. Jalankan UserSeeder & HrisMasterSeeder dulu.');
            return;
        }

        // Generate 10 hari kerja terakhir
        $workDays = collect();
        $date = Carbon::today();
        while ($workDays->count() < 10) {
            $date = $date->subDay();
            if ($date->isWeekday()) {
                $workDays->push($date->copy());
            }
        }

        foreach ($users as $user) {
            foreach ($workDays as $day) {
                // Randomisasi: kadang tepat waktu, kadang telat, kadang absen
                $scenario = rand(1, 10);

                if ($scenario <= 6) {
                    // Hadir tepat waktu (60%)
                    Attendance::firstOrCreate(
                        ['user_id' => $user->id, 'date' => $day->toDateString()],
                        [
                            'shift_id' => $shiftPagi->id,
                            'clock_in' => $day->copy()->setTime(7, rand(45, 59)),
                            'clock_out' => $day->copy()->setTime(17, rand(0, 30)),
                            'lateness_minutes' => 0,
                            'early_out_minutes' => 0,
                            'status' => 'Present',
                        ]
                    );
                } elseif ($scenario <= 9) {
                    // Terlambat (30%)
                    $lateMinutes = rand(5, 45);
                    Attendance::firstOrCreate(
                        ['user_id' => $user->id, 'date' => $day->toDateString()],
                        [
                            'shift_id' => $shiftPagi->id,
                            'clock_in' => $day->copy()->setTime(8, rand(15, 59)),
                            'clock_out' => $day->copy()->setTime(17, rand(0, 15)),
                            'lateness_minutes' => $lateMinutes,
                            'early_out_minutes' => 0,
                            'status' => 'Late',
                        ]
                    );
                } else {
                    // Absen (10%)
                    Attendance::firstOrCreate(
                        ['user_id' => $user->id, 'date' => $day->toDateString()],
                        [
                            'shift_id' => $shiftPagi->id,
                            'clock_in' => null,
                            'clock_out' => null,
                            'lateness_minutes' => 0,
                            'early_out_minutes' => 0,
                            'status' => 'Absent',
                        ]
                    );
                }
            }
        }

        $this->command->info("Attendance 10 hari kerja untuk {$users->count()} karyawan berhasil dibuat!");
    }
}

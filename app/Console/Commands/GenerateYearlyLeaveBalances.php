<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\HRIS\Models\LeaveType;
use App\Modules\HRIS\Models\LeaveBalance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateYearlyLeaveBalances extends Command
{
    // Nama command yang dipanggil di terminal
    protected $signature = 'hris:generate-leave-balances {year?}';
    protected $description = 'Otomatis generate saldo cuti karyawan untuk tahun tertentu (default tahun depan)';

    public function handle()
    {
        // Jika tahun tidak ditentukan, gunakan tahun depan
        $year = $this->argument('year') ?? now()->addYear()->year;

        $this->info("Memulai proses generate saldo cuti untuk tahun {$year}...");

        $users = User::all(); // Sebaiknya difilter user yang aktif saja
        $leaveTypes = LeaveType::where('is_active', true)
            ->where('is_unlimited', false)
            ->get();

        $count = 0;

        foreach ($users as $user) {
            foreach ($leaveTypes as $type) {
                // Gunakan firstOrCreate agar tidak menimpa data yang sudah dibuat manual
                $balance = LeaveBalance::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $type->id,
                        'year' => $year,
                    ],
                    [
                        'total_quota' => $type->default_quota,
                        'used_quota' => 0,
                        'notes' => 'Generated otomatis oleh sistem.',
                    ]
                );

                if ($balance->wasRecentlyCreated) {
                    $count++;
                }
            }
        }

        $message = "Berhasil membuat {$count} record saldo cuti baru untuk tahun {$year}.";
        $this->info($message);
        Log::info($message);

        return Command::SUCCESS;
    }
}

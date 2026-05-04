<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\HRIS\Models\LeaveType;
use App\Modules\HRIS\Models\LeaveBalance;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateYearlyLeaveBalances extends Command
{
    protected $signature = 'hris:generate-leave-balances {year?}';
    protected $description = 'Generate annual saldo cuti untuk tahun tertentu (default: tahun depan).';

    public function handle()
    {
        $year = (int) ($this->argument('year') ?? now()->addYear()->year);
        $expiresAt = Carbon::createFromDate($year, 12, 31)->toDateString();

        $this->info("Generate saldo annual untuk tahun {$year} (expires {$expiresAt})...");

        // Hanya user aktif (bukan resigned)
        $users = User::query()->where('employment_status', '!=', 'Resigned')->get();

        $leaveTypes = LeaveType::query()
            ->where('is_active', true)
            ->where('is_unlimited', false)
            ->get();

        $count = 0;

        foreach ($users as $user) {
            foreach ($leaveTypes as $type) {
                $balance = LeaveBalance::firstOrCreate(
                    [
                        'user_id'       => $user->id,
                        'leave_type_id' => $type->id,
                        'year'          => $year,
                        'source'        => LeaveBalance::SOURCE_ANNUAL,
                    ],
                    [
                        'total_quota' => $type->default_quota,
                        'used_quota'  => 0,
                        'expires_at'  => $expiresAt,
                        'notes'       => 'Generated otomatis oleh sistem.',
                    ]
                );

                if ($balance->wasRecentlyCreated) {
                    $count++;
                }
            }
        }

        $message = "Berhasil membuat {$count} record saldo annual baru untuk tahun {$year}.";
        $this->info($message);
        Log::info($message);

        return self::SUCCESS;
    }
}

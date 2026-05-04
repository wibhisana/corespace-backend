<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\HRIS\Models\LeaveBalance;
use App\Modules\HRIS\Models\LeaveType;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateCarryForwardBalances extends Command
{
    /**
     * Carry-forward sisa cuti tahunan dari tahun sumber → tahun tujuan.
     * Bucket carry-forward expired tanggal 31-Mar tahun tujuan.
     *
     * Business rule (per 2026-04-24):
     *   - Cap maksimal 12 hari per user per leave_type
     *   - Sisa di atas cap otomatis hangus (tidak dibuat row tambahan)
     *   - Hanya berlaku untuk leave_type yang `is_carry_forwardable = true`
     *     (kebijakan disetel HRD via Filament LeaveType form, bukan hardcode)
     *   - Hanya hitung dari source='annual' (bukan dari carry_forward sebelumnya
     *     — supaya tidak chain carry-forward berkepanjangan)
     *
     * Idempoten: kalau row sudah ada untuk (user, type, fromYear, carry_forward),
     * tidak akan diduplikasi (unique constraint leave_balances_unique_bucket).
     *
     * Usage:
     *   php artisan hris:generate-carry-forward                       # default: tahun lalu → tahun ini
     *   php artisan hris:generate-carry-forward --from=2025 --to=2026
     *   php artisan hris:generate-carry-forward --cap=10
     *   php artisan hris:generate-carry-forward --dry-run             # preview tanpa simpan
     */
    protected $signature = 'hris:generate-carry-forward
        {--from= : Tahun sumber (default: tahun lalu)}
        {--to= : Tahun tujuan, basis tanggal expired 31-Mar (default: tahun ini)}
        {--cap=12 : Cap hari maksimum yang bisa dibawa (sisa di atas cap hangus)}
        {--dry-run : Tampilkan rencana tanpa simpan ke database}';

    protected $description = 'Generate carry-forward saldo cuti tahunan dari tahun sumber ke tahun tujuan.';

    public function handle(): int
    {
        $fromYear = (int) ($this->option('from') ?? Carbon::now()->subYear()->year);
        $toYear   = (int) ($this->option('to')   ?? Carbon::now()->year);
        $cap      = (int) $this->option('cap');
        $dryRun   = (bool) $this->option('dry-run');

        if ($toYear <= $fromYear) {
            $this->error("--to ({$toYear}) harus lebih besar dari --from ({$fromYear}).");
            return self::FAILURE;
        }
        if ($cap <= 0) {
            $this->error("--cap harus > 0 (diberikan: {$cap}).");
            return self::FAILURE;
        }

        $expiresAt = Carbon::createFromDate($toYear, 3, 31)->toDateString();

        $eligibleTypes = LeaveType::query()
            ->where('is_carry_forwardable', true)
            ->where('is_active', true)
            ->get();

        if ($eligibleTypes->isEmpty()) {
            $this->warn('Tidak ada LeaveType dengan is_carry_forwardable=true. Setel via Filament → Leave Types.');
            return self::SUCCESS;
        }

        $this->info("Carry-forward {$fromYear} → {$toYear} | cap {$cap} hari | expires {$expiresAt}");
        if ($dryRun) {
            $this->warn('DRY RUN — tidak ada perubahan disimpan.');
        }

        $users = User::query()
            ->where('employment_status', '!=', 'Resigned')
            ->get();

        $created = 0;
        $forfeited = 0;
        $skipped = 0;
        $totalCarried = 0;

        DB::transaction(function () use (
            $users, $eligibleTypes, $fromYear, $cap, $expiresAt, $dryRun,
            &$created, &$forfeited, &$skipped, &$totalCarried
        ) {
            foreach ($users as $user) {
                foreach ($eligibleTypes as $type) {
                    $remaining = (int) LeaveBalance::query()
                        ->where('user_id', $user->id)
                        ->where('leave_type_id', $type->id)
                        ->where('year', $fromYear)
                        ->where('source', LeaveBalance::SOURCE_ANNUAL)
                        ->get()
                        ->sum(fn (LeaveBalance $b) => $b->remaining_quota);

                    if ($remaining <= 0) {
                        $skipped++;
                        continue;
                    }

                    $carry = min($remaining, $cap);
                    $forfeit = $remaining - $carry;
                    if ($forfeit > 0) {
                        $forfeited += $forfeit;
                    }

                    $note = sprintf(
                        'Carry-forward dari %d hari sisa annual %d (cap %d). %s. Generated %s.',
                        $remaining,
                        $fromYear,
                        $cap,
                        $forfeit > 0 ? "{$forfeit} hari hangus karena melebihi cap" : 'Semua sisa terbawa',
                        Carbon::now()->toDateString()
                    );

                    if ($dryRun) {
                        $this->line(sprintf(
                            '  [DRY] %s / %s : sisa=%d → carry=%d, forfeit=%d',
                            $user->name, $type->name, $remaining, $carry, $forfeit
                        ));
                        $created++;
                        $totalCarried += $carry;
                        continue;
                    }

                    $balance = LeaveBalance::firstOrCreate(
                        [
                            'user_id'       => $user->id,
                            'leave_type_id' => $type->id,
                            'year'          => $fromYear,
                            'source'        => LeaveBalance::SOURCE_CARRY_FORWARD,
                        ],
                        [
                            'total_quota' => $carry,
                            'used_quota'  => 0,
                            'expires_at'  => $expiresAt,
                            'notes'       => $note,
                        ]
                    );

                    if ($balance->wasRecentlyCreated) {
                        $created++;
                        $totalCarried += $carry;
                    } else {
                        $skipped++;
                    }
                }
            }
        });

        $msg = sprintf(
            'Selesai. Bucket dibuat: %d (%d hari terbawa). Hangus karena cap: %d hari. Skip (sudah ada / sisa nol): %d row.',
            $created, $totalCarried, $forfeited, $skipped
        );
        $this->info($msg);
        Log::info('[GenerateCarryForward] ' . $msg, compact('fromYear', 'toYear', 'cap'));

        return self::SUCCESS;
    }
}

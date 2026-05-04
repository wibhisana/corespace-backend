<?php

namespace App\Modules\HRIS\Services;

use App\Modules\HRIS\Exceptions\InsufficientLeaveBalanceException;
use App\Modules\HRIS\Models\LeaveBalance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeaveBalanceService
{
    /**
     * Bucket aktif (belum kedaluwarsa pada `asOf`), diurutkan FIFO:
     * `expires_at` paling awal duluan. Bucket tanpa expires_at (NULL)
     * ditaruh paling akhir.
     *
     * Carry-forward selalu duluan otomatis karena expires-nya 31-Mar
     * sementara annual 31-Dec.
     */
    public function availableBuckets(int $userId, int $leaveTypeId, ?Carbon $asOf = null): Collection
    {
        return LeaveBalance::query()
            ->where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->active($asOf)
            ->orderByRaw('expires_at IS NULL ASC, expires_at ASC')
            ->get();
    }

    public function totalRemaining(int $userId, int $leaveTypeId, ?Carbon $asOf = null): int
    {
        return (int) $this->availableBuckets($userId, $leaveTypeId, $asOf)
            ->sum(fn (LeaveBalance $b) => $b->remaining_quota);
    }

    /**
     * Kurangi `days` hari dari saldo, FIFO mulai dari bucket yang akan
     * kedaluwarsa duluan (carry-forward → annual). Atomic: kalau saldo
     * tidak cukup, transaksi di-rollback dan exception dilempar.
     *
     * Pakai lockForUpdate supaya dua approval simultan tidak race-condition
     * & oversell saldo.
     *
     * @return array<int, array{balance_id:int, source:string, year:int, deducted:int}>
     * @throws InsufficientLeaveBalanceException
     */
    public function deduct(int $userId, int $leaveTypeId, int $days, ?Carbon $asOf = null): array
    {
        if ($days <= 0) {
            return [];
        }

        return DB::transaction(function () use ($userId, $leaveTypeId, $days, $asOf) {
            $cutoff = ($asOf ?? Carbon::today())->toDateString();

            $buckets = LeaveBalance::query()
                ->where('user_id', $userId)
                ->where('leave_type_id', $leaveTypeId)
                ->where(function ($q) use ($cutoff) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', $cutoff);
                })
                ->orderByRaw('expires_at IS NULL ASC, expires_at ASC')
                ->lockForUpdate()
                ->get();

            $remaining = $days;
            $applied = [];

            foreach ($buckets as $bucket) {
                if ($remaining <= 0) {
                    break;
                }

                $available = (int) $bucket->total_quota - (int) $bucket->used_quota;
                if ($available <= 0) {
                    continue;
                }

                $take = min($available, $remaining);
                $bucket->used_quota = (int) $bucket->used_quota + $take;
                $bucket->save();

                $applied[] = [
                    'balance_id' => (int) $bucket->id,
                    'source'     => (string) $bucket->source,
                    'year'       => (int) $bucket->year,
                    'deducted'   => $take,
                ];
                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new InsufficientLeaveBalanceException(
                    "Saldo cuti tidak mencukupi. Diminta {$days} hari, kurang {$remaining} hari."
                );
            }

            return $applied;
        });
    }

    /**
     * Reverse deduksi (mis. cuti yang sudah approved kemudian dibatalkan).
     * Mengembalikan ke bucket yang sama. Kalau bucket sudah expired,
     * used_quota tetap dikurangi secara konsisten — tapi user tidak bisa
     * pakai ulang karena scope `active()` mengexclude bucket expired.
     *
     * @param array<int, array{balance_id:int, deducted:int}> $allocation
     */
    public function restore(array $allocation): void
    {
        if (empty($allocation)) {
            return;
        }

        DB::transaction(function () use ($allocation) {
            foreach ($allocation as $entry) {
                LeaveBalance::where('id', $entry['balance_id'])
                    ->lockForUpdate()
                    ->update([
                        'used_quota' => DB::raw(
                            'GREATEST(0, used_quota - ' . (int) $entry['deducted'] . ')'
                        ),
                    ]);
            }
        });
    }
}

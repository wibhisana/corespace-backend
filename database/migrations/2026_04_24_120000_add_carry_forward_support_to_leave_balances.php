<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mengubah leave_balances menjadi bucket-row model:
     * - Satu row per (user, leave_type, year, source).
     * - source = 'annual' (jatah reguler tahun berjalan) atau
     *            'carry_forward' (sisa tahun lalu yang dibawa).
     * - expires_at = tanggal hangus bucket. NULL = tidak pernah hangus.
     *
     * Contoh state setelah carry_forward 2025 di-rollover ke 2026:
     *   id  user  type  year  source         total  used  expires_at
     *   100   1    1   2026  annual          12     0    2026-12-31
     *   101   1    1   2025  carry_forward    3     0    2026-03-31
     *
     * FIFO deduction (di service layer) order BY expires_at ASC:
     * carry_forward dilumat duluan karena expires-nya paling awal.
     */
    public function up(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            // Catatan: ->after() di-ignore oleh Postgres (tidak ada konsep
            // posisi kolom). Sengaja tidak dipakai supaya konsisten lintas-driver.
            $table->string('source', 20)->default('annual');
            $table->date('expires_at')->nullable();

            $table->dropUnique(['user_id', 'leave_type_id', 'year']);
            $table->unique(
                ['user_id', 'leave_type_id', 'year', 'source'],
                'leave_balances_unique_bucket'
            );

            // Index untuk hot-path: ambil bucket aktif per user+type, urut FIFO.
            $table->index(
                ['user_id', 'leave_type_id', 'expires_at'],
                'leave_balances_user_type_expiry_idx'
            );
        });

        // Backfill expires_at = 31-Dec tahun-nya. Cast eksplisit karena di
        // Postgres `year` ter-map ke smallint (tidak ada tipe YEAR).
        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            DB::statement("UPDATE leave_balances SET expires_at = (year::text || '-12-31')::date WHERE expires_at IS NULL");
        } else {
            DB::statement("UPDATE leave_balances SET expires_at = CAST(CONCAT(year, '-12-31') AS DATE) WHERE expires_at IS NULL");
        }
    }

    public function down(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->dropIndex('leave_balances_user_type_expiry_idx');
            $table->dropUnique('leave_balances_unique_bucket');
            $table->unique(['user_id', 'leave_type_id', 'year']);
            $table->dropColumn(['source', 'expires_at']);
        });
    }
};

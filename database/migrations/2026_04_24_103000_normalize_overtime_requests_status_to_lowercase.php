<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Normalisasi status overtime_requests ke lowercase (konsisten dengan
     * leave_requests). Cross-driver: MySQL pakai ENUM asli; PostgreSQL
     * pakai VARCHAR + CHECK constraint — pendekatan beda per driver.
     *
     * Urutan operasi (penting):
     *   1. Hapus CHECK constraint (Postgres) supaya value lowercase tidak
     *      ditolak saat UPDATE.
     *   2. Ubah tipe kolom ke VARCHAR(20) via Schema builder (cross-driver).
     *   3. LOWER() data existing.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // Postgres: enum() di Laravel dibuat sebagai varchar + inline CHECK.
            // Query pg_constraint untuk cari nama constraint aktual (konvensi
            // default `{table}_{column}_check` tapi kita defensive saja).
            $constraints = DB::select("
                SELECT c.conname
                FROM pg_constraint c
                JOIN pg_class t ON c.conrelid = t.oid
                JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = ANY(c.conkey)
                WHERE t.relname = 'overtime_requests'
                  AND a.attname = 'status'
                  AND c.contype = 'c'
            ");

            foreach ($constraints as $row) {
                DB::statement('ALTER TABLE overtime_requests DROP CONSTRAINT "' . $row->conname . '"');
            }
        }

        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->change();
        });

        DB::statement("UPDATE overtime_requests SET status = LOWER(status)");
    }

    public function down(): void
    {
        // Pakai CONCAT + SUBSTRING(str, start, length) — supported di MySQL
        // dan Postgres modern. Kembalikan data ke TitleCase dulu supaya
        // CHECK/ENUM constraint baru tidak reject existing data.
        DB::statement("UPDATE overtime_requests SET status = CONCAT(UPPER(SUBSTRING(status, 1, 1)), SUBSTRING(status, 2))");

        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])
                  ->default('Pending')
                  ->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah flag `is_carry_forwardable` agar command carry-forward bisa
     * filter berdasarkan kebijakan, bukan hardcode nama leave_type.
     *
     * Backfill: aktifkan untuk leave_type 'Cuti Tahunan' supaya behavior
     * langsung match dengan eksekusi command sebelumnya (tidak ada gap).
     */
    public function up(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->boolean('is_carry_forwardable')->default(false);
        });

        DB::table('leave_types')
            ->where('name', 'Cuti Tahunan')
            ->update(['is_carry_forwardable' => true]);
    }

    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn('is_carry_forwardable');
        });
    }
};

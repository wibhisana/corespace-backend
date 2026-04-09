<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Unit Bisnis (Holding / Anak Perusahaan)
            $table->foreignId('unit_id')
                ->nullable()
                ->after('department_id')
                ->constrained('units')
                ->nullOnDelete();

            // Path file dokumen (Private Storage)
            $table->string('id_card_path')->nullable()->after('id_card_number');
            $table->string('tax_id_path')->nullable()->after('tax_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['unit_id', 'id_card_path', 'tax_id_path']);
        });
    }
};

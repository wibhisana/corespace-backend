<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('leave_quotas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->year('year'); // Tahun kuota (misal: 2025, 2026)
        $table->integer('quota'); // Total hak cuti (misal: 12 atau 24)
        $table->integer('used')->default(0); // Berapa yang sudah dipakai
        $table->date('expires_at'); // Kapan hangus (misal: 2026-03-31)
        $table->timestamps();

        // Satu user hanya punya 1 ember per tahun
        $table->unique(['user_id', 'year']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_quotas');
    }
};

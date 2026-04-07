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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // Relasi ke karyawan yang absen
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Tanggal absen
            $table->date('date');
            // Waktu masuk & pulang
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            // Opsional: Lokasi, foto selfie, atau catatan
            $table->text('notes')->nullable();
            $table->timestamps();

            // Mencegah 1 user absen 2 kali di hari yang sama
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

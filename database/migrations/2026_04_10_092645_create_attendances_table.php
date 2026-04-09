<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Relasi ke karyawan yang absen
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Relasi ke Master Sif (agar tahu hari itu dia pakai jadwal yang mana)
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();

            $table->date('date');

            // 💡 PERUBAHAN PENTING: Dari time() menjadi dateTime() agar support sif malam (lintas hari)
            $table->dateTime('clock_in')->nullable();
            $table->dateTime('clock_out')->nullable();

            // Kalkulasi keterlambatan
            $table->integer('lateness_minutes')->default(0);
            $table->integer('early_out_minutes')->default(0);

            // Status absensi hari itu
            $table->enum('status', [
                'Present',    // Hadir Tepat Waktu
                'Late',       // Hadir tapi Terlambat
                'Absent',     // Tidak Hadir / Bolos
                'On_Leave',   // Sedang Cuti
                'Day_Off'     // Hari Libur
            ])->default('Absent');

            // Opsional: Lokasi GPS
            $table->string('clock_in_location')->nullable();
            $table->string('clock_out_location')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            // Mencegah 1 user absen 2 kali di hari yang sama
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

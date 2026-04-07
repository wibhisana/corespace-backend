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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Periode Gaji
            $table->integer('month'); // Bulan (1-12)
            $table->integer('year');  // Tahun (misal: 2026)

            // Komponen Gaji
            $table->decimal('basic_salary', 15, 2);
            $table->integer('total_present')->default(0);
            $table->decimal('deduction', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2);

            // Status Pembayaran
            $table->boolean('is_paid')->default(false);

            $table->timestamps();

            // Aturan Bisnis: 1 Karyawan hanya memiliki 1 slip gaji untuk bulan dan tahun yang sama
            $table->unique(['user_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};

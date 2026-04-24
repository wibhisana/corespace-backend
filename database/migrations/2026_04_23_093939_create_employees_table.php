<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // --- Data HRD / Internal ---
            $table->string('employee_id')->unique()->nullable(); // NIK Internal Perusahaan
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('join_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'resigned'])->default('active');

            // --- File Uploads ---
            $table->string('ktp_image_path')->nullable(); // Foto KTP mentah
            $table->string('face_image_path')->nullable(); // Foto Wajah untuk Verifikasi AI (Selfie/Pas Foto)

            // --- Data Hasil Ekstraksi AI KTP ---
            $table->string('nik_ktp', 16)->unique()->nullable();
            $table->string('nama')->nullable();
            $table->string('tempat_tgl_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt_rw')->nullable();
            $table->string('kel_desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->string('berlaku_hingga')->nullable();

            // Meta Data AI (opsional, untuk audit HRD)
            $table->boolean('is_ktp_verified_by_ai')->default(false);
            $table->decimal('ai_completeness_score', 5, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

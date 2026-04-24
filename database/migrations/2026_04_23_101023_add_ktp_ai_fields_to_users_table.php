<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kita tambah prefix 'ktp_' untuk field nama/alamat dll agar tidak bentrok
            // dengan kolom nama/alamat bawaan (jika sudah ada di users)
            $table->string('ktp_image_path')->nullable();

            $table->string('nik_ktp', 16)->unique()->nullable();
            $table->string('ktp_nama')->nullable(); // Pakai prefix ktp_ agar tidak bentrok dgn $table->name bawaan Laravel
            $table->string('ktp_tempat_tgl_lahir')->nullable();
            $table->string('ktp_jenis_kelamin')->nullable();
            $table->text('ktp_alamat')->nullable();
            $table->string('ktp_rt_rw')->nullable();
            $table->string('ktp_kel_desa')->nullable();
            $table->string('ktp_kecamatan')->nullable();
            $table->string('ktp_agama')->nullable();
            $table->string('ktp_status_perkawinan')->nullable();
            $table->string('ktp_pekerjaan')->nullable();
            $table->string('ktp_kewarganegaraan')->nullable();
            $table->string('ktp_berlaku_hingga')->nullable();

            // Meta Data AI
            $table->boolean('is_ktp_verified_by_ai')->default(false);
            $table->decimal('ai_completeness_score', 5, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ktp_image_path', 'nik_ktp', 'ktp_nama', 'ktp_tempat_tgl_lahir',
                'ktp_jenis_kelamin', 'ktp_alamat', 'ktp_rt_rw', 'ktp_kel_desa',
                'ktp_kecamatan', 'ktp_agama', 'ktp_status_perkawinan', 'ktp_pekerjaan',
                'ktp_kewarganegaraan', 'ktp_berlaku_hingga', 'is_ktp_verified_by_ai',
                'ai_completeness_score'
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ==========================================
            // KATEGORI 1: Data HR (Diisi Admin/HR)
            // ==========================================
            $table->string('nik')->unique()->nullable()->after('phone_number');
            $table->string('job_title')->nullable()->after('department_id');
            $table->date('join_date')->nullable()->after('job_title');
            $table->string('employment_status')->nullable()->after('join_date'); // Tetap, Kontrak, Probation

            // ==========================================
            // KATEGORI 2: Data Karyawan (Self-Service/ESS)
            // ==========================================
            $table->string('personal_email')->nullable()->after('employment_status');
            $table->text('current_address')->nullable()->after('personal_email');
            $table->string('gender', 1)->nullable()->after('current_address'); // L / P
            $table->string('birth_place')->nullable()->after('gender');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('marital_status')->nullable()->after('birth_date');
            $table->string('emergency_contact_name')->nullable()->after('marital_status');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('id_card_number')->nullable()->after('emergency_contact_phone'); // KTP
            $table->string('tax_id')->nullable()->after('id_card_number'); // NPWP
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nik',
                'job_title',
                'join_date',
                'employment_status',
                'personal_email',
                'current_address',
                'gender',
                'birth_place',
                'birth_date',
                'marital_status',
                'emergency_contact_name',
                'emergency_contact_phone',
                'id_card_number',
                'tax_id',
            ]);
        });
    }
};

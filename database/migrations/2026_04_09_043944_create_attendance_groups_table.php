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
        // Pastikan nama tabel di sini adalah 'attendance_groups', BUKAN 'shifts'
        Schema::create('attendance_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cth: Grup Karyawan Pusat, Grup Satpam
            $table->text('description')->nullable();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_groups');
    }
};

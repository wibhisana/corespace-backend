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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cth: Cuti Tahunan, Cuti Sakit, Cuti Menikah
            $table->integer('default_quota')->default(0); // Jatah default per tahun
            $table->boolean('is_unlimited')->default(false); // True untuk Cuti Berduka/Melahirkan
            $table->boolean('requires_attachment')->default(false); // Wajib upload dokumen?
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};

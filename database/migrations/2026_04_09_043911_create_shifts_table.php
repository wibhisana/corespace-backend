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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cth: Sif Pagi, Sif Fleksibel
            $table->enum('type', ['Fixed', 'Scheduled', 'Free'])->default('Fixed');
            $table->time('start_time')->nullable(); // Jam masuk (Kosong jika Sif Free)
            $table->time('end_time')->nullable();   // Jam pulang (Kosong jika Sif Free)
            $table->integer('grace_period')->default(15); // Toleransi keterlambatan dalam menit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};

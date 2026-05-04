<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SAFETY NOTE — JANGAN jalankan migration ini sampai semua referensi
     * ke leave_quotas / LeaveQuota dipindah ke leave_balances / LeaveBalance.
     *
     * Cleanup checklist (per audit 2026-04-24):
     *   [ ] app/Models/User.php                 — hapus relasi leaveQuotas()
     *   [ ] app/Modules/HRIS/Models/LeaveQuota.php  — hapus model
     *   [ ] app/Modules/IAM/Filament/Resources/Users/RelationManagers/
     *           LeaveQuotasRelationManager.php  — ganti ke LeaveBalances
     *   [ ] app/Modules/IAM/Filament/Resources/Users/UserResource.php
     *           — registrasi RelationManager baru
     *
     * Setelah semua centang, baru jalankan: php artisan migrate
     */
    public function up(): void
    {
        Schema::dropIfExists('leave_quotas');
    }

    public function down(): void
    {
        Schema::create('leave_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->year('year');
            $table->integer('quota');
            $table->integer('used')->default(0);
            $table->date('expires_at');
            $table->timestamps();
            $table->unique(['user_id', 'year']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->json('allowance_details')->nullable()->after('net_salary');
            $table->json('deduction_details')->nullable()->after('allowance_details');
            $table->decimal('total_allowances', 15, 2)->default(0)->after('deduction_details');
            $table->decimal('total_deductions', 15, 2)->default(0)->after('total_allowances');
            $table->string('status')->default('Draft')->after('total_deductions'); // Draft, Approved, Paid
            $table->date('payment_date')->nullable()->after('status');
        });

        // Migrasi data lama: is_paid=true → status='Paid'
        \Illuminate\Support\Facades\DB::table('payrolls')
            ->where('is_paid', true)
            ->update(['status' => 'Paid']);
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'allowance_details', 'deduction_details',
                'total_allowances', 'total_deductions',
                'status', 'payment_date',
            ]);
        });
    }
};

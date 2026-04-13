<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->foreignId('leave_type_id')
                ->nullable()
                ->after('user_id')
                ->constrained('leave_types')
                ->nullOnDelete();

            $table->integer('total_days')->default(0)->after('end_date');
            $table->string('attachment_path')->nullable()->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['leave_type_id']);
            $table->dropColumn(['leave_type_id', 'total_days', 'attachment_path']);
        });
    }
};

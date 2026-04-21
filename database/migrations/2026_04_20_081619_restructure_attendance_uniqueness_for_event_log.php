<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendances_user_id_date_unique');
        });

        DB::statement('
            CREATE UNIQUE INDEX attendances_user_id_date_type_unique
            ON attendances (user_id, date, type)
            WHERE type IS NOT NULL
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS attendances_user_id_date_type_unique');

        Schema::table('attendances', function (Blueprint $table) {
            $table->unique(['user_id', 'date'], 'attendances_user_id_date_unique');
        });
    }
};

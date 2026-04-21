<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add the session column to clinic_visits
        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->unsignedTinyInteger('queue_session')->default(1)->after('visit_number');
        });

        // 2. Add current_queue_session tracker to units
        Schema::table('units', function (Blueprint $table) {
            $table->unsignedTinyInteger('current_queue_session')->default(1)->after('queue_started_at');
        });

        // 3. Add new unique constraint (includes queue_session) BEFORE dropping old one,
        //    so InnoDB always has an index covering unit_id for the FK.
        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->unique(
                ['unit_id', 'visit_date', 'category', 'queue_session', 'visit_number'],
                'cv_unit_date_cat_sess_num_unique'
            );
        });

        // 4. Now safe to drop the old constraint
        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->dropUnique('cv_unit_date_cat_num_unique');
        });
    }

    public function down(): void
    {
        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->unique(
                ['unit_id', 'visit_date', 'category', 'visit_number'],
                'cv_unit_date_cat_num_unique'
            );
        });

        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->dropUnique('cv_unit_date_cat_sess_num_unique');
            $table->dropColumn('queue_session');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('current_queue_session');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add the new index first so MySQL can use it for the unit_id FK,
        // then drop the old one (InnoDB refuses to drop an index it relies on for a FK).
        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->unique(['unit_id', 'visit_date', 'category', 'visit_number'],
                           'cv_unit_date_cat_num_unique');
        });

        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->dropUnique('clinic_visits_unit_id_visit_date_visit_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->unique(['unit_id', 'visit_date', 'visit_number']);
        });

        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->dropUnique('cv_unit_date_cat_num_unique');
        });
    }
};

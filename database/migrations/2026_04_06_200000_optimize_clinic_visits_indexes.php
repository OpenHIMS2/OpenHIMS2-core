<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add institution_id as a denormalized column for fast institution-level queries
        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->foreignId('institution_id')
                  ->nullable()
                  ->after('unit_id')
                  ->constrained('institutions')
                  ->nullOnDelete();
        });

        // 2. Back-fill institution_id from the units table for existing rows
        DB::statement('
            UPDATE clinic_visits cv
            JOIN units u ON cv.unit_id = u.id
            SET cv.institution_id = u.institution_id
        ');

        // 3. Add composite indexes for the most frequent query patterns
        Schema::table('clinic_visits', function (Blueprint $table) {
            // Queue loading: unit + date + status (most-hit query)
            $table->index(['unit_id', 'visit_date', 'status'], 'cv_unit_date_status');

            // Patient history: all visits for a patient ordered by date
            $table->index(['patient_id', 'visit_date'], 'cv_patient_date');

            // Institution-level reporting by date
            $table->index(['institution_id', 'visit_date'], 'cv_institution_date');

            // Date-only index for system-wide date-range reports
            $table->index('visit_date', 'cv_visit_date');
        });
    }

    public function down(): void
    {
        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->dropIndex('cv_unit_date_status');
            $table->dropIndex('cv_patient_date');
            $table->dropIndex('cv_institution_date');
            $table->dropIndex('cv_visit_date');
            $table->dropForeign(['institution_id']);
            $table->dropColumn('institution_id');
        });
    }
};

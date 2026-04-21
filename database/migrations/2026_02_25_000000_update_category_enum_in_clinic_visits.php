<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand enum to include new values alongside old 'clinic'
        DB::statement("ALTER TABLE clinic_visits MODIFY COLUMN category ENUM('opd','clinic','new_clinic_visit','recurrent_clinic_visit','staff') DEFAULT 'opd'");

        // Step 2: convert existing 'clinic' rows to 'new_clinic_visit'
        DB::table('clinic_visits')->where('category', 'clinic')->update(['category' => 'new_clinic_visit']);

        // Step 3: remove 'clinic' from enum now that no rows use it
        DB::statement("ALTER TABLE clinic_visits MODIFY COLUMN category ENUM('opd','new_clinic_visit','recurrent_clinic_visit','staff') DEFAULT 'opd'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE clinic_visits MODIFY COLUMN category ENUM('opd','clinic','new_clinic_visit','recurrent_clinic_visit','staff') DEFAULT 'opd'");

        DB::table('clinic_visits')
            ->whereIn('category', ['new_clinic_visit', 'recurrent_clinic_visit'])
            ->update(['category' => 'clinic']);

        DB::statement("ALTER TABLE clinic_visits MODIFY COLUMN category ENUM('opd','clinic','staff') DEFAULT 'opd'");
    }
};

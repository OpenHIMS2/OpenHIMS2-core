<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand enum to include 'urgent' alongside 'staff'
        DB::statement("ALTER TABLE clinic_visits MODIFY COLUMN category ENUM('opd','new_clinic_visit','recurrent_clinic_visit','staff','urgent') DEFAULT 'opd'");

        // Step 2: migrate existing 'staff' rows to 'urgent'
        DB::table('clinic_visits')->where('category', 'staff')->update(['category' => 'urgent']);

        // Step 3: drop 'staff' from enum
        DB::statement("ALTER TABLE clinic_visits MODIFY COLUMN category ENUM('opd','new_clinic_visit','recurrent_clinic_visit','urgent') DEFAULT 'opd'");
    }

    public function down(): void
    {
        // Step 1: expand enum to include 'staff' alongside 'urgent'
        DB::statement("ALTER TABLE clinic_visits MODIFY COLUMN category ENUM('opd','new_clinic_visit','recurrent_clinic_visit','urgent','staff') DEFAULT 'opd'");

        // Step 2: migrate 'urgent' rows back to 'staff'
        DB::table('clinic_visits')->where('category', 'urgent')->update(['category' => 'staff']);

        // Step 3: drop 'urgent' from enum
        DB::statement("ALTER TABLE clinic_visits MODIFY COLUMN category ENUM('opd','new_clinic_visit','recurrent_clinic_visit','staff') DEFAULT 'opd'");
    }
};

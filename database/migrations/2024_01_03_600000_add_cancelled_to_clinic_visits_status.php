<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE clinic_visits MODIFY COLUMN status ENUM('waiting','in_progress','visited','cancelled') NOT NULL DEFAULT 'waiting'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE clinic_visits MODIFY COLUMN status ENUM('waiting','in_progress','visited') NOT NULL DEFAULT 'waiting'");
    }
};

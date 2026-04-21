<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->string('opd_number', 20)->nullable()->after('category');
            $table->decimal('height', 5, 1)->nullable()->after('opd_number');   // cm
            $table->decimal('weight', 5, 1)->nullable()->after('height');       // kg
            $table->unsignedSmallInteger('bp_systolic')->nullable()->after('weight');
            $table->unsignedSmallInteger('bp_diastolic')->nullable()->after('bp_systolic');
            $table->string('clinic_number', 50)->nullable()->after('bp_diastolic');
        });
    }

    public function down(): void
    {
        Schema::table('clinic_visits', function (Blueprint $table) {
            $table->dropColumn(['opd_number', 'height', 'weight', 'bp_systolic', 'bp_diastolic', 'clinic_number']);
        });
    }
};

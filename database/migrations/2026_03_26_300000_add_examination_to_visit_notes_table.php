<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_notes', function (Blueprint $table) {
            $table->json('general_looking')->nullable();
            $table->unsignedSmallInteger('pulse_rate')->nullable();
            $table->json('cardiology_findings')->nullable();
            $table->json('respiratory_findings')->nullable();
            $table->json('abdominal_findings')->nullable();
            $table->json('neurological_findings')->nullable();
            $table->json('dermatological_findings')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('visit_notes', function (Blueprint $table) {
            $table->dropColumn([
                'general_looking', 'pulse_rate',
                'cardiology_findings', 'respiratory_findings',
                'abdominal_findings', 'neurological_findings', 'dermatological_findings',
            ]);
        });
    }
};

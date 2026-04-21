<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_drugs', function (Blueprint $table) {
            $table->enum('section', ['clinic', 'management'])->default('clinic')->after('visit_id');
            $table->string('duration', 50)->nullable()->after('frequency');
        });
    }

    public function down(): void
    {
        Schema::table('visit_drugs', function (Blueprint $table) {
            $table->dropColumn(['section', 'duration']);
        });
    }
};

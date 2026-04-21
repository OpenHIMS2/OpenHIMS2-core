<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drug_name_defaults', function (Blueprint $table) {
            $table->string('duration', 50)->nullable()->after('frequency');
        });
    }

    public function down(): void
    {
        Schema::table('drug_name_defaults', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};

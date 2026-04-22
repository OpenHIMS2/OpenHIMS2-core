<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_templates', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('code');
        });

        Schema::table('view_templates', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('blade_path');
        });
    }

    public function down(): void
    {
        Schema::table('unit_templates', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });

        Schema::table('view_templates', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }
};

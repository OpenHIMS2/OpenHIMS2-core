<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_notes', function (Blueprint $table) {
            $table->text('management_instruction')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('visit_notes', function (Blueprint $table) {
            $table->dropColumn('management_instruction');
        });
    }
};

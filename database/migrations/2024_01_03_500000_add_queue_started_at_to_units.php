<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            // Tracks when the current queue session started.
            // NULL = from start of day.  Set to now() when clerk creates a new queue.
            $table->dateTime('queue_started_at')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('queue_started_at');
        });
    }
};

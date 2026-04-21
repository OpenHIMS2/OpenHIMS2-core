<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_views', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_view_id')->constrained('unit_views')->onDelete('cascade');
            $table->primary(['user_id', 'unit_view_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_views');
    }
};

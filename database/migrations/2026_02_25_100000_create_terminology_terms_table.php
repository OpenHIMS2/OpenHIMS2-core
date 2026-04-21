<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terminology_terms', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('term');
            $table->timestamps();

            $table->unique(['category', 'term']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terminology_terms');
    }
};

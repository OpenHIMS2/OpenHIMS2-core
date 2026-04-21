<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('clinic_visits')->cascadeOnDelete();
            $table->json('presenting_complaints')->nullable();
            $table->json('complaint_durations')->nullable();
            $table->json('past_medical_history')->nullable();
            $table->json('past_surgical_history')->nullable();
            $table->json('social_history')->nullable();
            $table->json('menstrual_history')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_notes');
    }
};

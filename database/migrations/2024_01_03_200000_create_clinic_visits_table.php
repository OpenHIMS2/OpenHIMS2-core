<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->date('visit_date');
            $table->unsignedSmallInteger('visit_number');
            $table->enum('status', ['waiting', 'in_progress', 'visited'])->default('waiting');
            $table->foreignId('registered_by')->nullable()->nullOnDelete()->constrained('users');
            $table->timestamps();

            $table->unique(['unit_id', 'visit_date', 'visit_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_visits');
    }
};

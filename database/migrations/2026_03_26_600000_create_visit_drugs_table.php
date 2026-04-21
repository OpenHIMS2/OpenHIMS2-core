<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_drugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('clinic_visits')->cascadeOnDelete();
            $table->enum('type', ['Oral','S/C','IM','IV','S/L','Syrup','MDI','DPI','Suppository','LA'])->default('Oral');
            $table->string('name', 200);
            $table->string('dose', 50);
            $table->enum('unit', ['mg','g','mcg','ml','tabs','item']);
            $table->enum('frequency', ['mane','nocte','bd','tds','daily','EOD','SOS']);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('visit_drug_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('clinic_visits')->cascadeOnDelete();
            $table->unsignedBigInteger('drug_id')->nullable();   // not FK — drug may be deleted
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('action', ['added','edited','deleted']);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_drug_changes');
        Schema::dropIfExists('visit_drugs');
    }
};

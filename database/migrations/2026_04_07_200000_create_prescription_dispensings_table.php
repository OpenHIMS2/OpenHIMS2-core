<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_dispensings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('clinic_visits')->cascadeOnDelete();
            $table->foreignId('visit_drug_id')->constrained('visit_drugs')->cascadeOnDelete();
            $table->foreignId('stock_id')->nullable()->constrained('pharmacy_stock')->nullOnDelete();
            $table->enum('status', ['prescribed', 'os'])->default('prescribed');
            $table->unsignedInteger('quantity_dispensed')->default(0);
            $table->foreignId('dispensed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dispensed_at')->nullable();
            $table->timestamps();

            $table->unique('visit_drug_id'); // one record per drug per visit
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_dispensings');
    }
};

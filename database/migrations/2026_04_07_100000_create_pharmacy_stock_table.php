<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_view_id')->constrained('unit_views')->cascadeOnDelete();
            $table->string('drug_name', 200);
            $table->unsignedInteger('initial_amount')->default(0);
            $table->unsignedInteger('remaining')->default(0);
            $table->date('expiry_date')->nullable();
            $table->boolean('is_out_of_stock')->default(false);
            $table->unsignedSmallInteger('low_stock_threshold')->default(10);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['unit_view_id', 'drug_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_stock');
    }
};

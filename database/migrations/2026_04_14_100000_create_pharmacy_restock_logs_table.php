<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_restock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_view_id')->constrained('unit_views')->cascadeOnDelete();
            $table->foreignId('stock_id')->nullable()->constrained('pharmacy_stock')->nullOnDelete();
            $table->string('drug_name', 200);
            $table->enum('action', ['new_stock', 'restock']);
            $table->unsignedInteger('amount');
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['unit_view_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_restock_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drug_names', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->unique();
            $table->timestamps();
        });

        Schema::create('drug_name_defaults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drug_name_id')->unique()->constrained('drug_names')->cascadeOnDelete();
            $table->enum('type', ['Oral','S/C','IM','IV','S/L','Syrup','MDI','DPI','Suppository','LA'])->default('Oral');
            $table->string('dose', 50);
            $table->enum('unit', ['mg','g','mcg','ml','tabs','item']);
            $table->enum('frequency', ['mane','nocte','bd','tds','daily','EOD','SOS']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_name_defaults');
        Schema::dropIfExists('drug_names');
    }
};

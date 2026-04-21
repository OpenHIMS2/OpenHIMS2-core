<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('dob')->nullable();
            $table->smallInteger('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('nic')->nullable()->unique();
            $table->string('mobile')->nullable()->unique();
            $table->string('phn')->unique()->nullable(); // filled after insert
            $table->string('guardian_nic')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};

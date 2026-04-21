<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->date('dob')->nullable()->after('phone');
            $table->string('gender', 10)->nullable()->after('dob');
            $table->string('address')->nullable()->after('gender');
            $table->string('designation', 50)->nullable()->after('address');
            $table->string('specialty', 100)->nullable()->after('designation');
            $table->string('qualification', 200)->nullable()->after('specialty');
            $table->string('registration_no', 50)->nullable()->after('qualification');
            $table->text('bio')->nullable()->after('registration_no');
            $table->string('profile_image')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'dob', 'gender', 'address',
                'designation', 'specialty', 'qualification',
                'registration_no', 'bio', 'profile_image',
            ]);
        });
    }
};

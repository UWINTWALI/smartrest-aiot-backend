<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing columns to patient_profiles table
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->string('emergency_contact_name', 100)->nullable()->after('sex');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            $table->text('health_conditions')->nullable()->after('emergency_contact_phone');
            $table->text('medications')->nullable()->after('health_conditions');
        });

        // Add missing columns to doctor_profiles table
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->string('institution', 100)->nullable()->after('specialty');
            $table->integer('years_experience')->nullable()->after('institution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_name',
                'emergency_contact_phone', 
                'health_conditions',
                'medications'
            ]);
        });

        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'institution',
                'years_experience'
            ]);
        });
    }
};

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
    {        // Create patient_profiles table
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->uuid('patient_id')->primary();
            $table->foreign('patient_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->char('national_id', 16)->nullable()->unique();
            $table->date('date_of_birth')->nullable();
            $table->char('sex', 1)->nullable()->comment("M, F, or O (Other / prefer not to say)");
            $table->timestampTz('created_at')->default(now());
        });

        // Create doctor_profiles table
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->uuid('doctor_id')->primary();
            $table->foreign('doctor_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->string('license_no', 40)->nullable();
            $table->string('specialty', 60)->nullable();
            $table->timestampTz('created_at')->default(now());
        });

        // Create doctor_patients junction table
        Schema::create('doctor_patients', function (Blueprint $table) {
            $table->uuid('doctor_id');
            $table->uuid('patient_id');
            $table->timestampTz('assigned_at')->default(now());

            // Define composite primary key
            $table->primary(['doctor_id', 'patient_id']);

            // Add foreign key constraints
            $table->foreign('doctor_id')->references('doctor_id')->on('doctor_profiles')->onDelete('cascade');
            $table->foreign('patient_id')->references('patient_id')->on('patient_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_patients');
        Schema::dropIfExists('doctor_profiles');
        Schema::dropIfExists('patient_profiles');
    }
};

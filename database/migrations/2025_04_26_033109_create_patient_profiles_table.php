<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->bigInteger('patient_id')->primary();
            $table->char('national_id', 16)->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->char('sex', 1)->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->foreign('patient_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_profiles');
    }
};

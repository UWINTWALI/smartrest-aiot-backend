<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */    public function up(): void
    {
        // Create sensor_type enum if it doesn't exist
        $typeExists = DB::select("SELECT typname FROM pg_type WHERE typname = 'sensor_type'");
        if (empty($typeExists)) {
            DB::statement("CREATE TYPE sensor_type AS ENUM (
                'pressure', 'heart_rate', 'breathing_rate', 'temperature', 
                'humidity', 'body_movement', 'posture', 'vibration', 'sleep_apnea'
            )");
        }

        // Create sensor_readings table
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->uuid('reading_id')->primary();
            $table->uuid('patient_id');
            $table->string('bed_id', 64);
            $table->enum('sensor_type', [
                'pressure', 'heart_rate', 'breathing_rate', 'temperature', 
                'humidity', 'body_movement', 'posture', 'vibration', 'sleep_apnea'
            ]);
            $table->float('sensor_value');
            $table->string('sensor_unit', 20)->nullable();
            $table->timestampTz('timestamp')->default(now());
            $table->jsonb('additional_metadata')->nullable();

            // Add foreign key
            $table->foreign('patient_id')->references('patient_id')->on('patient_profiles')->onDelete('cascade');
            
            // Add indexes
            $table->index(['patient_id', 'timestamp']);
            $table->index(['bed_id']);
        });

        // Add constraint for pressure readings
        DB::statement("ALTER TABLE sensor_readings ADD CONSTRAINT valid_pressure 
            CHECK (sensor_type != 'pressure' OR (sensor_value >= 0 AND sensor_value <= 100))");

        // Create messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('message_id')->primary();
            $table->uuid('sender_id');
            $table->uuid('recipient_id');
            $table->text('title')->nullable();
            $table->text('body');
            $table->string('type', 24);
            $table->boolean('is_read')->default(false);
            $table->timestampTz('sent_at')->default(now());

            // Add foreign keys
            $table->foreign('sender_id')->references('user_id')->on('users');
            $table->foreign('recipient_id')->references('user_id')->on('users');

            // Add indexes
            $table->index(['recipient_id', 'is_read']);
            $table->index(['recipient_id', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('sensor_readings');
        DB::statement("DROP TYPE IF EXISTS sensor_type");
    }
};

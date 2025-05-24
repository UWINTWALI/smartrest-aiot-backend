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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->uuid('log_id')->primary();
            $table->string('bed_id', 64);
            $table->string('severity', 10)->nullable();
            $table->text('message');
            $table->timestampTz('logged_at')->default(now());

            // Add index
            $table->index(['bed_id']);
            $table->index(['logged_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};

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
        Schema::table('messages', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['sender_id']);
            
            // Make sender_id nullable
            $table->uuid('sender_id')->nullable()->change();
            
            // Re-add the foreign key with nullable constraint
            $table->foreign('sender_id')->references('user_id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->uuid('sender_id')->nullable(false)->change();
            $table->foreign('sender_id')->references('user_id')->on('users');
        });
    }
};

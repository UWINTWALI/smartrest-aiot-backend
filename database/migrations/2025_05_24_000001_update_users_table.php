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
        // Create user_role enum type if it doesn't exist
        $typeExists = DB::select("SELECT typname FROM pg_type WHERE typname = 'user_role'");
        if (empty($typeExists)) {
            DB::statement("CREATE TYPE user_role AS ENUM ('patient', 'doctor', 'customer', 'admin')");
        }

        // Update users table with new columns
        Schema::table('users', function (Blueprint $table) {
            // Rename name to first_name
            $table->renameColumn('name', 'first_name');
            
            // Add new columns
            $table->string('last_name', 80)->after('first_name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->uuid('user_id')->after('id');
            $table->enum('role', ['patient', 'doctor', 'customer', 'admin'])->after('user_id');

            // Add index on user_id
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('first_name', 'name');
            $table->dropColumn(['last_name', 'phone', 'user_id', 'role']);
        });

        DB::statement("DROP TYPE IF EXISTS user_role");
    }
};

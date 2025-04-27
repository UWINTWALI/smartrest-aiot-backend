<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    public function up()
    {
        // First create the user_role enum type if it doesn't exist
        DB::statement("DO $$ BEGIN
            CREATE TYPE user_role AS ENUM ('admin', 'patient', 'doctor', 'customer');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;");

        // Then create the users table
        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('user_id')->primary();
            $table->string('email', 80)->unique();
            $table->text('password_hash');
            $table->enum('role', ['admin', 'patient', 'doctor', 'customer'])->default('customer');
            $table->string('first_name', 80);
            $table->string('last_name', 80);
            $table->string('phone', 20)->nullable();
            $table->boolean('is_email_verified')->default(false);
            $table->json('permissions')->nullable();
            $table->timestampsTz();
        });

        // Add trigger for updated_at
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_users_timestamp()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ language \'plpgsql\';
        ');

        DB::unprepared('
            CREATE TRIGGER update_users_timestamp
            BEFORE UPDATE ON users
            FOR EACH ROW
            EXECUTE FUNCTION update_users_timestamp();
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_users_timestamp ON users;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_users_timestamp;');
        Schema::dropIfExists('users');
        DB::statement('DROP TYPE IF EXISTS user_role');
    }
}
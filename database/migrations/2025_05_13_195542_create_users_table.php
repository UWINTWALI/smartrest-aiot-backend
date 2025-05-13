<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
{
    // Check if enum type exists before creating it
    $check = DB::select("SELECT 1 FROM pg_type WHERE typname = 'user_role'");
    if (empty($check)) {
        DB::statement("CREATE TYPE user_role AS ENUM ('patient', 'doctor', 'customer', 'admin')");
    }

    Schema::create('users', function (Blueprint $table) {
        $table->uuid('user_id')->primary();
        $table->string('email', 80)->unique();
        $table->text('password');
        $table->enum('role', ['patient', 'doctor', 'customer', 'admin']); // Laravel internally maps to text
        $table->string('first_name', 80);
        $table->string('last_name', 80);
        $table->string('phone', 20)->nullable();
        $table->boolean('is_email_verified')->default(false);
        $table->timestampsTz();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('users');
        DB::statement("DROP TYPE IF EXISTS user_role");
    }
};

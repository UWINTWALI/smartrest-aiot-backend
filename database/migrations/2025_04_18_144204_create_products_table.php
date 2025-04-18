<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('product_id')->primary();      // 36‑char UUID column
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();
            $table->string('firmware_version', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz(0);                    // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

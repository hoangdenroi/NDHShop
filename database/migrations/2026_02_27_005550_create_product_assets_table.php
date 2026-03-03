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
        Schema::create('product_assets', function (Blueprint $table) {
            $table->baseColumns();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('type');
            $table->string('url_or_path');
            $table->string('file_size')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_assets');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng gift_categories — quản lý danh mục quà tặng.
     */
    public function up(): void
    {
        Schema::create('gift_categories', function (Blueprint $table) {
            $table->baseColumns();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();         // Icon name (Material Symbols)
            $table->integer('sort_order')->default(0);   // Thứ tự sắp xếp
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_categories');
    }
};

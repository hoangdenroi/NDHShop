<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng gift_assets — lưu trữ tài nguyên media cho quà tặng.
     * Ảnh động GIF, nhạc MP3, video MP4, Lottie JSON,...
     */
    public function up(): void
    {
        Schema::create('gift_assets', function (Blueprint $table) {
            $table->baseColumns();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');                          // Tên hiển thị: "Pháo hoa vàng"
            $table->string('type');                          // image, gif, audio, video, lottie
            $table->text('url');                             // URL tài nguyên (CDN/external)
            $table->string('thumbnail')->nullable();         // URL ảnh preview
            $table->string('file_size')->nullable();         // Dung lượng: "2.5 MB"
            $table->text('description')->nullable();         // Mô tả ngắn
            $table->json('tags')->nullable();                // Tags tìm kiếm: ["tet", "phao-hoa"]
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_assets');
    }
};

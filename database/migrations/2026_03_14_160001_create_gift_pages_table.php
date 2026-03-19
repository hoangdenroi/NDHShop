<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng gift_pages — lưu trang quà tặng user đã tạo.
     * Mỗi trang có share_code 8 ký tự để chia sẻ link.
     */
    public function up(): void
    {
        Schema::create('gift_pages', function (Blueprint $table) {
            $table->id();
            $table->string('unitcode', 26)->unique();
            $table->string('share_code', 12)->unique();              // Code chia sẻ: "aB3xK9mZ"
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('gift_templates')->cascadeOnDelete();
            $table->json('page_data')->nullable();                   // Dữ liệu user nhập theo schema
            $table->string('meta_title')->nullable();                // OG Title cho SEO
            $table->string('meta_image')->nullable();                // OG Image cho share
            $table->integer('view_count')->default(0);               // Lượt xem
            $table->timestamp('expires_at')->nullable();             // Hết hạn (gói cơ bản 30-60 ngày)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_pages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng gift_templates — lưu mẫu template quà tặng.
     * Code HTML/CSS/JS được lưu dạng Base64 để tránh lỗi ký tự đặc biệt.
     */
    public function up(): void
    {
        Schema::create('gift_templates', function (Blueprint $table) {
            $table->id();
            $table->string('unitcode', 26)->unique();
            $table->string('name');                          // Tên mẫu: "Pháo hoa Tết 2026"
            $table->string('slug')->unique();                // URL-friendly slug
            $table->string('thumbnail')->nullable();         // URL ảnh preview
            $table->string('category')->default('other');    // tet, sinh-nhat, valentine...
            $table->longText('html_code');                   // HTML (Base64 encoded)
            $table->longText('css_code')->nullable();        // CSS (Base64 encoded)
            $table->longText('js_code')->nullable();         // JS hiệu ứng (Base64 encoded)
            $table->json('schema')->nullable();              // JSON định nghĩa form fields
            $table->boolean('is_active')->default(true);
            $table->boolean('is_premium')->default(false);   // Mẫu trả phí
            $table->integer('price')->default(0);            // Giá (VND), 0 = miễn phí
            $table->integer('usage_count')->default(0);      // Số lần sử dụng
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_templates');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng gift_analytics — Tracking chi tiết lượt truy cập (premium only).
     */
    public function up(): void
    {
        Schema::create('gift_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gift_page_id')->constrained('gift_pages')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable()->comment('IP người xem');
            $table->text('user_agent')->nullable()->comment('Thông tin trình duyệt/thiết bị');
            $table->string('referer', 500)->nullable()->comment('Nguồn truy cập');
            $table->timestamp('visited_at')->useCurrent()->comment('Thời gian truy cập');

            // Index cho query thống kê theo gift_page_id
            $table->index('gift_page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_analytics');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Bảng log hoạt động đơn hàng VPS
 * Ghi lại mọi action: tạo, provision, restart, rebuild, đổi pass, gia hạn, hủy, hết hạn
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vps_order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vps_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);        // VD: created, provisioned, restarted, renewed, cancelled
            $table->text('detail')->nullable();   // Chi tiết (VD: error message, OS mới)
            $table->unsignedBigInteger('amount')->default(0); // Số tiền liên quan
            $table->timestamps();

            $table->index(['vps_order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vps_order_logs');
    }
};

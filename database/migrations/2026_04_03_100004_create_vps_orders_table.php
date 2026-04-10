<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Bảng đơn hàng VPS
 * Lưu thông tin đơn hàng, kết nối với Hetzner server_id
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vps_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vps_category_id')->constrained()->cascadeOnDelete();
            $table->string('order_code', 20)->unique();           // VD: "VPS-A1B2C3"
            $table->unsignedBigInteger('hetzner_server_id')->nullable(); // ID server trên Hetzner
            $table->unsignedBigInteger('price');                   // Giá đã trả (VNĐ)
            $table->unsignedInteger('duration_months');             // Thời hạn (tháng)
            $table->string('operating_system');                    // HĐH đã chọn (VD: "ubuntu-22.04")
            $table->string('location');                            // Location đã chọn (VD: "fsn1")
            $table->string('ip_address')->nullable();              // IPv4
            $table->string('username')->nullable();                // Mặc định là root cho Hetzner
            $table->string('ipv6_address')->nullable();            // IPv6
            $table->text('root_password')->nullable();             // Mật khẩu root (encrypted)
            $table->text('note')->nullable();                      // Ghi chú của khách
            $table->text('admin_note')->nullable();                // Ghi chú admin
            $table->enum('status', [
                'pending',       // Chờ xử lý
                'provisioning',  // Đang tạo trên Hetzner
                'active',        // Đang hoạt động
                'expired',       // Hết hạn
                'cancelled',     // Đã hủy
                'failed',        // Tạo thất bại
                'suspended',     // Tạm khóa
            ])->default('pending');
            $table->string('coupon_code', 50)->nullable();
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('hetzner_server_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vps_orders');
    }
};

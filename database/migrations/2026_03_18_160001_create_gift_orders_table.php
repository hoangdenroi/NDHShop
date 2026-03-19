<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng gift_orders — Đơn hàng thanh toán cho gift pages.
     */
    public function up(): void
    {
        Schema::create('gift_orders', function (Blueprint $table) {
            $table->id();
            $table->string('unitcode', 26)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gift_page_id')->constrained('gift_pages')->cascadeOnDelete();
            $table->string('order_code', 50)->unique()->comment('Mã đơn hàng: GO-xxxxxxxx');
            $table->string('plan', 20)->comment('Gói: basic, premium');
            $table->unsignedInteger('amount')->default(0)->comment('Số tiền thanh toán (VND)');
            $table->string('payment_method', 30)->default('balance')->comment('Phương thức: balance, vnpay, momo');
            $table->string('status', 20)->default('pending')->comment('Trạng thái: pending, paid, failed, refunded');
            $table->timestamp('paid_at')->nullable()->comment('Thời gian thanh toán thành công');
            $table->json('metadata')->nullable()->comment('Dữ liệu thêm (gateway response, v.v.)');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_orders');
    }
};

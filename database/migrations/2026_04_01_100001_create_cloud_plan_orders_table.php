<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bảng cloud_plan_orders — Lịch sử thanh toán gói dịch vụ.
 *
 * Mỗi khi user nâng cấp / gia hạn / downgrade, tạo 1 record ở đây.
 * Dùng làm audit trail và hỗ trợ hoàn tiền.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cloud_plan_orders', function (Blueprint $table) {
            $table->id();
            $table->string('unitcode', 26)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_code', 20)->unique();

            // Thông tin gói
            $table->string('plan', 10)->comment('pro / max');
            $table->string('action', 20)->comment('upgrade / renew / downgrade / refund');
            $table->string('billing_cycle', 15)->comment('monthly / quarterly / semiannual / annual');
            $table->unsignedTinyInteger('months')->comment('Số tháng hiệu lực: 1/3/6/12');

            // Thông tin tài chính
            $table->integer('original_amount')->default(0)->comment('Giá gốc (chưa giảm)');
            $table->unsignedTinyInteger('discount_percent')->default(0)->comment('% giảm giá');
            $table->integer('amount')->default(0)->comment('Số tiền thực trả (dương=trừ, âm=hoàn)');
            $table->decimal('balance_before', 12, 2)->default(0)->comment('Số dư trước giao dịch');
            $table->decimal('balance_after', 12, 2)->default(0)->comment('Số dư sau giao dịch');

            // Thời gian hiệu lực
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->text('note')->nullable()->comment('Ghi chú (lý do hoàn tiền, v.v.)');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            // Index
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cloud_plan_orders');
    }
};

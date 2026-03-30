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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // Mã giảm giá (VD: SALE10, FREESHIP)
            $table->enum('type', ['percent', 'fixed'])->default('fixed'); // Loại: phần trăm hoặc số tiền cố định
            $table->decimal('value', 10, 2); // Giá trị giảm (VD: 10 = 10% hoặc 10.000đ)
            $table->decimal('max_discount', 10, 2)->nullable(); // Giới hạn giảm tối đa (chỉ dùng cho type=percent)
            $table->decimal('min_order', 10, 2)->default(0); // Đơn hàng tối thiểu để áp dụng
            $table->integer('max_uses')->nullable(); // Tổng lượt sử dụng tối đa (null = không giới hạn)
            $table->integer('used_count')->default(0); // Đã sử dụng bao nhiêu lần
            $table->timestamp('starts_at')->nullable(); // Ngày bắt đầu hiệu lực
            $table->timestamp('expires_at')->nullable(); // Ngày hết hạn
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Thêm cột coupon vào bảng orders
        Schema::table('orders', function (Blueprint $table) {
            $table->string('coupon_code', 50)->nullable()->after('total_amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('coupon_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'discount_amount']);
        });
        Schema::dropIfExists('coupons');
    }
};

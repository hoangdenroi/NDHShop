<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm các cột Cloud Plan vào bảng users.
 *
 * Lưu gói dịch vụ chung (cloud_plan) + chu kỳ + hết hạn + grace period.
 * Admin override cho phép nâng cấp riêng từng dịch vụ (DB / Storage).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Gói dịch vụ chung: 'free' | 'pro' | 'max'
            $table->string('cloud_plan', 10)->default('free')->after('language');

            // Chu kỳ thanh toán: 'monthly' | 'quarterly' | 'semiannual' | 'annual'
            $table->string('cloud_plan_billing_cycle', 15)->default('monthly')->after('cloud_plan');

            // Ngày hết hạn gói (NULL = free, không hết hạn)
            $table->timestamp('cloud_plan_expires_at')->nullable()->after('cloud_plan_billing_cycle');

            // Ngày hết grace period (hết hạn + 7 ngày → xóa resource)
            $table->timestamp('cloud_plan_grace_ends_at')->nullable()->after('cloud_plan_expires_at');

            // Admin override — nâng cấp riêng cho từng dịch vụ
            $table->string('cloud_db_override', 10)->nullable()->after('cloud_plan_grace_ends_at');
            $table->string('cloud_storage_override', 10)->nullable()->after('cloud_db_override');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'cloud_plan',
                'cloud_plan_billing_cycle',
                'cloud_plan_expires_at',
                'cloud_plan_grace_ends_at',
                'cloud_db_override',
                'cloud_storage_override',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm các cột SaaS (status, plan, is_premium, rendered_html) vào gift_pages.
     * Gift cũ sẽ được mặc định: status=active, plan=basic.
     */
    public function up(): void
    {
        Schema::table('gift_pages', function (Blueprint $table) {
            $table->string('status', 20)->default('draft')->after('meta_image')
                  ->comment('Trạng thái: draft, pending, active, expired, disabled');
            $table->string('plan', 20)->default('basic')->after('status')
                  ->comment('Gói dịch vụ: basic (0đ/7 ngày), premium (49k/vĩnh viễn)');
            $table->boolean('is_premium')->default(false)->after('plan')
                  ->comment('Flag premium cho query nhanh');
            $table->longText('rendered_html')->nullable()->after('is_premium')
                  ->comment('HTML đã render sẵn (cache) để tối ưu hiệu năng');
        });

        // Cập nhật gift cũ đã tồn tại → active + basic
        DB::table('gift_pages')
            ->where('is_active', true)
            ->update(['status' => 'active', 'plan' => 'basic']);
    }

    public function down(): void
    {
        Schema::table('gift_pages', function (Blueprint $table) {
            $table->dropColumn(['status', 'plan', 'is_premium', 'rendered_html']);
        });
    }
};

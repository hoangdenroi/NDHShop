<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm cột active_connections để lưu số connections đang hoạt động,
 * cập nhật bởi cron job dbaas:monitor-activity mỗi 10 phút.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cloud_databases', function (Blueprint $table) {
            $table->unsignedSmallInteger('active_connections')->default(0)->after('max_connections');
        });
    }

    public function down(): void
    {
        Schema::table('cloud_databases', function (Blueprint $table) {
            $table->dropColumn('active_connections');
        });
    }
};

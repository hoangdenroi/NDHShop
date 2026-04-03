<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Hỗ trợ chế độ hybrid (auto Hetzner + thủ công)
 * - vps_categories: thêm provision_type (auto/manual)
 * - vps_orders: thêm username (manual có thể không phải root)
 * - vps_categories: hetzner_server_type cho phép null (gói thủ công không cần)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vps_categories', function (Blueprint $table) {
            $table->enum('provision_type', ['auto', 'manual'])->default('auto')->after('hetzner_server_type');
            $table->string('hetzner_server_type')->nullable()->change();
        });

        Schema::table('vps_orders', function (Blueprint $table) {
            $table->string('username')->nullable()->after('ip_address');  // Mặc định là root cho Hetzner
        });
    }

    public function down(): void
    {
        Schema::table('vps_categories', function (Blueprint $table) {
            $table->dropColumn('provision_type');
        });

        Schema::table('vps_orders', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};

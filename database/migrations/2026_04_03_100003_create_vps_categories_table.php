<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Bảng gói VPS + 2 pivot tables (OS, Location)
 * Admin tạo gói, map với server_type Hetzner
 */
return new class extends Migration
{
    public function up(): void
    {
        // Bảng gói VPS chính
        Schema::create('vps_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // VD: "VPS Starter"
            $table->string('slug')->unique();                // SEO URL
            $table->string('hetzner_server_type')->nullable();      // VD: "cpx11" — map với Hetzner server_type
            $table->enum('provision_type', ['auto', 'manual'])->default('auto');
            $table->unsignedBigInteger('price');              // Giá bán /tháng (VNĐ)
            $table->unsignedBigInteger('annual_price')->nullable(); // Giá bán /năm
            $table->string('cpu');                            // VD: "2 vCPU"
            $table->string('ram');                            // VD: "2 GB"
            $table->string('server_group')->nullable();       // Nhóm máy chủ
            $table->string('storage');                        // VD: "40 GB NVMe"
            $table->string('bandwidth')->default('20 TB');
            $table->boolean('is_renewable')->default(true);
            $table->boolean('is_best_seller')->default(false);
            $table->string('warranty')->nullable();           // VD: "99.9% Uptime"
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('sold_count')->default(0);
            $table->timestamps();
        });

        // Pivot: Gói VPS <-> Hệ điều hành hỗ trợ
        Schema::create('vps_category_operating_system', function (Blueprint $table) {
            $table->foreignId('vps_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vps_operating_system_id')->constrained()->cascadeOnDelete();
            $table->primary(['vps_category_id', 'vps_operating_system_id'], 'vps_cat_os_primary');
        });

        // Pivot: Gói VPS <-> Vị trí datacenter
        Schema::create('vps_category_location', function (Blueprint $table) {
            $table->foreignId('vps_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vps_location_id')->constrained()->cascadeOnDelete();
            $table->primary(['vps_category_id', 'vps_location_id'], 'vps_cat_loc_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vps_category_location');
        Schema::dropIfExists('vps_category_operating_system');
        Schema::dropIfExists('vps_categories');
    }
};

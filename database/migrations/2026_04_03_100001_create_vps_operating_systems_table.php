<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Bảng hệ điều hành VPS
 * Sync từ Hetzner API GET /v1/images?type=system
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vps_operating_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // VD: "Ubuntu 22.04"
            $table->string('hetzner_name')->unique();        // VD: "ubuntu-22.04" — key để gọi API
            $table->string('os_flavor')->nullable();         // VD: "ubuntu", "centos", "debian"
            $table->string('architecture')->default('x86');  // VD: "x86", "arm"
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vps_operating_systems');
    }
};

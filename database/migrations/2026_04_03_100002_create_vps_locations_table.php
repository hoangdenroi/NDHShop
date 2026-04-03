<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Bảng vị trí datacenter VPS
 * Sync từ Hetzner API GET /v1/locations
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vps_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // VD: "Falkenstein DC Park 1"
            $table->string('hetzner_name')->unique();        // VD: "fsn1" — key để gọi API
            $table->string('city')->nullable();              // VD: "Falkenstein"
            $table->string('country', 10)->nullable();       // VD: "DE"
            $table->string('network_zone')->nullable();      // VD: "eu-central"
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vps_locations');
    }
};

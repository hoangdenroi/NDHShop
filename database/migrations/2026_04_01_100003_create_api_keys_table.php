<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bảng api_keys — API Key để user truy cập qua SDK/API.
 *
 * Key được hash trước khi lưu (tương tự password).
 * Chỉ hiện 8 ký tự cuối khi hiển thị trên UI.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100)->comment('Label (VD: Production Key)');
            $table->string('key_hash', 64)->unique()->comment('SHA-256 hash của key');
            $table->string('key_last8', 8)->comment('8 ký tự cuối để hiển thị trên UI');
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};

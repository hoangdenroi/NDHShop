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
        Schema::create('users', function (Blueprint $table) {
            $table->baseColumns();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('avatar_url')->nullable();
            $table->decimal('balance', 19, 4)->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('google_id')->nullable();
            $table->string('role')->default('user');
            $table->string('status')->default('active');
            $table->json('theme')->nullable();
            $table->json('notification')->nullable();
            $table->string('language', 10)->default('vi');
            $table->string('cloud_plan')->default('free');
            $table->string('cloud_plan_billing_cycle')->default('monthly');
            $table->timestamp('cloud_plan_expires_at')->nullable();
            $table->timestamp('cloud_plan_grace_ends_at')->nullable();
            $table->string('cloud_db_override')->nullable();
            $table->string('cloud_storage_override')->nullable();
            $table->timestamp('last_change_password_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

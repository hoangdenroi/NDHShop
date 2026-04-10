<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bảng cloud_databases — Metadata các database instance đã tạo.
 *
 * Mỗi DB instance tương ứng 1 database thật trên PostgreSQL/MySQL server.
 * Mật khẩu được mã hóa AES trước khi lưu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cloud_databases', function (Blueprint $table) {
            $table->id();
            $table->string('unitcode', 26)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Thông tin engine
            $table->string('engine', 15)->comment('postgresql / mysql');
            $table->string('db_name', 100)->comment('Tên DB thực tế (prefix ndh_)');
            $table->string('db_user', 100)->comment('Username DB');
            $table->text('db_password_encrypted')->comment('Mật khẩu mã hóa AES');
            $table->string('host', 255)->default('127.0.0.1');
            $table->unsignedSmallInteger('port')->default(3306);

            // Trạng thái & giới hạn
            $table->string('status', 20)->default('provisioning')
                ->comment('provisioning / active / suspended / deleting / deleted');
            $table->unsignedSmallInteger('max_connections')->default(3);
            $table->unsignedInteger('max_storage_mb')->default(50);
            $table->decimal('storage_used_mb', 10, 2)->default(0);
            $table->integer('active_connections')->default(0);

            // Theo dõi hoạt động (cho cleanup gói Free)
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            // Index
            $table->index(['user_id', 'status']);
            $table->index(['status', 'last_activity_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cloud_databases');
    }
};

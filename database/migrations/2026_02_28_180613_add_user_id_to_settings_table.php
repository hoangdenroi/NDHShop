<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Thêm user_id, nullable để hỗ trợ cả setting hệ thống (user_id = null) và setting user
            $table->foreignId('user_id')->nullable()->after('unitcode')->constrained('users')->onDelete('cascade');
            // Đổi unique key: mỗi user có thể có cùng key khác nhau
            $table->dropUnique(['key']);
            $table->unique(['user_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'key']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->unique('key');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Cho phép share_code = null (gift draft chưa có share_code).
     * Dùng raw SQL vì PostgreSQL không hỗ trợ nullable change qua Blueprint.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE gift_pages ALTER COLUMN share_code DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE gift_pages ALTER COLUMN share_code SET NOT NULL');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Chuyển đổi cột category (string) → category_id (foreign key).
     * 1. Seed các categories cũ vào bảng gift_categories.
     * 2. Thêm cột category_id, map dữ liệu cũ.
     * 3. Xóa cột category cũ.
     */
    public function up(): void
    {
        // Bước 1: Seed categories từ constant cũ
        $categories = [
            ['name' => 'Tết',       'slug' => 'tet',       'icon' => 'celebration',  'sort_order' => 1],
            ['name' => 'Sinh nhật', 'slug' => 'sinh-nhat', 'icon' => 'cake',         'sort_order' => 2],
            ['name' => 'Valentine', 'slug' => 'valentine', 'icon' => 'favorite',     'sort_order' => 3],
            ['name' => 'Đám cưới', 'slug' => 'cuoi',      'icon' => 'diamond',      'sort_order' => 4],
            ['name' => 'Khác',     'slug' => 'other',      'icon' => 'category',     'sort_order' => 99],
        ];

        foreach ($categories as $cat) {
            DB::table('gift_categories')->insert(array_merge($cat, [
                'unitcode'   => Str::ulid(),
                'is_active'  => true,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Bước 2: Thêm cột category_id
        Schema::table('gift_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('category');
        });

        // Bước 3: Map category slug cũ → category_id mới
        $giftCategories = DB::table('gift_categories')->pluck('id', 'slug');

        DB::table('gift_templates')->orderBy('id')->chunk(100, function ($templates) use ($giftCategories) {
            foreach ($templates as $template) {
                $categoryId = $giftCategories[$template->category] ?? $giftCategories['other'] ?? null;
                DB::table('gift_templates')
                    ->where('id', $template->id)
                    ->update(['category_id' => $categoryId]);
            }
        });

        // Bước 4: Xóa cột category cũ
        Schema::table('gift_templates', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        // Khôi phục: thêm lại cột category, map ngược
        Schema::table('gift_templates', function (Blueprint $table) {
            $table->string('category')->default('other')->after('thumbnail');
        });

        $giftCategories = DB::table('gift_categories')->pluck('slug', 'id');

        DB::table('gift_templates')->orderBy('id')->chunk(100, function ($templates) use ($giftCategories) {
            foreach ($templates as $template) {
                $slug = $giftCategories[$template->category_id] ?? 'other';
                DB::table('gift_templates')
                    ->where('id', $template->id)
                    ->update(['category' => $slug]);
            }
        });

        Schema::table('gift_templates', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};

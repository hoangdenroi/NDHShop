<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SrcAppGameController extends Controller
{
    /**
     * Slug của category "SRC - APP - GAME" trong DB
     */
    private const CATEGORY_SLUG = 'apps/src-app-game';

    /**
     * Thời gian cache (giây) - 1 giờ
     */
    private const CACHE_TTL = 3600;

    /**
     * 3 nhóm lọc sidebar: SRC, APP, GAME
     * Key = label hiển thị, value = danh sách platform tương ứng
     */
    private const FILTER_GROUPS = [
        'SRC'  => ['Web', 'Source'],
        'APP'  => ['IOS', 'Android', 'APK', 'IPA', 'EXE'],
        'GAME' => ['Game', 'GameAPK', 'GameIPA'],
    ];

    /**
     * Hiển thị trang SRC / APP / GAME với bộ lọc sidebar
     */
    public function index(Request $request)
    {
        $selectedGroup = $request->input('group', '');

        // Lấy category ID từ slug (có cache)
        $categoryId = $this->getCategoryId();

        if (!$categoryId) {
            abort(404, 'Không tìm thấy danh mục SRC - APP - GAME');
        }

        // Query base: tất cả sản phẩm thuộc category này
        $query = Product::where('is_active', true)
            ->where('is_deleted', false)
            ->where('category_id', $categoryId)
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with(['assets' => function ($q) {
                $q->orderBy('sort_order', 'desc');
            }]);

        // Áp dụng bộ lọc theo nhóm (dựa trên platform)
        if (!empty($selectedGroup) && isset(self::FILTER_GROUPS[$selectedGroup])) {
            $platforms = self::FILTER_GROUPS[$selectedGroup];
            $query->whereIn('platform', $platforms);
        }

        $products = $query->latest()->paginate(15);

        // Lấy sidebar data (có cache)
        $sidebarGroups = $this->getSidebarGroups($categoryId);

        return view('pages.app.src-app-game', compact(
            'products',
            'sidebarGroups',
            'selectedGroup',
        ));
    }

    /**
     * Lấy category ID từ slug (có cache)
     */
    private function getCategoryId(): ?int
    {
        return Cache::remember('src_app_game_category_id', self::CACHE_TTL, function () {
            $category = \App\Models\Category::where('slug', self::CATEGORY_SLUG)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->first();

            return $category?->id;
        });
    }

    /**
     * Đếm số sản phẩm cho từng nhóm lọc (có cache)
     */
    private function getSidebarGroups(int $categoryId): array
    {
        return Cache::remember('src_app_game_sidebar', self::CACHE_TTL, function () use ($categoryId) {
            $icons = [
                'SRC'  => 'code',
                'APP'  => 'phone_iphone',
                'GAME' => 'sports_esports',
            ];

            $groups = [];
            foreach (self::FILTER_GROUPS as $label => $platforms) {
                $count = Product::where('is_active', true)
                    ->where('is_deleted', false)
                    ->where('category_id', $categoryId)
                    ->whereIn('platform', $platforms)
                    ->count();

                $groups[] = [
                    'label' => $label,
                    'key'   => $label,
                    'icon'  => $icons[$label],
                    'count' => $count,
                ];
            }

            return $groups;
        });
    }
}
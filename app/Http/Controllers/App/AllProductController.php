<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AllProductController extends Controller
{
    /**
     * Hiển thị trang Liên hệ
     */
    public function index(Request $request)
    {
        $searchCategories = $request->input('categories', []);
        $minPrice = $request->input('min_price', 0);
        $globalMaxPrice = 10000000;
        $maxPrice = $request->input('max_price', 500000);

        // Lấy danh sách Category có sản phẩm kèm số lượng
        $sidebarCategories = \App\Models\Category::where('is_active', true)
            ->where('is_deleted', false)
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true)->where('is_deleted', false);
            }])
            ->has('products')
            ->get();

        $query = \App\Models\Product::where('is_active', true)
            ->where('is_deleted', false)
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with(['assets' => function ($q) {
                $q->orderBy('sort_order', 'desc');
            }]);

        // Áp dụng bộ lọc
        if (! empty($searchCategories)) {
            $query->whereIn('category_id', $searchCategories);
        }

        if ($minPrice > 0) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice && $maxPrice < $globalMaxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        // Nếu có lọc hoặc tìm kiếm, hiển thị danh sách phẳng
        if (! empty($searchCategories) || $minPrice > 0 || ($maxPrice && $maxPrice < $globalMaxPrice)) {
            $products = $query->latest()->paginate(15);

            return view('pages.app.all-product', compact('products', 'sidebarCategories', 'globalMaxPrice', 'minPrice', 'maxPrice', 'searchCategories'));
        }

        // Mặc định (không lọc): Hiển thị theo nhóm Category (mỗi cái 5 sản phẩm)
        $categories = \App\Models\Category::where('is_active', true)
            ->where('is_deleted', false)
            ->has('products')
            ->paginate(10);

        $categories->getCollection()->each(function ($cat) {
            $cat->setRelation('products', \App\Models\Product::where('category_id', $cat->id)
                ->where('is_active', true)
                ->where('is_deleted', false)
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->with(['assets' => function ($q) {
                    $q->orderBy('sort_order', 'desc');
                }])
                ->withMax('assets', 'sort_order')
                ->orderByDesc('assets_max_sort_order')
                ->limit(5)
                ->get());
        });

        return view('pages.app.all-product', compact('categories', 'sidebarCategories', 'globalMaxPrice', 'minPrice', 'maxPrice', 'searchCategories'));
    }
}

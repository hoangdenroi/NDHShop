<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductDetailController extends Controller
{
    /**
     * Display the specified product.
     */
    public function show($slug)
    {
        $cacheKey = "app_product_detail_{$slug}";

        $data = Cache::remember($cacheKey, 3600, function () use ($slug) {
            // 1. Lấy thông tin sản phẩm và các relation cần thiết (category, assets)
            $product = Product::with(['category', 'assets' => function($query) {
                    $query->where('type', 'image')->orderBy('sort_order', 'asc');
                }])
                ->where('slug', $slug)
                ->where('is_deleted', false)
                ->where('is_active', true)
                ->firstOrFail();

            // 2. Tìm 5 sản phẩm liên quan (cùng category), trừ sản phẩm hiện tại
            $relatedProducts = Product::with(['category', 'assets' => function($q) {
                    $q->orderBy('sort_order', 'asc');
                }])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_deleted', false)
                ->where('is_active', true)
                ->inRandomOrder() 
                ->take(5)
                ->get();

            return compact('product', 'relatedProducts');
        });

        $product = $data['product'];
        $relatedProducts = $data['relatedProducts'];

        return view('pages.app.product-detail', compact('product', 'relatedProducts'));
    }
}

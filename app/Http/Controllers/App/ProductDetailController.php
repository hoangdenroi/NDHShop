<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    /**
     * Display the specified product.
     */
    public function show($slug)
    {
        // 1. Lấy thông tin sản phẩm và các relation cần thiết (category, assets)
        $product = Product::with(['category', 'assets' => function($query) {
                // Đảm bảo lấy theo đúng thứ tự (nếu có sắp xếp) hoặc chỉ lấy type 'image'
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
            ->inRandomOrder() // Hoặc orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('pages.app.product-detail', compact('product', 'relatedProducts'));
    }
}

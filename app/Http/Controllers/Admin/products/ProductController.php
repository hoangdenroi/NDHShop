<?php

namespace App\Http\Controllers\Admin\products;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAsset;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_deleted', false)->with(['category', 'assets']);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Lọc Status
        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('is_active', true);
            } elseif ($request->status === '0') {
                $query->where('is_active', false);
            }
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('is_deleted', false)->where('is_active', true)->get();

        return view('pages.admin.products.product-index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'version' => 'nullable|string|max:255',
            'platform' => 'nullable|string|max:255',
            'developer' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'image_urls' => 'nullable|array|max:5',
            'image_urls.*' => 'nullable|string|max:2048',
            'file_urls' => 'nullable|array|max:2',
            'file_urls.*' => 'nullable|string|max:2048',
            'primary_image_index' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Tạo Product
        $product = Product::create($validated);

        // Xử lý lưu URL ảnh
        if ($request->has('image_urls')) {
            $primaryIndex = $request->input('primary_image_index', 0);
            $sortOrder = 0;
            foreach ($request->input('image_urls') as $index => $url) {
                if (!$url) continue;
                ProductAsset::create([
                    'product_id' => $product->id,
                    'type' => 'image',
                    'url_or_path' => $url,
                    'file_size' => 0,
                    'is_primary' => ($index == $primaryIndex),
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        // Xử lý lưu URL file
        if ($request->has('file_urls')) {
            $sortOrder = 0;
            foreach ($request->input('file_urls') as $index => $url) {
                if (!$url) continue;
                ProductAsset::create([
                    'product_id' => $product->id,
                    'type' => 'file',
                    'url_or_path' => $url,
                    'file_size' => 0,
                    'is_primary' => false,
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        return back()->with('success', 'Thêm sản phẩm thành công!');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,'.$product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'version' => 'nullable|string|max:255',
            'platform' => 'nullable|string|max:255',
            'developer' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'image_urls' => 'nullable|array|max:5',
            'image_urls.*' => 'nullable|string|max:2048',
            'file_urls' => 'nullable|array|max:2',
            'file_urls.*' => 'nullable|string|max:2048',
            'primary_image_index' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $product->update($validated);

        // Xóa toàn bộ assets cũ của sản phẩm này (vì ta sử dụng URL ngoài nên xóa thoải mái)
        $product->assets()->delete();

        // Thêm lại toàn bộ URL mới cập nhật
        if ($request->has('image_urls')) {
            $primaryIndex = $request->input('primary_image_index', 0);
            $sortOrder = 0;
            foreach ($request->input('image_urls') as $index => $url) {
                if (!$url) continue;
                ProductAsset::create([
                    'product_id' => $product->id,
                    'type' => 'image',
                    'url_or_path' => $url,
                    'file_size' => 0,
                    'is_primary' => ($index == $primaryIndex),
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        if ($request->has('file_urls')) {
            $sortOrder = 0;
            foreach ($request->input('file_urls') as $index => $url) {
                if (!$url) continue;
                ProductAsset::create([
                    'product_id' => $product->id,
                    'type' => 'file',
                    'url_or_path' => $url,
                    'file_size' => 0,
                    'is_primary' => false,
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        return back()->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_deleted' => true]);

        return back()->with('success', 'Xóa sản phẩm thành công!');
    }
}

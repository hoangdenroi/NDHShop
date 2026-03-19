<?php

namespace App\Http\Controllers\Admin\categorys;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {

        $query = \App\Models\Category::where('is_deleted', false);

        // Tìm kiếm theo tên hoặc meta
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Filter Status theo Active hoặc Unactive theo Request
        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('is_active', true);
            } elseif ($request->status === '0') {
                $query->where('is_active', false);
            }
        }

        $categories = $query->latest()->paginate(10)->withQueryString();

        return view('pages.admin.categorys.category-index', compact('categories'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        // Handle metadata
        if ($request->has('metadata')) {
            $validated['metadata'] = $request->metadata;
        }

        \App\Models\Category::create($validated);

        return back()->with('success', 'Thêm danh mục thành công!');
    }

    public function update(Request $request, \App\Models\Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        // Handle metadata
        if ($request->has('metadata')) {
            $validated['metadata'] = $request->metadata;
        }
        
        $category->update($validated);

        return back()->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(\App\Models\Category $category)
    {
        // Có thể bổ sung check danh mục con hoặc sản phẩm trước khi xóa
        if ($category->children()->count() > 0) {
             return back()->with('error', 'Không thể xóa danh mục đang có danh mục con!');
        }

        $category->delete();

        return back()->with('success', 'Xóa cấu hình danh mục thành công!');
    }

    public function getCategories(Request $request)
    {
        $parent_id = $request->parent_id;
        
        $cacheKey = 'categories_menu_' . ($parent_id ?: 'null');
        
        // Cache danh mục trong 1 giờ (3600 giây)
        $categories = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($parent_id) {
            $query = \App\Models\Category::where('is_deleted', false)
                ->where('is_active', true);
            
            return $query->where('parent_id', $parent_id)
                ->with(['children' => function ($q) {
                    $q->where('is_deleted', false)
                      ->where('is_active', true)
                      ->orderBy('id', 'asc');
                }])
                ->orderBy('id', 'asc')
                ->get();
        });
            
        return response()->json($categories);
    }
}

<?php

namespace App\Http\Controllers\Admin\post_categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostCategory;

class PostCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = PostCategory::query();

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Lọc bài danh mục hoạt động
        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('is_active', true);
            } elseif ($request->status === '0') {
                $query->where('is_active', false);
            }
        }

        // Lọc danh mục đã bị xóa (is_deleted)
        if ($request->filled('is_deleted')) {
            if ($request->is_deleted === '1') {
                $query->where('is_deleted', true);
            } elseif ($request->is_deleted === '0') {
                $query->where('is_deleted', false);
            }
        } else {
            // Mặc định chỉ hiển thị chưa xóa nếu không filter
            $query->where('is_deleted', false);
        }

        // Đếm số lượng bài viết của từng danh mục
        $categories = $query->withCount('posts')->latest()->paginate(10)->withQueryString();

        return view('pages.admin.post-categories.post-category-index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:post_categories,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        PostCategory::create($validated);

        return back()->with('success', 'Thêm danh mục bài viết mới thành công!');
    }

    public function update(Request $request, PostCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:post_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return back()->with('success', 'Cập nhật danh mục bài viết thành công!');
    }

    public function destroy(PostCategory $category)
    {
        if ($category->posts()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục đang có bài viết!');
        }

        $category->update(['is_deleted' => true]);

        return back()->with('success', 'Xóa danh mục thành công!');
    }
}

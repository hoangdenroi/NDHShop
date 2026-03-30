<?php

namespace App\Http\Controllers\Admin\gift_categories;

use App\Http\Controllers\Controller;
use App\Models\GiftCategory;
use Illuminate\Http\Request;

class GiftCategoryController extends Controller
{
    /**
     * Danh sách gift categories — có filter và search.
     */
    public function index(Request $request)
    {
        $query = GiftCategory::query();

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Lọc trạng thái
        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('is_active', true);
            } elseif ($request->status === '0') {
                $query->where('is_active', false);
            }
        }

        // Lọc đã xóa
        if ($request->filled('is_deleted')) {
            if ($request->is_deleted === '1') {
                $query->withDeleted()->where('is_deleted', true);
            } elseif ($request->is_deleted === 'all') {
                $query->withDeleted();
            }
        }

        // Đếm số templates thuộc mỗi category
        $categories = $query->withCount('giftTemplates')
            ->orderBy('sort_order')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.gift-categories.gift-category-index', compact('categories'));
    }

    /**
     * Tạo gift category mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:gift_categories,slug',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active']   = $request->has('is_active');
        $validated['sort_order']  = $validated['sort_order'] ?? 0;

        GiftCategory::create($validated);

        return back()->with('success', 'Thêm danh mục quà tặng thành công!');
    }

    /**
     * Cập nhật gift category.
     */
    public function update(Request $request, GiftCategory $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:gift_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active']  = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category->update($validated);

        return back()->with('success', 'Cập nhật danh mục quà tặng thành công!');
    }

    /**
     * Xóa mềm gift category.
     */
    public function destroy(GiftCategory $category)
    {
        // Không cho xóa nếu đang có templates dùng
        if ($category->giftTemplates()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục đang có templates sử dụng!');
        }

        $category->update(['is_deleted' => true]);

        return back()->with('success', 'Xóa danh mục quà tặng thành công!');
    }
}

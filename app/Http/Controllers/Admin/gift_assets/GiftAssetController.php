<?php

namespace App\Http\Controllers\Admin\gift_assets;

use App\Http\Controllers\Controller;
use App\Models\GiftAsset;
use App\Models\GiftCategory;
use Illuminate\Http\Request;

class GiftAssetController extends Controller
{
    /**
     * Danh sách assets — grid view với filter.
     */
    public function index(Request $request)
    {
        $query = GiftAsset::with('category');

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Lọc theo category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Lọc theo type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Lọc trạng thái
        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('is_active', true);
            } elseif ($request->status === '0') {
                $query->where('is_active', false);
            }
        }

        $assets = $query->orderBy('sort_order')->latest()->paginate(20)->withQueryString();
        $categories = GiftCategory::active()->ordered()->get();
        $types = GiftAsset::TYPES;

        return view('pages.admin.gift-assets.gift-asset-index', compact('assets', 'categories', 'types'));
    }

    /**
     * Thêm 1 asset mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|string|in:' . implode(',', array_keys(GiftAsset::TYPES)),
            'url'         => 'required|url|max:2048',
            'category_id' => 'nullable|integer|exists:gift_categories,id',
            'thumbnail'   => 'nullable|url|max:2048',
            'file_size'   => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'tags'        => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active']  = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Parse tags: chuỗi phân cách bằng dấu phẩy → JSON array
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        GiftAsset::create($validated);

        return back()->with('success', 'Thêm tài nguyên thành công!');
    }

    /**
     * Thêm hàng loạt — mỗi URL một dòng.
     */
    public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'type'        => 'required|string|in:' . implode(',', array_keys(GiftAsset::TYPES)),
            'category_id' => 'nullable|integer|exists:gift_categories,id',
            'urls'        => 'required|string',
            'is_active'   => 'boolean',
        ]);

        $isActive = $request->has('is_active');

        // Tách URLs theo dòng
        $urls = array_filter(
            array_map('trim', explode("\n", str_replace("\r", '', $validated['urls']))),
            fn ($line) => $line !== ''
        );

        if (empty($urls)) {
            return back()->with('error', 'Không có URL nào hợp lệ!');
        }

        $count = 0;
        foreach ($urls as $index => $url) {
            // Bỏ qua URL không hợp lệ
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            // Tự tạo tên từ URL (lấy filename)
            $filename = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);
            $name = $filename ?: 'Asset ' . ($index + 1);

            GiftAsset::create([
                'name'        => $name,
                'type'        => $validated['type'],
                'url'         => $url,
                'category_id' => $validated['category_id'] ?? null,
                'is_active'   => $isActive,
                'sort_order'  => $index,
            ]);

            $count++;
        }

        return back()->with('success', "Đã thêm {$count} tài nguyên thành công!");
    }

    /**
     * Cập nhật asset.
     */
    public function update(Request $request, GiftAsset $asset)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|string|in:' . implode(',', array_keys(GiftAsset::TYPES)),
            'url'         => 'required|url|max:2048',
            'category_id' => 'nullable|integer|exists:gift_categories,id',
            'thumbnail'   => 'nullable|url|max:2048',
            'file_size'   => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'tags'        => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active']  = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Parse tags
        if (!empty($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        } else {
            $validated['tags'] = null;
        }

        $asset->update($validated);

        return back()->with('success', 'Cập nhật tài nguyên thành công!');
    }

    /**
     * Xóa mềm asset.
     */
    public function destroy(GiftAsset $asset)
    {
        $asset->softDelete();

        return back()->with('success', 'Xóa tài nguyên thành công!');
    }
}

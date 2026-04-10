<?php

namespace App\Http\Controllers\Admin\gift_templates;

use App\Http\Controllers\Controller;
use App\Models\GiftCategory;
use App\Models\GiftTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GiftTemplateController extends Controller
{
    /**
     * Upload ảnh lên Cloudinary
     */
    private function uploadToCloudinary($file): ?string
    {
        try {
            $result = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'ndhshop/gift_templates',
            ]);
            return $result['secure_url'] ?? $result['url'];
        } catch (\Exception $e) {
            Log::error('Upload Cloudinary thất bại: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Danh sách templates — có filter và search.
     */
    public function index(Request $request)
    {
        $query = GiftTemplate::query();

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Lọc theo category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
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
                $query->withDeleted()->where('is_deleted', '=', true);
            } elseif ($request->is_deleted === 'all') {
                $query->withDeleted();
            }
        }

        $templates = $query->orderByDesc('id')->paginate(10)->withQueryString();
        $categories = GiftCategory::active()->ordered()->get();

        return view('pages.admin.gift-templates.gift-template-index', compact('templates', 'categories'));
    }

    /**
     * Tạo template mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:gift_templates,slug',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'category_id' => 'required|integer|exists:gift_categories,id',
            'html_code' => 'required|string',
            'css_code' => 'nullable|string',
            'js_code' => 'nullable|string',
            'schema' => 'nullable|json',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'price' => 'nullable|integer|min:0',
        ]);

        // Encode code sang Base64
        $validated['html_code'] = GiftTemplate::encodeCode($validated['html_code']);
        if (! empty($validated['css_code'])) {
            $validated['css_code'] = GiftTemplate::encodeCode($validated['css_code']);
        }
        if (! empty($validated['js_code'])) {
            $validated['js_code'] = GiftTemplate::encodeCode($validated['js_code']);
        }

        // Handle Thumbnail Upload
        if ($request->hasFile('thumbnail')) {
            $url = $this->uploadToCloudinary($request->file('thumbnail'));
            if ($url) {
                $validated['thumbnail'] = $url;
            } else {
                unset($validated['thumbnail']);
            }
        }

        // Parse schema JSON
        if (! empty($validated['schema'])) {
            $validated['schema'] = json_decode($validated['schema'], true);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_premium'] = $request->has('is_premium');
        $validated['price'] = $validated['price'] ?? 0;

        GiftTemplate::create($validated);

        return back()->with('success', 'Thêm mẫu template thành công!');
    }

    /**
     * Cập nhật template.
     */
    public function update(Request $request, GiftTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:gift_templates,slug,'.$template->id,
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
            'category_id' => 'required|integer|exists:gift_categories,id',
            'html_code' => 'required|string',
            'css_code' => 'nullable|string',
            'js_code' => 'nullable|string',
            'schema' => 'nullable|json',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'price' => 'nullable|integer|min:0',
        ]);

        // Encode code sang Base64
        $validated['html_code'] = GiftTemplate::encodeCode($validated['html_code']);
        if (! empty($validated['css_code'])) {
            $validated['css_code'] = GiftTemplate::encodeCode($validated['css_code']);
        }
        if (! empty($validated['js_code'])) {
            $validated['js_code'] = GiftTemplate::encodeCode($validated['js_code']);
        }

        // Handle Thumbnail Upload
        if ($request->hasFile('thumbnail')) {
            $url = $this->uploadToCloudinary($request->file('thumbnail'));
            if ($url) {
                $validated['thumbnail'] = $url;
            } else {
                unset($validated['thumbnail']); // Giữ nguyên ảnh cũ nếu upload lỗi (có thể tùy chọn)
            }
        } else {
            // Nếu không upload ảnh mới, loại bỏ key validation để tránh null
            unset($validated['thumbnail']);
        }

        // Parse schema JSON
        if (! empty($validated['schema'])) {
            $validated['schema'] = json_decode($validated['schema'], true);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_premium'] = $request->has('is_premium');
        $validated['price'] = $validated['price'] ?? 0;

        $template->update($validated);

        return back()->with('success', 'Cập nhật mẫu template thành công!');
    }

    /**
     * Xóa mềm template.
     */
    public function destroy(GiftTemplate $template)
    {
        $template->softDelete();

        return back()->with('success', 'Xóa mẫu template thành công!');
    }
}

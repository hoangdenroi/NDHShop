<?php

namespace App\Http\Controllers\Admin\products;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    /**
     * Upload ảnh lên Cloudinary và trả về [url, public_id].
     * Dùng helper cloudinary() (trả về instance \Cloudinary\Cloudinary).
     */
    private function uploadToCloudinary($file): ?array
    {
        try {
            $result = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'ndhshop/products',
            ]);

            return [
                'url' => $result['secure_url'] ?? $result['url'],
                'public_id' => $result['public_id'] ?? null,
                'size' => $result['bytes'] ?? $file->getSize(),
            ];
        } catch (\Exception $e) {
            Log::error('Upload Cloudinary thất bại: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Xóa ảnh trên Cloudinary theo public_id.
     */
    private function deleteFromCloudinary(string $publicId): void
    {
        try {
            cloudinary()->uploadApi()->destroy($publicId);
        } catch (\Exception $e) {
            Log::error('Xóa Cloudinary thất bại: ' . $e->getMessage());
        }
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
            // Ảnh upload (tối đa 5 file, mỗi file tối đa 20MB)
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:20480',
            // File sản phẩm số (giữ nguyên nhập link)
            'file_urls' => 'nullable|array|max:2',
            'file_urls.*' => 'nullable|string|max:2048',
            'primary_image_index' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Tạo Product
        $product = Product::create($validated);

        // Upload ảnh lên Cloudinary
        if ($request->hasFile('images')) {
            $primaryIndex = (int) $request->input('primary_image_index', 0);
            $sortOrder = 0;

            foreach ($request->file('images') as $index => $file) {
                if (!$file || !$file->isValid()) continue;

                $uploaded = $this->uploadToCloudinary($file);
                if (!$uploaded) continue;

                ProductAsset::create([
                    'product_id' => $product->id,
                    'type' => 'image',
                    'url_or_path' => $uploaded['url'],
                    'cloud_public_id' => $uploaded['public_id'],
                    'file_size' => $uploaded['size'],
                    'is_primary' => ($index == $primaryIndex),
                    'sort_order' => $sortOrder++,
                ]);
            }
        }

        // Xử lý lưu URL file sản phẩm số (giữ nguyên logic cũ)
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
            // Ảnh mới upload
            'new_images' => 'nullable|array|max:5',
            'new_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:20480',
            // ID ảnh cũ giữ lại
            'existing_image_ids' => 'nullable|array',
            'existing_image_ids.*' => 'nullable|integer',
            // File sản phẩm số (giữ nguyên nhập link)
            'file_urls' => 'nullable|array|max:2',
            'file_urls.*' => 'nullable|string|max:2048',
            'primary_image_index' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $product->update($validated);

        // Lấy danh sách ID ảnh cũ được giữ lại
        $keepImageIds = array_filter($request->input('existing_image_ids', []));

        // Xóa ảnh cũ KHÔNG nằm trong danh sách giữ lại (và xóa trên Cloudinary)
        $deleteQuery = $product->assets()->where('type', 'image');
        if (!empty($keepImageIds)) {
            $deleteQuery->whereNotIn('id', $keepImageIds);
        }

        foreach ($deleteQuery->get() as $asset) {
            if ($asset->cloud_public_id) {
                $this->deleteFromCloudinary($asset->cloud_public_id);
            }
            $asset->delete();
        }

        // Xóa toàn bộ file assets cũ (vì dùng URL ngoài, không cần xóa cloud)
        $product->assets()->where('type', 'file')->delete();

        // Cập nhật primary + sort_order cho ảnh cũ còn lại
        $primaryIndex = (int) $request->input('primary_image_index', 0);
        $existingImages = $product->assets()->where('type', 'image')->orderBy('sort_order')->get();
        $sortOrder = 0;

        foreach ($existingImages as $i => $asset) {
            $asset->update([
                'is_primary' => ($i == $primaryIndex),
                'sort_order' => $sortOrder++,
            ]);
        }

        // Upload ảnh mới lên Cloudinary
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $index => $file) {
                if (!$file || !$file->isValid()) continue;

                $uploaded = $this->uploadToCloudinary($file);
                if (!$uploaded) continue;

                $currentIndex = $sortOrder + $index;
                ProductAsset::create([
                    'product_id' => $product->id,
                    'type' => 'image',
                    'url_or_path' => $uploaded['url'],
                    'cloud_public_id' => $uploaded['public_id'],
                    'file_size' => $uploaded['size'],
                    'is_primary' => (count($existingImages) + $index == $primaryIndex),
                    'sort_order' => $currentIndex,
                ]);
            }
        }

        // Thêm lại URL file sản phẩm số
        if ($request->has('file_urls')) {
            $fileSortOrder = 0;
            foreach ($request->input('file_urls') as $index => $url) {
                if (!$url) continue;
                ProductAsset::create([
                    'product_id' => $product->id,
                    'type' => 'file',
                    'url_or_path' => $url,
                    'file_size' => 0,
                    'is_primary' => false,
                    'sort_order' => $fileSortOrder++,
                ]);
            }
        }

        return back()->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        // Xóa ảnh trên Cloudinary trước khi soft delete sản phẩm
        foreach ($product->assets()->where('type', 'image')->get() as $asset) {
            if ($asset->cloud_public_id) {
                $this->deleteFromCloudinary($asset->cloud_public_id);
            }
        }

        $product->update(['is_deleted' => true]);

        return back()->with('success', 'Xóa sản phẩm thành công!');
    }
}

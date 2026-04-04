<?php

namespace App\Http\Controllers\Admin\vps;

use App\Http\Controllers\Controller;
use App\Models\VpsCategory;
use App\Models\VpsOperatingSystem;
use App\Models\VpsLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Admin Controller: Quản lý gói VPS
 * CRUD gói VPS, map với Hetzner server_type
 */
class VpsCategoryController extends Controller
{
    /**
     * Danh sách gói VPS + Form thêm mới.
     */
    public function index()
    {
        $categories = VpsCategory::withCount('orders')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $operatingSystems = VpsOperatingSystem::active()->orderBy('sort_order')->get();
        $locations = VpsLocation::active()->orderBy('sort_order')->get();

        return view('pages.admin.vps-categories', compact('categories', 'operatingSystems', 'locations'));
    }

    /**
     * Tạo gói VPS mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provision_type' => 'required|in:auto,manual',
            'hetzner_server_type' => 'required_if:provision_type,auto|nullable|string|max:50',
            'server_group' => 'required|string|in:cost-optimized,regular,general-purpose',
            'price' => 'required|integer|min:1000',
            'annual_price' => 'nullable|integer|min:1000',
            'cpu' => 'required|string|max:50',
            'ram' => 'required|string|max:50',
            'storage' => 'required|string|max:100',
            'bandwidth' => 'nullable|string|max:50',
            'is_renewable' => 'boolean',
            'is_best_seller' => 'boolean',
            'warranty' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'integer|min:0',
            'operating_system_ids' => 'required|array|min:1',
            'operating_system_ids.*' => 'exists:vps_operating_systems,id',
            'location_ids' => 'required|array|min:1',
            'location_ids.*' => 'exists:vps_locations,id',
            'metadata' => 'nullable|array',
            'metadata.available_months' => 'nullable|array',
            'metadata.available_months.*' => 'integer|in:1,2,3,6,9,12,24,36',
            'metadata.connection_methods' => 'nullable|array',
            'metadata.connection_methods.*' => 'string|in:password,ssh',
            'metadata.ip' => 'nullable|string|max:255',
            'metadata.firewall' => 'nullable|string|max:255',
            'metadata.backup' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['bandwidth'] = $validated['bandwidth'] ?? '20 TB';
        $validated['is_renewable'] = $request->boolean('is_renewable', true);
        $validated['is_best_seller'] = $request->boolean('is_best_seller', false);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Đảm bảo slug unique
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (VpsCategory::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $counter++;
        }

        $category = VpsCategory::create($validated);

        // Attach OS và Location
        $category->operatingSystems()->attach($request->operating_system_ids);
        $category->locations()->attach($request->location_ids);

        return back()
            ->with('toast_type', 'success')
            ->with('toast_message', 'Tạo gói VPS thành công!');
    }

    /**
     * Cập nhật gói VPS.
     */
    public function update(Request $request, VpsCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provision_type' => 'required|in:auto,manual',
            'hetzner_server_type' => 'required_if:provision_type,auto|nullable|string|max:50',
            'server_group' => 'required|string|in:cost-optimized,regular,general-purpose',
            'price' => 'required|integer|min:1000',
            'annual_price' => 'nullable|integer|min:1000',
            'cpu' => 'required|string|max:50',
            'ram' => 'required|string|max:50',
            'storage' => 'required|string|max:100',
            'bandwidth' => 'nullable|string|max:50',
            'is_renewable' => 'boolean',
            'is_best_seller' => 'boolean',
            'warranty' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'integer|min:0',
            'operating_system_ids' => 'required|array|min:1',
            'operating_system_ids.*' => 'exists:vps_operating_systems,id',
            'location_ids' => 'required|array|min:1',
            'location_ids.*' => 'exists:vps_locations,id',
            'metadata' => 'nullable|array',
            'metadata.available_months' => 'nullable|array',
            'metadata.available_months.*' => 'integer|in:1,2,3,6,9,12,24,36',
            'metadata.connection_methods' => 'nullable|array',
            'metadata.connection_methods.*' => 'string|in:password,ssh',
            'metadata.ip' => 'nullable|string|max:255',
            'metadata.firewall' => 'nullable|string|max:255',
            'metadata.backup' => 'nullable|string|max:255',
        ]);

        $validated['is_renewable'] = $request->boolean('is_renewable', true);
        $validated['is_best_seller'] = $request->boolean('is_best_seller', false);

        $category->update($validated);

        // Sync OS và Location
        $category->operatingSystems()->sync($request->operating_system_ids);
        $category->locations()->sync($request->location_ids);

        return back()
            ->with('toast_type', 'success')
            ->with('toast_message', 'Cập nhật gói VPS thành công!');
    }

    /**
     * Xóa gói VPS (chỉ khi không có đơn hàng active).
     */
    public function destroy(VpsCategory $category)
    {
        $activeOrders = $category->orders()
            ->whereIn('status', ['pending', 'provisioning', 'active'])
            ->count();

        if ($activeOrders > 0) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', "Không thể xóa gói VPS đang có {$activeOrders} đơn hàng hoạt động.");
        }

        $category->operatingSystems()->detach();
        $category->locations()->detach();
        $category->delete();

        return back()
            ->with('toast_type', 'success')
            ->with('toast_message', 'Đã xóa gói VPS.');
    }
}

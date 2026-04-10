<?php

namespace App\Http\Controllers\Admin\vps;

use App\Http\Controllers\Controller;
use App\Models\VpsOperatingSystem;
use App\Models\VpsLocation;
use App\Services\HetznerService;
use Illuminate\Http\Request;

/**
 * Admin Controller: Cài đặt VPS — Sync dữ liệu từ Hetzner API
 * Quản lý HĐH và Location (bật/tắt)
 */
class VpsSettingController extends Controller
{
    /**
     * Trang cài đặt: danh sách HĐH + Location + server types.
     */
    public function index()
    {
        $operatingSystems = VpsOperatingSystem::orderBy('sort_order')->get();
        $locations = VpsLocation::orderBy('sort_order')->get();

        return view('pages.admin.vps-settings', compact('operatingSystems', 'locations'));
    }

    /**
     * Sync dữ liệu từ Hetzner API vào DB.
     */
    public function sync()
    {
        try {
            // Chạy Artisan command để lấy HĐH, Location và Server Types một cách tự động
            \Illuminate\Support\Facades\Artisan::call('vps:sync-hetzner');
            $output = \Illuminate\Support\Facades\Artisan::output();

            return back()
                ->with('toast_type', 'success')
                ->with('toast_message', "Đồng bộ thành công! Kiểm tra logs hoặc danh sách gói VPS để xem chi tiết.")
                ->with('sync_output', $output);

        } catch (\Exception $e) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', 'Đồng bộ thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Bật/tắt hệ điều hành.
     */
    public function toggleOs(VpsOperatingSystem $os)
    {
        $os->update(['is_active' => !$os->is_active]);

        $status = $os->is_active ? 'bật' : 'tắt';
        return back()
            ->with('toast_type', 'success')
            ->with('toast_message', "Đã {$status} HĐH: {$os->name}");
    }

    /**
     * Bật/tắt location.
     */
    public function toggleLocation(VpsLocation $location)
    {
        $location->update(['is_active' => !$location->is_active]);

        $status = $location->is_active ? 'bật' : 'tắt';
        return back()
            ->with('toast_type', 'success')
            ->with('toast_message', "Đã {$status} Location: {$location->name}");
    }
}

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
            $hetzner = app(HetznerService::class);

            // Sync HĐH (images)
            $images = $hetzner->getImages();
            $syncedOs = 0;
            foreach ($images as $image) {
                $name = $image['description'] ?? $image['name'];
                $hetznerName = $image['name'] ?? null;

                if (!$hetznerName) continue;

                VpsOperatingSystem::updateOrCreate(
                    ['hetzner_name' => $hetznerName],
                    [
                        'name' => $name,
                        'os_flavor' => $image['os_flavor'] ?? null,
                        'architecture' => $image['architecture'] ?? 'x86',
                    ]
                );
                $syncedOs++;
            }

            // Sync Locations
            $locations = $hetzner->getLocations();
            $syncedLoc = 0;
            foreach ($locations as $loc) {
                VpsLocation::updateOrCreate(
                    ['hetzner_name' => $loc['name']],
                    [
                        'name' => $loc['description'] ?? $loc['name'],
                        'city' => $loc['city'] ?? null,
                        'country' => $loc['country'] ?? null,
                        'network_zone' => $loc['network_zone'] ?? null,
                    ]
                );
                $syncedLoc++;
            }

            return back()
                ->with('toast_type', 'success')
                ->with('toast_message', "Sync thành công! {$syncedOs} HĐH, {$syncedLoc} Location.");

        } catch (\Exception $e) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', 'Sync thất bại: ' . $e->getMessage());
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

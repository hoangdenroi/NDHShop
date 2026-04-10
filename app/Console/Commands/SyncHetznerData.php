<?php

namespace App\Console\Commands;

use App\Models\VpsCategory;
use App\Models\VpsLocation;
use App\Models\VpsOperatingSystem;
use App\Services\HetznerService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Command sync dữ liệu từ Hetzner API
 * Lấy HĐH, Location và Server Types để tự động cập nhật hệ thống.
 */
class SyncHetznerData extends Command
{
    protected $signature = 'vps:sync-hetzner';

    protected $description = 'Sync danh sách HĐH, Location và Server Types từ Hetzner Cloud API';

    public function handle(HetznerService $hetzner): int
    {
        $this->info('Đang sync dữ liệu từ Hetzner Cloud...');

        // 1. Sync HĐH
        $this->info('→ Sync hệ điều hành...');
        $images = $hetzner->getImages();
        $syncedOs = 0;
        foreach ($images as $image) {
            $hetznerName = $image['name'] ?? null;
            if (! $hetznerName) {
                continue;
            }

            VpsOperatingSystem::updateOrCreate(
                ['hetzner_name' => $hetznerName],
                [
                    'name' => $image['description'] ?? $hetznerName,
                    'os_flavor' => $image['os_flavor'] ?? null,
                    'architecture' => $image['architecture'] ?? 'x86',
                ]
            );
            $syncedOs++;
        }
        $this->info("  ✓ {$syncedOs} hệ điều hành.");

        // 2. Sync Location
        $this->info('→ Sync locations...');
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
        $this->info("  ✓ {$syncedLoc} locations.");

        // 3. Sync Server Types (VpsCategory)
        $this->info('→ Lấy tỷ giá và Sync server types...');

        // Gọi tỉ giá uy tín từ API (EUR vì Hetzner API mặc định bắt buộc là EUR)
        $exchangeRate = 28000; // Fallback
        try {
            $currencyApi = \Illuminate\Support\Facades\Http::get('https://open.er-api.com/v6/latest/EUR');
            if ($currencyApi->successful()) {
                $rates = $currencyApi->json('rates');
                $exchangeRate = $rates['VND'] ?? 28000;
                $usdRate = $rates['USD'] ?? 1.08;
                $this->info('  ✓ Tỷ giá hiện tại: 1 EUR = '.number_format($exchangeRate, 0, ',', '.').' VNĐ (Khoảng '.number_format($usdRate, 4).' USD)');
            } else {
                $this->warn('  ! Không thể lấy tỷ giá. Dùng tỷ giá mặc định: '.number_format($exchangeRate, 0, ',', '.').' VNĐ');
            }
        } catch (\Exception $e) {
            $this->warn('  ! Lỗi lấy tỷ giá ('.$e->getMessage().'). Dùng mặc định: '.number_format($exchangeRate, 0, ',', '.').' VNĐ');
        }

        $serverTypes = $hetzner->getServerTypes();
        $syncedTypes = 0;

        // Lợi nhuận
        $profitMargin = 1.5; // 30%

        foreach ($serverTypes as $type) {
            $nameStr = $type['name']; // vd: cx11
            $nameStrUpper = strtoupper($nameStr);

            // Tìm giá gốc thấp nhất (chưa VAT - net) làm chuẩn, các nơi khác làm phần thừa (addons)
            $prices = $type['prices'] ?? [];
            if (empty($prices)) {
                continue;
            }

            $baseNetEur = null;
            $addonsEur = [];

            // Tìm base
            foreach ($prices as $p) {
                $net = (float) $p['price_monthly']['net'];
                if ($baseNetEur === null || $net < $baseNetEur) {
                    $baseNetEur = $net;
                }
            }

            // Tính các addon phụ trợ
            foreach ($prices as $p) {
                $net = (float) $p['price_monthly']['net'];
                $locName = $p['location'];
                if ($net > $baseNetEur) {
                    $diffEur = $net - $baseNetEur;
                    $diffVnd = round(($diffEur * $exchangeRate * $profitMargin) / 1000) * 1000; // Làm tròn nghìn
                    if ($diffVnd > 0) {
                        $addonsEur[$locName] = $diffVnd;
                    }
                }
            }

            // Tính giá chuẩn trong VND
            $vndPrice = round(($baseNetEur * $exchangeRate * $profitMargin) / 1000) * 1000;

            // Xác định phân loại (server group) dựa vào cái tên hay kiến trúc
            // Theo yêu cầu hiện tại: cost-optimized, regular, general-purpose
            $serverGroup = 'regular';
            if (str_starts_with($nameStr, 'cx') || str_starts_with($nameStr, 'cax')) {
                // cx = x86 Cost-Optimized, cax = Arm64 Cost-Optimized
                $serverGroup = 'cost-optimized';
            } elseif (str_starts_with($nameStr, 'ccx')) {
                $serverGroup = 'general-purpose';
            }

            // Tạo meta-data
            $metadata = [
                'location_addons' => $addonsEur,
                'available_months' => [1], // Mặc định gói nào cũng add 1 tháng!
                'ip' => '1 IPv4',
                'firewall' => 'Tường lửa cơ bản',
                'backup' => 'Thủ công',
                'connection_methods' => ['password', 'ssh'],
            ];

            // Cập nhật hoặc tạo Category
            $category = VpsCategory::updateOrCreate(
                ['hetzner_server_type' => $nameStr],
                [
                    'name' => "VPS {$nameStrUpper}",
                    'slug' => Str::slug("VPS {$nameStrUpper}").'-'.Str::random(4),
                    'provision_type' => 'auto',
                    'server_group' => $serverGroup,
                    'price' => $vndPrice,
                    'annual_price' => $vndPrice * 12,
                    'cpu' => $type['cores'].' vCPU',
                    'ram' => $type['memory'].' GB',
                    'storage' => $type['disk'].' GB',
                    'status' => 'active',
                    'is_renewable' => true,
                    // Giữ nguyên metadata nếu đã tồn tại và chỉ overwrite phần addon/month
                ]
            );

            // Bổ sung xử lý merge metadata an toàn hơn
            $currentMeta = $category->metadata ?? [];
            $currentMeta['location_addons'] = $addonsEur;
            $currentMeta['available_months'] = $currentMeta['available_months'] ?? [1]; // Cập nhật lại list
            $currentMeta['ip'] = $currentMeta['ip'] ?? '1 IPv4, 1 IPv6';
            $currentMeta['firewall'] = $currentMeta['firewall'] ?? 'DDoS Protection';
            $currentMeta['backup'] = $currentMeta['backup'] ?? 'Có (Tùy chọn)';
            $currentMeta['connection_methods'] = $currentMeta['connection_methods'] ?? ['password', 'ssh'];
            $category->update(['metadata' => $currentMeta]);

            // Tự động gán (sync) các OS và Location đang available
            $activeOsIds = VpsOperatingSystem::active()->pluck('id')->toArray();
            $activeLocIds = VpsLocation::active()->pluck('id')->toArray();

            // Nếu muốn giới hạn location theo array giá trả về, ta có thể lọc `vps_locations.hetzner_name` trùng với `$prices[*]['location']`
            $availableHetznerLocs = array_column($prices, 'location');
            $validLocs = VpsLocation::active()
                ->whereIn('hetzner_name', $availableHetznerLocs)
                ->pluck('id')->toArray();

            $category->operatingSystems()->sync($activeOsIds);
            $category->locations()->sync($validLocs);

            $syncedTypes++;
        }

        $this->info("  ✓ {$syncedTypes} server types (VpsCategory) đã được đồng bộ chuẩn.");

        $this->info('Toàn bộ thao tác hoàn tất!');

        return 0;
    }
}

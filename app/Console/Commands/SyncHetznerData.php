<?php

namespace App\Console\Commands;

use App\Models\VpsOperatingSystem;
use App\Models\VpsLocation;
use App\Services\HetznerService;
use Illuminate\Console\Command;

/**
 * Command sync dữ liệu từ Hetzner API
 * Sync HĐH (images) và Location vào DB local
 */
class SyncHetznerData extends Command
{
    protected $signature = 'vps:sync-hetzner';
    protected $description = 'Sync danh sách HĐH và Location từ Hetzner Cloud API';

    public function handle(HetznerService $hetzner): int
    {
        $this->info('Đang sync dữ liệu từ Hetzner Cloud...');

        // Sync HĐH
        $this->info('→ Sync hệ điều hành...');
        $images = $hetzner->getImages();
        $syncedOs = 0;

        foreach ($images as $image) {
            $hetznerName = $image['name'] ?? null;
            if (!$hetznerName) continue;

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
        $this->info("  ✓ {$syncedOs} hệ điều hành đã sync.");

        // Sync Location
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
        $this->info("  ✓ {$syncedLoc} locations đã sync.");

        // Hiển thị server types (không lưu DB, chỉ show cho admin tham khảo)
        $this->info('→ Lấy danh sách server types...');
        $serverTypes = $hetzner->getServerTypes();

        $this->table(
            ['Name', 'CPU', 'RAM (GB)', 'Disk (GB)', 'Type'],
            collect($serverTypes)->map(function ($type) {
                return [
                    $type['name'],
                    $type['cores'] . ' cores',
                    $type['memory'],
                    $type['disk'],
                    $type['cpu_type'] ?? 'shared',
                ];
            })->toArray()
        );

        $this->info("Sync hoàn tất! {$syncedOs} HĐH, {$syncedLoc} locations, " . count($serverTypes) . ' server types.');

        return 0;
    }
}

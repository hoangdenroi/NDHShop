<?php

namespace App\Console\Commands;

use App\Models\VpsOrder;
use App\Models\VpsOrderLog;
use App\Services\HetznerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Command kiểm tra VPS hết hạn
 * Chạy hàng ngày: tìm VPS active đã hết hạn → xóa server Hetzner → cập nhật status
 */
class CheckVpsExpiration extends Command
{
    protected $signature = 'vps:check-expiration';
    protected $description = 'Kiểm tra và xử lý các VPS đã hết hạn sử dụng';

    public function handle(HetznerService $hetzner): int
    {
        $expiredOrders = VpsOrder::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('Không có VPS nào hết hạn.');
            return 0;
        }

        $this->info("Tìm thấy {$expiredOrders->count()} VPS hết hạn.");

        $successCount = 0;
        $errorCount = 0;

        foreach ($expiredOrders as $order) {
            try {
                // Xóa server trên Hetzner
                if ($order->hetzner_server_id) {
                    $deleted = $hetzner->deleteServer($order->hetzner_server_id);
                    if (!$deleted) {
                        Log::warning("Không thể xóa server Hetzner #{$order->hetzner_server_id} cho đơn {$order->order_code}");
                    }
                }

                // Cập nhật trạng thái
                $order->update(['status' => 'expired']);

                VpsOrderLog::create([
                    'vps_order_id' => $order->id,
                    'action' => 'expired',
                    'detail' => "VPS hết hạn. Server Hetzner #{$order->hetzner_server_id} đã được xóa tự động.",
                ]);

                $successCount++;
                $this->line("  ✓ {$order->order_code} — IP: {$order->ip_address}");

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("  ✗ {$order->order_code} — Lỗi: {$e->getMessage()}");
                Log::error("VPS expiration error for {$order->order_code}: {$e->getMessage()}");
            }
        }

        $this->info("Hoàn tất: {$successCount} thành công, {$errorCount} lỗi.");

        return $errorCount > 0 ? 1 : 0;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\GiftPage;
use Illuminate\Console\Command;

/**
 * ExpireGifts — Vô hiệu hóa các gift đã hết hạn.
 *
 * Chạy hàng ngày bằng scheduler:
 * $schedule->command('gift:expire')->daily();
 */
class ExpireGifts extends Command
{
    protected $signature = 'gift:expire';
    protected $description = 'Vô hiệu hóa các gift page đã hết hạn (basic plan)';

    public function handle(): int
    {
        // Tìm gift active nhưng đã quá hạn
        $expiredGifts = GiftPage::where('status', GiftPage::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        $count = $expiredGifts->count();

        if ($count === 0) {
            $this->info('Không có gift nào hết hạn.');
            return self::SUCCESS;
        }

        foreach ($expiredGifts as $gift) {
            $gift->update([
                'status'    => GiftPage::STATUS_EXPIRED,
                'is_active' => false,
            ]);
        }

        $this->info("Đã vô hiệu hóa {$count} gift hết hạn.");

        return self::SUCCESS;
    }
}

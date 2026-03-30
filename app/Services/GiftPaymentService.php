<?php

namespace App\Services;

use App\Models\GiftOrder;
use App\Models\GiftPage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * GiftPaymentService — Xử lý thanh toán cho gift pages.
 *
 * Sử dụng DB::transaction (Atomicity) để đảm bảo:
 * - Nếu bất kỳ bước nào fail → rollback toàn bộ
 * - Balance user không bị trừ sai
 * - Gift không bị kích hoạt khi chưa thanh toán xong
 */
class GiftPaymentService
{
    public function __construct(
        private GiftActivationService $activationService
    ) {}

    /**
     * Lấy giá theo plan.
     */
    public static function getPrice(string $plan): int
    {
        return GiftPage::PLAN_PRICES[$plan] ?? 0;
    }

    /**
     * Kiểm tra user có đủ balance để thanh toán không.
     */
    public function validateBalance(User $user, int $amount): bool
    {
        return (int) $user->balance >= $amount;
    }

    /**
     * Xử lý thanh toán bằng balance.
     *
     * Flow (Atomic):
     * 1. Validate balance đủ tiền
     * 2. Trừ balance user
     * 3. Tạo GiftOrder (status = paid)
     * 4. Kích hoạt gift (generate share_code, set status=active, render HTML)
     *
     * @throws \Exception Nếu không đủ tiền hoặc lỗi trong quá trình xử lý
     * @return GiftOrder Đơn hàng đã thanh toán thành công
     */
    public function processPayment(GiftPage $giftPage, User $user, string $plan): GiftOrder
    {
        // Ưu tiên giá template premium, fallback sang giá plan mặc định
        $template = $giftPage->template;
        if ($template && $template->is_premium && $template->price > 0) {
            $amount = (int) $template->price;
        } else {
            $amount = self::getPrice($plan);
        }

        // Gói basic miễn phí → không cần validate balance
        if ($amount > 0 && !$this->validateBalance($user, $amount)) {
            throw new \Exception('Số dư tài khoản không đủ. Vui lòng nạp thêm tiền.');
        }

        return DB::transaction(function () use ($giftPage, $user, $plan, $amount) {
            // Bước 1: Trừ balance user (nếu có phí)
            if ($amount > 0) {
                $user->decrement('balance', $amount);
                $user->refresh(); // Refresh để đảm bảo balance mới nhất
            }

            // Bước 2: Tạo đơn hàng (paid ngay)
            $giftOrder = GiftOrder::create([
                'user_id'        => $user->id,
                'gift_page_id'   => $giftPage->id,
                'order_code'     => GiftOrder::generateOrderCode(),
                'plan'           => $plan,
                'amount'         => $amount,
                'payment_method' => GiftOrder::METHOD_BALANCE,
                'status'         => GiftOrder::STATUS_PAID,
                'paid_at'        => now(),
                'metadata'       => [
                    'balance_before' => (int) $user->balance + $amount,
                    'balance_after'  => (int) $user->balance,
                ],
            ]);

            // Bước 3: Kích hoạt gift
            $this->activationService->activate($giftPage, $plan);

            Log::info('Gift payment success', [
                'user_id'     => $user->id,
                'gift_id'     => $giftPage->id,
                'order_code'  => $giftOrder->order_code,
                'plan'        => $plan,
                'amount'      => $amount,
            ]);

            \App\Services\AuditLogService::log(
                'purchased_gift_template',
                $giftOrder,
                ['balance' => (float) ($user->balance + $amount)],
                ['balance' => (float) $user->balance, 'plan' => $plan, 'amount' => (float) $amount],
                $user->id
            );

            return $giftOrder;
        });
    }

    /**
     * Nâng cấp gift từ Basic lên Premium.
     *
     * @throws \Exception Nếu không đủ tiền hoặc gift không hợp lệ
     * @return GiftOrder Đơn hàng nâng cấp
     */
    public function upgradeToPremium(GiftPage $giftPage, User $user): GiftOrder
    {
        if ($giftPage->isPremium()) {
            throw new \Exception('Gift này đã là gói Premium.');
        }

        $amount = self::getPrice(GiftPage::PLAN_PREMIUM);

        if (!$this->validateBalance($user, $amount)) {
            throw new \Exception('Số dư tài khoản không đủ. Vui lòng nạp thêm tiền.');
        }

        return DB::transaction(function () use ($giftPage, $user, $amount) {
            // Trừ balance
            $user->decrement('balance', $amount);
            $user->refresh();

            // Tạo đơn hàng upgrade
            $giftOrder = GiftOrder::create([
                'user_id'        => $user->id,
                'gift_page_id'   => $giftPage->id,
                'order_code'     => GiftOrder::generateOrderCode(),
                'plan'           => GiftPage::PLAN_PREMIUM,
                'amount'         => $amount,
                'payment_method' => GiftOrder::METHOD_BALANCE,
                'status'         => GiftOrder::STATUS_PAID,
                'paid_at'        => now(),
                'metadata'       => [
                    'type'           => 'upgrade',
                    'from_plan'      => $giftPage->plan,
                    'balance_before' => (int) $user->balance + $amount,
                    'balance_after'  => (int) $user->balance,
                ],
            ]);

            // Kích hoạt lại với plan premium
            $this->activationService->activate($giftPage, GiftPage::PLAN_PREMIUM);

            Log::info('Gift upgrade success', [
                'user_id'    => $user->id,
                'gift_id'    => $giftPage->id,
                'order_code' => $giftOrder->order_code,
            ]);

            \App\Services\AuditLogService::log(
                'upgraded_gift_premium',
                $giftOrder,
                ['balance' => (float) ($user->balance + $amount)],
                ['balance' => (float) $user->balance, 'amount' => (float) $amount],
                $user->id
            );

            return $giftOrder;
        });
    }
}

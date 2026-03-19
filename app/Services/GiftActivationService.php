<?php

namespace App\Services;

use App\Models\GiftPage;

/**
 * GiftActivationService — Kích hoạt gift sau khi thanh toán thành công.
 *
 * Xử lý:
 * - Generate share_code
 * - Set status = active
 * - Set expires_at (nếu basic = 7 ngày, premium = null)
 * - Set plan và is_premium
 * - Pre-render HTML (cache)
 */
class GiftActivationService
{
    public function __construct(
        private GiftRenderService $renderService
    ) {}

    /**
     * Kích hoạt gift page sau thanh toán.
     */
    public function activate(GiftPage $giftPage, string $plan): GiftPage
    {
        $isPremium = ($plan === GiftPage::PLAN_PREMIUM);

        // Tính thời hạn hết hạn
        // Lưu ý: premium = null (vĩnh viễn), không dùng ?? vì null là giá trị hợp lệ
        $duration = array_key_exists($plan, GiftPage::PLAN_DURATIONS)
            ? GiftPage::PLAN_DURATIONS[$plan]
            : 7;
        $expiresAt = $duration !== null ? now()->addDays($duration) : null;

        // Generate share_code nếu chưa có
        $shareCode = $giftPage->share_code ?: GiftPage::generateShareCode();

        // Pre-render HTML
        $renderedHtml = $this->renderService->render($giftPage);

        // Cập nhật gift page
        $giftPage->update([
            'share_code'    => $shareCode,
            'status'        => GiftPage::STATUS_ACTIVE,
            'plan'          => $plan,
            'is_premium'    => $isPremium,
            'is_active'     => true,
            'expires_at'    => $expiresAt,
            'rendered_html' => $renderedHtml,
        ]);

        return $giftPage->fresh();
    }
}

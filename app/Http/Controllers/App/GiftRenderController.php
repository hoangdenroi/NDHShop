<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\GiftAnalytic;
use App\Models\GiftPage;
use App\Models\GiftTemplate;
use App\Services\GiftRenderService;

/**
 * GiftRenderController — Render trang quà tặng cho người nhận.
 *
 * Link format: /gifts/{share_code}
 * Middleware CheckGiftAccess kiểm tra hết hạn / status trước khi vào đây.
 */
class GiftRenderController extends Controller
{
    public function __construct(
        private GiftRenderService $renderService
    ) {}

    /**
     * Hiển thị trang quà tặng cho người nhận.
     */
    public function show($shareCode)
    {
        // Tìm gift page theo share_code (chỉ lấy active)
        $giftPage = GiftPage::where('share_code', $shareCode)
            ->where('status', GiftPage::STATUS_ACTIVE)
            ->firstOrFail();

        // Kiểm tra hết hạn → show trang hết hạn
        if ($giftPage->isExpired()) {
            return view('pages.app.gifts.expired', compact('giftPage'));
        }

        // Tăng lượt xem
        $giftPage->incrementViews();

        // Tracking analytics chi tiết cho premium
        if ($giftPage->isPremium()) {
            GiftAnalytic::track($giftPage);
        }

        $template = $giftPage->template;

        // Kiểm tra template còn hoạt động không
        if (!$template || !$template->is_active) {
            abort(404, 'Mẫu thiệp này tạm thời không khả dụng.');
        }

        // Lấy HTML (ưu tiên cache, nếu không có → render mới)
        $htmlCode = $this->renderService->getRenderedHtml($giftPage);

        // Inject meta tags SEO
        $htmlCode = $this->renderService->injectMetaTags($htmlCode, $giftPage);

        // Inject watermark cho gói basic
        if (!$giftPage->isPremium()) {
            $htmlCode = $this->renderService->injectWatermark($htmlCode);
        }

        return response($htmlCode);
    }

    /**
     * Render demo template với data mẫu mặc định từ schema.
     * Route: /gifts/demo/{template:slug}
     */
    public function demo(GiftTemplate $template)
    {
        if (!$template->is_active) {
            abort(404, 'Mẫu thiệp này không khả dụng.');
        }

        // Lấy data mặc định từ schema fields
        $defaultData = [];
        foreach ($template->getSchemaFields() as $field) {
            $defaultData[$field['key']] = $field['default'] ?? $field['label'] ?? $field['key'];
        }

        // Render template với data mặc định
        $htmlCode = $this->renderService->renderFromTemplate($template, $defaultData);

        // Inject watermark DEMO
        $htmlCode = $this->renderService->injectWatermark($htmlCode, true);

        return response($htmlCode);
    }
}

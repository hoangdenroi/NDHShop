<?php

namespace App\Http\Middleware;

use App\Models\GiftPage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckGiftAccess — Middleware kiểm tra quyền truy cập gift page.
 *
 * Logic:
 * - Gift không tồn tại hoặc chưa active → 404
 * - Gift hết hạn → hiển thị trang hết hạn
 * - Gift basic → set flag để inject watermark
 * - Gift premium → full feature
 */
class CheckGiftAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $shareCode = $request->route('share_code');

        // Tìm gift page (bỏ qua global scope is_deleted vì sẽ check status)
        $giftPage = GiftPage::where('share_code', $shareCode)
            ->where('status', GiftPage::STATUS_ACTIVE)
            ->first();

        // Không tìm thấy hoặc chưa active → 404
        if (!$giftPage) {
            abort(404);
        }

        // Hết hạn → redirect trang hết hạn
        if ($giftPage->isExpired()) {
            return response()->view('pages.app.gifts.expired', [
                'giftPage' => $giftPage,
            ], 410); // 410 Gone
        }

        // Bind gift vào request để controller dùng
        $request->merge(['gift_page' => $giftPage]);

        return $next($request);
    }
}

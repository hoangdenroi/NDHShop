<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\GiftPage;
use App\Services\GiftPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * GiftPaymentController — Xử lý thanh toán + kích hoạt gift.
 *
 * Flow:
 * 1. showPayment() — Trang xác nhận thanh toán
 * 2. processPayment() — Xử lý trừ balance (DB::transaction)
 * 3. success() — Trang thành công, hiển thị link + QR
 * 4. upgradePlan() — Nâng cấp Basic → Premium
 */
class GiftPaymentController extends Controller
{
    public function __construct(
        private GiftPaymentService $paymentService
    ) {}

    /**
     * Hiển thị trang xác nhận thanh toán.
     */
    public function showPayment(GiftPage $gift, string $plan)
    {
        if ($gift->user_id !== Auth::id()) {
            abort(403);
        }

        // Validate plan hợp lệ
        if (! in_array($plan, [GiftPage::PLAN_BASIC, GiftPage::PLAN_PREMIUM])) {
            abort(404);
        }

        // Chỉ cho thanh toán khi ở trạng thái draft
        if ($gift->status !== GiftPage::STATUS_DRAFT) {
            return redirect()->route('app.gifts.my-gifts')
                ->with('info', 'Gift này đã được kích hoạt.');
        }

        $amount = GiftPaymentService::getPrice($plan);
        $user = Auth::user();
        $hasEnoughBalance = $this->paymentService->validateBalance($user, $amount);

        return view('pages.app.gifts.payment', compact('gift', 'plan', 'amount', 'user', 'hasEnoughBalance'));
    }

    /**
     * Xử lý thanh toán — trừ balance, tạo order, kích hoạt gift.
     * Toàn bộ wrap trong DB::transaction.
     */
    public function processPayment(Request $request, GiftPage $gift)
    {
        if ($gift->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'plan' => 'required|in:basic,premium',
        ]);

        $plan = $request->input('plan');
        $user = Auth::user();

        try {
            $giftOrder = $this->paymentService->processPayment($gift, $user, $plan);

            return redirect()->route('app.gifts.success', $gift->fresh()->share_code)
                ->with('toast_type', 'success')
                ->with('toast_message', 'Thanh toán thành công! Gift đã được kích hoạt.');
        } catch (\Exception $e) {
            $redirect = back()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());

            // Nếu lỗi liên quan đến số dư, thêm link nạp tiền
            if (str_contains($e->getMessage(), 'Số dư')) {
                $redirect->with('toast_link', route('app.profile', ['tab' => 'topup']))
                    ->with('toast_link_text', 'Nạp tiền ngay');
            }

            return $redirect;
        }
    }

    /**
     * Trang thành công — hiển thị link + QR.
     */
    public function success(GiftPage $gift)
    {
        if ($gift->user_id !== Auth::id()) {
            abort(403);
        }

        if ($gift->status !== GiftPage::STATUS_ACTIVE) {
            return redirect()->route('app.gifts.my-gifts');
        }

        return view('pages.app.gifts.success', compact('gift'));
    }

    /**
     * Nâng cấp gift từ Basic lên Premium (upsell).
     */
    public function upgradePlan(Request $request, GiftPage $gift)
    {
        if ($gift->user_id !== Auth::id()) {
            abort(403);
        }

        if ($gift->isPremium()) {
            return back()->with('info', 'Gift này đã là gói Premium.');
        }

        $user = Auth::user();

        try {
            $giftOrder = $this->paymentService->upgradeToPremium($gift, $user);

            return redirect()->route('app.gifts.success', $gift->fresh()->share_code)
                ->with('toast_type', 'success')
                ->with('toast_message', 'Nâng cấp Premium thành công! Gift đã được cập nhật.');
        } catch (\Exception $e) {
            $redirect = back()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());

            // Nếu lỗi liên quan đến số dư, thêm link nạp tiền
            if (str_contains($e->getMessage(), 'Số dư')) {
                $redirect->with('toast_link', route('app.profile', ['tab' => 'topup']))
                    ->with('toast_link_text', 'Nạp tiền ngay');
            }

            return $redirect;
        }
    }
}

<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Hiển thị trang thanh toán.
     */
    public function index()
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        // Giỏ hàng trống → redirect
        if (! $cart || $cart->items()->count() === 0) {
            return redirect()->route('app.src-app-game')
                ->with('toast_type', 'warning')
                ->with('toast_message', 'Giỏ hàng của bạn đang trống.');
        }

        $cartItems = $cart->items()->with(['product.assets' => function ($q) {
            $q->orderBy('sort_order', 'asc');
        }])->get();

        $subtotal = $cartItems->sum('price');

        return view('pages.app.checkout', compact('user', 'cartItems', 'subtotal'));
    }

    /**
     * API: Áp dụng mã giảm giá (AJAX).
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại.',
            ], 404);
        }

        if (! $coupon->isValid($request->subtotal)) {
            $reason = 'Mã giảm giá không hợp lệ.';
            if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
                $reason = 'Mã giảm giá đã hết hạn.';
            } elseif ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
                $reason = 'Mã giảm giá đã hết lượt sử dụng.';
            } elseif ($request->subtotal < $coupon->min_order) {
                $reason = 'Đơn hàng tối thiểu '.number_format($coupon->min_order, 0, ',', '.').'đ để áp dụng mã này.';
            }

            return response()->json([
                'success' => false,
                'message' => $reason,
            ], 400);
        }

        $discount = $coupon->calculateDiscount($request->subtotal);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount' => $discount,
            'coupon_code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);
    }

    /**
     * Xử lý thanh toán đơn hàng.
     */
    public function process(Request $request)
    {
        $request->validate([
            'coupon_code' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (! $cart || $cart->items()->count() === 0) {
            return redirect()->route('app.src-app-game')
                ->with('toast_type', 'warning')
                ->with('toast_message', 'Giỏ hàng của bạn đang trống.');
        }

        $cartItems = $cart->items()->with('product')->get();
        $subtotal = $cartItems->sum('price');
        $discount = 0;
        $couponCode = null;

        // Xử lý mã giảm giá nếu có
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', strtoupper(trim($request->coupon_code)))->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
                $couponCode = $coupon->code;
            }
        }

        $totalAmount = max(0, $subtotal - $discount);

        try {
            DB::transaction(function () use ($user, $cart, $cartItems, $totalAmount, $discount, $couponCode) {
                // 1. Lock row user để tránh race condition trừ tiền đồng thời
                $lockedUser = \App\Models\User::lockForUpdate()->findOrFail($user->id);

                // 2. Kiểm tra lại số dư bên trong lock (tránh double-spend)
                if ($lockedUser->balance < $totalAmount) {
                    throw new \Exception('Số dư không đủ. Vui lòng nạp thêm '.number_format($totalAmount - $lockedUser->balance, 0, ',', '.').'đ.');
                }

                // 3. Tạo đơn hàng
                $order = Order::create([
                    'user_id' => $lockedUser->id,
                    'order_code' => 'ORD-'.strtoupper(Str::random(8)),
                    'status' => 'completed',
                    'payment_method' => 'balance',
                    'payment_status' => 'paid',
                    'total_amount' => $totalAmount,
                    'coupon_code' => $couponCode,
                    'discount_amount' => $discount,
                    'note' => null,
                ]);

                // 4. Tạo order items
                foreach ($cartItems as $item) {
                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'price' => $item->price,
                    ]);
                }

                // 5. Trừ số dư bằng query trực tiếp (atomic decrement)
                $oldBalance = $lockedUser->balance;
                \App\Models\User::where('id', $lockedUser->id)
                    ->decrement('balance', $totalAmount);

                \App\Services\AuditLogService::log(
                    'purchased_product_order',
                    $order,
                    ['balance' => (float) $oldBalance],
                    ['balance' => (float) ($oldBalance - $totalAmount), 'order_code' => $order->order_code],
                    $user->id
                );

                // 6. Tăng lượt sử dụng coupon (atomic increment)
                if ($couponCode) {
                    Coupon::where('code', $couponCode)->increment('used_count');
                }

                // 7. Xoá giỏ hàng khỏi DB
                $cart->items()->delete();
                $cart->delete();
            });

            return redirect()->route('app.checkout.success')
                ->with('toast_type', 'success')
                ->with('toast_message', 'Thanh toán thành công! Cảm ơn bạn đã mua hàng.');

        } catch (\Exception $e) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());
        }
    }

    /**
     * Trang thanh toán thành công.
     */
    public function success()
    {
        return view('pages.app.checkout-success');
    }
}

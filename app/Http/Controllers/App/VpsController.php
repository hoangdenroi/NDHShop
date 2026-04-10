<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\VpsCategory;
use App\Models\VpsOrder;
use App\Models\VpsOperatingSystem;
use App\Models\VpsLocation;
use App\Services\VpsOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller quản lý VPS phía user
 *
 * Danh sách gói, chi tiết, đặt mua, lịch sử, quản lý VPS (restart, rebuild, đổi MK)
 */
class VpsController extends Controller
{
    /**
     * Trang danh sách gói VPS.
     */
    public function index()
    {
        $categories = VpsCategory::active()
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('pages.app.vps.vps-index', compact('categories'));
    }

    /**
     * Chi tiết gói VPS + Form đặt mua.
     */
    public function show(VpsCategory $slug)
    {
        $category = $slug;

        // Lấy HĐH và Location active cho gói này
        $operatingSystems = $category->operatingSystems()->active()->orderBy('sort_order')->get();
        $locations = $category->locations()->active()->orderBy('sort_order')->get();

        return view('pages.app.vps.vps-show', compact('category', 'operatingSystems', 'locations'));
    }

    /**
     * Xử lý đặt mua VPS.
     */
    public function purchase(Request $request, VpsCategory $slug)
    {
        $category = $slug;

        $request->validate([
            'operating_system' => 'required|string|max:100',
            'location' => 'required|string|max:50',
            'duration_months' => 'required|integer|min:1|max:24',
            'connection_method' => 'required|in:password,ssh',
            'ssh_key_name' => 'required_if:connection_method,ssh|nullable|string|max:100',
            'ssh_key_content' => 'required_if:connection_method,ssh|nullable|string',
            'coupon_code' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $service = app(VpsOrderService::class);
            $order = $service->purchase(
                $category,
                $request->operating_system,
                $request->location,
                $request->duration_months,
                $request->connection_method,
                $request->ssh_key_name,
                $request->ssh_key_content,
                $request->coupon_code,
                $request->note
            );

            return redirect()
                ->route('app.vps.order-detail', $order)
                ->with('toast_type', 'success')
                ->with('toast_message', 'Đặt mua VPS thành công! Server đang được khởi tạo.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());
        }
    }

    /**
     * Lịch sử đơn hàng VPS của user.
     */
    public function orders()
    {
        $orders = VpsOrder::where('user_id', Auth::id())
            ->with('vpsCategory')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.app.vps.vps-orders', compact('orders'));
    }

    /**
     * Chi tiết đơn hàng VPS + Panel quản lý.
     */
    public function orderDetail(VpsOrder $order)
    {
        // Chỉ cho phép chủ sở hữu xem
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        $order->load(['vpsCategory', 'logs']);

        // Lấy danh sách OS cho rebuild
        $operatingSystems = [];
        if ($order->isActive()) {
            $operatingSystems = $order->vpsCategory
                ->operatingSystems()
                ->active()
                ->orderBy('sort_order')
                ->get();
        }

        return view('pages.app.vps.vps-order-detail', compact('order', 'operatingSystems'));
    }

    /**
     * Hủy đơn hàng VPS.
     */
    public function cancelOrder(VpsOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $service = app(VpsOrderService::class);
            $service->cancel($order);

            return back()
                ->with('toast_type', 'success')
                ->with('toast_message', 'Đã hủy VPS. Số tiền hoàn đã được cộng vào tài khoản.');

        } catch (\Exception $e) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());
        }
    }

    /**
     * Gia hạn VPS.
     */
    public function renewOrder(Request $request, VpsOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'duration_months' => 'required|integer|min:1|max:24',
        ]);

        try {
            $service = app(VpsOrderService::class);
            $service->renew($order, $request->duration_months);

            return back()
                ->with('toast_type', 'success')
                ->with('toast_message', 'Gia hạn VPS thành công!');

        } catch (\Exception $e) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());
        }
    }

    /**
     * Restart VPS.
     */
    public function reboot(VpsOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $service = app(VpsOrderService::class);
            $service->reboot($order);

            return back()
                ->with('toast_type', 'success')
                ->with('toast_message', 'VPS đang được restart. Vui lòng đợi 1-2 phút.');

        } catch (\Exception $e) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());
        }
    }

    /**
     * Đổi mật khẩu root VPS.
     */
    public function resetPassword(VpsOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $service = app(VpsOrderService::class);
            $newPassword = $service->resetPassword($order);

            return back()
                ->with('toast_type', 'success')
                ->with('toast_message', 'Mật khẩu mới: ' . $newPassword);

        } catch (\Exception $e) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());
        }
    }

    /**
     * Cài lại HĐH (rebuild) VPS.
     */
    public function rebuild(Request $request, VpsOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'image' => 'required|string|max:100',
        ]);

        try {
            $service = app(VpsOrderService::class);
            $service->rebuild($order, $request->image);

            return back()
                ->with('toast_type', 'success')
                ->with('toast_message', 'Đã cài lại HĐH. Mật khẩu root mới đã được cập nhật.');

        } catch (\Exception $e) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers\Admin\vps;

use App\Http\Controllers\Controller;
use App\Models\VpsOrder;
use App\Services\VpsOrderService;
use Illuminate\Http\Request;

/**
 * Admin Controller: Quản lý đơn hàng VPS
 * Xem, lọc, hủy đơn hàng
 */
class VpsOrderController extends Controller
{
    /**
     * Danh sách đơn hàng VPS (filter theo status).
     */
    public function index(Request $request)
    {
        $query = VpsOrder::with(['user', 'vpsCategory'])
            ->orderByDesc('created_at');

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo order_code hoặc IP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        // Thống kê tổng quan
        $stats = [
            'total' => VpsOrder::count(),
            'active' => VpsOrder::where('status', 'active')->count(),
            'pending' => VpsOrder::where('status', 'pending')->count(),
            'expired' => VpsOrder::where('status', 'expired')->count(),
        ];

        return view('pages.admin.vps-orders', compact('orders', 'stats'));
    }

    /**
     * Chi tiết đơn hàng VPS.
     */
    public function show(VpsOrder $order)
    {
        $order->load(['user', 'vpsCategory', 'logs.user']);

        return view('pages.admin.vps-order-detail', compact('order'));
    }

    /**
     * Admin hủy đơn hàng VPS (hoàn tiền).
     */
    public function cancel(Request $request, VpsOrder $order)
    {
        try {
            $service = app(VpsOrderService::class);
            $service->cancel($order);

            return back()
                ->with('toast_type', 'success')
                ->with('toast_message', "Đã hủy đơn hàng {$order->order_code}. Tiền đã hoàn cho khách.");

        } catch (\Exception $e) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());
        }
    }

    /**
     * Admin giao VPS thủ công — Điền thông tin kết nối.
     */
    public function fulfill(Request $request, VpsOrder $order)
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip',
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:6|max:255',
            'ipv6_address' => 'nullable|string|max:255',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        try {
            $service = app(VpsOrderService::class);
            $service->fulfill(
                $order,
                $validated['ip_address'],
                $validated['username'],
                $validated['password'],
                $validated['ipv6_address'] ?? null,
                $validated['admin_note'] ?? null
            );

            return back()
                ->with('toast_type', 'success')
                ->with('toast_message', "Đã giao VPS cho đơn {$order->order_code} thành công!");

        } catch (\Exception $e) {
            return back()
                ->with('toast_type', 'error')
                ->with('toast_message', $e->getMessage());
        }
    }
}

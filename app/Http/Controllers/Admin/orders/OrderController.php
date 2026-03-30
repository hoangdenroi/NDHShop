<?php

namespace App\Http\Controllers\Admin\orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng (search + filter status + paginate)
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        // Tìm kiếm theo mã đơn hoặc tên khách hàng
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('pages.admin.orders.order-index', compact('orders'));
    }

    /**
     * API: Lấy chi tiết đơn hàng (cho modal)
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product.assets', 'licenses']);

        return response()->json([
            'id' => $order->id,
            'order_code' => $order->order_code,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'total_amount' => $order->total_amount,
            'coupon_code' => $order->coupon_code,
            'discount_amount' => $order->discount_amount,
            'note' => $order->note,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'user' => $order->user ? [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
                'avatar_url' => $order->user->avatar_url,
                'balance' => $order->user->balance ?? 0,
            ] : null,
            'items' => $order->items->map(function ($item) {
                $product = $item->product;
                $image = $product?->assets?->where('type', 'image')->first();
                return [
                    'product_id' => $item->product_id,
                    'name' => $product?->name ?? 'Sản phẩm đã xóa',
                    'price' => $item->price,
                    'image' => $image?->url_or_path ?? null,
                ];
            }),
            'licenses' => $order->licenses->map(function ($lic) {
                return [
                    'license_key' => $lic->license_key,
                    'product_name' => $lic->product?->name,
                    'is_active' => $lic->is_active,
                    'granted_at' => $lic->granted_at?->format('d/m/Y H:i'),
                    'expires_at' => $lic->expires_at?->format('d/m/Y H:i'),
                ];
            }),
        ]);
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * Nếu hủy → hoàn tiền tự động + tạo thông báo
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:completed,pending,cancelled',
        ]);

        $newStatus = $request->status;
        $oldStatus = $order->status;

        // Đơn hàng đã hủy không cho phép cập nhật lại
        if ($oldStatus === 'cancelled') {
            return back()->with('error', 'Đơn hàng đã bị hủy, không thể thay đổi trạng thái.');
        }

        // Không cho phép thay đổi nếu trạng thái giống nhau
        if ($oldStatus === $newStatus) {
            return back()->with('info', 'Trạng thái đơn hàng không thay đổi.');
        }

        // Nếu hủy đơn → hoàn tiền atomic
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            DB::transaction(function () use ($order, $newStatus) {
                // Hoàn tiền cho user
                $user = User::lockForUpdate()->find($order->user_id);
                if ($user) {
                    $oldBalance = $user->balance;
                    $user->increment('balance', $order->total_amount);

                    \App\Services\AuditLogService::log(
                        'order_refund_balance',
                        $order,
                        ['balance' => (float) $oldBalance],
                        ['balance' => (float) ($oldBalance + $order->total_amount), 'order_code' => $order->order_code]
                    );

                    // Tạo thông báo cho user
                    Notification::create([
                        'user_id' => $user->id,
                        'scope' => 'personal',
                        'title' => 'Đơn hàng đã bị hủy',
                        'message' => "Đơn hàng #{$order->order_code} đã bị hủy bởi quản trị viên. Số tiền " . number_format($order->total_amount, 0, ',', '.') . "đ đã được hoàn vào tài khoản của bạn.",
                        'type' => 'order_cancelled',
                        'related_entity_type' => 'order',
                        'related_entity_id' => $order->id,
                        'is_read' => false,
                        'priority' => 'high',
                        'action_url' => '/apps/profile',
                    ]);
                }

                $order->update(['status' => $newStatus]);
            });

            return back()->with('success', "Đã hủy đơn hàng #{$order->order_code} và hoàn " . number_format($order->total_amount, 0, ',', '.') . "đ cho khách hàng.");
        }

        // Cập nhật trạng thái bình thường
        $order->update(['status' => $newStatus]);

        // Tạo thông báo nếu chuyển sang completed
        if ($newStatus === 'completed') {
            Notification::create([
                'user_id' => $order->user_id,
                'scope' => 'personal',
                'title' => 'Đơn hàng hoàn tất',
                'message' => "Đơn hàng #{$order->order_code} đã được xác nhận hoàn tất. Bạn có thể tải sản phẩm ngay bây giờ.",
                'type' => 'order_completed',
                'related_entity_type' => 'order',
                'related_entity_id' => $order->id,
                'is_read' => false,
                'priority' => 'normal',
                'action_url' => '/apps/profile',
            ]);
        }

        $statusLabels = ['completed' => 'Hoàn tất', 'pending' => 'Chờ xử lý', 'cancelled' => 'Đã hủy'];
        return back()->with('success', "Đã cập nhật trạng thái đơn hàng #{$order->order_code} thành \"{$statusLabels[$newStatus]}\".");
    }
}

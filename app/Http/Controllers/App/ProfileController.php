<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('ui.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        
        // Remove file handle from massive assignment array
        if (isset($data['avatar_file'])) {
            unset($data['avatar_file']);
        }
        $user->fill($data);

        // Xử lý upload avatar lên Cloudinary
        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');

            // Xóa file cũ trên Cloudinary thông qua extract URL
            if ($user->avatar_url && str_contains($user->avatar_url, 'res.cloudinary.com')) {
                try {
                    $urlParts = explode('/', $user->avatar_url);
                    $uploadIndex = array_search('upload', $urlParts);
                    if ($uploadIndex !== false && isset($urlParts[$uploadIndex + 2])) {
                        $publicIdWithExt = implode('/', array_slice($urlParts, $uploadIndex + 2));
                        $publicId = pathinfo($publicIdWithExt, PATHINFO_DIRNAME) . '/' . pathinfo($publicIdWithExt, PATHINFO_FILENAME);
                        cloudinary()->uploadApi()->destroy($publicId);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Lỗi xóa avatar cũ Cloudinary: ' . $e->getMessage());
                }
            }

            // Upload file mới
            try {
                $uploadResult = cloudinary()->uploadApi()->upload(
                    $file->getRealPath(),
                    ['folder' => 'ndhshop/users']
                );
                $user->avatar_url = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Lỗi up avatar mới: ' . $e->getMessage());
                throw \Illuminate\Validation\ValidationException::withMessages(['avatar_file' => 'Không thể tải ảnh hệ thống lúc này.']);
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::back()->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('app.home');
    }

    /**
     * API: Lấy danh sách đơn hàng của user.
     */
    public function orders(Request $request)
    {
        $userId = Auth::id();

        $query = \App\Models\Order::where('user_id', $userId)
            ->with(['items.product.assets' => function ($q) {
                $q->orderBy('sort_order', 'asc');
            }])
            ->latest();

        // Tìm kiếm theo mã đơn hàng hoặc tên sản phẩm
        if ($search = $request->input('search')) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(10);

        // Lấy tất cả reviews (kể cả đã xóa mềm) của user cho các đơn hàng hiện tại
        $orderIds = $orders->getCollection()->pluck('id')->toArray();
        $userReviews = \App\Models\Review::withDeleted()
            ->where('user_id', $userId)
            ->whereIn('order_id', $orderIds)
            ->get();

        $data = $orders->getCollection()->map(function ($order) use ($userReviews) {
            return [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'total_amount' => (float) $order->total_amount,
                'discount_amount' => (float) $order->discount_amount,
                'coupon_code' => $order->coupon_code,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'items' => $order->items->map(function ($item) use ($order, $userReviews) {
                    $imageAsset = $item->product->assets->where('type', 'image')->first();
                    $fileAsset = $item->product->assets->where('type', 'file')->first();

                    // Tìm review của user cho sản phẩm này trong đơn hàng này (kể cả đã xóa)
                    $review = $userReviews
                        ->where('product_id', $item->product_id)
                        ->where('order_id', $order->id)
                        ->first();

                    return [
                        'product_id' => $item->product_id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'price' => (float) $item->price,
                        'image' => $imageAsset ? $imageAsset->url_or_path : asset('images/placeholder.png'),
                        'download_url' => $fileAsset ? $fileAsset->url_or_path : null,
                        // Trạng thái review: null = chưa đánh giá, có data = đã đánh giá, is_deleted = đã xóa
                        'review' => $review ? [
                            'id' => $review->id,
                            'rating' => $review->rating,
                            'comment' => $review->comment,
                            'is_deleted' => (bool) $review->is_deleted,
                        ] : null,
                    ];
                }),
            ];
        });

        return response()->json([
            'data' => $data,
            'has_more' => $orders->hasMorePages(),
            'next_page' => $orders->currentPage() + 1,
        ]);
    }

    /**
     * API: Lấy danh sách lịch sử giao dịch (Audit Logs) của user.
     */
    public function history(Request $request)
    {
        $logs = \App\Models\AuditLog::where('user_id', Auth::id())
            ->whereIn('action', [
                'topup_bank_transfer', 
                'admin_update_balance', 
                'purchased_product_order', 
                'order_refund_balance', 
                'purchased_gift_template', 
                'upgraded_gift_premium'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $data = $logs->getCollection()->map(function ($log) {
            $title = match ($log->action) {
                'topup_bank_transfer' => 'Nạp tiền tự động (SePay)',
                'admin_update_balance' => 'Cập nhật số dư bởi Hệ thống',
                'purchased_product_order' => 'Mua Sản Phẩm',
                'order_refund_balance' => 'Hoàn Tiền (Hủy đơn)',
                'purchased_gift_template' => 'Thanh toán mẫu Website Quà Tặng',
                'upgraded_gift_premium' => 'Nâng cấp Premium (Website Quà Tặng)',
                default => 'Thao tác: ' . strtoupper($log->action),
            };

            $amountChange = null;
            $rawChange = 0;
            if (isset($log->old_values['balance']) && isset($log->new_values['balance'])) {
                $rawChange = $log->new_values['balance'] - $log->old_values['balance'];
                $amountChange = $rawChange > 0 ? '+' . number_format($rawChange, 0, ',', '.') . 'đ' : number_format($rawChange, 0, ',', '.') . 'đ';
            }

            $details = [];
            if (isset($log->new_values['order_code'])) $details[] = 'Mã đơn: ' . $log->new_values['order_code'];
            if (isset($log->new_values['transaction_no'])) $details[] = 'Mã GD: ' . $log->new_values['transaction_no'];
            if (isset($log->new_values['gateway'])) $details[] = 'Ngân hàng: ' . $log->new_values['gateway'];

            return [
                'id' => $log->id,
                'title' => $title,
                'action' => $log->action,
                'amount_change' => $amountChange,
                'amount_change_raw' => $rawChange,
                'details' => empty($details) ? null : implode(' | ', $details),
                'created_at' => $log->created_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'data' => $data,
            'has_more' => $logs->hasMorePages(),
            'next_page' => $logs->currentPage() + 1,
        ]);
    }

    /**
     * API: Lấy danh sách sản phẩm yêu thích của user.
     */
    public function favorites(Request $request)
    {
        // Lấy danh sách product_id đã mua thành công
        $purchasedProductIds = \App\Models\OrderItem::whereHas('order', function ($q) {
            $q->where('user_id', Auth::id())->where('status', 'completed');
        })->pluck('product_id')->unique()->toArray();

        $favorites = \App\Models\Wishlist::where('user_id', Auth::id())
            ->with(['product.assets' => function ($q) {
                $q->orderBy('sort_order', 'asc');
            }, 'product.category'])
            ->latest()
            ->paginate(10);

        $data = $favorites->getCollection()->map(function ($item) use ($purchasedProductIds) {
            $asset = $item->product->assets->where('type', 'image')->first();
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'slug' => $item->product->slug,
                'price' => (float) $item->product->price,
                'sale_price' => $item->product->sale_price ? (float) $item->product->sale_price : null,
                'platform' => $item->product->platform,
                'category' => $item->product->category->name ?? null,
                'image' => $asset ? $asset->url_or_path : asset('images/placeholder.png'),
                'added_at' => $item->created_at->format('d/m/Y'),
                'is_purchased' => in_array($item->product_id, $purchasedProductIds),
            ];
        });

        return response()->json([
            'data' => $data,
            'has_more' => $favorites->hasMorePages(),
            'next_page' => $favorites->currentPage() + 1,
        ]);
    }

    /**
     * API: Lấy danh sách thông báo của user đang đăng nhập
     * Bảo mật: chỉ lấy notification thuộc về user hiện tại (auth()->id())
     */
    public function notifications()
    {
        $userId = Auth::id();

        $notifications = \App\Models\Notification::where('user_id', $userId)
            ->where('scope', 'personal')
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($noti) {
                return [
                    'id' => $noti->id,
                    'title' => $noti->title,
                    'message' => $noti->message,
                    'type' => $noti->type,
                    'is_read' => $noti->is_read,
                    'action_url' => $noti->action_url,
                    'created_at' => $noti->created_at->diffForHumans(),
                ];
            });

        $unreadCount = \App\Models\Notification::where('user_id', $userId)
            ->where('scope', 'personal')
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * API: Đánh dấu tất cả thông báo đã đọc
     * Bảo mật: chỉ update những notification thuộc về user hiện tại
     */
    public function markAllRead()
    {
        \App\Models\Notification::where('user_id', Auth::id())
            ->where('scope', 'personal')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }
}

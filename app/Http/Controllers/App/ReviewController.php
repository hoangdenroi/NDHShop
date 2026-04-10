<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    /**
     * Tạo đánh giá mới cho sản phẩm trong đơn hàng.
     * Bảo mật: kiểm tra quyền sở hữu, trạng thái đơn hàng, và trùng lặp.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer',
            'product_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();

        // 1. Kiểm tra đơn hàng thuộc về user hiện tại
        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không tồn tại hoặc không thuộc về bạn.'
            ], 403);
        }

        // 2. Kiểm tra đơn hàng đã hoàn tất
        if ($order->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể đánh giá đơn hàng đã hoàn tất.'
            ], 422);
        }

        // 3. Kiểm tra sản phẩm nằm trong đơn hàng
        $hasProduct = OrderItem::where('order_id', $order->id)
            ->where('product_id', $validated['product_id'])
            ->exists();

        if (!$hasProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không nằm trong đơn hàng này.'
            ], 422);
        }

        // 4. Kiểm tra đã đánh giá trước đó (kể cả đã xóa mềm)
        $existingReview = Review::withDeleted()
            ->where('user_id', $userId)
            ->where('product_id', $validated['product_id'])
            ->where('order_id', $order->id)
            ->exists();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá sản phẩm này trong đơn hàng này rồi.'
            ], 422);
        }

        // 5. Tạo đánh giá — XSS: strip_tags cho comment
        $review = Review::create([
            'user_id' => $userId,
            'product_id' => $validated['product_id'],
            'order_id' => $order->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ? strip_tags($validated['comment']) : null,
        ]);

        // Xóa cache đánh giá của sản phẩm
        $this->clearReviewCache($validated['product_id']);

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá thành công!',
            'review' => [
                'rating' => $review->rating,
                'comment' => $review->comment,
            ],
        ]);
    }

    /**
     * Xóa đánh giá (soft delete). Chỉ owner mới có quyền xóa.
     * Sau khi xóa, nút đánh giá sẽ không hiển thị lại.
     */
    public function destroy($id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Đánh giá không tồn tại hoặc bạn không có quyền xóa.'
            ], 403);
        }

        $review->softDelete();

        // Xóa cache đánh giá của sản phẩm
        $this->clearReviewCache($review->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa đánh giá.',
        ]);
    }

    /**
     * Lấy danh sách đánh giá của sản phẩm (public API).
     * Phân trang 10 items, kèm thống kê tổng quan.
     */
    public function productReviews(Request $request, $productId)
    {
        // Kiểm tra sản phẩm tồn tại
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại.'], 404);
        }

        $page = (int) $request->input('page', 1);
        $cacheKey = "product_reviews:{$productId}:page:{$page}";

        // Cache 10 phút, tự động clear khi tạo/xóa review
        $result = Cache::remember($cacheKey, 600, function () use ($productId, $page) {
            // Lấy tất cả reviews (chưa xóa) để tính thống kê
            $allReviews = Review::where('product_id', $productId)->get();

            $total = $allReviews->count();
            $average = $total > 0 ? round($allReviews->avg('rating'), 1) : 0;

            // Phân phối số sao
            $distribution = [];
            for ($i = 5; $i >= 1; $i--) {
                $distribution[$i] = $allReviews->where('rating', $i)->count();
            }

            // Phân trang đánh giá
            $reviews = Review::where('product_id', $productId)
                ->with('user')
                ->latest()
                ->paginate(10, ['*'], 'page', $page);

            $data = $reviews->getCollection()->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'user_name' => $review->user->name ?? 'Ẩn danh',
                    'user_avatar' => $review->user->avatar_url ?? null,
                    'created_at' => $review->created_at->format('d/m/Y'),
                ];
            });

            return [
                'data' => $data,
                'has_more' => $reviews->hasMorePages(),
                'next_page' => $reviews->currentPage() + 1,
                'stats' => [
                    'total' => $total,
                    'average' => $average,
                    'distribution' => $distribution,
                ],
            ];
        });

        return response()->json($result);
    }

    /**
     * Xóa tất cả cache đánh giá của sản phẩm.
     * Dùng pattern key để clear toàn bộ pages.
     */
    private function clearReviewCache(int $productId): void
    {
        // Xóa cache các trang (giả định tối đa 50 trang)
        for ($i = 1; $i <= 50; $i++) {
            Cache::forget("product_reviews:{$productId}:page:{$i}");
        }
    }
}

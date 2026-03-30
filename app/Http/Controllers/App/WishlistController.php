<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Toggle product in wishlist
     */
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thêm vào yêu thích.',
                'require_login' => true
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            // Đã thích -> Bỏ thích
            $wishlistItem->delete();
            $isFavorited = false;
            $message = 'Đã bỏ yêu thích.';
        } else {
            // Chưa thích -> Thêm vào yêu thích
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $isFavorited = true;
            $message = 'Đã thêm vào mục yêu thích.';
        }

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'message' => $message,
            'count' => Wishlist::where('user_id', $user->id)->count(),
        ]);
    }

    /**
     * Display wishlist page
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $wishlists = Wishlist::where('user_id', Auth::id())
            ->with(['product.assets'])
            ->latest()
            ->paginate(15);
            
        // TODO: Tạo view resources/views/pages/app/wishlist.blade.php
        return view('pages.app.wishlist', compact('wishlists'));
    }
}

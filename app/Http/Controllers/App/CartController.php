<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Helper: Get or Create the current Cart
     */
    private function getCart()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return Cart::firstOrCreate(['user_id' => $user->id]);
        }

        // Guest session cart
        $cartId = session()->get('cart_id');
        if ($cartId) {
            $cart = Cart::find($cartId);
            if ($cart && is_null($cart->user_id)) {
                return $cart;
            }
        }

        // Create new guest cart
        $cart = Cart::create(['session_id' => session()->getId()]);
        session()->put('cart_id', $cart->id);

        return $cart;
    }

    /**
     * Add an item to the cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::where('is_deleted', false)->where('is_active', true)->findOrFail($request->product_id);
        $cart = $this->getCart();

        // Check if item already exists in cart. User wants strict limit: 1 quantity max.
        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm đã có trong giỏ hàng (giới hạn 1 sản phẩm).',
            ], 400);
        }

        // Add to cart
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->sale_price ?? $product->price,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm ' . $product->name . ' vào giỏ hàng.',
            'count' => $cart->items()->count(),
        ]);
    }

    /**
     * Remove an item from the cart
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = $this->getCart();
        $cart->items()->where('product_id', $request->product_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            'count' => $cart->items()->count(),
        ]);
    }

    /**
     * Get the cart items count (for UI badge updates)
     */
    public function count()
    {
        $cart = $this->getCart();
        $totalCount = $cart->items()->count();
        $totalPrice = $cart->items()->sum('price');

        // Chỉ lấy 5 sản phẩm mới nhất để show trên dropdown header, kèm ảnh đúng định dạng
        $items = $cart->items()->with(['product.assets' => function($q) {
            $q->where('type', 'image')->orderBy('sort_order', 'asc');
        }])->latest()->take(5)->get()->map(function($item) {
            $imageAsset = $item->product->assets->first();
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'price' => $item->price,
                'image' => $imageAsset ? $imageAsset->url_or_path : asset('images/placeholder.png')
            ];
        });

        return response()->json([
            'count' => $totalCount,
            'total' => $totalPrice,
            'items' => $items
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $cartId = $request->session()->get('cart_id');

        $request->authenticate();

        $request->session()->regenerate();

        // Đồng bộ giỏ hàng từ khách sang user
        if ($cartId) {
            $sessionCart = \App\Models\Cart::find($cartId);
            if ($sessionCart && is_null($sessionCart->user_id)) {
                $userCart = \App\Models\Cart::firstOrCreate(['user_id' => $request->user()->id]);
                
                // Gộp items
                foreach ($sessionCart->items as $item) {
                    $existing = $userCart->items()->where('product_id', $item->product_id)->first();
                    if (!$existing) {
                        $item->update(['cart_id' => $userCart->id]);
                    } else {
                        $item->delete(); // Ngăn trùng tự add số lượng vì giới hạn 1
                    }
                }
                
                $sessionCart->delete();
                $request->session()->forget('cart_id');
            }
        }

        $request->user()->update([
            'last_login_at' => now(),
        ]);

        // Phân luồng redirect theo role
        if ($request->user()->role === 'admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('app.profile', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

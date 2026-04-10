<?php

use App\Http\Controllers\Admin\categorys\CategoryController;
use App\Http\Controllers\App\AboutController;
use App\Http\Controllers\App\AllProductController;
use App\Http\Controllers\App\BlogController;
use App\Http\Controllers\App\ContactController;
use App\Http\Controllers\App\ProfileController;
use App\Http\Controllers\App\QrWebsiteController;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// web public
Route::get('/', function () {
    return view('pages.app.home');
})->name('app.home');

Route::prefix('apps')->group(function () {
    Route::get('/about', [AboutController::class, 'index'])->name('app.about');
    Route::get('/blog', [BlogController::class, 'index'])->name('app.blog');
    Route::get('/contact', [ContactController::class, 'index'])->name('app.contact');
    Route::get('/all-product', [AllProductController::class, 'index'])->name('app.all-product');
    Route::get('/qr-website', [QrWebsiteController::class, 'index'])->name('app.qr-website');
    // Gallery mẫu thiệp
    Route::get('/gift-templates', [\App\Http\Controllers\App\GiftController::class, 'index'])->name('app.gifts.templates');

    Route::get('/src-app-game', [\App\Http\Controllers\App\SrcAppGameController::class, 'index'])->name('app.src-app-game');

    // Chi tiết sản phẩm
    Route::get('/product/{slug}', [\App\Http\Controllers\App\ProductDetailController::class, 'show'])->name('app.product.detail');

    // Mua sắm (Giỏ hàng)
    Route::post('/cart/add', [\App\Http\Controllers\App\CartController::class, 'add'])->name('app.cart.add');
    Route::post('/cart/remove', [\App\Http\Controllers\App\CartController::class, 'remove'])->name('app.cart.remove');
    Route::get('/cart/count', [\App\Http\Controllers\App\CartController::class, 'count'])->name('app.cart.count');

    // VPS (public)
    Route::get('/vps', [\App\Http\Controllers\App\VpsController::class, 'index'])->name('app.vps');

    // Đánh giá sản phẩm (public: xem)
    Route::get('/api/v1/products/{product}/reviews', [\App\Http\Controllers\App\ReviewController::class, 'productReviews'])->name('api.product.reviews');

    // sale và khuyến mãi
    Route::get('/sale-shopee', [\App\Http\Controllers\App\SaleShopeeController::class, 'index'])->name('app.sale-shopee');
    Route::get('/sale-tiktok', [\App\Http\Controllers\App\SaleTiktokController::class, 'index'])->name('app.sale-tiktok');

});

// VPS (auth required) — đặt ngoài group trên để có middleware auth riêng
Route::prefix('apps/vps')->middleware('auth')->group(function () {
    Route::post('/{slug}/purchase', [\App\Http\Controllers\App\VpsController::class, 'purchase'])->name('app.vps.purchase');
    Route::get('/orders', [\App\Http\Controllers\App\VpsController::class, 'orders'])->name('app.vps.orders');
    Route::get('/orders/{order:order_code}', [\App\Http\Controllers\App\VpsController::class, 'orderDetail'])->name('app.vps.order-detail');
    Route::post('/orders/{order:order_code}/cancel', [\App\Http\Controllers\App\VpsController::class, 'cancelOrder'])->name('app.vps.cancel');
    Route::post('/orders/{order:order_code}/renew', [\App\Http\Controllers\App\VpsController::class, 'renewOrder'])->name('app.vps.renew');
    Route::post('/orders/{order:order_code}/reboot', [\App\Http\Controllers\App\VpsController::class, 'reboot'])->name('app.vps.reboot');
    Route::post('/orders/{order:order_code}/reset-password', [\App\Http\Controllers\App\VpsController::class, 'resetPassword'])->name('app.vps.reset-password');
    Route::post('/orders/{order:order_code}/rebuild', [\App\Http\Controllers\App\VpsController::class, 'rebuild'])->name('app.vps.rebuild');
});

// Route show VPS nằm dưới cùng để không bị đụng với các route static như /orders
Route::prefix('apps')->group(function () {
    Route::get('/vps/{slug}', [\App\Http\Controllers\App\VpsController::class, 'show'])->name('app.vps.show');
});

Route::get('/auth/google', [SocialiteController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback']);

Route::post('/api/v1/categories', [CategoryController::class, 'getCategories'])->name('api.v1.categories');

// web auth
Route::prefix('apps')->middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('pages.app.profile');
    })->name('app.profile');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('app.profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('app.profile.destroy');
    // API: Dữ liệu cho Profile Tabs
    Route::get('/api/v1/profile/orders', [\App\Http\Controllers\App\ProfileController::class, 'orders'])->name('api.profile.orders');
    Route::get('/api/v1/profile/favorites', [\App\Http\Controllers\App\ProfileController::class, 'favorites'])->name('api.profile.favorites');
    Route::get('/api/v1/profile/history', [\App\Http\Controllers\App\ProfileController::class, 'history'])->name('api.profile.history');
    Route::get('/api/v1/notifications', [\App\Http\Controllers\App\ProfileController::class, 'notifications'])->name('api.notifications');
    Route::post('/api/v1/notifications/read-all', [\App\Http\Controllers\App\ProfileController::class, 'markAllRead'])->name('api.notifications.read-all');

    // Đánh giá sản phẩm (auth: tạo, xóa)
    Route::post('/api/v1/reviews', [\App\Http\Controllers\App\ReviewController::class, 'store'])->name('api.reviews.store');
    Route::delete('/api/v1/reviews/{review}', [\App\Http\Controllers\App\ReviewController::class, 'destroy'])->name('api.reviews.destroy');

    // Thanh toán giỏ hàng
    Route::get('/checkout', [\App\Http\Controllers\App\CheckoutController::class, 'index'])->name('app.checkout');
    Route::post('/checkout', [\App\Http\Controllers\App\CheckoutController::class, 'process'])->name('app.checkout.process');
    Route::post('/checkout/apply-coupon', [\App\Http\Controllers\App\CheckoutController::class, 'applyCoupon'])->name('app.checkout.apply-coupon');
    Route::get('/checkout/success', [\App\Http\Controllers\App\CheckoutController::class, 'success'])->name('app.checkout.success');

    // Quà tặng của tôi (My Gifts)
    Route::get('/my-gifts', [\App\Http\Controllers\App\GiftController::class, 'myGifts'])->name('app.gifts.my-gifts');
    Route::get('/my-gifts/{gift:unitcode}/edit', [\App\Http\Controllers\App\GiftController::class, 'edit'])->name('app.gifts.edit');
    Route::put('/my-gifts/{gift:unitcode}', [\App\Http\Controllers\App\GiftController::class, 'update'])->name('app.gifts.update');
    Route::delete('/my-gifts/{gift:unitcode}', [\App\Http\Controllers\App\GiftController::class, 'destroy'])->name('app.gifts.destroy');

    // Tạo thiệp từ template (dùng slug để SEO-friendly)
    Route::get('/gift-templates/{template:slug}', [\App\Http\Controllers\App\GiftController::class, 'create'])->name('app.gifts.create');
    Route::post('/gift-templates/{template:slug}', [\App\Http\Controllers\App\GiftController::class, 'store'])->name('app.gifts.store');

    // Payment Flow: Chọn gói → Thanh toán → Thành công (dùng unitcode để không lộ ID)
    Route::get('/my-gifts/{gift:unitcode}/choose-plan', [\App\Http\Controllers\App\GiftController::class, 'choosePlan'])->name('app.gifts.choose-plan');
    Route::get('/my-gifts/{gift:unitcode}/payment/{plan}', [\App\Http\Controllers\App\GiftPaymentController::class, 'showPayment'])->name('app.gifts.payment');
    Route::post('/my-gifts/{gift:unitcode}/payment', [\App\Http\Controllers\App\GiftPaymentController::class, 'processPayment'])->name('app.gifts.process-payment');
    Route::get('/my-gifts/{gift:share_code}/success', [\App\Http\Controllers\App\GiftPaymentController::class, 'success'])->name('app.gifts.success');
    Route::post('/my-gifts/{gift:share_code}/upgrade', [\App\Http\Controllers\App\GiftPaymentController::class, 'upgradePlan'])->name('app.gifts.upgrade');

    // Yêu thích
    Route::post('/wishlist/toggle', [\App\Http\Controllers\App\WishlistController::class, 'toggle'])->name('app.wishlist.toggle');
    Route::get('/wishlist', [\App\Http\Controllers\App\WishlistController::class, 'index'])->name('app.wishlist.index');

    // Database
    Route::get('/database', [\App\Http\Controllers\App\DatabaseController::class, 'index'])->name('app.database');

    // Storage
    Route::get('/storage', [\App\Http\Controllers\App\StorageController::class, 'index'])->name('app.storage');

    // Cloud Plan — Nâng cấp / Gia hạn / Hạ gói
    Route::prefix('cloud-plan')->name('app.cloud-plan.')->group(function () {
        Route::get('/current', [\App\Http\Controllers\App\CloudPlanController::class, 'current'])->name('current');
        Route::get('/calculate-price', [\App\Http\Controllers\App\CloudPlanController::class, 'calculatePrice'])->name('calculate-price');
        Route::get('/refund-preview', [\App\Http\Controllers\App\CloudPlanController::class, 'refundPreview'])->name('refund-preview');
        Route::post('/upgrade', [\App\Http\Controllers\App\CloudPlanController::class, 'upgrade'])->name('upgrade');
        Route::post('/renew', [\App\Http\Controllers\App\CloudPlanController::class, 'renew'])->name('renew');
        Route::post('/downgrade', [\App\Http\Controllers\App\CloudPlanController::class, 'downgrade'])->name('downgrade');
        Route::post('/apply-coupon', [\App\Http\Controllers\App\CloudPlanController::class, 'applyCoupon'])->name('apply-coupon');
        Route::post('/create-database', [\App\Http\Controllers\App\CloudPlanController::class, 'createDatabase'])->name('create-database');
        Route::delete('/database/{cloudDatabase}', [\App\Http\Controllers\App\CloudPlanController::class, 'deleteDatabase'])->name('delete-database');
    });

});

// api web auth (gọi từ Blade bằng AJAX, dùng session cookie để xác thực)
Route::middleware('auth')->group(function () {

    Route::post('/api/v1/settings/theme', function (Request $request) {
        $data = $request->validate([
            'mode' => 'required|string',
            'primaryColor' => 'required|string',
        ]);

        $request->user()->update(['theme' => $data]);

        return response()->json(['success' => true]);
    })->name('api.v1.settings.theme');

    Route::post('/api/v1/settings/notification', function (Request $request) {
        $data = $request->validate([
            'push' => 'required|boolean',
            'email' => 'required|boolean',
        ]);

        $request->user()->update(['notification' => $data]);

        return response()->json(['success' => true]);
    })->name('api.v1.settings.notification');

    Route::post('/api/v1/settings/language', function (Request $request) {
        $data = $request->validate([
            'lang' => 'required|string|max:10',
        ]);

        $request->user()->update(['language' => $data['lang']]);

        return response()->json(['success' => true]);
    })->name('api.v1.settings.language');

    // Upload ảnh lên Cloudinary
    Route::post('/api/v1/upload-image', [\App\Http\Controllers\App\ImageUploadController::class, 'upload'])->name('api.v1.upload-image');
});

require __DIR__.'/auth.php';

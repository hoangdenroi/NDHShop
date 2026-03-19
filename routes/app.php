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

    // Quà tặng của tôi (My Gifts)
    Route::get('/my-gifts', [\App\Http\Controllers\App\GiftController::class, 'myGifts'])->name('app.gifts.my-gifts');
    Route::get('/my-gifts/{gift:share_code}/edit', [\App\Http\Controllers\App\GiftController::class, 'edit'])->name('app.gifts.edit');
    Route::put('/my-gifts/{gift:share_code}', [\App\Http\Controllers\App\GiftController::class, 'update'])->name('app.gifts.update');
    Route::delete('/my-gifts/{gift:share_code}', [\App\Http\Controllers\App\GiftController::class, 'destroy'])->name('app.gifts.destroy');

    // Tạo thiệp từ template (dùng slug để SEO-friendly)
    Route::get('/gift-templates/{template:slug}', [\App\Http\Controllers\App\GiftController::class, 'create'])->name('app.gifts.create');
    Route::post('/gift-templates/{template:slug}', [\App\Http\Controllers\App\GiftController::class, 'store'])->name('app.gifts.store');

    // Payment Flow: Chọn gói → Thanh toán → Thành công (dùng unitcode để không lộ ID)
    Route::get('/my-gifts/{gift:unitcode}/choose-plan', [\App\Http\Controllers\App\GiftController::class, 'choosePlan'])->name('app.gifts.choose-plan');
    Route::get('/my-gifts/{gift:unitcode}/payment/{plan}', [\App\Http\Controllers\App\GiftPaymentController::class, 'showPayment'])->name('app.gifts.payment');
    Route::post('/my-gifts/{gift:unitcode}/payment', [\App\Http\Controllers\App\GiftPaymentController::class, 'processPayment'])->name('app.gifts.process-payment');
    Route::get('/my-gifts/{gift:share_code}/success', [\App\Http\Controllers\App\GiftPaymentController::class, 'success'])->name('app.gifts.success');
    Route::post('/my-gifts/{gift:share_code}/upgrade', [\App\Http\Controllers\App\GiftPaymentController::class, 'upgradePlan'])->name('app.gifts.upgrade');
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
});

require __DIR__.'/auth.php';

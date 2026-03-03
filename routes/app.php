<?php

use App\Http\Controllers\App\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.app.home');
})->name('app.home');

Route::prefix('apps')->middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('pages.app.profile');
    })->name('app.profile');
    
    Route::patch('/profile', [ProfileController::class, 'update'])->name('app.profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('app.profile.destroy');
});

Route::middleware('auth')->post('/api/settings/theme', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'mode' => 'required|string',
        'primaryColor' => 'required|string'
    ]);

    \App\Models\Setting::setForUser(auth()->id(), 'theme', $data, 'Cấu hình giao diện người dùng');

    return response()->json(['success' => true]);
})->name('api.settings.theme');

// API call (Tạo mã QR nạp tiền) - Dùng middleware auth của Web thay vì Sanctum
Route::middleware('auth')->post('/api/v1/topup/qrcode', [\App\Http\Controllers\Api\TopupController::class, 'getPaymentQr']);

// api hook từ sepay
Route::post('/api/v1/topup/sepay-hook', [\App\Http\Controllers\Api\TopupController::class, 'sepayHook']);

require __DIR__.'/auth.php';


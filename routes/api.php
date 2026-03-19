<?php

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TopupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:web');

// api auth
Route::middleware('auth:web')->group(function () {
    Route::post('/v1/topup/qrcode', [TopupController::class, 'getPaymentQr']);
    Route::get('/v1/topup/history', [TopupController::class, 'history']);

    // API Server-Sent Events cho Notification & Real-time Update
    Route::get('/v1/user/notifications/pull', [NotificationController::class, 'pull'])->name('api.v1.notifications.pull');
});

// api public
Route::post('/v1/topup/sepay-hook', [TopupController::class, 'sepayHook']);

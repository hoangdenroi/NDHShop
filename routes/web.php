<?php

use App\Http\Controllers\App\GiftRenderController;
use App\Http\Middleware\CheckGiftAccess;
use Illuminate\Support\Facades\Route;

// Route public — Bản xem thử trước khi lưu (nhận POST hoặc PUT từ form)
Route::match(['post', 'put'], '/gifts/preview/{template:slug}', [GiftRenderController::class, 'preview'])
    ->name('app.gifts.preview');

// Route public — Demo mẫu (render template với data mặc định)
Route::get('/gifts/demo/{template:slug}', [GiftRenderController::class, 'demo'])
    ->name('app.gifts.demo');

// Route public — Render gift page cho người nhận (có middleware kiểm tra truy cập)
Route::get('/gifts/{share_code}', [GiftRenderController::class, 'show'])
    ->middleware(CheckGiftAccess::class)
    ->name('app.gifts.render');

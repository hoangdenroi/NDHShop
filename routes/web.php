<?php

use App\Http\Controllers\App\GiftRenderController;
use App\Http\Middleware\CheckGiftAccess;
use Illuminate\Support\Facades\Route;

// Route public cho Gift Render Engine (có middleware kiểm tra truy cập)
// Link người nhận thiệp: /g/aB3xK9mZ
Route::get('/g/{share_code}', [GiftRenderController::class, 'show'])
    ->middleware(CheckGiftAccess::class)
    ->name('app.gifts.render');

<?php

use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Các route dành cho admin, yêu cầu đăng nhập và có role 'admin'.
| Prefix: /admin (đã cấu hình trong bootstrap/app.php)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    // Trang Dashboard
    Route::get('/dashboard', function () {
        return view('pages.admin.dashboard');
    })->name('admin.dashboard');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::patch('/users/{user}/delete', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Categories
    Route::get('/categories', [\App\Http\Controllers\Admin\Categorys\CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [\App\Http\Controllers\Admin\Categorys\CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [\App\Http\Controllers\Admin\Categorys\CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\Categorys\CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // Products
    Route::get('/products', [\App\Http\Controllers\Admin\products\ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/products', [\App\Http\Controllers\Admin\products\ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/products/{product}', [\App\Http\Controllers\Admin\products\ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [\App\Http\Controllers\Admin\products\ProductController::class, 'destroy'])->name('admin.products.destroy');

    // Post Categories
    Route::get('/post-categories', [\App\Http\Controllers\Admin\post_categories\PostCategoryController::class, 'index'])->name('admin.post-categories.index');
    Route::post('/post-categories', [\App\Http\Controllers\Admin\post_categories\PostCategoryController::class, 'store'])->name('admin.post-categories.store');
    Route::put('/post-categories/{category}', [\App\Http\Controllers\Admin\post_categories\PostCategoryController::class, 'update'])->name('admin.post-categories.update');
    Route::delete('/post-categories/{category}', [\App\Http\Controllers\Admin\post_categories\PostCategoryController::class, 'destroy'])->name('admin.post-categories.destroy');

    // Blogs Posts
    Route::get('/blogs-posts', [\App\Http\Controllers\Admin\blogs_posts\BlogPostController::class, 'index'])->name('admin.blogs-posts.index');
    Route::post('/blogs-posts', [\App\Http\Controllers\Admin\blogs_posts\BlogPostController::class, 'store'])->name('admin.blogs-posts.store');
    Route::put('/blogs-posts/{post}', [\App\Http\Controllers\Admin\blogs_posts\BlogPostController::class, 'update'])->name('admin.blogs-posts.update');
    Route::delete('/blogs-posts/{post}', [\App\Http\Controllers\Admin\blogs_posts\BlogPostController::class, 'destroy'])->name('admin.blogs-posts.destroy');

    // Gift Templates
    Route::get('/gift-templates', [\App\Http\Controllers\Admin\gift_templates\GiftTemplateController::class, 'index'])->name('admin.gift-templates.index');
    Route::post('/gift-templates', [\App\Http\Controllers\Admin\gift_templates\GiftTemplateController::class, 'store'])->name('admin.gift-templates.store');
    Route::put('/gift-templates/{template}', [\App\Http\Controllers\Admin\gift_templates\GiftTemplateController::class, 'update'])->name('admin.gift-templates.update');
    Route::delete('/gift-templates/{template}', [\App\Http\Controllers\Admin\gift_templates\GiftTemplateController::class, 'destroy'])->name('admin.gift-templates.destroy');
});

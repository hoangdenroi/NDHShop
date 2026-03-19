<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Hiển thị trang danh sách bài viết
     */
    public function index()
    {
        return view('pages.app.blog-post');
    }
}

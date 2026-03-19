<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;

class QrWebsiteController extends Controller
{
    /**
     * Hiển thị trang Liên hệ
     */
    public function index()
    {
        return view('pages.app.qr-website');
    }
}

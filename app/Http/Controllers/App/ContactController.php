<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Hiển thị trang Liên hệ
     */
    public function index()
    {
        return view('pages.app.contact');
    }
}

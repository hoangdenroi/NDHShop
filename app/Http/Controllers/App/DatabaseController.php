<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    /**
     * Hiển thị trang Giới thiệu
     */
    public function index()
    {
        return view('pages.app.database');
    }
}

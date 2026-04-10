<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SaleTiktokController extends Controller
{
    public function index()
    {
        return view('pages.app.sale-tiktok');
    }
}
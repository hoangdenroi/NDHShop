<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SaleShopeeController extends Controller
{
    public function index()
    {
        return view('pages.app.sale-shopee');
    }
}
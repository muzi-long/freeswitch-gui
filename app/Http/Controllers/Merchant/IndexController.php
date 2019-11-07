<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{
    //后台布局
    public function layout()
    {
        return View::make('merchant.layout');
    }

    public function index()
    {
        return View::make('merchant.index.index');
    }
}

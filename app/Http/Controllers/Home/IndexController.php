<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{
    //后台布局
    public function layout()
    {
        return View::make('home.layout');
    }

    public function index()
    {
        return View::make('home.index.index');
    }

    public function onlinecall()
    {
        return View::make('home.index.onlinecall');
    }

}

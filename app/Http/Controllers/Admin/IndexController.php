<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class IndexController extends Controller
{
    //后台布局
    public function layout()
    {
        return View::make('admin.layout');
    }

    public function index()
    {
        return View::make('admin.index.index');
    }
}

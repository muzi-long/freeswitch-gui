<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{

    public function index()
    {
        return View::make("layout");
    }

    public function console()
    {
        return View::make("index.console");
    }

}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class StaffController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){

        }
        return View::make('backend.platform.staff');
    }

    public function create()
    {
        return View::make('backend.platform.staff.create');
    }

}

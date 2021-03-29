<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Sip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AudioController extends Controller
{

    public function index(Request $request)
    {
        $data = Sip::with('user')->get();
        return View::make('chat.audio.index',compact('data'));
    }

}

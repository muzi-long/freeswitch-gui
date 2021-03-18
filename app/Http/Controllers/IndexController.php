<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{

    public function index(Request $request)
    {

        $user = $request->user();
        $data = [
            'sip_id' => $user->sip->id ?? 0,
            'username' => $user->sip->username ?? null,
            'password' => $user->sip->password ?? null,
            'host' => config('freeswitch.host'),
            'uri' => $user->sip->username . '@' . config('freeswitch.host'),
            'wss_url' => config('freeswitch.wss_url'),
        ];
        return View::make("layout", compact('data'));
    }

    public function console()
    {
        return View::make("index.console");
    }

    public function onlinecall()
    {
        return View::make('index.onlinecall');
    }

}

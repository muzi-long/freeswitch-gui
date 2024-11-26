<?php

namespace App\Listeners;

use App\Events\UserLogined;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Session;

class Menu
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserLogined  $event
     * @return void
     */
    public function handle(UserLogined $event)
    {
        $user = $event->user;
        $menus = $user->menus();
        Session::put('menus',$menus);
    }
}

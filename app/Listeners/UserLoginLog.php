<?php

namespace App\Listeners;

use App\Events\UserLogined;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserLoginLog
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
        $user->update([
            'last_login_ip' => request()->ip(),
            'last_login_time' => date("Y-m-d H:i:s"),
        ]);
    }
}

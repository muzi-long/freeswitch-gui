<?php

namespace App\Providers;

use App\Models\Sip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        //前台用户的分机
        view()->composer('admin.base',function ($view){
            $sip = Sip::where('id',Auth::user()->sip_id)->first();
            $view->with('exten',$sip->username??null);
        });
    }
}

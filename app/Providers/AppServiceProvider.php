<?php

namespace App\Providers;

use App\Models\Sip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        //左侧菜单
        view()->composer('admin.layout',function($view){
            $menus = \App\Models\Permission::with('allChilds')->where('parent_id',0)->orderBy('sort','desc')->get();
            $view->with('menus',$menus);
        });
        //前台用户的分机
        view()->composer('home.base',function ($view){
            $sip = Sip::where('id',Auth::guard('merchant')->user()->sip_id)->first();
            $view->with('exten',$sip->username??'');
        });
    }
}

<?php

/*
|--------------------------------------------------------------------------
| 商户 用户登录、退出、更改密码
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Merchant','prefix'=>'merchant/user'],function (){
    //登录
    Route::get('login','UserController@showLoginForm')->name('merchant.user.loginForm');
    Route::post('login','UserController@login')->name('merchant.user.login');
    //退出
    Route::get('logout','UserController@logout')->name('merchant.user.logout');
    //更改密码
    Route::get('change_my_password_form','UserController@changeMyPasswordForm')->name('merchant.user.changeMyPasswordForm');
    Route::post('change_my_password','UserController@changeMyPassword')->name('merchant.user.changeMyPassword');
});

/*
|--------------------------------------------------------------------------
| 后台公共页面
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Merchant','prefix'=>'merchant','middleware'=>'merchant'],function (){
    //后台布局
    Route::get('/','IndexController@layout')->name('merchant.layout');
    //后台首页
    Route::get('/index','IndexController@index')->name('merchant.index');
});


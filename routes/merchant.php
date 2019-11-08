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

/*
|--------------------------------------------------------------------------
| 系统管理模块
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Merchant','prefix'=>'merchant','middleware'=>'merchant'],function (){

    //会员管理
    Route::group(['middleware'=>['permission:merchant.member']],function (){
        Route::get('member','MermberController@index')->name('merchant.member');
        Route::get('member/data','MermberController@data')->name('merchant.member.data');
        //添加
        Route::get('member/create','MermberController@create')->name('merchant.member.create')->middleware('permission:merchant.member.create');
        Route::post('member/store','MermberController@store')->name('merchant.member.store')->middleware('permission:merchant.member.create');
        //编辑
        Route::get('member/{id}/edit','MermberController@edit')->name('merchant.member.edit')->middleware('permission:merchant.member.edit');
        Route::put('member/{id}/update','MermberController@update')->name('merchant.member.update')->middleware('permission:merchant.member.edit');
        //删除
        Route::delete('member/destroy','MermberController@destroy')->name('merchant.member.destroy')->middleware('permission:merchant.member.destroy');
        //分配角色
        Route::get('member/{id}/role','MermberController@role')->name('merchant.user.role')->middleware('permission:merchant.member.role');
        Route::put('member/{id}/assignRole','MermberController@assignRole')->name('merchant.member.assignRole')->middleware('permission:merchant.member.role');

    });


});


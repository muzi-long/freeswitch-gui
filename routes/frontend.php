<?php
/*
|--------------------------------------------------------------------------
| 前台用户登录、退出、更改密码
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Frontend','prefix'=>'system/staff'],function (){
    //登录
    Route::get('login','StaffController@showLoginForm')->name('frontend.system.staff.loginForm');
    Route::post('login','StaffController@login')->name('frontend.system.staff.login');
    //退出
    Route::get('logout','StaffController@logout')->name('frontend.system.staff.logout')->middleware('auth:frontend');
    //更改密码
    Route::get('change_my_password_form','StaffController@changeMyPasswordForm')->name('frontend.system.staff.changeMyPasswordForm')->middleware('auth:frontend');
    Route::post('change_my_password','StaffController@changeMyPassword')->name('frontend.system.staff.changeMyPassword')->middleware('auth:frontend');
});

/*
|--------------------------------------------------------------------------
| 前台公共页面
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Frontend','middleware'=>'auth:frontend'],function (){
    //前台布局
    Route::get('/','IndexController@layout')->name('frontend.layout');
    //前台首页
    Route::get('/index','IndexController@index')->name('frontend.index');

});

/*
|--------------------------------------------------------------------------
| 呼叫中心
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Frontend','prefix'=>'call','middleware'=>['auth:frontend']],function (){

    //分机管理
    Route::group([],function (){
        Route::get('sip','SipController@index')->name('frontend.call.sip')->middleware('permission:frontend.call.sip');
        //解绑
        Route::post('sip/unbind','SipController@unbind')->name('frontend.call.sip.unbind')->middleware('permission:frontend.call.sip.unbind');
        //绑定
        Route::get('sip/{id}/bindForm','SipController@bindForm')->name('frontend.call.sip.bindForm')->middleware('permission:frontend.call.sip.bind');
        Route::post('sip/bind','SipController@bind')->name('frontend.call.sip.bind')->middleware('permission:frontend.call.sip.bind');
    });
    //我的分机
    Route::group([],function (){
        Route::match(['get','post'],'sip/mine','SipController@mine')->name('frontend.call.sip.mine')->middleware('permission:frontend.call.sip.mine');
    });
    //通话记录
    Route::group([],function (){
        Route::get('cdr','CdrController@index')->name('frontend.call.cdr')->middleware('permission:frontend.call.cdr');
    });
});



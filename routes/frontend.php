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
| 系统管理模块
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Backend','prefix'=>'system','middleware'=>['auth:backend']],function (){

    //用户管理
    Route::group([],function (){
        Route::get('admin','AdminController@index')->name('backend.system.admin')->middleware('permission:backend.system.admin');
        //添加
        Route::get('admin/create','AdminController@create')->name('backend.system.admin.create')->middleware('permission:backend.system.admin.create');
        Route::post('admin/store','AdminController@store')->name('backend.system.admin.store')->middleware('permission:backend.system.admin.create');
        //编辑
        Route::get('admin/{id}/edit','AdminController@edit')->name('backend.system.admin.edit')->middleware('permission:backend.system.admin.edit');
        Route::put('admin/{id}/update','AdminController@update')->name('backend.system.admin.update')->middleware('permission:backend.system.admin.edit');
        //重置密码
        Route::get('admin/{id}/resetPassword','AdminController@resetPasswordForm')->name('backend.system.admin.resetPasswordForm')->middleware('permission:backend.system.admin.resetPassword');
        Route::put('admin/{id}/resetPassword','AdminController@resetPassword')->name('backend.system.admin.resetPassword')->middleware('permission:backend.system.admin.resetPassword');
        //删除
        Route::delete('admin/destroy','AdminController@destroy')->name('backend.system.admin.destroy')->middleware('permission:backend.system.admin.destroy');
        //分配角色
        Route::get('admin/{id}/role','AdminController@role')->name('backend.system.admin.role')->middleware('permission:backend.system.admin.role');
        Route::put('admin/{id}/assignRole','AdminController@assignRole')->name('backend.system.admin.assignRole')->middleware('permission:backend.system.admin.role');
        //分配权限
        Route::get('admin/{id}/permission','AdminController@permission')->name('backend.system.admin.permission')->middleware('permission:backend.system.admin.permission');
        Route::put('admin/{id}/assignPermission','AdminController@assignPermission')->name('backend.system.admin.assignPermission')->middleware('permission:backend.system.admin.permission');
    });

});



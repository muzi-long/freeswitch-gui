<?php

/*
|--------------------------------------------------------------------------
| 用户登录、退出
|--------------------------------------------------------------------------
*/
//登录
Route::get('login','AuthController@showLoginForm')->name('auth.loginForm');
Route::post('login','AuthController@login')->name('auth.login');
//退出
Route::get('logout','AuthController@logout')->name('auth.logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| 后台公共页面
|--------------------------------------------------------------------------
*/
Route::group(['middleware'=>'auth'],function (){
    //后台首页
    Route::get('/','IndexController@index')->name('index.index');
    //后台控制台
    Route::get('/console','IndexController@console')->name('index.console');
    //修改密码
    Route::get('/change_my_password_form','UserController@changeMyPassword')->name('index.changeMyPasswordForm')->middleware('auth');
    Route::post('/change_my_password','UserController@changeMyPassword')->name('index.changeMyPassword')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| 系统管理模块
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'system','middleware'=>['auth','permission:system']],function (){

    //权限管理
    Route::group([],function (){
        Route::get('permission','PermissionController@index')->name('system.permission')->middleware('permission:system.permission');
        //添加
        Route::get('permission/create','PermissionController@create')->name('system.permission.create')->middleware('permission:system.permission.create');
        Route::post('permission/store','PermissionController@store')->name('system.permission.store')->middleware('permission:system.permission.create');
        //编辑
        Route::get('permission/{id}/edit','PermissionController@edit')->name('system.permission.edit')->middleware('permission:system.permission.edit');
        Route::put('permission/{id}/update','PermissionController@update')->name('system.permission.update')->middleware('permission:system.permission.edit');
        //删除
        Route::delete('permission/destroy','PermissionController@destroy')->name('system.permission.destroy')->middleware('permission:system.permission.destroy');
    });

    //角色管理
    Route::group([],function (){
        Route::get('role','RoleController@index')->name('system.role')->middleware('permission:system.role');
        //添加
        Route::get('role/create','RoleController@create')->name('system.role.create')->middleware('permission:system.role.create');
        Route::post('role/store','RoleController@store')->name('system.role.store')->middleware('permission:system.role.create');
        //编辑
        Route::get('role/{id}/edit','RoleController@edit')->name('system.role.edit')->middleware('permission:system.role.edit');
        Route::put('role/{id}/update','RoleController@update')->name('system.role.update')->middleware('permission:system.role.edit');
        //删除
        Route::delete('role/destroy','RoleController@destroy')->name('system.role.destroy')->middleware('permission:system.role.destroy');

    });

    //用户管理
    Route::group([],function (){
        Route::get('user','UserController@index')->name('system.user')->middleware('permission:system.user');
        //添加
        Route::get('user/create','UserController@create')->name('system.user.create')->middleware('permission:system.user.create');
        Route::post('user/store','UserController@store')->name('system.user.store')->middleware('permission:system.user.create');
        //编辑
        Route::get('user/{id}/edit','UserController@edit')->name('system.user.edit')->middleware('permission:system.user.edit');
        Route::put('user/{id}/update','UserController@update')->name('system.user.update')->middleware('permission:system.user.edit');
        //启用/禁用
        Route::post('user/status','UserController@status')->name('system.user.status')->middleware('permission:system.user.status');
        //删除
        Route::delete('user/destroy','UserController@destroy')->name('system.user.destroy')->middleware('permission:system.user.destroy');
    });

    //菜单管理
    Route::group([],function (){
        Route::get('menu','MenuController@index')->name('system.menu')->middleware('permission:system.menu');
        //添加
        Route::get('menu/create','MenuController@create')->name('system.menu.create')->middleware('permission:system.menu.create');
        Route::post('menu/store','MenuController@store')->name('system.menu.store')->middleware('permission:system.menu.create');
        //编辑
        Route::get('menu/{id}/edit','MenuController@edit')->name('system.menu.edit')->middleware('permission:system.permission.edit');
        Route::put('menu/{id}/update','MenuController@update')->name('system.menu.update')->middleware('permission:system.permission.edit');
        //删除
        Route::delete('menu/destroy','MenuController@destroy')->name('system.menu.destroy')->middleware('permission:system.permission.destroy');
    });

});

<?php
/*
|--------------------------------------------------------------------------
| 用户登录、退出、更改密码
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Backend','prefix'=>'backend/admin'],function (){
    //登录
    Route::get('login','AdminController@showLoginForm')->name('backend.admin.loginForm');
    Route::post('login','AdminController@login')->name('backend.admin.login');
    //退出
    Route::get('logout','AdminController@logout')->name('backend.admin.logout')->middleware('auth:backend');
    //更改密码
    Route::get('change_my_password_form','AdminController@changeMyPasswordForm')->name('backend.admin.changeMyPasswordForm')->middleware('auth:backend');
    Route::post('change_my_password','AdminController@changeMyPassword')->name('backend.admin.changeMyPassword')->middleware('auth:backend');
});

/*
|--------------------------------------------------------------------------
| 后台公共页面
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Backend','prefix'=>'backend','middleware'=>'auth:backend'],function (){
    //后台布局
    Route::get('/','IndexController@layout')->name('backend.layout');
    //后台首页
    Route::get('/index','IndexController@index')->name('backend.index');
    //后台首页图表统计
    Route::post('/index/chart','IndexController@chart')->name('backend.index.chart');
});

/*
|--------------------------------------------------------------------------
| 系统管理模块
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth','permission:system']],function (){

    //用户管理
    Route::group([],function (){
        Route::get('user','UserController@index')->name('admin.user')->middleware('permission:system.user');
        //添加
        Route::get('user/create','UserController@create')->name('admin.user.create')->middleware('permission:system.user.create');
        Route::post('user/store','UserController@store')->name('admin.user.store')->middleware('permission:system.user.create');
        //编辑
        Route::get('user/{id}/edit','UserController@edit')->name('admin.user.edit')->middleware('permission:system.user.edit');
        Route::put('user/{id}/update','UserController@update')->name('admin.user.update')->middleware('permission:system.user.edit');
        //重置密码
        Route::get('user/{id}/resetPassword','UserController@resetPasswordForm')->name('admin.user.resetPasswordForm')->middleware('permission:system.user.resetPassword');
        Route::put('user/{id}/resetPassword','UserController@resetPassword')->name('admin.user.resetPassword')->middleware('permission:system.user.resetPassword');
        //删除
        Route::delete('user/destroy','UserController@destroy')->name('admin.user.destroy')->middleware('permission:system.user.destroy');
        //分配角色
        Route::get('user/{id}/role','UserController@role')->name('admin.user.role')->middleware('permission:system.user.role');
        Route::put('user/{id}/assignRole','UserController@assignRole')->name('admin.user.assignRole')->middleware('permission:system.user.role');
        //分配权限
        Route::get('user/{id}/permission','UserController@permission')->name('admin.user.permission')->middleware('permission:system.user.permission');
        Route::put('user/{id}/assignPermission','UserController@assignPermission')->name('admin.user.assignPermission')->middleware('permission:system.user.permission');
    });

    //角色管理
    Route::group([],function (){
        Route::get('role','RoleController@index')->name('admin.role')->middleware('permission:system.role');
        //添加
        Route::get('role/create','RoleController@create')->name('admin.role.create')->middleware('permission:system.role.create');
        Route::post('role/store','RoleController@store')->name('admin.role.store')->middleware('permission:system.role.create');
        //编辑
        Route::get('role/{id}/edit','RoleController@edit')->name('admin.role.edit')->middleware('permission:system.role.edit');
        Route::put('role/{id}/update','RoleController@update')->name('admin.role.update')->middleware('permission:system.role.edit');
        //删除
        Route::delete('role/destroy','RoleController@destroy')->name('admin.role.destroy')->middleware('permission:system.role.destroy');
        //分配权限
        Route::get('role/{id}/permission','RoleController@permission')->name('admin.role.permission')->middleware('permission:system.role.permission');
        Route::put('role/{id}/assignPermission','RoleController@assignPermission')->name('admin.role.assignPermission')->middleware('permission:system.role.permission');
    });

    //权限管理
    Route::group([],function (){
        Route::get('permission','PermissionController@index')->name('admin.permission')->middleware('permission:system.permission');
        //添加
        Route::get('permission/create','PermissionController@create')->name('admin.permission.create')->middleware('permission:system.permission.create');
        Route::post('permission/store','PermissionController@store')->name('admin.permission.store')->middleware('permission:system.permission.create');
        //编辑
        Route::get('permission/{id}/edit','PermissionController@edit')->name('admin.permission.edit')->middleware('permission:system.permission.edit');
        Route::put('permission/{id}/update','PermissionController@update')->name('admin.permission.update')->middleware('permission:system.permission.edit');
        //删除
        Route::delete('permission/destroy','PermissionController@destroy')->name('admin.permission.destroy')->middleware('permission:system.permission.destroy');
    });

    //菜单管理
    Route::group([],function (){
        Route::get('menu','MenuController@index')->name('admin.menu')->middleware('permission:system.menu');
        //添加
        Route::get('menu/create','MenuController@create')->name('admin.menu.create')->middleware('permission:system.menu.create');
        Route::post('menu/store','MenuController@store')->name('admin.menu.store')->middleware('permission:system.menu.create');
        //编辑
        Route::get('menu/{id}/edit','MenuController@edit')->name('admin.menu.edit')->middleware('permission:system.permission.edit');
        Route::put('menu/{id}/update','MenuController@update')->name('admin.menu.update')->middleware('permission:system.permission.edit');
        //删除
        Route::delete('menu/destroy','MenuController@destroy')->name('admin.menu.destroy')->middleware('permission:system.permission.destroy');
    });

});


<?php
/*
|--------------------------------------------------------------------------
| 用户登录、退出、更改密码
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin/user'],function (){
    //登录
    Route::get('login','UserController@showLoginForm')->name('admin.user.loginForm');
    Route::post('login','UserController@login')->name('admin.user.login');
    //退出
    Route::get('logout','UserController@logout')->name('admin.user.logout')->middleware('auth');
    //更改密码
    Route::get('change_my_password_form','UserController@changeMyPasswordForm')->name('admin.user.changeMyPasswordForm')->middleware('auth');
    Route::post('change_my_password','UserController@changeMyPassword')->name('admin.user.changeMyPassword')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| 后台公共页面
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>'auth'],function (){
    //后台布局
    Route::get('/','IndexController@layout')->name('admin.layout');
    //后台首页
    Route::get('/index','IndexController@index')->name('admin.index');
});

/*
|--------------------------------------------------------------------------
| 系统管理模块
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth']],function (){

    //用户管理
    Route::group([],function (){
        Route::get('user','UserController@index')->name('admin.user');
        //添加
        Route::get('user/create','UserController@create')->name('admin.user.create');
        Route::post('user/store','UserController@store')->name('admin.user.store');
        //编辑
        Route::get('user/{id}/edit','UserController@edit')->name('admin.user.edit');
        Route::put('user/{id}/update','UserController@update')->name('admin.user.update');
        //重置密码
        Route::get('user/{id}/resetPassword','UserController@resetPasswordForm')->name('admin.user.resetPasswordForm');
        Route::put('user/{id}/resetPassword','UserController@resetPassword')->name('admin.user.resetPassword');
        //删除
        Route::delete('user/destroy','UserController@destroy')->name('admin.user.destroy');
        //分配角色
        Route::get('user/{id}/role','UserController@role')->name('admin.user.role');
        Route::put('user/{id}/assignRole','UserController@assignRole')->name('admin.user.assignRole');
        //分配权限
        Route::get('user/{id}/permission','UserController@permission')->name('admin.user.permission');
        Route::put('user/{id}/assignPermission','UserController@assignPermission')->name('admin.user.assignPermission');
    });

    //角色管理
    Route::group([],function (){
        Route::get('role','RoleController@index')->name('admin.role');
        //添加
        Route::get('role/create','RoleController@create')->name('admin.role.create');
        Route::post('role/store','RoleController@store')->name('admin.role.store');
        //编辑
        Route::get('role/{id}/edit','RoleController@edit')->name('admin.role.edit');
        Route::put('role/{id}/update','RoleController@update')->name('admin.role.update');
        //删除
        Route::delete('role/destroy','RoleController@destroy')->name('admin.role.destroy');
        //分配权限
        Route::get('role/{id}/permission','RoleController@permission')->name('admin.role.permission');
        Route::put('role/{id}/assignPermission','RoleController@assignPermission')->name('admin.role.assignPermission');
    });

    //权限管理
    Route::group([],function (){
        Route::get('permission','PermissionController@index')->name('admin.permission');
        //添加
        Route::get('permission/create','PermissionController@create')->name('admin.permission.create');
        Route::post('permission/store','PermissionController@store')->name('admin.permission.store');
        //编辑
        Route::get('permission/{id}/edit','PermissionController@edit')->name('admin.permission.edit');
        Route::put('permission/{id}/update','PermissionController@update')->name('admin.permission.update');
        //删除
        Route::delete('permission/destroy','PermissionController@destroy')->name('admin.permission.destroy');
    });

    //菜单管理
    Route::group([],function (){
        Route::get('menu','MenuController@index')->name('admin.menu');
        //添加
        Route::get('menu/create','MenuController@create')->name('admin.menu.create');
        Route::post('menu/store','MenuController@store')->name('admin.menu.store');
        //编辑
        Route::get('menu/{id}/edit','MenuController@edit')->name('admin.menu.edit');
        Route::put('menu/{id}/update','MenuController@update')->name('admin.menu.update');
        //删除
        Route::delete('menu/destroy','MenuController@destroy')->name('admin.menu.destroy');
    });


});

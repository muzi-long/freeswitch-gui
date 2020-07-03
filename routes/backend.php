<?php
/*
|--------------------------------------------------------------------------
| 用户登录、退出、更改密码
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Backend','prefix'=>'system/admin'],function (){
    //登录
    Route::get('login','AdminController@showLoginForm')->name('backend.system.admin.loginForm');
    Route::post('login','AdminController@login')->name('backend.system.admin.login');
    //退出
    Route::get('logout','AdminController@logout')->name('backend.system.admin.logout')->middleware('auth:backend');
    //更改密码
    Route::get('change_my_password_form','AdminController@changeMyPasswordForm')->name('backend.system.admin.changeMyPasswordForm')->middleware('auth:backend');
    Route::post('change_my_password','AdminController@changeMyPassword')->name('backend.system.admin.changeMyPassword')->middleware('auth:backend');
});

/*
|--------------------------------------------------------------------------
| 后台公共页面
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Backend','middleware'=>'auth:backend'],function (){
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

    //角色管理
    Route::group([],function (){
        Route::get('role','RoleController@index')->name('backend.system.role')->middleware('permission:backend.system.role');
        //添加
        Route::get('role/create','RoleController@create')->name('backend.system.role.create')->middleware('permission:backend.system.role.create');
        Route::post('role/store','RoleController@store')->name('backend.system.role.store')->middleware('permission:backend.system.role.create');
        //编辑
        Route::get('role/{id}/edit','RoleController@edit')->name('backend.system.role.edit')->middleware('permission:backend.system.role.edit');
        Route::put('role/{id}/update','RoleController@update')->name('backend.system.role.update')->middleware('permission:backend.system.role.edit');
        //删除
        Route::delete('role/destroy','RoleController@destroy')->name('backend.system.role.destroy')->middleware('permission:backend.system.role.destroy');
        //分配权限
        Route::get('role/{id}/permission','RoleController@permission')->name('backend.system.role.permission')->middleware('permission:backend.system.role.permission');
        Route::put('role/{id}/assignPermission','RoleController@assignPermission')->name('backend.system.role.assignPermission')->middleware('permission:backend.system.role.permission');
    });

    //权限管理
    Route::group([],function (){
        Route::get('permission','PermissionController@index')->name('backend.system.permission')->middleware('permission:backend.system.permission');
        //添加
        Route::get('permission/create','PermissionController@create')->name('backend.system.permission.create')->middleware('permission:backend.system.permission.create');
        Route::post('permission/store','PermissionController@store')->name('backend.system.permission.store')->middleware('permission:backend.system.permission.create');
        //编辑
        Route::get('permission/{id}/edit','PermissionController@edit')->name('backend.system.permission.edit')->middleware('permission:backend.system.permission.edit');
        Route::put('permission/{id}/update','PermissionController@update')->name('backend.system.permission.update')->middleware('permission:backend.system.permission.edit');
        //删除
        Route::delete('permission/destroy','PermissionController@destroy')->name('backend.system.permission.destroy')->middleware('permission:backend.system.permission.destroy');
    });

    //菜单管理
    Route::group([],function (){
        Route::get('menu','MenuController@index')->name('backend.system.menu')->middleware('permission:backend.system.menu');
        //添加
        Route::get('menu/create','MenuController@create')->name('backend.system.menu.create')->middleware('permission:backend.system.menu.create');
        Route::post('menu/store','MenuController@store')->name('backend.system.menu.store')->middleware('permission:backend.system.menu.create');
        //编辑
        Route::get('menu/{id}/edit','MenuController@edit')->name('backend.system.menu.edit')->middleware('permission:backend.system.permission.edit');
        Route::put('menu/{id}/update','MenuController@update')->name('backend.system.menu.update')->middleware('permission:backend.system.permission.edit');
        //删除
        Route::delete('menu/destroy','MenuController@destroy')->name('backend.system.menu.destroy')->middleware('permission:backend.system.permission.destroy');
    });

});


/*
|--------------------------------------------------------------------------
| 呼叫中心管理模块
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Backend','prefix'=>'call','middleware'=>['auth:backend']],function (){

    //FS管理
    Route::group([],function (){
        Route::get('freeswitch','FreeswitchController@index')->name('backend.call.freeswitch')->middleware('permission:backend.call.freeswitch');
        //添加
        Route::get('freeswitch/create','FreeswitchController@create')->name('backend.call.freeswitch.create')->middleware('permission:backend.call.freeswitch.create');
        Route::post('freeswitch/store','FreeswitchController@store')->name('backend.call.freeswitch.store')->middleware('permission:backend.call.freeswitch.create');
        //编辑
        Route::get('freeswitch/{id}/edit','FreeswitchController@edit')->name('backend.call.freeswitch.edit')->middleware('permission:backend.call.freeswitch.edit');
        Route::put('freeswitch/{id}/update','FreeswitchController@update')->name('backend.call.freeswitch.update')->middleware('permission:backend.call.freeswitch.edit');
        //删除
        Route::delete('freeswitch/destroy','FreeswitchController@destroy')->name('backend.call.freeswitch.destroy')->middleware('permission:backend.call.freeswitch.destroy');
    });

    //拨号计划
    Route::group([],function (){
        Route::get('extension','ExtensionController@index')->name('backend.call.extension')->middleware('permission:backend.call.extension');
        //详情
        Route::get('extension/{id}/show','ExtensionController@show')->name('backend.call.extension.show')->middleware('permission:backend.call.extension.show');
        //添加
        Route::get('extension/create','ExtensionController@create')->name('backend.call.extension.create')->middleware('permission:backend.call.extension.create');
        Route::post('extension/store','ExtensionController@store')->name('backend.call.extension.store')->middleware('permission:backend.call.extension.create');
        //编辑
        Route::get('extension/{id}/edit','ExtensionController@edit')->name('backend.call.extension.edit')->middleware('permission:backend.call.extension.edit');
        Route::put('extension/{id}/update','ExtensionController@update')->name('backend.call.extension.update')->middleware('permission:backend.call.extension.edit');
        //删除
        Route::delete('extension/destroy','ExtensionController@destroy')->name('backend.call.extension.destroy')->middleware('permission:backend.call.extension.destroy');
        //更新配置
        Route::post('extension/updateXml','ExtensionController@updateXml')->name('backend.call.extension.updateXml')->middleware('permission:backend.call.extension.updateXml');
    });
    //拨号规则，同属拨号计划权限
    Route::group([],function (){
        Route::get('extension/{extension_id}/condition','ConditionController@index')->name('backend.call.condition');
        //添加
        Route::get('extension/{extension_id}/condition/create','ConditionController@create')->name('backend.call.condition.create');
        Route::post('extension/{extension_id}/condition/store','ConditionController@store')->name('backend.call.condition.store');
        //编辑
        Route::get('extension/{extension_id}/condition/{id}/edit','ConditionController@edit')->name('backend.call.condition.edit');
        Route::put('extension/{extension_id}/condition/{id}/update','ConditionController@update')->name('backend.call.condition.update');
        //删除
        Route::delete('condition/destroy','ConditionController@destroy')->name('backend.call.condition.destroy');
    });
    //拨号应用，同属拨号计划权限
    Route::group([],function (){
        Route::get('condition/{condition_id}/action','ActionController@index')->name('backend.call.action');
        Route::get('condition/{condition_id}/action/data','ActionController@data')->name('backend.call.action.data');
        //添加
        Route::get('condition/{condition_id}/action/create','ActionController@create')->name('backend.call.action.create');
        Route::post('condition/{condition_id}/action/store','ActionController@store')->name('backend.call.action.store');
        //编辑
        Route::get('condition/{condition_id}/action/{id}/edit','ActionController@edit')->name('backend.call.action.edit');
        Route::put('condition/{condition_id}/action/{id}/update','ActionController@update')->name('backend.call.action.update');
        //删除
        Route::delete('action/destroy','ActionController@destroy')->name('backend.call.action.destroy');
    });

});

/*
|--------------------------------------------------------------------------
| 平台管理模块
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Backend','prefix'=>'platform','middleware'=>['auth:backend']],function (){

    //商户管理
    Route::group([],function (){
        Route::get('merchant','MerchantController@index')->name('backend.platform.merchant')->middleware('permission:backend.platform.merchant');
        //添加
        Route::get('merchant/create','MerchantController@create')->name('backend.platform.merchant.create')->middleware('permission:backend.platform.merchant.create');
        Route::post('merchant/store','MerchantController@store')->name('backend.platform.merchant.store')->middleware('permission:backend.platform.merchant.create');
        //编辑
        Route::get('merchant/{id}/edit','MerchantController@edit')->name('backend.platform.merchant.edit')->middleware('permission:backend.platform.merchant.edit');
        Route::put('merchant/{id}/update','MerchantController@update')->name('backend.platform.merchant.update')->middleware('permission:backend.platform.merchant.edit');
        //删除
        Route::delete('merchant/destroy','MerchantController@destroy')->name('backend.platform.merchant.destroy')->middleware('permission:backend.platform.merchant.destroy');
    });


});
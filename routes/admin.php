<?php
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 后台公共路由部分
|
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin'],function (){
    //登录、注销
    Route::get('login','LoginController@showLoginForm')->name('admin.loginForm');
    Route::post('login','LoginController@login')->name('admin.login');
    Route::get('logout','LoginController@logout')->name('admin.logout');

});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 后台需要授权的路由 admins
|
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>'auth'],function (){
    //后台布局
    Route::get('/','IndexController@layout')->name('admin.layout');
    //后台首页
    Route::get('/index','IndexController@index')->name('admin.index');
    Route::get('/index1','IndexController@index1')->name('admin.index1');
    Route::get('/index2','IndexController@index2')->name('admin.index2');
    //拨打电话
    Route::get('/call','IndexController@call')->name('admin.call');
    //图标
    Route::get('icons','IndexController@icons')->name('admin.icons');
});

//系统管理
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth','permission:system.manage']],function (){
    //数据表格接口
    Route::get('data','IndexController@data')->name('admin.data')->middleware('permission:system.role|system.user|system.permission');
    //用户管理
    Route::group(['middleware'=>['permission:system.user']],function (){
        Route::get('user','UserController@index')->name('admin.user');
        //添加
        Route::get('user/create','UserController@create')->name('admin.user.create')->middleware('permission:system.user.create');
        Route::post('user/store','UserController@store')->name('admin.user.store')->middleware('permission:system.user.create');
        //编辑
        Route::get('user/{id}/edit','UserController@edit')->name('admin.user.edit')->middleware('permission:system.user.edit');
        Route::put('user/{id}/update','UserController@update')->name('admin.user.update')->middleware('permission:system.user.edit');
        //删除
        Route::delete('user/destroy','UserController@destroy')->name('admin.user.destroy')->middleware('permission:system.user.destroy');
        //分配角色
        Route::get('user/{id}/role','UserController@role')->name('admin.user.role')->middleware('permission:system.user.role');
        Route::put('user/{id}/assignRole','UserController@assignRole')->name('admin.user.assignRole')->middleware('permission:system.user.role');
        //分配权限
        Route::get('user/{id}/permission','UserController@permission')->name('admin.user.permission')->middleware('permission:system.user.permission');
        Route::put('user/{id}/assignPermission','UserController@assignPermission')->name('admin.user.assignPermission')->middleware('permission:system.user.permission');
        //分配外呼号
        Route::post('user/{id}/set_sip','UserController@setSip')->name('admin.user.setSip')->middleware('permission:system.user.setSip');
    });
    //角色管理
    Route::group(['middleware'=>'permission:system.role'],function (){
        Route::get('role','RoleController@index')->name('admin.role');
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
    Route::group(['middleware'=>'permission:system.permission'],function (){
        Route::get('permission','PermissionController@index')->name('admin.permission');
        //添加
        Route::get('permission/create','PermissionController@create')->name('admin.permission.create')->middleware('permission:system.permission.create');
        Route::post('permission/store','PermissionController@store')->name('admin.permission.store')->middleware('permission:system.permission.create');
        //编辑
        Route::get('permission/{id}/edit','PermissionController@edit')->name('admin.permission.edit')->middleware('permission:system.permission.edit');
        Route::put('permission/{id}/update','PermissionController@update')->name('admin.permission.update')->middleware('permission:system.permission.edit');
        //删除
        Route::delete('permission/destroy','PermissionController@destroy')->name('admin.permission.destroy')->middleware('permission:system.permission.destroy');
    });
    //配置管理
    Route::group(['middleware'=>'permission:system.config'],function (){
        Route::get('config','ConfigController@index')->name('admin.config');
        Route::get('config/data','ConfigController@data')->name('admin.config.data');
        //添加
        Route::get('config/create','ConfigController@create')->name('admin.config.create')->middleware('permission:system.config.create');
        Route::post('config/store','ConfigController@store')->name('admin.config.store')->middleware('permission:system.config.create');
        //编辑
        Route::get('config/{id}/edit','ConfigController@edit')->name('admin.config.edit')->middleware('permission:system.config.edit');
        Route::put('config/{id}/update','ConfigController@update')->name('admin.config.update')->middleware('permission:system.config.edit');
        //删除
        Route::delete('config/destroy','ConfigController@destroy')->name('admin.config.destroy')->middleware('permission:system.config.destroy');
    });
});

//PBX配置管理
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth','permission:pbx.manage']],function (){
    //分机管理
    Route::group(['middleware'=>'permission:pbx.sip'],function (){
        Route::get('sip','SipController@index')->name('admin.sip');
        Route::get('sip/data','SipController@data')->name('admin.sip.data');
        //添加
        Route::get('sip/create','SipController@create')->name('admin.sip.create')->middleware('permission:pbx.sip.create');
        Route::post('sip/store','SipController@store')->name('admin.sip.store')->middleware('permission:pbx.sip.create');
        //批量添加
        Route::get('sip/create_list','SipController@createList')->name('admin.sip.create_list')->middleware('permission:pbx.sip.create_list');
        Route::post('sip/store_list','SipController@storeList')->name('admin.sip.store_list')->middleware('permission:pbx.sip.create_list');
        //编辑
        Route::get('sip/{id}/edit','SipController@edit')->name('admin.sip.edit')->middleware('permission:pbx.sip.edit');
        Route::put('sip/{id}/update','SipController@update')->name('admin.sip.update')->middleware('permission:pbx.sip.edit');
        //删除
        Route::delete('sip/destroy','SipController@destroy')->name('admin.sip.destroy')->middleware('permission:pbx.sip.destroy');
    });
    //分机组管理
    Route::group(['middleware'=>'permission:pbx.group'],function (){
        Route::get('group','GroupController@index')->name('admin.group');
        Route::get('group/data','GroupController@data')->name('admin.group.data');
        //添加
        Route::get('group/create','GroupController@create')->name('admin.group.create')->middleware('permission:pbx.group.create');
        Route::post('group/store','GroupController@store')->name('admin.group.store')->middleware('permission:pbx.group.create');
        //添加
        Route::get('group/create','GroupController@create')->name('admin.group.create')->middleware('permission:pbx.group.create');
        Route::post('group/store','GroupController@store')->name('admin.group.store')->middleware('permission:pbx.group.create');
        //编辑
        Route::get('group/{id}/edit','GroupController@edit')->name('admin.group.edit')->middleware('permission:pbx.group.edit');
        Route::put('group/{id}/update','GroupController@update')->name('admin.group.update')->middleware('permission:pbx.group.edit');
        //删除
        Route::delete('group/destroy','GroupController@destroy')->name('admin.group.destroy')->middleware('permission:pbx.group.destroy');
        //分配分机
        Route::get('group/{id}/sip','GroupController@sip')->name('admin.group.sip')->middleware('permission:pbx.group.sip');
        Route::put('group/{id}/assignSip','GroupController@assignSip')->name('admin.group.assignSip')->middleware('permission:pbx.group.sip');
    });
    //网关管理
    Route::group(['middleware'=>'permission:pbx.gateway'],function (){
        Route::get('gateway','GatewayController@index')->name('admin.gateway');
        Route::get('gateway/data','GatewayController@data')->name('admin.gateway.data');
        //添加
        Route::get('gateway/create','GatewayController@create')->name('admin.gateway.create')->middleware('permission:pbx.gateway.create');
        Route::post('gateway/store','GatewayController@store')->name('admin.gateway.store')->middleware('permission:pbx.gateway.create');
        //编辑
        Route::get('gateway/{id}/edit','GatewayController@edit')->name('admin.gateway.edit')->middleware('permission:pbx.gateway.edit');
        Route::put('gateway/{id}/update','GatewayController@update')->name('admin.gateway.update')->middleware('permission:pbx.gateway.edit');
        //删除
        Route::delete('gateway/destroy','GatewayController@destroy')->name('admin.gateway.destroy')->middleware('permission:pbx.gateway.destroy');
        //更新配置
        Route::post('gateway/updateXml','GatewayController@updateXml')->name('admin.gateway.updateXml')->middleware('permission:pbx.gateway.updateXml');
    });
    //拨号计划
    Route::group(['middleware'=>'permission:pbx.extension'],function (){
        Route::get('extension','ExtensionController@index')->name('admin.extension');
        Route::get('extension/data','ExtensionController@data')->name('admin.extension.data');
        //详情
        Route::get('extension/{id}/show','ExtensionController@show')->name('admin.extension.show')->middleware('permission:pbx.extension.show');
        //添加
        Route::get('extension/create','ExtensionController@create')->name('admin.extension.create')->middleware('permission:pbx.extension.create');
        Route::post('extension/store','ExtensionController@store')->name('admin.extension.store')->middleware('permission:pbx.extension.create');
        //编辑
        Route::get('extension/{id}/edit','ExtensionController@edit')->name('admin.extension.edit')->middleware('permission:pbx.extension.edit');
        Route::put('extension/{id}/update','ExtensionController@update')->name('admin.extension.update')->middleware('permission:pbx.extension.edit');
        //删除
        Route::delete('extension/destroy','ExtensionController@destroy')->name('admin.extension.destroy')->middleware('permission:pbx.extension.destroy');
    });
    //拨号规则，同属拨号计划权限
    Route::group(['middleware'=>'permission:pbx.extension'],function (){
        Route::get('extension/{extension_id}/condition','ConditionController@index')->name('admin.condition');
        Route::get('extension/{extension_id}/condition/data','ConditionController@data')->name('admin.condition.data');
        //添加
        Route::get('extension/{extension_id}/condition/create','ConditionController@create')->name('admin.condition.create');
        Route::post('extension/{extension_id}/condition/store','ConditionController@store')->name('admin.condition.store');
        //编辑
        Route::get('extension/{extension_id}/condition/{id}/edit','ConditionController@edit')->name('admin.condition.edit');
        Route::put('extension/{extension_id}/condition/{id}/update','ConditionController@update')->name('admin.condition.update');
        //删除
        Route::delete('condition/destroy','ConditionController@destroy')->name('admin.condition.destroy');
    });
    //拨号应用，同属拨号计划权限
    Route::group(['middleware'=>'permission:pbx.extension'],function (){
        Route::get('condition/{condition_id}/action','ActionController@index')->name('admin.action');
        Route::get('condition/{condition_id}/action/data','ActionController@data')->name('admin.action.data');
        //添加
        Route::get('condition/{condition_id}/action/create','ActionController@create')->name('admin.action.create');
        Route::post('condition/{condition_id}/action/store','ActionController@store')->name('admin.action.store');
        //编辑
        Route::get('condition/{condition_id}/action/{id}/edit','ActionController@edit')->name('admin.action.edit');
        Route::put('condition/{condition_id}/action/{id}/update','ActionController@update')->name('admin.action.update');
        //删除
        Route::delete('action/destroy','ActionController@destroy')->name('admin.action.destroy');
    });
    //队列管理
    Route::group(['middleware'=>'permission:pbx.queue'],function (){
        Route::get('queue','QueueController@index')->name('admin.queue');
        Route::get('queue/data','QueueController@data')->name('admin.queue.data');
        //添加
        Route::get('queue/create','QueueController@create')->name('admin.queue.create')->middleware('permission:pbx.queue.create');
        Route::post('queue/store','QueueController@store')->name('admin.queue.store')->middleware('permission:pbx.queue.create');
        //编辑
        Route::get('queue/{id}/edit','QueueController@edit')->name('admin.queue.edit')->middleware('permission:pbx.queue.edit');
        Route::put('queue/{id}/update','QueueController@update')->name('admin.queue.update')->middleware('permission:pbx.queue.edit');
        //删除
        Route::delete('queue/destroy','QueueController@destroy')->name('admin.queue.destroy')->middleware('permission:pbx.queue.destroy');
        //更新配置
        Route::post('queue/updateXml','QueueController@updateXml')->name('admin.queue.updateXml')->middleware('permission:pbx.queue.updateXml');
        //分配坐席
        Route::get('queue/{id}/agent','QueueController@agent')->name('admin.queue.agent')->middleware('permission:pbx.queue.agent');
        Route::put('queue/{id}/assignAgent','QueueController@assignAgent')->name('admin.queue.assignAgent')->middleware('permission:pbx.queue.agent');

    });

});

//录音管理
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth','permission:record.manage']],function (){

    //CDR录音
    Route::group(['middleware'=>'permission:record.cdr'],function (){
        Route::get('cdr','CdrController@index')->name('admin.cdr');
        Route::get('cdr/data','CdrController@data')->name('admin.cdr.data');
        //播放
        Route::get('cdr/{uuid}/play','CdrController@play')->name('admin.cdr.play');
        //下载
        Route::get('cdr/{uuid}/download','CdrController@download')->name('admin.cdr.download');
        //通话详单
        Route::get('cdr/{id}/show','CdrController@show')->name('admin.cdr.show');
    });
});

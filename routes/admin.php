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

/*
|--------------------------------------------------------------------------
| 服务配置
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth','permission:fs']],function (){
    //分机管理
    Route::group([],function (){
        Route::get('sip','SipController@index')->name('admin.sip')->middleware('permission:fs.sip');
        //添加
        Route::get('sip/create','SipController@create')->name('admin.sip.create')->middleware('permission:fs.sip.create');
        Route::post('sip/store','SipController@store')->name('admin.sip.store')->middleware('permission:fs.sip.create');
        //批量添加
        Route::get('sip/create_list','SipController@createList')->name('admin.sip.create_list')->middleware('permission:fs.sip.create_list');
        Route::post('sip/store_list','SipController@storeList')->name('admin.sip.store_list')->middleware('permission:fs.sip.create_list');
        //编辑
        Route::get('sip/{id}/edit','SipController@edit')->name('admin.sip.edit')->middleware('permission:fs.sip.edit');
        Route::put('sip/{id}/update','SipController@update')->name('admin.sip.update')->middleware('permission:fs.sip.edit');
        //删除
        Route::delete('sip/destroy','SipController@destroy')->name('admin.sip.destroy')->middleware('permission:fs.sip.destroy');
        //更新配置
        Route::post('sip/updateXml','SipController@updateXml')->name('admin.sip.updateXml')->middleware('permission:fs.sip.updateXml');
        //切换网关
        Route::get('sip/updateGatewayForm','SipController@updateGatewayForm')->name('admin.sip.updateGatewayForm')->middleware('permission:fs.sip.updateGateway');
        Route::post('sip/updateGateway','SipController@updateGateway')->name('admin.sip.updateGateway')->middleware('permission:fs.sip.updateGateway');
    });
    //网关管理
    Route::group([],function (){
        Route::get('gateway','GatewayController@index')->name('admin.gateway')->middleware('permission:fs.gateway');
        //添加
        Route::get('gateway/create','GatewayController@create')->name('admin.gateway.create')->middleware('permission:fs.gateway.create');
        Route::post('gateway/store','GatewayController@store')->name('admin.gateway.store')->middleware('permission:fs.gateway.create');
        //编辑
        Route::get('gateway/{id}/edit','GatewayController@edit')->name('admin.gateway.edit')->middleware('permission:fs.gateway.edit');
        Route::put('gateway/{id}/update','GatewayController@update')->name('admin.gateway.update')->middleware('permission:fs.gateway.edit');
        //删除
        Route::delete('gateway/destroy','GatewayController@destroy')->name('admin.gateway.destroy')->middleware('permission:fs.gateway.destroy');
        //更新配置
        Route::post('gateway/updateXml','GatewayController@updateXml')->name('admin.gateway.updateXml')->middleware('permission:fs.gateway.updateXml');
    });
    //网关号码管理
    Route::group([],function (){
        Route::get('gateway_outbound','GatewayOutboundController@index')->name('admin.gateway_outbound')->middleware('permission:fs.gateway_outbound');
        //添加
        Route::get('gateway_outbound/create','GatewayOutboundController@create')->name('admin.gateway_outbound.create')->middleware('permission:fs.gateway_outbound.create');
        Route::post('gateway_outbound/store','GatewayOutboundController@store')->name('admin.gateway_outbound.store')->middleware('permission:fs.gateway_outbound.create');
        //编辑
        Route::get('gateway_outbound/{id}/edit','GatewayOutboundController@edit')->name('admin.gateway_outbound.edit')->middleware('permission:fs.gateway_outbound.edit');
        Route::put('gateway_outbound/{id}/update','GatewayOutboundController@update')->name('admin.gateway_outbound.update')->middleware('permission:fs.gateway_outbound.edit');
        //删除
        Route::delete('gateway_outbound/destroy','GatewayOutboundController@destroy')->name('admin.gateway_outbound.destroy')->middleware('permission:fs.gateway_outbound.destroy');
        //导入
        Route::get('gateway_outbound/importForm','GatewayOutboundController@importForm')->name('admin.gateway_outbound.importForm')->middleware('permission:fs.gateway_outbound.import');
        Route::post('gateway_outbound/import','GatewayOutboundController@import')->name('admin.gateway_outbound.import')->middleware('permission:fs.gateway_outbound.import');
    });
    //拨号计划
    Route::group([],function (){
        Route::get('extension','ExtensionController@index')->name('admin.extension')->middleware('permission:fs.extension');
        //详情
        Route::get('extension/{id}/show','ExtensionController@show')->name('admin.extension.show')->middleware('permission:fs.extension.show');
        //添加
        Route::get('extension/create','ExtensionController@create')->name('admin.extension.create')->middleware('permission:fs.extension.create');
        Route::post('extension/store','ExtensionController@store')->name('admin.extension.store')->middleware('permission:fs.extension.create');
        //编辑
        Route::get('extension/{id}/edit','ExtensionController@edit')->name('admin.extension.edit')->middleware('permission:fs.extension.edit');
        Route::put('extension/{id}/update','ExtensionController@update')->name('admin.extension.update')->middleware('permission:fs.extension.edit');
        //删除
        Route::delete('extension/destroy','ExtensionController@destroy')->name('admin.extension.destroy')->middleware('permission:fs.extension.destroy');
        //更新配置
        Route::post('extension/updateXml','ExtensionController@updateXml')->name('admin.extension.updateXml')->middleware('permission:fs.extension.updateXml');
    });
    //拨号规则，同属拨号计划权限
    Route::group([],function (){
        Route::get('extension/{extension_id}/condition','ConditionController@index')->name('admin.condition');
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
    Route::group([],function (){
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
});

/*
|--------------------------------------------------------------------------
| 批量外呼
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth','permission:callcenter']],function (){
    //队列管理
    Route::group([],function (){
        Route::get('queue','QueueController@index')->name('admin.queue')->middleware('permission:callcenter.queue');
        //添加
        Route::get('queue/create','QueueController@create')->name('admin.queue.create')->middleware('permission:callcenter.queue.create');
        Route::post('queue/store','QueueController@store')->name('admin.queue.store')->middleware('permission:callcenter.queue.create');
        //编辑
        Route::get('queue/{id}/edit','QueueController@edit')->name('admin.queue.edit')->middleware('permission:callcenter.queue.edit');
        Route::put('queue/{id}/update','QueueController@update')->name('admin.queue.update')->middleware('permission:callcenter.queue.edit');
        //删除
        Route::delete('queue/destroy','QueueController@destroy')->name('admin.queue.destroy')->middleware('permission:callcenter.queue.destroy');
        //更新配置
        Route::post('queue/updateXml','QueueController@updateXml')->name('admin.queue.updateXml')->middleware('permission:callcenter.queue.updateXml');
        //分配坐席
        Route::get('queue/{id}/agent','QueueController@agent')->name('admin.queue.agent')->middleware('permission:callcenter.queue.agent');
        Route::put('queue/{id}/assignAgent','QueueController@assignAgent')->name('admin.queue.assignAgent')->middleware('permission:callcenter.queue.agent');

    });
    //坐席管理
    Route::group([],function (){
        Route::get('agent','AgentController@index')->name('admin.agent')->middleware('permission:callcenter.agent');
        //添加
        Route::get('agent/create','AgentController@create')->name('admin.agent.create')->middleware('permission:callcenter.agent.create');
        Route::post('agent/store','AgentController@store')->name('admin.agent.store')->middleware('permission:callcenter.agent.create');
        //编辑
        Route::get('agent/{id}/edit','AgentController@edit')->name('admin.agent.edit')->middleware('permission:callcenter.agent.edit');
        Route::put('agent/{id}/update','AgentController@update')->name('admin.agent.update')->middleware('permission:callcenter.agent.edit');
        //删除
        Route::delete('agent/destroy','AgentController@destroy')->name('admin.agent.destroy')->middleware('permission:callcenter.agent.destroy');
        //签入、签出
        Route::post('agent/check','AgentController@check')->name('admin.agent.check')->middleware('permission:callcenter.agent.check');

    });
    //任务管理
    Route::group([],function (){
        Route::get('task','TaskController@index')->name('admin.task')->middleware('permission:callcenter.task');
        //详情
        Route::match(['get','post'],'task/{id}/show','TaskController@show')->name('admin.task.show')->middleware('permission:callcenter.task.show');
        //添加
        Route::get('task/create','TaskController@create')->name('admin.task.create')->middleware('permission:callcenter.task.create');
        Route::post('task/store','TaskController@store')->name('admin.task.store')->middleware('permission:callcenter.task.create');
        //编辑
        Route::get('task/{id}/edit','TaskController@edit')->name('admin.task.edit')->middleware('permission:callcenter.task.edit');
        Route::put('task/{id}/update','TaskController@update')->name('admin.task.update')->middleware('permission:callcenter.task.edit');
        //删除
        Route::delete('task/destroy','TaskController@destroy')->name('admin.task.destroy')->middleware('permission:callcenter.task.destroy');
        //设置状态
        Route::post('task/setStatus','TaskController@setStatus')->name('admin.task.setStatus')->middleware('permission:ai.task.setStatus');
        //导入号码
        Route::post('task/{id}/importCall','TaskController@importCall')->name('admin.task.importCall')->middleware('permission:callcenter.task.importCall');
    });

});

/*
|--------------------------------------------------------------------------
| CRM管理
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth','permission:crm']],function (){
    //节点管理
    Route::group([],function (){
        Route::get('node','NodeController@index')->name('admin.node')->middleware('permission:crm.node');
        //添加
        Route::get('node/create','NodeController@create')->name('admin.node.create')->middleware('permission:crm.node.create');
        Route::post('node/store','NodeController@store')->name('admin.node.store')->middleware('permission:crm.node.create');
        //编辑
        Route::get('node/{id}/edit','NodeController@edit')->name('admin.node.edit')->middleware('permission:crm.node.edit');
        Route::put('node/{id}/update','NodeController@update')->name('admin.node.update')->middleware('permission:crm.node.edit');
        //删除
        Route::delete('node/destroy','NodeController@destroy')->name('admin.node.destroy')->middleware('permission:crm.node.destroy');

    });

    //部门管理
    Route::group([],function (){
        Route::get('department','DepartmentController@index')->name('admin.department')->middleware('permission:crm.department');
        //添加
        Route::get('department/create','DepartmentController@create')->name('admin.department.create')->middleware('permission:crm.department.create');
        Route::post('department/store','DepartmentController@store')->name('admin.department.store')->middleware('permission:crm.department.create');
        //编辑
        Route::get('department/{id}/edit','DepartmentController@edit')->name('admin.department.edit')->middleware('permission:crm.department.edit');
        Route::put('department/{id}/update','DepartmentController@update')->name('admin.department.update')->middleware('permission:crm.department.edit');
        //删除
        Route::delete('department/destroy','DepartmentController@destroy')->name('admin.department.destroy')->middleware('permission:crm.department.destroy');

    });


});
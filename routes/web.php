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
    Route::get('/onlinecall','IndexController@onlinecall')->name('index.onlinecall');
    //修改密码
    Route::get('/change_my_password_form','System\UserController@changeMyPassword')->name('index.changeMyPasswordForm')->middleware('auth');
    Route::post('/change_my_password','System\UserController@changeMyPassword')->name('index.changeMyPassword')->middleware('auth');

    Route::post('cdr_count','IndexController@cdrCount')->name('index.cdr_count');
    Route::post('customer_count','IndexController@customerCount')->name('index.customer_count');
});

/*
|--------------------------------------------------------------------------
| 系统管理模块
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'system','namespace'=> 'System','middleware'=>['auth','permission:system']],function (){

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


/*
|--------------------------------------------------------------------------
| 呼叫配置
|--------------------------------------------------------------------------
*/
Route::group(['prefix'=>'call','namespace'=> 'Call','middleware'=>['auth','permission:call']],function (){

    //网关管理
    Route::group([],function (){
        Route::get('gateway','GatewayController@index')->name('call.gateway')->middleware('permission:call.gateway');
        //添加
        Route::get('gateway/create','GatewayController@create')->name('call.gateway.create')->middleware('permission:call.gateway.create');
        Route::post('gateway/store','GatewayController@store')->name('call.gateway.store')->middleware('permission:call.gateway.create');
        //编辑
        Route::get('gateway/{id}/edit','GatewayController@edit')->name('call.gateway.edit')->middleware('permission:call.gateway.edit');
        Route::put('gateway/{id}/update','GatewayController@update')->name('call.gateway.update')->middleware('permission:call.gateway.edit');
        //删除
        Route::delete('gateway/destroy','GatewayController@destroy')->name('call.gateway.destroy')->middleware('permission:call.gateway.destroy');
        //更新配置
        Route::post('gateway/updateXml','GatewayController@updateXml')->name('call.gateway.updateXml')->middleware('permission:call.gateway.updateXml');
    });

    //分机管理
    Route::group([],function (){
        Route::get('sip','SipController@index')->name('call.sip')->middleware('permission:call.sip');
        //添加
        Route::get('sip/create','SipController@create')->name('call.sip.create')->middleware('permission:call.sip.create');
        Route::post('sip/store','SipController@store')->name('call.sip.store')->middleware('permission:call.sip.create');
        //批量添加
        Route::get('sip/create_list','SipController@createList')->name('call.sip.create_list')->middleware('permission:call.sip.create_list');
        Route::post('sip/store_list','SipController@storeList')->name('call.sip.store_list')->middleware('permission:call.sip.create_list');
        //编辑
        Route::get('sip/{id}/edit','SipController@edit')->name('call.sip.edit')->middleware('permission:call.sip.edit');
        Route::put('sip/{id}/update','SipController@update')->name('call.sip.update')->middleware('permission:call.sip.edit');
        //删除
        Route::delete('sip/destroy','SipController@destroy')->name('call.sip.destroy')->middleware('permission:call.sip.destroy');
        //更新配置
        Route::post('sip/updateXml','SipController@updateXml')->name('call.sip.updateXml')->middleware('permission:call.sip.updateXml');
        //切换网关
        Route::get('sip/updateGatewayForm','SipController@updateGatewayForm')->name('call.sip.updateGatewayForm')->middleware('permission:call.sip.updateGateway');
        Route::post('sip/updateGateway','SipController@updateGateway')->name('call.sip.updateGateway')->middleware('permission:call.sip.updateGateway');
    });

    //拨号计划
    Route::group([],function (){
        Route::get('extension','ExtensionController@index')->name('call.extension')->middleware('permission:call.extension');
        //详情
        Route::get('extension/{id}/show','ExtensionController@show')->name('call.extension.show')->middleware('permission:call.extension.show');
        //添加
        Route::get('extension/create','ExtensionController@create')->name('call.extension.create')->middleware('permission:call.extension.create');
        Route::post('extension/store','ExtensionController@store')->name('call.extension.store')->middleware('permission:call.extension.create');
        //编辑
        Route::get('extension/{id}/edit','ExtensionController@edit')->name('call.extension.edit')->middleware('permission:call.extension.edit');
        Route::put('extension/{id}/update','ExtensionController@update')->name('call.extension.update')->middleware('permission:call.extension.edit');
        //删除
        Route::delete('extension/destroy','ExtensionController@destroy')->name('call.extension.destroy')->middleware('permission:call.extension.destroy');
        //更新配置
        Route::post('extension/updateXml','ExtensionController@updateXml')->name('call.extension.updateXml')->middleware('permission:call.extension.updateXml');
    });

    //拨号规则，同属拨号计划权限
    Route::group([],function (){
        Route::get('extension/{extension_id}/condition','ConditionController@index')->name('call.condition');
        //添加
        Route::get('extension/{extension_id}/condition/create','ConditionController@create')->name('call.condition.create');
        Route::post('extension/{extension_id}/condition/store','ConditionController@store')->name('call.condition.store');
        //编辑
        Route::get('extension/{extension_id}/condition/{id}/edit','ConditionController@edit')->name('call.condition.edit');
        Route::put('extension/{extension_id}/condition/{id}/update','ConditionController@update')->name('call.condition.update');
        //删除
        Route::delete('condition/destroy','ConditionController@destroy')->name('call.condition.destroy');
    });

    //拨号应用，同属拨号计划权限
    Route::group([],function (){
        Route::get('condition/{condition_id}/action','ActionController@index')->name('call.action');
        Route::get('condition/{condition_id}/action/data','ActionController@data')->name('call.action.data');
        //添加
        Route::get('condition/{condition_id}/action/create','ActionController@create')->name('call.action.create');
        Route::post('condition/{condition_id}/action/store','ActionController@store')->name('call.action.store');
        //编辑
        Route::get('condition/{condition_id}/action/{id}/edit','ActionController@edit')->name('call.action.edit');
        Route::put('condition/{condition_id}/action/{id}/update','ActionController@update')->name('call.action.update');
        //删除
        Route::delete('action/destroy','ActionController@destroy')->name('call.action.destroy');
    });

    //通话记录
    Route::group([],function (){
        Route::get('cdr','CdrController@index')->name('call.cdr')->middleware('permission:call.cdr');
    });

});

/*
|--------------------------------------------------------------------------
| 群呼模块
|--------------------------------------------------------------------------
*/
Route::group(['prefix'=>'callcenter','namespace'=>'Callcenter','middleware'=>['auth','permission:callcenter']],function () {

    //队列管理
    Route::group([],function (){
        Route::get('queue','QueueController@index')->name('callcenter.queue')->middleware('permission:callcenter.queue');
        //添加
        Route::get('queue/create','QueueController@create')->name('callcenter.queue.create')->middleware('permission:callcenter.queue.create');
        Route::post('queue/store','QueueController@store')->name('callcenter.queue.store')->middleware('permission:callcenter.queue.create');
        //编辑
        Route::get('queue/{id}/edit','QueueController@edit')->name('callcenter.queue.edit')->middleware('permission:callcenter.queue.edit');
        Route::put('queue/{id}/update','QueueController@update')->name('callcenter.queue.update')->middleware('permission:callcenter.queue.edit');
        //删除
        Route::delete('queue/destroy','QueueController@destroy')->name('callcenter.queue.destroy')->middleware('permission:callcenter.queue.destroy');
        //更新配置
        Route::post('queue/updateXml','QueueController@updateXml')->name('callcenter.queue.updateXml')->middleware('permission:callcenter.queue.updateXml');
    });

});


/*
|--------------------------------------------------------------------------
| CRM模块
|--------------------------------------------------------------------------
*/
Route::group(['prefix'=>'crm','namespace'=>'Crm','middleware'=>['auth','permission:crm']],function (){

    //部门管理
    Route::group([],function (){
        Route::get('department','DepartmentController@index')->name('crm.department')->middleware('permission:crm.department');
        //添加
        Route::get('department/create','DepartmentController@create')->name('crm.department.create')->middleware('permission:crm.department.create');
        Route::post('department/store','DepartmentController@store')->name('crm.department.store')->middleware('permission:crm.department.create');
        //编辑
        Route::get('department/{id}/edit','DepartmentController@edit')->name('crm.department.edit')->middleware('permission:crm.department.edit');
        Route::put('department/{id}/update','DepartmentController@update')->name('crm.department.update')->middleware('permission:crm.department.edit');
        //删除
        Route::delete('department/destroy','DepartmentController@destroy')->name('crm.department.destroy')->middleware('permission:crm.department.destroy');
    });

    //节点管理
    Route::group([],function (){
        Route::get('node','NodeController@index')->name('crm.node')->middleware('permission:crm.node');
        //添加
        Route::get('node/create','NodeController@create')->name('crm.node.create')->middleware('permission:crm.node.create');
        Route::post('node/store','NodeController@store')->name('crm.node.store')->middleware('permission:crm.node.create');
        //编辑
        Route::get('node/{id}/edit','NodeController@edit')->name('crm.node.edit')->middleware('permission:crm.node.edit');
        Route::put('node/{id}/update','NodeController@update')->name('crm.node.update')->middleware('permission:crm.node.edit');
        //删除
        Route::delete('node/destroy','NodeController@destroy')->name('crm.node.destroy')->middleware('permission:crm.node.destroy');
    });

    //客户字段
    Route::group([],function (){
        Route::get('customer_field','CustomerFieldController@index')->name('crm.customer_field')->middleware('permission:crm.customer_field');
        //添加
        Route::get('customer_field/create','CustomerFieldController@create')->name('crm.customer_field.create')->middleware('permission:crm.customer_field.create');
        Route::post('customer_field/store','CustomerFieldController@store')->name('crm.customer_field.store')->middleware('permission:crm.customer_field.create');
        //编辑
        Route::get('customer_field/{id}/edit','CustomerFieldController@edit')->name('crm.customer_field.edit')->middleware('permission:crm.customer_field.edit');
        Route::put('customer_field/{id}/update','CustomerFieldController@update')->name('crm.customer_field.update')->middleware('permission:crm.customer_field.edit');
        //删除
        Route::delete('customer_field/destroy','CustomerFieldController@destroy')->name('crm.customer_field.destroy')->middleware('permission:crm.customer_field.destroy');

    });

    //待分配库
    Route::group([],function (){
        Route::get('assignment','AssignmentController@index')->name('crm.assignment')->middleware('permission:crm.assignment');
        //删除
        Route::delete('assignment/destroy','AssignmentController@destroy')->name('crm.assignment.destroy')->middleware('permission:crm.assignment.destroy');
        //分配
        Route::post('assignment/to','AssignmentController@to')->name('crm.assignment.to')->middleware('permission:crm.assignment.to');
        //导入
        Route::match(['get','post'],'assignment/import','AssignmentController@import')->name('crm.assignment.import')->middleware('permission:crm.assignment.import');
        //添加
        Route::get('assignment/create','AssignmentController@create')->name('crm.assignment.create')->middleware('permission:crm.assignment.create');
        Route::post('assignment/store','AssignmentController@store')->name('crm.assignment.store')->middleware('permission:crm.assignment.create');
        //编辑
        Route::get('assignment/{id}/edit','AssignmentController@edit')->name('crm.assignment.edit')->middleware('permission:crm.assignment.edit');
        Route::put('assignment/{id}/update','AssignmentController@update')->name('crm.assignment.update')->middleware('permission:crm.assignment.edit');
    });

    //经理库
    Route::group([],function (){
        Route::get('business','BusinessController@index')->name('crm.business')->middleware('permission:crm.business');
        //分配
        Route::post('business/to','BusinessController@to')->name('crm.business.to')->middleware('permission:crm.business.to');
    });

    //部门抢单库
    Route::group([],function (){
        Route::get('grab','GrabController@index')->name('crm.grab')->middleware('permission:crm.grab');
        //抢单
        Route::post('grab/store','GrabController@store')->name('crm.grab.store')->middleware('permission:crm.grab.store');
        Route::delete('grab/destroy','WasteController@destroy')->name('crm.grab.destroy')->middleware('permission:crm.grab.destroy');
    });

    //客户管理
    Route::group([],function (){
        Route::get('customer','CustomerController@index')->name('crm.customer')->middleware('permission:crm.customer');
        //添加
        Route::get('customer/create','CustomerController@create')->name('crm.customer.create')->middleware('permission:crm.customer.create');
        Route::post('customer/store','CustomerController@store')->name('crm.customer.store')->middleware('permission:crm.customer.create');
        //编辑
        Route::get('customer/{id}/edit','CustomerController@edit')->name('crm.customer.edit')->middleware('permission:crm.customer.edit');
        Route::put('customer/{id}/update','CustomerController@update')->name('crm.customer.update')->middleware('permission:crm.customer.edit');
        //详情
        Route::get('customer/{id}/show','CustomerController@show')->name('crm.customer.show')->middleware('permission:crm.customer.show');
        //删除
        Route::delete('customer/destroy','CustomerController@destroy')->name('crm.customer.destroy')->middleware('permission:crm.customer.destroy');
        //剔除
        Route::post('customer/remove','CustomerController@remove')->name('crm.customer.remove')->middleware('permission:crm.customer.remove');
        //备注跟进
        Route::match(['get','post'],'customer/{id}/remark','CustomerController@remark')->name('crm.customer.remark')->middleware('permission:crm.customer.remark');
        //移交
        Route::post('customer/transfer','CustomerController@transfer')->name('crm.customer.transfer')->middleware('permission:crm.customer.transfer');

    });

    //公海库
    Route::group([],function (){
        Route::get('waste','WasteController@index')->name('crm.waste')->middleware('permission:crm.waste');
        Route::post('waste/retrieve','WasteController@retrieve')->name('crm.waste.retrieve')->middleware('permission:crm.waste.retrieve');
        Route::get('waste/{id}/show','WasteController@show')->name('crm.waste.show')->middleware('permission:crm.waste.show');
        Route::delete('waste/destroy','WasteController@destroy')->name('crm.waste.destroy')->middleware('permission:crm.waste.destroy');
    });

});


/*
|--------------------------------------------------------------------------
| 实时聊天模块
|--------------------------------------------------------------------------
*/
Route::group(['prefix'=>'chat','namespace'=>'Chat','middleware'=>['auth','permission:chat']],function (){

    //消息中心
    Route::group([],function (){
        Route::get('message','MessageController@index')->name('chat.message')->middleware('permission:chat.message');
        //添加
        Route::get('message/create','MessageController@create')->name('chat.message.create')->middleware('permission:chat.message.create');
        Route::post('message/store','MessageController@store')->name('chat.message.store')->middleware('permission:chat.message.create');
        //详情
        Route::get('message/{id}/show','MessageController@show')->name('chat.message.show')->middleware('permission:chat.message.show');
        //已读
        Route::post('message/read','MessageController@read')->name('chat.message.read')->middleware('permission:chat.message.read');
        //删除
        Route::delete('message/destroy','MessageController@destroy')->name('chat.message.destroy')->middleware('permission:chat.message.destroy');
    });

    //语音通话
    Route::group([],function (){
        Route::get('audio','AudioController@index')->name('chat.audio')->middleware('permission:chat.audio');
    });

});


/*
|--------------------------------------------------------------------------
| 订单模块
|--------------------------------------------------------------------------
*/

Route::group(['prefix'=>'order','namespace'=>'Order','middleware'=>['auth','permission:order']],function () {

    //订单
    Route::group([],function (){
        Route::get('order','OrderController@index')->name('order.order')->middleware('permission:order.order');
        //下单
        Route::get('order/create','OrderController@create')->name('order.order.create')->middleware('permission:order.order.create');
        Route::post('order/store','OrderController@store')->name('order.order.store')->middleware('permission:order.order.create');
        //付款
        Route::get('order/payForm','OrderController@payForm')->name('order.order.payForm')->middleware('permission:order.order.pay');
        Route::post('order/pay','OrderController@pay')->name('order.order.pay')->middleware('permission:order.order.pay');
        //备注跟进
        Route::match(['get','post'],'order/remark','OrderController@remark')->name('order.order.remark')->middleware('permission:order.order.remark');
        //删除
        Route::delete('order/destroy','OrderController@destroy')->name('order.order.destroy')->middleware('permission:order.order.destroy');
    });

});


/*
|--------------------------------------------------------------------------
| 财务模块
|--------------------------------------------------------------------------
*/

Route::group(['prefix'=>'account','namespace'=>'Account','middleware'=>['auth','permission:account']],function () {

    //订单付款
    Route::group([],function (){
        Route::get('pay','PayController@index')->name('account.pay')->middleware('permission:account.pay');
        //审核
        Route::match(['get','post'],'pay/check','PayController@check')->name('account.pay.check')->middleware('permission:account.pay.check');
        //详情
        Route::get('pay/show','PayController@show')->name('account.pay.show')->middleware('permission:account.pay.show');

    });

});


/*
|--------------------------------------------------------------------------
| 数据可视化
|--------------------------------------------------------------------------
*/

Route::group(['prefix'=>'data_view','namespace'=>'DataView','middleware'=>['auth','permission:data_view']],function () {

    //通话统计
    Route::group([],function (){
        Route::get('cdr','CdrController@index')->name('data_view.cdr')->middleware('permission:data_view.cdr');
    });

});









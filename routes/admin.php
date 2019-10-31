<?php

/*
|--------------------------------------------------------------------------
| 后台路由
|--------------------------------------------------------------------------
|
| 统一命名空间 Admin
| 统一前缀 admin
| 用户认证统一使用 auth 中间件
| 权限认证统一使用 permission:权限名称
|
*/

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
    Route::group(['middleware'=>['permission:system.user']],function (){
        Route::get('user','UserController@index')->name('admin.user');
        Route::get('user/data','UserController@data')->name('admin.user.data');
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
    });

    //角色管理
    Route::group(['middleware'=>'permission:system.role'],function (){
        Route::get('role','RoleController@index')->name('admin.role');
        Route::get('role/data','RoleController@data')->name('admin.role.data');
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
        Route::get('permission/data','PermissionController@data')->name('admin.permission.data');
        //添加
        Route::get('permission/create','PermissionController@create')->name('admin.permission.create')->middleware('permission:system.permission.create');
        Route::post('permission/store','PermissionController@store')->name('admin.permission.store')->middleware('permission:system.permission.create');
        //编辑
        Route::get('permission/{id}/edit','PermissionController@edit')->name('admin.permission.edit')->middleware('permission:system.permission.edit');
        Route::put('permission/{id}/update','PermissionController@update')->name('admin.permission.update')->middleware('permission:system.permission.edit');
        //删除
        Route::delete('permission/destroy','PermissionController@destroy')->name('admin.permission.destroy')->middleware('permission:system.permission.destroy');
    });

    //配置组
    Route::group(['middleware'=>'permission:system.config_group'],function (){
        Route::get('config_group','ConfigGroupController@index')->name('admin.config_group');
        Route::get('config_group/data','ConfigGroupController@data')->name('admin.config_group.data');
        //添加
        Route::get('config_group/create','ConfigGroupController@create')->name('admin.config_group.create')->middleware('permission:system.config_group.create');
        Route::post('config_group/store','ConfigGroupController@store')->name('admin.config_group.store')->middleware('permission:system.config_group.create');
        //编辑
        Route::get('config_group/{id}/edit','ConfigGroupController@edit')->name('admin.config_group.edit')->middleware('permission:system.config_group.edit');
        Route::put('config_group/{id}/update','ConfigGroupController@update')->name('admin.config_group.update')->middleware('permission:system.config_group.edit');
        //删除
        Route::delete('config_group/destroy','ConfigGroupController@destroy')->name('admin.config_group.destroy')->middleware('permission:system.config_group.destroy');
    });

    //配置项
    Route::group(['middleware'=>'permission:system.configuration'],function (){
        Route::get('configuration','ConfigurationController@index')->name('admin.configuration');
        //添加
        Route::get('configuration/create','ConfigurationController@create')->name('admin.configuration.create')->middleware('permission:system.configuration.create');
        Route::post('configuration/store','ConfigurationController@store')->name('admin.configuration.store')->middleware('permission:system.configuration.create');
        //编辑
        Route::put('configuration/update','ConfigurationController@update')->name('admin.configuration.update')->middleware('permission:system.configuration.edit');
        //删除
        Route::delete('configuration/destroy','ConfigurationController@destroy')->name('admin.configuration.destroy')->middleware('permission:system.configuration.destroy');
    });

    //登录日志
    Route::group(['middleware'=>'permission:system.login_log'],function (){
        Route::get('login_log','LoginLogController@index')->name('admin.login_log');
        Route::get('login_log/data','LoginLogController@data')->name('admin.login_log.data');
        Route::delete('login_log/destroy','LoginLogController@destroy')->name('admin.login_log.destroy');
    });

    //操作日志
    Route::group(['middleware'=>'permission:system.operate_log'],function (){
        Route::get('operate_log','OperateLogController@index')->name('admin.operate_log');
        Route::get('operate_log/data','OperateLogController@data')->name('admin.operate_log.data');
        Route::delete('operate_log/destroy','OperateLogController@destroy')->name('admin.operate_log.destroy');
    });

});

/*
|--------------------------------------------------------------------------
| 资讯管理模块
|--------------------------------------------------------------------------
*/
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth', 'permission:information', 'operate.log']], function () {
    //分类管理
    Route::group(['middleware' => 'permission:information.category'], function () {
        Route::get('category/data', 'CategoryController@data')->name('admin.category.data');
        Route::get('category', 'CategoryController@index')->name('admin.category');
        //添加分类
        Route::get('category/create', 'CategoryController@create')->name('admin.category.create')->middleware('permission:information.category.create');
        Route::post('category/store', 'CategoryController@store')->name('admin.category.store')->middleware('permission:information.category.create');
        //编辑分类
        Route::get('category/{id}/edit', 'CategoryController@edit')->name('admin.category.edit')->middleware('permission:information.category.edit');
        Route::put('category/{id}/update', 'CategoryController@update')->name('admin.category.update')->middleware('permission:information.category.edit');
        //删除分类
        Route::delete('category/destroy', 'CategoryController@destroy')->name('admin.category.destroy')->middleware('permission:information.category.destroy');
    });
    //文章管理
    Route::group(['middleware' => 'permission:information.article'], function () {
        Route::get('article/data', 'ArticleController@data')->name('admin.article.data');
        Route::get('article', 'ArticleController@index')->name('admin.article');
        //添加
        Route::get('article/create', 'ArticleController@create')->name('admin.article.create')->middleware('permission:information.article.create');
        Route::post('article/store', 'ArticleController@store')->name('admin.article.store')->middleware('permission:information.article.create');
        //编辑
        Route::get('article/{id}/edit', 'ArticleController@edit')->name('admin.article.edit')->middleware('permission:information.article.edit');
        Route::put('article/{id}/update', 'ArticleController@update')->name('admin.article.update')->middleware('permission:information.article.edit');
        //删除
        Route::delete('article/destroy', 'ArticleController@destroy')->name('admin.article.destroy')->middleware('permission:information.article.destroy');
    });
    //标签管理
    Route::group(['middleware' => 'permission:information.tag'], function () {
        Route::get('tag/data', 'TagController@data')->name('admin.tag.data');
        Route::get('tag', 'TagController@index')->name('admin.tag');
        //添加
        Route::get('tag/create', 'TagController@create')->name('admin.tag.create')->middleware('permission:information.tag.create');
        Route::post('tag/store', 'TagController@store')->name('admin.tag.store')->middleware('permission:information.tag.create');
        //编辑
        Route::get('tag/{id}/edit', 'TagController@edit')->name('admin.tag.edit')->middleware('permission:information.tag.edit');
        Route::put('tag/{id}/update', 'TagController@update')->name('admin.tag.update')->middleware('permission:information.tag.edit');
        //删除
        Route::delete('tag/destroy', 'TagController@destroy')->name('admin.tag.destroy')->middleware('permission:information.tag.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| PBX配置管理模块
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth','permission:pbx.manage']],function (){
    //商户管理
    Route::group(['middleware'=>'permission:pbx.merchant'],function (){
        Route::get('merchant','MerchantController@index')->name('admin.merchant');
        Route::get('merchant/data','MerchantController@data')->name('admin.merchant.data');
        //添加
        Route::get('merchant/create','MerchantController@create')->name('admin.merchant.create')->middleware('permission:pbx.merchant.create');
        Route::post('merchant/store','MerchantController@store')->name('admin.merchant.store')->middleware('permission:pbx.merchant.create');
        //编辑
        Route::get('merchant/{id}/edit','MerchantController@edit')->name('admin.merchant.edit')->middleware('permission:pbx.merchant.edit');
        Route::put('merchant/{id}/update','MerchantController@update')->name('admin.merchant.update')->middleware('permission:pbx.merchant.edit');
        //删除
        Route::delete('merchant/destroy','MerchantController@destroy')->name('admin.merchant.destroy')->middleware('permission:pbx.merchant.destroy');
        //帐单列表，与帐单同权限
        Route::get('merchant/bill','MerchantController@bill')->name('admin.merchant.bill')->middleware('permission:pbx.bill');
        //添加帐单， 与帐单同权限
        Route::post('merchant/bill/create','MerchantController@billCreate')->name('admin.merchant.bill.create')->middleware('permission:pbx.bill.create');
        //分配网关
        Route::get('merchant/{id}/gateway','MerchantController@gateway')->name('admin.merchant.gateway')->middleware('permission:pbx.merchant.gateway');
        Route::put('merchant/{id}/assignGateway','MerchantController@assignGateway')->name('admin.merchant.assignGateway')->middleware('permission:pbx.merchant.gateway');

    });
    //商户帐单
    Route::group(['middleware'=>'permission:pbx.bill'], function (){
        Route::get('bill','BillController@index')->name('admin.bill');
        Route::get('bill/data','BillController@data')->name('admin.bill.data');
        //添加
        Route::get('bill/create','BillController@create')->name('admin.bill.create')->middleware('permission:pbx.bill.create');
        Route::post('bill/store','BillController@store')->name('admin.bill.store')->middleware('permission:pbx.bill.create');
    });
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
    //坐席管理
    Route::group(['middleware'=>'permission:pbx.agent'],function (){
        Route::get('agent','AgentController@index')->name('admin.agent');
        Route::get('agent/data','AgentController@data')->name('admin.agent.data');
        //添加
        Route::get('agent/create','AgentController@create')->name('admin.agent.create')->middleware('permission:pbx.agent.create');
        Route::post('agent/store','AgentController@store')->name('admin.agent.store')->middleware('permission:pbx.agent.create');
        //编辑
        Route::get('agent/{id}/edit','AgentController@edit')->name('admin.agent.edit')->middleware('permission:pbx.agent.edit');
        Route::put('agent/{id}/update','AgentController@update')->name('admin.agent.update')->middleware('permission:pbx.agent.edit');
        //删除
        Route::delete('agent/destroy','AgentController@destroy')->name('admin.agent.destroy')->middleware('permission:pbx.agent.destroy');

    });

    //IVR管理
    Route::group(['middleware'=>'permission:pbx.ivr'],function (){
        Route::get('ivr','IvrController@index')->name('admin.ivr');
        Route::get('ivr/data','IvrController@data')->name('admin.ivr.data');
        //添加
        Route::get('ivr/create','IvrController@create')->name('admin.ivr.create')->middleware('permission:pbx.ivr.create');
        Route::post('ivr/store','IvrController@store')->name('admin.ivr.store')->middleware('permission:pbx.ivr.create');
        //编辑
        Route::get('ivr/{id}/edit','IvrController@edit')->name('admin.ivr.edit')->middleware('permission:pbx.ivr.edit');
        Route::put('ivr/{id}/update','IvrController@update')->name('admin.ivr.update')->middleware('permission:pbx.ivr.edit');
        //删除
        Route::delete('ivr/destroy','IvrController@destroy')->name('admin.ivr.destroy')->middleware('permission:pbx.ivr.destroy');
        //更新配置
        Route::post('ivr/updateXml','IvrController@updateXml')->name('admin.ivr.updateXml')->middleware('permission:pbx.ivr.updateXml');

    });

    //按键管理
    Route::group(['middleware'=>'permission:pbx.digits'],function (){
        Route::get('digits','DigitsController@index')->name('admin.digits');
        Route::get('digits/data','DigitsController@data')->name('admin.digits.data');
        //添加
        Route::get('digits/create','DigitsController@create')->name('admin.digits.create')->middleware('permission:pbx.digits.create');
        Route::post('digits/store','DigitsController@store')->name('admin.digits.store')->middleware('permission:pbx.digits.create');
        //编辑
        Route::get('digits/{id}/edit','DigitsController@edit')->name('admin.digits.edit')->middleware('permission:pbx.digits.edit');
        Route::put('digits/{id}/update','DigitsController@update')->name('admin.digits.update')->middleware('permission:pbx.digits.edit');
        //删除
        Route::delete('digits/destroy','DigitsController@destroy')->name('admin.digits.destroy')->middleware('permission:pbx.digits.destroy');

    });

    //音频管理
    Route::group(['middleware'=>'permission:pbx.audio'],function (){
        Route::get('audio','AudioController@index')->name('admin.audio');
        Route::get('audio/data','AudioController@data')->name('admin.audio.data');
        //添加
        Route::post('audio/store','AudioController@store')->name('admin.audio.store')->middleware('permission:pbx.audio.create');
        //删除
        Route::delete('audio/destroy','AudioController@destroy')->name('admin.audio.destroy')->middleware('permission:pbx.audio.destroy');

    });

});

/*
|--------------------------------------------------------------------------
| 批量外呼
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth','permission:ai.manage']],function (){

    //任务管理
    Route::group(['middleware'=>'permission:ai.task'],function (){
        Route::get('task','TaskController@index')->name('admin.task');
        Route::get('task/data','TaskController@data')->name('admin.task.data');
        //详情
        Route::match(['get','post'],'task/{id}/show','TaskController@show')->name('admin.task.show');
        //添加
        Route::get('task/create','TaskController@create')->name('admin.task.create')->middleware('permission:ai.task.create');
        Route::post('task/store','TaskController@store')->name('admin.task.store')->middleware('permission:ai.task.create');
        //编辑
        Route::get('task/{id}/edit','TaskController@edit')->name('admin.task.edit')->middleware('permission:ai.task.edit');
        Route::put('task/{id}/update','TaskController@update')->name('admin.task.update')->middleware('permission:ai.task.edit');
        //删除
        Route::delete('task/destroy','TaskController@destroy')->name('admin.task.destroy')->middleware('permission:ai.task.destroy');
        //设置状态
        Route::post('task/setStatus','TaskController@setStatus')->name('admin.task.setStatus')->middleware('permission:ai.task.setStatus');
        //导入号码
        Route::post('task/{id}/importCall','TaskController@importCall')->name('admin.task.importCall')->middleware('permission:ai.task.importCall');
    });

});

/*
|--------------------------------------------------------------------------
| 录音管理
|--------------------------------------------------------------------------
*/
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
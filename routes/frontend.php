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
    //前台-通话记录
    Route::post('/index/cdr','IndexController@cdr')->name('frontend.index.cdr');
    //前台-节点
    Route::post('/index/node','IndexController@node')->name('frontend.index.node');
    //在线拨号
    Route::get('/index/online','IndexController@online')->name('frontend.index.online');

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

/*
|--------------------------------------------------------------------------
| 帐户中心
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Frontend','prefix'=>'account','middleware'=>['auth:frontend']],function (){

    //商户资料
    Route::group([],function (){
        Route::get('merchant','MerchantController@index')->name('frontend.account.merchant')->middleware('permission:frontend.account.merchant');
    });

    //费用明细
    Route::group([],function (){
        Route::get('bill','BillController@index')->name('frontend.account.bill')->middleware('permission:frontend.account.bill');
    });

    //部门管理
    Route::group([],function (){
        Route::get('department','DepartmentController@index')->name('frontend.account.department')->middleware('permission:frontend.account.department');
        //添加
        Route::get('department/create','DepartmentController@create')->name('frontend.account.department.create')->middleware('permission:frontend.account.department.create');
        Route::post('department/store','DepartmentController@store')->name('frontend.account.department.store')->middleware('permission:frontend.account.department.create');
        //编辑
        Route::get('department/{id}/edit','DepartmentController@edit')->name('frontend.account.department.edit')->middleware('permission:frontend.account.department.edit');
        Route::put('department/{id}/update','DepartmentController@update')->name('frontend.account.department.update')->middleware('permission:frontend.account.department.edit');
        //删除
        Route::delete('department/destroy','DepartmentController@destroy')->name('frontend.account.department.destroy')->middleware('permission:frontend.account.department.destroy');

    });

    //员工管理
    Route::group([],function (){
        Route::get('staff','StaffController@index')->name('frontend.account.staff')->middleware('permission:frontend.account.staff');
        //添加
        Route::get('staff/create','StaffController@create')->name('frontend.account.staff.create')->middleware('permission:frontend.account.staff.create');
        Route::post('staff/store','StaffController@store')->name('frontend.account.staff.store')->middleware('permission:frontend.account.staff.create');
        //编辑
        Route::get('staff/{id}/edit','StaffController@edit')->name('frontend.account.staff.edit')->middleware('permission:frontend.account.staff.edit');
        Route::put('staff/{id}/update','StaffController@update')->name('frontend.account.staff.update')->middleware('permission:frontend.account.staff.edit');
        //删除
        Route::delete('staff/destroy','StaffController@destroy')->name('frontend.account.staff.destroy')->middleware('permission:frontend.account.staff.destroy');
        //分配角色
        Route::get('staff/{id}/role','StaffController@role')->name('frontend.account.staff.role')->middleware('permission:frontend.account.staff.role');
        Route::put('staff/{id}/assignRole','StaffController@assignRole')->name('frontend.account.staff.assignRole')->middleware('permission:frontend.account.staff.role');

        //重置密码
        Route::get('staff/{id}/resetPassword','StaffController@resetPasswordForm')->name('frontend.account.staff.resetPasswordForm')->middleware('permission:frontend.account.staff.resetPassword');
        Route::put('staff/{id}/resetPassword','StaffController@resetPassword')->name('frontend.account.staff.resetPassword')->middleware('permission:frontend.account.staff.resetPassword');

    });

});

/*
|--------------------------------------------------------------------------
| 系统设置
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Frontend','prefix'=>'system','middleware'=>['auth:frontend']],function (){

    //个人资料
    Route::get('staff/mine','StaffController@mine')->name('frontend.system.staff.mine')->middleware('permission:frontend.system.staff.mine');


    //更改密码
    Route::get('staff/change_my_password_form','StaffController@changeMyPasswordForm')->name('frontend.system.staff.changeMyPasswordForm');
    Route::post('staff/change_my_password','StaffController@changeMyPassword')->name('frontend.system.staff.changeMyPassword');

    //登录日志
    Route::get('staff/log','StaffController@loginLog')->name('frontend.system.staff.loginLog')->middleware('permission:frontend.system.staff.loginLog');

});

/*
|--------------------------------------------------------------------------
| 客户管理
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Frontend','prefix'=>'crm','middleware'=>['auth:frontend']],function (){

    //节点管理
    Route::group([],function (){
        Route::get('node','NodeController@index')->name('frontend.crm.node')->middleware('permission:frontend.crm.node');
        //添加
        Route::get('node/create','NodeController@create')->name('frontend.crm.node.create')->middleware('permission:frontend.crm.node.create');
        Route::post('node/store','NodeController@store')->name('frontend.crm.node.store')->middleware('permission:frontend.crm.node.create');
        //编辑
        Route::get('node/{id}/edit','NodeController@edit')->name('frontend.crm.node.edit')->middleware('permission:frontend.crm.node.edit');
        Route::put('node/{id}/update','NodeController@update')->name('frontend.crm.node.update')->middleware('permission:frontend.crm.node.edit');
        //删除
        Route::delete('node/destroy','NodeController@destroy')->name('frontend.crm.node.destroy')->middleware('permission:frontend.crm.node.destroy');

    });

    //客户属性
    Route::group([],function (){
        Route::get('project-design','ProjectDesignController@index')->name('frontend.crm.project-design')->middleware('permission:frontend.crm.project-design');
        //添加
        Route::get('project-design/create','ProjectDesignController@create')->name('frontend.crm.project-design.create')->middleware('permission:frontend.crm.project-design.create');
        Route::post('project-design/store','ProjectDesignController@store')->name('frontend.crm.project-design.store')->middleware('permission:frontend.crm.project-design.create');
        //编辑
        Route::get('project-design/{id}/edit','ProjectDesignController@edit')->name('frontend.crm.project-design.edit')->middleware('permission:frontend.crm.project-design.edit');
        Route::put('project-design/{id}/update','ProjectDesignController@update')->name('frontend.crm.project-design.update')->middleware('permission:frontend.crm.project-design.edit');
        //删除
        Route::delete('project-design/destroy','ProjectDesignController@destroy')->name('frontend.crm.project-design.destroy')->middleware('permission:frontend.crm.project-design.destroy');

    });

    //待分配
    Route::group([],function (){
        Route::get('assignment','ProjectController@assignment')->name('frontend.crm.assignment')->middleware('permission:frontend.crm.assignment');
        //删除
        Route::delete('assignment/destroy','ProjectController@assignmentDestroy')->name('frontend.crm.assignment.destroy')->middleware('permission:frontend.crm.assignment.destroy');
        //导入
        Route::post('assignment/import','ProjectController@import')->name('frontend.crm.assignment.import')->middleware('permission:frontend.crm.assignment.import');
        //分配
        Route::post('assignment/to','ProjectController@assignmentTo')->name('frontend.crm.assignment.to')->middleware('permission:frontend.crm.assignment.to');

    });

    //我的客户管理
    Route::group([],function (){
        Route::get('project','ProjectController@index')->name('frontend.crm.project')->middleware('permission:frontend.crm.project');
        //添加
        Route::get('project/create','ProjectController@create')->name('frontend.crm.project.create')->middleware('permission:frontend.crm.project.create');
        Route::post('project/store','ProjectController@store')->name('frontend.crm.project.store')->middleware('permission:frontend.crm.project.create');
        //编辑
        Route::get('project/{id}/edit','ProjectController@edit')->name('frontend.crm.project.edit')->middleware('permission:frontend.crm.project.edit');
        Route::put('project/{id}/update','ProjectController@update')->name('frontend.crm.project.update')->middleware('permission:frontend.crm.project.edit');
        //详情
        Route::get('project/{id}/show','ProjectController@show')->name('frontend.crm.project.show')->middleware('permission:frontend.crm.project.show');
        //删除
        Route::delete('project/destroy','ProjectController@destroy')->name('frontend.crm.project.destroy')->middleware('permission:frontend.crm.project.destroy');
        //跟进
        Route::match(['get','post'],'project/{id}/follow','ProjectController@follow')->name('frontend.crm.project.follow')->middleware('permission:frontend.crm.project.follow');
        //跟进记录
        Route::get('project/{id}/followList','ProjectController@followList')->name('frontend.crm.project.followList')->middleware('permission:frontend.crm.project.followList');
        //公海库
        Route::get('project/waste','ProjectController@waste')->name('frontend.crm.project.waste')->middleware('permission:frontend.crm.project.waste');
        //拾回
        Route::post('project/waste/retrieve','ProjectController@retrieve')->name('frontend.crm.project.waste.retrieve')->middleware('permission:frontend.crm.project.waste.retrieve');
        //详情
        Route::get('project/{id}/waste/show','ProjectController@wasteShow')->name('frontend.crm.project.waste.show')->middleware('permission:frontend.crm.project.waste.show');
        //详情
        Route::delete('project/{id}/waste/destroy','ProjectController@wasteDestroy')->name('frontend.crm.project.waste.destroy')->middleware('permission:frontend.crm.project.waste.destroy');

    });


});



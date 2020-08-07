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



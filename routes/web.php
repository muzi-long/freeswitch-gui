<?php

/*
|--------------------------------------------------------------------------
| 商户 用户登录、退出、更改密码
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Home','prefix'=>'home/user'],function (){
    //登录
    Route::get('login','UserController@showLoginForm')->name('home.user.loginForm');
    Route::post('login','UserController@login')->name('home.user.login');
    //退出
    Route::get('logout','UserController@logout')->name('home.user.logout');
    //更改密码
    Route::get('change_my_password_form','UserController@changeMyPasswordForm')->name('home.user.changeMyPasswordForm');
    Route::post('change_my_password','UserController@changeMyPassword')->name('home.user.changeMyPassword');
});

/*
|--------------------------------------------------------------------------
| 后台公共页面
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Home','prefix'=>'home','middleware'=>'merchant'],function (){
    //后台布局
    Route::get('/','IndexController@layout')->name('home.layout');
    //后台首页
    Route::get('/index','IndexController@index')->name('home.index');
    //在线拨号
    Route::get('onlinecall','IndexController@onlinecall')->name('home.onlinecall');
});

/*
|--------------------------------------------------------------------------
| 系统管理模块
|--------------------------------------------------------------------------
*/
Route::group(['namespace'=>'Home','prefix'=>'home','middleware'=>'merchant'],function (){

    //节点管理
    Route::group([],function (){
        Route::get('node','NodeController@index')->name('home.node');
        Route::get('node/data','NodeController@data')->name('home.node.data');
        //添加
        Route::get('node/create','NodeController@create')->name('home.node.create');
        Route::post('node/store','NodeController@store')->name('home.node.store');
        //编辑
        Route::get('node/{id}/edit','NodeController@edit')->name('home.node.edit');
        Route::put('node/{id}/update','NodeController@update')->name('home.node.update');
        //删除
        Route::delete('node/destroy','NodeController@destroy')->name('home.node.destroy');

    });

    //表单设计
    Route::group([],function (){
        Route::get('project-design','ProjectDesignController@index')->name('home.project-design');
        Route::get('project-design/data','ProjectDesignController@data')->name('home.project-design.data');
        //添加
        Route::get('project-design/create','ProjectDesignController@create')->name('home.project-design.create');
        Route::post('project-design/store','ProjectDesignController@store')->name('home.project-design.store');
        //编辑
        Route::get('project-design/{id}/edit','ProjectDesignController@edit')->name('home.project-design.edit');
        Route::put('project-design/{id}/update','ProjectDesignController@update')->name('home.project-design.update');
        //删除
        Route::delete('project-design/destroy','ProjectDesignController@destroy')->name('home.project-design.destroy');

    });

    //项目管理
    Route::group([],function (){
        Route::get('project','ProjectController@index')->name('home.project');
        Route::get('project/data','ProjectController@data')->name('home.project.data');
        //添加
        Route::get('project/create','ProjectController@create')->name('home.project.create');
        Route::post('project/store','ProjectController@store')->name('home.project.store');
        //编辑
        Route::get('project/{id}/edit','ProjectController@edit')->name('home.project.edit');
        Route::put('project/{id}/update','ProjectController@update')->name('home.project.update');
        //详情
        Route::get('project/{id}/show','ProjectController@show')->name('home.project.show');
        //删除
        Route::delete('project/destroy','ProjectController@destroy')->name('home.project.destroy');

    });


});





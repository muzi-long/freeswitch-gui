<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

//文件上传接口
Route::post('upload', 'ApiController@upload')->name('api.upload');

Route::get('merchant-gateway', 'ApiController@MerchantGateway')->name('merchant-gateway');

//动态分机注册
Route::post('directory','ApiController@directory');

//动态拨号计划
Route::post('dialplan','ApiController@dialplan');

//动态configuration 包含动态网关。（未使用）
Route::post('configuration','ApiController@configuration');
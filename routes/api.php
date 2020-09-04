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

//通过商户ID获取网关信息
Route::post('getGatewayByMerchantId','ApiController@getGatewayByMerchantId')->name('getGatewayByMerchantId');
//通过商户ID获取部门信息
Route::post('getDepartmentByMerchantId','ApiController@getDepartmentByMerchantId')->name('getDepartmentByMerchantId');
//文件上传
Route::post('upload','ApiController@upload')->name('api.upload');
//呼叫
Route::post('dial','ApiController@dial')->name('api.dial');


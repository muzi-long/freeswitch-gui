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


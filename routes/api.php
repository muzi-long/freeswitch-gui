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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('get_permission_by_role_id','ApiController@getPermissionByRoleId')->name('api.getPermissionByRoleId');
Route::post('get_role_by_user_id','ApiController@getRoleByUserId')->name('api.getRoleByUserId');
Route::post('get_sips_by_queue_id','ApiController@getSipsByQueueId')->name('api.getSipsByQueueId');
Route::post('get_department_by_user_id','ApiController@getDepartmentByUserId')->name('api.getDepartmentByUserId');
Route::post('get_user','ApiController@getUser')->name('api.getUser');
Route::post('get_node','ApiController@getNode')->name('api.getNode');
Route::post('remark_list','ApiController@remarkList')->name('api.remarkList');
Route::post('pay_list','ApiController@payList')->name('api.payList');
Route::post('call','ApiController@call')->name('api.call');
Route::post('upload','ApiController@upload')->name('api.upload');


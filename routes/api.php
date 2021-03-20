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

Route::post('/api/get_permission_by_role_id','ApiController@getPermissionByRoleId')->name('api.getPermissionByRoleId');
Route::post('/api/get_role_by_user_id','ApiController@getRoleByUserId')->name('api.getRoleByUserId');
Route::post('/api/get_department_by_user_id','ApiController@getDepartmentByUserId')->name('api.getDepartmentByUserId');
Route::post('/api/call','ApiController@call')->name('api.call');

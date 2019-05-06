<?php
//文件上传接口，前后台共用
Route::post('uploadImg', 'PublicController@uploadImg')->name('uploadImg');

Route::get('/','Home\IndexController@index')->name('home');

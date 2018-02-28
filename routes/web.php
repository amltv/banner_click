<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/banner/click/{banner_id}', 'BannerController@click')->where(['banner_id' => '[0-9]+']);
Route::get('/statistic/banner/{banner_id}/{group}', 'StatisticController@show')
    ->where(['banner_id' => '[0-9]+', 'group' => '\w+']);

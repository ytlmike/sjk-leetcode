<?php

use Illuminate\Support\Facades\Route;

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
Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');
Route::get('leetcode/user', 'LeetcodeController@user');

Route::group(['middleware' => ['auth:api']], function () {

    Route::get('me', 'AuthController@me');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('avatar', 'AuthController@uploadAvatar');
    Route::post('name', 'AuthController@editUserName');

    Route::group(['prefix' => 'leetcode'], function () {
        Route::get('submissions', 'LeetcodeController@submissions');
        Route::post('sync', 'LeetcodeController@syncSubmissions');
    });

    Route::get('week/rank/normal', 'RankController@normal'); //正常排名
    Route::get('week/rank/abnormal', 'RankController@abnormal'); //本周小黑屋
});

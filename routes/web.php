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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::group(['prefix' => 'user', 'namespace' => 'User'], function () {
    Route::any('login', 'UserController@login');
    Route::any('register', 'UserController@register');
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'ease', 'namespace' => 'Ease'], function () {
        Route::any('friends', 'EaseController@friends');
        Route::any('add', 'EaseController@add');
        Route::any('agree', 'EaseController@agree');
        Route::any('decline', 'EaseController@decline');
        Route::any('history', 'EaseController@history');
        Route::any('getBlackList', 'EaseController@getBlackList');
        Route::any('addBlackList', 'EaseController@addBlackList');
        Route::any('deleteBlackList', 'EaseController@deleteBlackList');
        Route::any('createGroup', 'EaseController@createGroup');
        Route::any('groupInfo', 'EaseController@groupInfo');
        Route::post('changeGroupInfo', 'EaseController@changeGroupInfo');
        Route::post('changeGroupAvatar', 'EaseController@changeGroupAvatar');
        Route::post('addMembers', 'EaseController@addMembers');
        Route::post('joinChatRoom', 'EaseController@joinChatRoom');
        Route::any('roomInfo', 'EaseController@roomInfo');
    });

    Route::group(['prefix' => 'user', 'namespace' => 'User'], function () {
        Route::any('logout', 'UserController@logout');
        Route::post('bonus', 'UserController@bonus');
        Route::post('groupBonus', 'UserController@groupBonus');
        Route::post('openBonus', 'UserController@openBonus');
        Route::post('openZhuanZhang', 'UserController@openZhuanZhang');
        Route::post('openGroupBonus', 'UserController@openGroupBonus');
        Route::any('avatar', 'UserController@getAvatar');
        Route::post('search', 'UserController@search');
        Route::any('changeAvatar', 'UserController@changeAvatar');
        Route::any('changeUserInfo', 'UserController@changeUserInfo');
        Route::put('changePwd', 'UserController@changePwd');
        Route::any('getUserInfo', 'UserController@getUserInfo');
    });

    Route::group(['prefix' => 'moments', 'namespace' => 'User'], function () {
        Route::get('/', 'MomentsController@get');
        Route::get('/moments', 'MomentsController@getMoments');
        Route::post('/', 'MomentsController@create');
        Route::post('/like', 'MomentsController@like');
        Route::post('/comment', 'MomentsController@comment');
    });

    Route::group(['prefix' => 'file', 'namespace' => 'File'], function () {
        Route::post('upload', 'FilesController@upload');
    });

    Route::group(['prefix' => 'game', 'namespace' => 'Game'], function () {
        Route::group(['prefix' => 'niuniu'], function () {
            // 配置游戏规则
            Route::put('/config', 'NiuniuController@setConfig');
            // 获取游戏规则
            Route::get('/config', 'NiuniuController@getConfig');
            // 创建游戏
            Route::post('/create', 'NiuniuController@create');
            // 下注
            Route::any('/bet', 'NiuniuController@bet');
            // 结束下注
            Route::any('/end', 'NiuniuController@end');
            // 发包
            Route::post('/sendBonus', 'NiuniuController@sendBonus');
            // 开包
            Route::any('/openBonus', 'NiuniuController@openBonus');
            // 获取结果
            Route::any('/result', 'NiuniuController@result');
            // 获取富豪榜
            Route::any('/bang', 'NiuniuController@bang');
            // 获取牛牛群列表
            Route::any('/list', 'NiuniuController@list');
            // 重推
            Route::any('/reset', 'NiuniuController@reset');
        });
    });
});

Route::group(['prefix' => 'common', 'namespace' => 'Common'], function () {
    Route::get('ads', 'CommonController@ads');
    Route::get('games', 'CommonController@games');
    Route::get('cfg', 'CommonController@config');
    Route::get('rooms', 'CommonController@rooms');
});




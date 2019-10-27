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

Route::group(['prefix' => 'user', 'namespace' => 'User', 'middleware' => 'token'], function () {
    // 开放给算账网的接口
    Route::any('register4B', 'UserController@register4B');
    Route::any('userList4B', 'UserController@userList4B');
    Route::any('setJifen4B', 'UserController@setJifen4B');
    Route::any('setBonus4B', 'UserController@setBonus4B');
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
        Route::put('changeShoukuanma', 'UserController@changeShoukuanma');
        Route::put('changeBonusToJifen', 'UserController@changeBonusToJifen');
        Route::post('uploadPingzheng', 'UserController@uploadPingzheng');
    });

    Route::group(['prefix' => 'moments', 'namespace' => 'User'], function () {
        Route::get('/', 'MomentsController@get');
        Route::get('/moments', 'MomentsController@getMoments');
        Route::get('/sync', 'MomentsController@syncMoments');
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
            // 游戏结果列表
            Route::any('/history', 'NiuniuController@history');
            // 游戏结果详情
            Route::any('/historyDetail', 'NiuniuController@historyDetail');
        });
    });
});

Route::group(['prefix' => 'common', 'namespace' => 'Common'], function () {
    Route::get('ads', 'CommonController@ads');
    Route::get('games', 'CommonController@games');
    Route::get('cfg', 'CommonController@config');
    Route::get('rooms', 'CommonController@rooms');
    Route::get('avatar/{username}', 'CommonController@avatar');
});

Route::group(['prefix' => 'h5', 'namespace' => 'H5'], function () {
    Route::get('login', 'H5Controller@login');
});

Route::get('app/download', '\App\Http\Controllers\H5\H5Controller@login');

// 登录后才能访问的界面和接口
Route::group(['middleware' => 'h5Auth', 'prefix' => 'h5'], function () {
    Route::get('/', '\App\Http\Controllers\H5\H5Controller@index');
    Route::get('/changePwd', '\App\Http\Controllers\H5\H5Controller@changePwd');
    Route::get('/logout', '\App\Http\Controllers\H5\H5Controller@logout');
    Route::get('/rooms', '\App\Http\Controllers\H5\H5Controller@rooms');
    Route::get('/chat', '\App\Http\Controllers\H5\H5Controller@chat');
    Route::get('cfg', '\App\Http\Controllers\H5\H5Controller@cfg');
    Route::get('config/{roomId}', '\App\Http\Controllers\H5\H5Controller@config');
    Route::get('result/{roomId}/{bonusId}', '\App\Http\Controllers\H5\H5Controller@result');
    Route::get('history/{roomId}', '\App\Http\Controllers\H5\H5Controller@history');
    Route::get('hisDetail/{roomId}/{gameId}', '\App\Http\Controllers\H5\H5Controller@hisDetail');
});


// 管理员
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('login', function () { return view('admin/login'); });
    Route::post('login', 'AdminController@login');
});

Route::group(['middleware' => 'adminAuth', 'prefix' => 'admin', 'namespace' => 'Admin'], function () {
    // 页面
    Route::get('/', 'AdminController@user');
    Route::get('/user', 'AdminController@user');
    Route::get('/room', 'AdminController@room');
    Route::get('/game', 'AdminController@game');
    Route::get('/admin', 'AdminController@admin');
    Route::get('/agent', 'AdminController@agent');
    Route::get('/bet', 'AdminController@bet');
    Route::get('/logout', 'AdminController@logout');
    // 用户相关接口
    Route::get('/userList', 'AdminController@userList');
    Route::post('/changeUserPassword', 'AdminController@changeUserPassword');
    Route::post('/changeUserJifen', 'AdminController@changeUserJifen');
    Route::post('/changeUserBonus', 'AdminController@changeUserBonus');
    Route::post('/delUser', 'AdminController@delUser');
    Route::post('/addAdmin', 'AdminController@addAdmin');
    Route::delete('/delAdmin', 'AdminController@delAdmin');
    Route::any('/userJifenHistory', 'AdminController@userJifenHistory');
    // 房间相关接口
    Route::get('/roomList', 'AdminController@roomList');
    Route::post('/changeRoomRules', 'AdminController@changeRoomRules');
    Route::delete('/delRoom', 'AdminController@delRoom');
    // 历史游戏相关
    Route::get('/gameList', 'AdminController@gameList');
    // 管理员相关
    Route::get('/admins', 'AdminController@admins');
    Route::put('/changeAdminName', 'AdminController@changeAdminName');
    Route::put('/changeAdminPassword', 'AdminController@changeAdminPassword');
});






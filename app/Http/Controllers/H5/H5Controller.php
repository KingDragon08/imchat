<?php

namespace App\Http\Controllers\H5;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\CommonService;
use App\Services\NiuniuService;
use App\Services\EaseService;

use Validator;

class H5Controller extends Controller
{
    // h5的首页
    public function index(Request $request) {
        //获取广告
        $ads = CommonService::getAds();
        $games = CommonService::getGames();
        $userInfo = UserService::h5GetUserInfo();
        return view('h5/index', ['ads' => $ads, 'games' => $games, 'userInfo' => $userInfo]);
    }

    // h5的登录页面
    public function login(Request $request) {
        return view('h5/login');
    }

    // 更改密码
    public function changePwd(Request $request) {
        $userInfo = UserService::h5GetUserInfo();
        return view('h5/changePwd', ['userInfo' => $userInfo]);
    }

    // 登出
    public function logout(Request $request) {
        $id = $request->session()->pull('id');
        $request->session()->forget('token');
        UserService::logout($id);
        return view('h5/login');
    }

    // 聊天室列表
    public function rooms(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return 403;
        }
        $rooms = CommonService::getGameRooms($request->type);
        $userInfo = UserService::h5GetUserInfo();
        return view('h5/rooms', ['rooms' => $rooms, 'title' => $request->name, 'userInfo' => $userInfo]);
    }

    // 聊天
    public function chat(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string',
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return 403;
        }
        $userInfo = UserService::h5GetUserInfo();
        $roomInfo = EaseService::roomInfo($request->id);
        return view('h5/chat', ['userInfo' => $userInfo, 
                                'title' => $request->name, 
                                'conversationId' => $request->id, 
                                'roomInfo' => $roomInfo]);
    }

    // 配置项
    public function cfg(Request $request) {
        $validator = Validator::make($request->all(), [
            'key' => 'required|exists:cfg,key'
        ]);
        if ($validator->fails()) {
            $data = '404';
        } else {
            $data = CommonService::getConfig($request->key);    
        }
        return view('h5/cfg', ['data' => $data, 'title' => $request->key]);
    }

    // 配置游戏
    public function config(Request $request, $roomId) {
        $validator = Validator::make(['roomId' => $roomId], [
            'roomId' => 'required|exists:chatrooms,roomId'
        ]);
        $userInfo = UserService::h5GetUserInfo();
        if ($validator->fails()) {
            return '404';
        } else {
            return view('h5/config', ['roomId' => $roomId, 'userInfo' => $userInfo]);
        }
    }

    // 开红包
    public function result(Request $request, $roomId, $bonusId) {
        $validator = Validator::make(['roomId' => $roomId, 'bonusId' => $bonusId], [
            'roomId' => 'required|exists:chatrooms,roomId',
            'bonusId' => 'required|exists:bonus,id'
        ]);
        $userInfo = UserService::h5GetUserInfo();
        if ($validator->fails()) {
            return '404';
        } else {
            return view('h5/result', ['roomId' => $roomId, 'bonusId' => $bonusId, 'userInfo' => $userInfo]);
        }
    }

    // 历史列表
    public function history(Request $request, $roomId) {
        $validator = Validator::make(['roomId' => $roomId], [
            'roomId' => 'required|exists:chatrooms,roomId'
        ]);
        if ($validator->fails()) {
            return '404';
        } else {
            $data = NiuNiuService::getHistory($roomId);
            return view('h5/history', ['roomId' => $roomId, 'data' => $data]);
        }
    }

    // 历史详情
    public function hisDetail(Request $request, $roomId, $gameId) {
        $validator = Validator::make(['roomId' => $roomId, 'gameId' => $gameId], [
            'roomId' => 'required|exists:game_niuniu,roomId',
            'gameId' => 'required|exists:game_niuniu,id'
        ]);
        if ($validator->fails()) {
            return '404';
        } else {
            $data = NiuNiuService::historyDetail($roomId, $gameId);
            return view('h5/historyDetail', ['data' => $data]);
        }
    }

}

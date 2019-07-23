<?php

namespace App\Http\Controllers\H5;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\CommonService;

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
        return view('h5/chat', ['userInfo' => $userInfo, 'title' => $request->name, 'conversationId' => $request->id]);
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

}

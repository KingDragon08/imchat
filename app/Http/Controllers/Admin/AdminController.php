<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;
use App\Services\UserService;
use App\Services\AdminService;

class AdminController extends Controller
{
    /**
     * 登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'string|required|min:4',
            'password' => 'string|required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }
        try {
            $data = AdminService::login($request->username, $request->password);
            return response()->json(['status' => 0, 'msg' => '登录成功', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 登出
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function logout(Request $request) {
        try {
            $id = $request->session()->pull('id');
            $request->session()->forget('token');
            $request->session()->forget('userInfo');
            UserService::logout($id);
            return view('h5/login');
            return response()->json(['status' => 0, 'msg' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '退出失败']);
        }
    }

    /**
     * 用户列表页
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function user(Request $request) {
        $userInfo = AdminService::getUserInfo();
        return view('admin/user', ['userInfo' => $userInfo]);
    }

    /**
     * 房间列表页
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function room(Request $request) {
        $userInfo = AdminService::getUserInfo();
        return view('admin/room', ['userInfo' => $userInfo]);
    }

    /**
     * 游戏历史记录列表页
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function game(Request $request) {
        $userInfo = AdminService::getUserInfo();
        return view('admin/game', ['userInfo' => $userInfo]);
    }

    /**
     * 管理员列表页
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function admin(Request $request) {
        $userInfo = AdminService::getUserInfo();
        return view('admin/admin', ['userInfo' => $userInfo]);
    }

    /**
     * 代理列表页
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function agent(Request $request) {
        $userInfo = AdminService::getUserInfo();
        return view('admin/agent', ['userInfo' => $userInfo]);
    }

    /**
     * 下注记录页
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bet(Request $request) {
        $userInfo = AdminService::getUserInfo();
        return view('admin/bet', ['userInfo' => $userInfo]);
    }

    /**
     * 用户列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function userList(Request $request) {
        $userInfo = AdminService::getUserInfo();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        try {
            $data = UserService::getList($page, $size);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data['data'], 'total' => $data['total']]);    
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }



}
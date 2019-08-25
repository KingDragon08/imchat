<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;
use App\Services\UserService;
use App\Services\EaseService;

class UserController extends Controller
{
    /**
     * 登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'string|required|min:6',
            'password' => 'string|required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }
        try {
            $data = UserService::login($request->username, $request->password);
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
            UserService::logout($request->id);
            return response()->json(['status' => 0, 'msg' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => '退出失败']);
        }
    }

    /**
     * 注册
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'string|required|min:6',
            'password' => 'string|required|min:6',
            'shareCode' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            UserService::register($request->username, $request->password, $request->input('shareCode', ''));
            return response()->json(['status' => 0, 'msg' => '注册成功', 'data' => []]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 上传收款码
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changeShoukuanma(Request $request) {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            UserService::changeShoukuanma($request->id, $request->url);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }

    }

    /**
     * 转换红包为积分
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changeBonusToJifen(Request $request) {
        try {
            UserService::changeBonusToJifen($request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 上传充值凭证
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function uploadPingzheng(Request $request) {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            UserService::uploadPingzheng($request->id, $request->url);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 发红包--单发
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bonus(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|int|min:0.01',
            'ext' => 'required|string',
            'username' => 'required|string|exists:user,username',
            'type' => 'required|int'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        if ($request->type != 0 && $request->type != 2) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);   
        }

        try {
            $id = UserService::bonus($request->id, $request->username, $request->amount, $request->ext, $request->type);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $id]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 发红包--群发
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function groupBonus(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'number' => 'required|int|min:1',
            'ext' => 'required|string',
            'username' => 'required|string',
            'type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        if (!in_array($request->type, ['normal', 'shouqi'])) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);   
        }

        try {
            $id = UserService::groupBonus($request->id, $request->username, $request->type, floatval($request->amount), intval($request->number), $request->ext, 'group');
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $id]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 开红包--私发
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function openBonus(Request $request) {
        $validator = Validator::make($request->all(), [
            'bonusId' => 'required|int|exists:bonus,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = UserService::openBonus($request->bonusId, $request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }

    }

    // 接受转账
    public function openZhuanZhang(Request $request) {
        $validator = Validator::make($request->all(), [
            'zId' => 'required|int|exists:bonus,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = UserService::openZhuanZhang($request->zId, $request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    // 打开红包,组红包
    public function openGroupBonus(Request $request) {
        $validator = Validator::make($request->all(), [
            'bonusId' => 'required|int|exists:bonus,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = UserService::openGroupBonus($request->bonusId, $request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }

    }

    // 获取用户头像
    public function getAvatar(Request $request) {
	$tmp = explode('-', $request->username);
	$username = $tmp[count($tmp) - 1];
        $validator = Validator::make(['username' => $username], [
            'username' => 'required|string|exists:user,username'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = UserService::getAvatar($username);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {dd($e);
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    // 获取用户基础信息
    public function getUserInfo(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|exists:user,username'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            $data = UserService::getUserInfo($request->username);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    // 模糊搜索用户
    public function search(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|exists:user,username'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => []]);
        }

        try {
            $data = UserService::getAvatar($request->username);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    // 更新头像
    public function changeAvatar(Request $request) {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string|min:6',
            'type' => 'required|int|between:0,1'// 0->改头像,1->改朋友圈背景
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            UserService::changeAvatar($request->id, $request->path, $request->type);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }   

    }

    // 更新用户信息
    public function changeUserInfo(Request $request) {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|min:1',
            'value' => 'required|string|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        $keys = ['nickname', 'sign', 'phone', 'email'];
        if (!in_array($request->key, $keys)) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);   
        }

        try {
            UserService::changeUserInfo($request->id, $request->key, $request->value);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }         
    }

    // 更改密码
    public function changePwd (Request $request) {
        $validator = Validator::make($request->all(), [
            'pwd' => 'required|string|min:6',
            'npwd' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            UserService::changePwd($request->id, $request->pwd, $request->npwd);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        } 
    }

    // 开放给第三方的注册接口,自动完成环信注册
    public function register4B(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'string|required|min:6',
            'password' => 'string|required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            UserService::register($request->username, $request->password);
            EaseService::register($request->username);
            return response()->json(['status' => 0, 'msg' => '注册成功', 'data' => 'ok']);
        } catch (Exception $e) {
            // 失败时删除用户信息
            UserService::delete($request->username);
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    // 开放给第三方的用户列表接口
    public function userList4B(Request $request) {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        try {
            $data = UserService::getList($page, $size);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data]);    
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    // 开放给第三方的更改积分接口
    public function setJifen4B(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|exists:user,username',
            'jifen' => 'int|required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            UserService::changeUserInfoByName($request->username, 'jifen', $request->jifen);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    // 开放给第三方的更改红包接口
    public function setBonus4B(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|exists:user,username',
            'bonus' => 'int|required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'msg' => '参数校验失败']);
        }

        try {
            UserService::changeUserInfoByName($request->username, 'bonus', $request->bonus);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }




}


















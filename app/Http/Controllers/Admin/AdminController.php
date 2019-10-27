<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;
use App\Services\UserService;
use App\Services\AdminService;
use App\Services\CommonService;
use App\Services\EaseService;
use App\Services\NiuniuService;

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
            return view('admin/login');
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
     * 添加管理员
     * @param Request $request [description]
     */
    public function addAdmin(Request $request) {
        $id = $request->id;
        try {
            AdminService::addAdmin($id);    
            return response()->json(['status' => 0, 'msg' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
        
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
        $agent = $request->input('agent', null);
        $user = $request->input('user', null);
        try {
            $whereArr = [];
            if ($userInfo->agent != '*') {
                $whereArr[] = ['agent', '=', $userInfo->agent];
            }
            if ($agent) {
                $whereArr[] = ['agent', 'like', '%' . $agent . '%'];
            }
            if ($user) {
                $whereArr[] = ['username', 'like', '%' . $user . '%'];
            }
            $data = UserService::getList($page, $size, $whereArr, true);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data['data'], 'total' => $data['total'], 'static' => [$data['static']]]);    
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 后台更改用户密码
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changeUserPassword(Request $request) {
        try {
            UserService::changePwd($request->id, $request->password, $request->npassword, false);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 更改用户积分
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changeUserJifen(Request $request) {
        try {
            UserService::changeJifen($request->id, $request->jifen * 100);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 更改用户红包
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changeUserBonus(Request $request) {
        try {
            UserService::changeBonus($request->id, $request->bonus);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 删除用户
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delUser(Request $request) {
        try {
            UserService::delUser($request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    public function delAdmin(Request $request) {
        try {
            $this->checkAuth();
            AdminService::delAdmin($request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);   
        }
    }

    /**
     * 获取房间列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function roomList(Request $request) {
        try {
            $data = CommonService::getGameRooms('niuniu');
            for ($i=0; $i<count($data); $i++) {
                $data[$i]['admin'] = EaseService::getRoomAdmin($data[$i]['roomId']);
            }
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data, 'total' => count($data)]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }   
    }

    /**
     * 更改房间规则说明
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changeRoomRules(Request $request) {
        try {
            $this->checkAuth();
            AdminService::changeRoomRules($request->id, $request->rules);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 检查管理员权限
     * @return [type] [description]
     */
    public function checkAuth() {
        $userInfo = AdminService::getUserInfo();
        if ($userInfo->role != 'admin') {
            throw new Exception("没有权限");
        }
    }

    /**
     * 删除游戏房间
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delRoom(Request $request) {
        try {
            $this->checkAuth();
            AdminService::delRoom($request->id);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }   
    }

    /**
     * 历史游戏列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function gameList(Request $request) {
        try {
            $whereArr = [];
            if ($request->has('gameId')) {
                $whereArr[] = ['id', $request->gameId];
            }
            if ($request->has('roomId')) {
                $whereArr[] = ['roomId', $request->roomId];
            }
            $data = AdminService::gameList($whereArr);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data, 'total' => count($data)]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 管理员列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function admins(Request $request) {
        try {
            $data = AdminService::getAdmins();
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data, 'total' => count($data)]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 更改管理员用户名
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changeAdminName(Request $request) {
        try {
            AdminService::changeAdminName($request->id, $request->name);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 更改管理员密码
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function changeAdminPassword(Request $request) {
        try {
            AdminService::changeAdminPassword($request->id, $request->password);
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 获取用户积分变动历史
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function userJifenHistory(Request $request) {
        try {
            $data = UserService::jifenHistory($request->id, $request->input('page', 1), $request->input('size', 10));
            return response()->json(['status' => 0, 'msg' => 'ok', 'data' => $data['data'], 'total' => $data['total']]);
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }   
    }


}

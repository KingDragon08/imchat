<?php 
namespace App\Services;
use App\Models\UserModel;
use App\Models\GroupsModel;
use App\Models\BonusModel;
use App\Models\ChatRoomsModel;
use App\Models\AdminModel;
use App\Models\NiuniuModel;
use App\Services\UserService;
use Cache;
use Exception;
use DB;
use Redis;


class AdminService {

    const CACHE_EXPIRE_TIME = 7 * 24 * 60;

    /**
     * 注册账号
     * @param  string $username [description]
     * @param  string $password [description]
     * @return [type]           [description]
     */
    public static function register(string $username, string $password) {
        // 判断用户名是否已经存在
        $isExist = UserModel::select('id')->where('username', $username)->count();
        if ($isExist > 0) {
            throw new Exception("用户名已存在");
        }
        $userModel = new UserModel();
        $userModel->nickname = $username;
        $userModel->username = $username;
        $userModel->password = md5($password);
        $userModel->jifen = 0;
        $userModel->bonus = 0;
        $userModel->avatar = 'http://via.placeholder.com/200/f2f2f2/666666?text=' . $username;
        $userModel->save();
    }

    /**
     * 登录
     * @param  string $username [description]
     * @param  string $password [description]
     * @return [type]           [description]
     */
    public static function login(string $username, string $password) {
        $data = AdminModel::select(['id', 'username', 'created_time', 'role', 'agent'])
                    ->where('username', $username)->where('password', md5($password))
                    ->get()->toArray();
        if (empty($data)) {
            throw new Exception("账户名不存在或密码错误");
        }
        $data = $data[0];
        // 创建token
        $token = md5(microtime(true));
        Cache::put($data['id'], $token, self::CACHE_EXPIRE_TIME);
        $data['token'] = $token;
        $data['timestamp'] = time();
        session([
            'id' => $data['id'],
            'token' => $token,
            'userInfo' => json_encode($data)
        ]);
        return $data;
    }

    /**
     * 获取当前登录的用户信息
     * @return [type] [description]
     */
    public static function getUserInfo() {
        return json_decode(session('userInfo'));
    }

    /**
     * 退出登录
     * @param  int    $id 用户id
     */
    public static function logout(int $id) {
        Cache::forget($id);
    }

    /**
     * 更改游戏刚见规则说明
     * @param  [type] $id    [description]
     * @param  [type] $rules [description]
     * @return [type]        [description]
     */
    public static function changeRoomRules($id, $rules) {
        $room = ChatRoomsModel::find($id);
        $room->rules = $rules;
        $room->save();
    }

    /**
     * 删除游戏房间
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function delRoom($id) {
        $room = ChatRoomsModel::find($id);
        $room->delete();
    }

    /**
     * 获取游戏历史列表
     * @param  [type] $whereArr [description]
     * @return [type]           [description]
     */
    public static function gameList($whereArr) {
        $data = NiuniuModel::select('*')->where($whereArr)->orderBy('id', 'desc')->get()->toArray();
        for ($i=0; $i<count($data); $i++) {
            $data[$i]['timestamp'] = date('Y-m-d H:i:s', $data[$i]['timestamp']);
        }
        return $data;
    }

    /**
     * 获取管理员列表
     * @return [type] [description]
     */
    public static function getAdmins() {
        $userInfo = self::getUserInfo();
        if ($userInfo->role == 'admin') {
            $data = AdminModel::select('*')->orderBy('id', 'desc')->get()->toArray();    
        } else {
            $data = AdminModel::select('*')->where('id', $userInfo->id)->get()->toArray();
        }
        return $data;
    }

    /**
     * 更改管理员用户名
     * @param  [type] $id   [description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function changeAdminName($id, $name) {
        $admin = AdminModel::find($id);
        $admin->username = $name;
        $admin->save();
    }

    /**
     * 更改管理员密码
     * @param  [type] $id       [description]
     * @param  [type] $password [description]
     * @return [type]           [description]
     */
    public static function changeAdminPassword($id, $password) {
        $admin = AdminModel::find($id);
        $admin->password = md5($password);
        $admin->save();
    }

    /**
     * 添加管理员
     * @param [type] $id [description]
     */
    public static function addAdmin($id) {
        $userInfo = UserModel::find($id);
        $isExist = AdminModel::select('*')->where('username', $userInfo->username)->count();
        if ($isExist) {
            throw new Exception("已是代理");
        }
        $adminModel = new AdminModel();
        $adminModel->username = $userInfo->username;
        $adminModel->password = $userInfo->password;
        $adminModel->created_time = time();
        $adminModel->role = 'agent';
        $adminModel->agent = base64_encode($id);
        $adminModel->save();
    }

    /**
     * 删除管理员
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function delAdmin($id) {
        AdminModel::find($id)->delete();
    }

}


?>
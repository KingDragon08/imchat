<?php 
namespace App\Services;
use App\Models\UserModel;
use App\Models\GroupsModel;
use App\Models\BonusModel;
use App\Models\ChatRoomsModel;
use App\Models\AdminModel;
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
        $data = AdminModel::select(['id', 'username', 'created_time', 'role'])
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



}


?>
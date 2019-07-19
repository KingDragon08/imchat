<?php 
namespace App\Services;
use App\Models\UserModel;
use App\Models\GroupsModel;
use App\Models\BonusModel;
use App\Models\ChatRoomsModel;
use Cache;
use Exception;
use DB;
use Redis;


class UserService {

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
        $data = UserModel::select(['id', 'nickname', 'username', 'jifen', 
                                    'bonus', 'created_at', 'avatar', 'pyqImg', 'sign',
                                    'agent', 'phone', 'email'])
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
        return $data;
    }

    /**
     * 退出登录
     * @param  int    $id 用户id
     */
    public static function logout(int $id) {
        Cache::forget($id);
    }

    /**
     * 发送红包,单发
     * @param  int    $fromId     [description]
     * @param  string $toUserName [description]
     * @param  float $amount      [description]
     * @param  string $ext        [description]
     * @param  int    $type       类型,0->普通红包,2->转账
     * @return int                [红包id]
     */
    public static function bonus(int $fromId, string $toUserName, float $amount, string $ext, int $type) {
        DB::beginTransaction();
        try {
            $fromUser = UserModel::select(['id', 'username', 'bonus'])->where('id', $fromId)->first();
            if ($fromUser->bonus < $amount) {
                throw new Exception("红包余额不足");
            }
            $fromUser->bonus = $fromUser->bonus - $amount;
            $fromUser->save();
            $toUser = UserModel::select('id')->where('username', $toUserName)->first();
            $bonusModel = new BonusModel();
            $bonusModel->from = $fromId;
            $bonusModel->to = $toUser->id;
            $bonusModel->amount = $amount;
            $bonusModel->ext = $ext;
            $bonusModel->timestamp = time();
            $bonusModel->type = $type;
            $bonusModel->save();
            DB::commit();
            return $bonusModel->id;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }

    }

    /**
     * 群发红包
     * 使用Cache
     * @param  int    $fromId    [description]
     * @param  string $toGroupId [description]
     * @param  string $type      [description]
     * @param  float  $amount    [description]
     * @param  int    $number    [description]
     * @param  string $ext       [description]
     * @return int               [红包id]
     */
    public static function groupBonus(
        int $fromId, // 发送者id
        string $toGroupId, // 群聊conversationId
        string $type, // 红包类型,normal=>普通红包,shouqi=>拼手气红包
        float $amount, // 金额,normal时为单个金额,shouqi时为总金额
        int $number, // 个数
        string $ext, // 祝福语
        string $chatType = 'group' // 聊天类型
    ) {
       DB::beginTransaction();
       try {
            $fromUser = UserModel::select(['id', 'username', 'bonus'])->where('id', $fromId)->first();
            if ($fromUser->bonus < $amount) {
                throw new Exception("红包余额不足");
            }
            if ($type == 'normal') {
                $fromUser->bonus = $fromUser->bonus - $amount * $number;
            } else {
                $fromUser->bonus = $fromUser->bonus - $amount;
            }
            $fromUser->save();
            $toGroup = '';
            if ($chatType == 'group') {
                $toGroup = GroupsModel::select('id')->where('groupId', $toGroupId)->first();
            } else {
                $toGroup = ChatRoomsModel::select('id')->where('roomId', $toGroupId)->first();    
            }
            $bonusModel = new BonusModel();
            $bonusModel->from = $fromId;
            $bonusModel->to = $toGroup->id;
            $bonusModel->amount = $amount;
            $bonusModel->ext = $ext;
            $bonusModel->timestamp = time();
            $bonusModel->type = $type == 'normal' ? 0 : 1;
            $bonusModel->number = $number;
            $bonusModel->save();
            DB::commit();
            // 初始化缓存
            $data = [
                'id' => $bonusModel->id,
                'type' => $bonusModel->type,
                'amount' => $amount,
                'number' => $number,
                'timestamp' => time()
            ];
            // 拆分成小红包
            $splitedBonus = [];
            if ($type == 'normal') {
                for ($i=0; $i<$number; $i++) {
                    $splitedBonus[] = $number;
                }
            } else {
                while($number > 1) {
                    $avg = $amount / $number;
                    $tmp = mt_rand(intval($avg / 2), intval($avg * 2));
                    // 后面的小红包至少每个要剩一分钱
                    $tmp = min($tmp, $amount - $number + 1);
                    // 至少1分钱
                    $tmp = max($tmp, 1);
                    $splitedBonus[] = intval($tmp);
                    $number--;
                    $amount -= $tmp;
                }
                // 最后剩下的放进一个小红包
                $splitedBonus[] = intval($amount);
            }
            // 将红包存入redis中
            foreach ($splitedBonus as $item) {
                Redis::rpush('bonus-' . $bonusModel->id, $item);
            }
            // 已抢红包的用户id bonus-joiners-{{id}}
            // 红包基本信息缓存1天,监听过期事件退还红包
            Cache::put('bonus-info-' . $bonusModel->id, json_encode($data), 24 * 60);
            return $bonusModel->id;

        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        } 
    }

    /**
     * 开个人单包
     * @param  int    $bonusId 红包id
     * @param  int    $id      发起请求的用户id
     * @return array
     */
    public static function openBonus(int $bonusId, int $id) {
        $ret = [
            'opened' => 0,
            'openTime' => '未领取'
        ];
        DB::beginTransaction();
        try {
            $data = BonusModel::select('*')->where('id', $bonusId)->first();
            if ($data->number != 0) {
                throw new Exception("非法访问");
            }
            // 没被打开过且是红包接收方进行请求
            if ($data->status == 0 && $data->to == $id) {
                $ret['opened'] = 1;
                $data->opened = 1;
                $data->status = 1;
                $data->openTime = time();
                $data->save();
                $user = UserModel::select('*')->where('id', $data->to)->first();
                $user->bonus = $user->bonus + $data->amount;
                $user->save();
                $ret['openTime'] = $data->openTime;
            }
            $ret['opened'] = $data->opened;
            if ($ret['opened']) {
                $ret['openTime'] = $data->openTime;
            }
            DB::commit();
            return $ret;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }
        
    /**
     * 开个人转账包
     * @param  int    $zId     转账id
     * @param  int    $id      发起请求的用户id
     * @return array
     */
    public static function openZhuanZhang(int $zId, int $id) {
        $ret = [
            'opened' => 0,
            'openTime' => '未领取'
        ];
        try {
            $data = BonusModel::select('*')->where('id', $zId)->first();
            if ($data->number != 0 || $data->type != 2) {
                throw new Exception("非法访问");
            }
            // 没被打开过且是转账接收方进行请求
            if ($data->status == 0 && $data->to == $id) {
                $ret['opened'] = 1;
                $ret['openTime'] = time();
                $data->opened = 1;
                $data->status = 1;
                $data->openTime = time();
                $data->save();
                $user = UserModel::select('*')->where('id', $data->to)->first();
                $user->bonus = $user->bonus + $data->amount;
                $user->save();
                $ret['openTime'] = $data->openTime;
            }
            $ret['opened'] = $data->opened;
            if ($ret['opened']) {
                $ret['openTime'] = $data->openTime;
            }
            DB::commit();
            return $ret;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 抢红包
     * @param  int    $bonusId 红包ID
     * @param  int    $id      用户ID
     * @return array
     */
    public static function openGroupBonus(int $bonusId, int $id) {
        $ret = [
            'opened' => false,
            'joiner' => []
        ];

        $bonusKey = 'bonus-' . $bonusId;
        $bonusInfoKey = 'bonus-info-' . $bonusId;
        $bonusJoinersKey = 'bonus-joiners-' . $bonusId;

        if (Cache::has($bonusInfoKey)) {
            // 未过期且未抢完
            $joiners = Redis::lrange($bonusJoinersKey, 0, Redis::llen($bonusJoinersKey));
            foreach ($joiners as &$joiner) {
                $joiner = json_decode($joiner, true);
            }

            if (in_array($id, array_column($joiners, 'id'))) {
                // 领过了
                $ret['opened'] = true;
                $ret['joiner'] = $joiners;
                // 判断是否领完
                $data = json_decode(Cache::get($bonusInfoKey), true);
                if ($data['number'] == Redis::llen($bonusJoinersKey)) {
                    // 领完了回写数据库
                    $bonusModel = BonusModel::select('*')->where('id', $bonusId)->first();
                    $bonusModel->status = 1;
                    $bonusModel->joiner = json_encode($joiners);
                    $bonusModel->save();
                    Cache::forget($bonusInfoKey);
                    // 红包数据再缓存7天,方便对账
                    Redis::expire($bonusJoinersKey, 604800);
                }
            } else {
                // 抢红包
                $bonus = Redis::lpop($bonusKey);
                // 抢完了,写回数据库,清空缓存
                if (!$bonus) {
                    $bonusModel = BonusModel::select('*')->where('id', $bonusId)->first();
                    $bonusModel->status = 1;
                    $bonusModel->joiner = json_encode($joiners);
                    $bonusModel->save();
                    Cache::forget($bonusInfoKey);
                    // 红包数据再缓存7天,方便对账
                    Redis::expire($bonusJoinersKey, 604800);
                    $ret['opened'] = true;
                    $ret['joiners'] = $joiners;
                } else {
                    $userModel = UserModel::select('*')->where('id', $id)->first();
                    $tmp = [
                        'id' => $id,
                        'username' => $userModel->username,
                        'nickname' => $userModel->nickname,
                        'amount' => $bonus,
                        'timestamp' => time(),
                        'avatar' => $userModel->avatar
                    ];
                    $userModel->bonus += intval($bonus);
                    $userModel->save();
                    Redis::rpush($bonusJoinersKey, json_encode($tmp));
                    $joiners[] = $tmp;
                    $ret['opened'] = true;
                    $ret['joiners'] = $joiners;
                }
            }
        } else {
            // 已写回数据库
            $ret['opened'] = true;
            $bonus = BonusModel::select('joiner')->where('id', $bonusId)->get()->toArray()[0];
            $ret['joiner'] = json_decode($bonus['joiner'], true);
        }

        return $ret;
    }

    /**
     * 获取红包结果
     * @param  [type] $bonusId [description]
     * @return [type]          [description]
     */
    public static function getBonusResult($bonusId) {
        $ret = [
            'opened' => false,
            'joiner' => []
        ];

        $bonusKey = 'bonus-' . $bonusId;
        $bonusInfoKey = 'bonus-info-' . $bonusId;
        $bonusJoinersKey = 'bonus-joiners-' . $bonusId;
        if (Cache::has($bonusInfoKey)) {
            // 未过期且未抢完
            $joiners = Redis::lrange($bonusJoinersKey, 0, Redis::llen($bonusJoinersKey));
            foreach ($joiners as &$joiner) {
                $joiner = json_decode($joiner, true);
            }
            $ret['opened'] = true;
            $ret['joiners'] = $joiners;
            
            // 判断是否领完
            $data = json_decode(Cache::get($bonusInfoKey), true);
            if ($data['number'] == Redis::llen($bonusJoinersKey)) {
                // 领完了回写数据库
                $bonusModel = BonusModel::select('*')->where('id', $bonusId)->first();
                $bonusModel->status = 1;
                $bonusModel->joiner = json_encode($joiners);
                $bonusModel->save();
                Cache::forget($bonusInfoKey);
                // 红包数据再缓存7天,方便对账
                Redis::expire($bonusJoinersKey, 604800);
            }
            
        } else {
            // 已写回数据库
            $ret['opened'] = true;
            $bonus = BonusModel::select('joiner')->where('id', $bonusId)->get()->toArray()[0];
            $ret['joiner'] = json_decode($bonus['joiner'], true);
        }

        return $ret;
    }

    /**
     * 获取用户头像信息
     * @param  string $username [description]
     * @return [type]           [description]
     */
    public static function getAvatar(string $username) {
        $data = UserModel::select(['id', 'username', 'nickname', 'avatar', 'pyqImg', 'sign'])
                ->where('username', $username)->get()->toArray();
        return $data[0];
    }

    /**
     * 获取用户基础信息
     * @param  string $username [description]
     * @return [type]           [description]
     */
    public static function getUserInfo(string $username) {
        $data = UserModel::select(['id', 'nickname', 'username', 'jifen', 
                                    'bonus', 'created_at', 'avatar', 'pyqImg', 'sign',
                                    'agent', 'phone', 'email'])
                ->where('username', $username)->get()->toArray();
        return $data[0];
    }

    /**
     * 更新用户头像
     * @param  int    $id     [description]
     * @param  string $path   图片路径
     * @param  int    $type   0->改头像,1->改朋友圈背景
     * @return [type]         [description]
     */
    public static function changeAvatar(int $id, string $path, int $type) {
        $user = UserModel::select(['id', 'avatar', 'pyqImg'])->where('id', $id)->first();
        if ($type == 0) {
            $user->avatar = $path;
        }
        if ($type == 1) {
            $user->pyqImg = $path;
        }
        $user->save();
    }

    /**
     * 更改用户信息
     * @param  int    $id    [description]
     * @param  string $key   [description]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public static function changeUserInfo(int $id, string $key, string $value) {
        $userModel = UserModel::select('*')->where('id', $id)->first();
        $userModel->$key = $value;
        $userModel->save();
    }

    /**
     * 更改密码
     * @param  int    $id   [description]
     * @param  string $pwd  [description]
     * @param  string $npwd [description]
     * @return [type]       [description]
     */
    public static function changePwd(int $id, string $pwd, string $npwd) {
        $pwd = md5($pwd);
        $user = UserModel::find($id);
        if ($user->password != $pwd) {
            throw new Exception("原始密码错误");
        }
        $user->password = md5($npwd);
        $user->save();
    }



}


?>
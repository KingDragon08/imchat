<?php 
namespace App\Services;
use App\Models\UserModel;
use App\Models\ChatRoomsModel;
use App\Models\BonusModel;
use App\Models\NiuniuModel;

use App\Services\UserService;
use App\Services\EaseService;

use Storage;
use Exception;
use Cache;
use Redis;
use DB;

class NiuniuService {

    const GAME_NAME = 'niuniu:';

    const CACHE_TIME = 24 * 60 * 365;

    /**
     * 创建游戏
     * 一个群组只允许有一场进行中的游戏
     *
     * 当一个群为游戏群时需要先在redis中将 群主信息写入
     * 
     * Redis::get(groupOwner:{{roomId}}->owner name)
     * 
     * @param  [type] $roomId [description]
     * @param  [type] $banker  [description]
     * @param  [type] $jifen   单位：元
     * @return [type]          [description]
     */
    public static function create ($roomId, string $banker, int $jifen) {
        $groupInfo = ChatRoomsModel::select('*')->where('roomId', $roomId)->where('type', 'niuniu')->get()->toArray();
        // 不是牛牛群
        if (empty($groupInfo)) {
            throw new Exception("非法的请求");
        }
        // 还没有配置规则
        if (empty($groupInfo[0]['cfg'])) {
            throw new Exception("尚未配置规则,不能开始游戏");
        }
        // 一个群只能有一场进行中的游戏
        $gameExist = NiuniuModel::select('*')->where('roomId', $roomId)->where('status', 0)->count();
        if ($gameExist > 0) {
            throw new Exception("一个群只能有一场进行中的游戏");
        }

        // 删除上一场游戏的下注信息
        $joinersKey = self::GAME_NAME . $roomId;
        Redis::expire($joinersKey, 0);
        
        $config = json_decode($groupInfo[0]['cfg'], true);
        
        DB::beginTransaction();

        try {
            // 创建一条新记录
            $model = new NiuniuModel();
            $model->roomId = $roomId;
            $model->banker = $banker;
            $model->status = 0;
            $model->timestamp = time();
            $model->save();

            // 缓存的游戏信息
            $data = Cache::get(self::GAME_NAME . $roomId);
            $data = json_decode($data, true);

            $currentBankerModel = UserModel::select('*')->where('username', $banker)->first();
            $groupOwner = Redis::get('groupOwner:' . $roomId); // 取群主
            $groupOwnerModel = UserModel::select('*')->where('username', $groupOwner)->first();

            // 不是第一局
            if (!empty($data)) {
                // 换庄
                if ($data['banker'] != $banker) {
                    // 将上一场庄的积分退还
                    $lastBankerModel = UserModel::select('*')->where('username', $data['banker'])->first();
                    $lastBankerModel->bonus += $data['jifen'];
                    $lastBankerModel->save();
                    // 将本场庄的积分扣除        
                    $data['startJifen'] = $jifen * 100;
                    $currentBankerModel->bonus -= $jifen * 100;
                    if ($currentBankerModel->bonus < 0) {
                        throw new Exception("上庄积分不足");
                    }
                    $currentBankerModel->save();
                    $data['jifen'] = $jifen * 100;
                    // 上庄抽水
                    $choushui = intval($jifen * 100 * $config['shangzhuangchoushui']); // 分
                    $data['jifen'] -= $choushui;
                    $groupOwnerModel->bonus += $choushui; // 水钱归群主
                    $groupOwnerModel->save();
                    // 清空庄家结果
                    $data['bankerResult'] = [];
                }
            } else {
                // 第一局
                // 将本场庄的积分扣除
                $data['startJifen'] = $jifen * 100;
                $currentBankerModel->bonus -= $jifen * 100;
                if ($currentBankerModel->bonus < 0) {
                    throw new Exception("上庄积分不足");
                }
                $currentBankerModel->save();
                $data['jifen'] = $jifen * 100;
                // 上庄抽水
                $choushui = intval($jifen * 100 * $config['shangzhuangchoushui']); // 分
                $data['jifen'] -= $choushui;
                $groupOwnerModel->bonus += $choushui; // 水钱归群主
                $groupOwnerModel->save();
                // 初始化庄家结果
                $data['bankerResult'] = [];
            }
            
            // 设置缓存信息
            $cache = [
                'timestamp' => $model->timestamp, // 游戏时间
                'banker' => $banker, // 本局庄家
                'id' => $model->id, // 本局id
                'bonusId' => -1, // 本局红包id
                'startJifen' => $data['startJifen'], // 上庄积分
                'jifen' => $data['jifen'], // 剩余积分
                'bankerResult' => $data['bankerResult'], // 庄的结果
                'number' => 0, // 本局参与人数
                'amount' => 0, // 应发红包额度
            ];

            Cache::put(self::GAME_NAME . $roomId, json_encode($cache), self::CACHE_TIME);

            // 构造返回信息
            $strs = [];
            $strs[] = '本局庄家:' . $currentBankerModel->nickname;
            $strs[] = '庄家积分:' . intval($cache['jifen'] / 100);
            $strs[] = '最低下注:' . $config['minZhu'];
            // 最高下注
            if ($config['maxZhuType'] == 'banker') {
                $config['maxZhu'] = intval($cache['jifen'] * $config['maxZhu'] / 100);
            }
            $strs[] = '最高下注:' . $config['maxZhu'];
            // 梭哈
            if ($config['showHand']) {
                $strs[] = '梭哈最小:' . $config['minShowHand'];
                if ($config['maxShowHandType'] == 'banker') {
                    $config['maxShowHand'] = intval($cache['jifen'] * $config['maxShowHand'] / 100);
                }
                $strs[] = '梭哈最高:' . $config['maxShowHand'];
            }
            $strs[] = '------------------------------------';
            $strs[] = '⚠️下注时间禁止闲聊⚠️';
            $strs[] = '押注时长:' . $config['yazhushichang'];
            $strs[] = '最低标庄:' . $config['zuidibiaozhuang'];
            $strs[] = $config['fengding'] . '封顶';
            DB::commit();
            return ['id' => $model->id, 'msg' => implode('<br/>', $strs)];    
        } catch (Exception $e) {
            DB::rollback();
            dd($e);
            throw new Exception($e->getMessage());
        }

        
    }

    /**
     * 下注
     * @param  [type] $userId  [description]
     * @param  [type] $roomId [description]
     * @param  int    $bets    [description]
     * @param  string $type    下注类型：normal=>正常下注,showHand=>梭哈下注
     * @return [type]          [description]
     */
    public static function bet($userId, $roomId, int $bet, string $type) {
        $game = Cache::get(self::GAME_NAME . $roomId, -1);
        if ($game == -1) {
            throw new Exception("游戏不存在");
        }
        $game = json_decode($game, true);
        if ($game['bonusId'] != -1) {
            throw new Exception("已停止下注");
        }
        // 获取游戏配置
        $config = ChatRoomsModel::select('cfg')->where('roomId', $roomId)->first()->cfg;
        $config = json_decode($config, true);
        $userInfo = UserModel::select('*')->where('id', $userId)->first();
        $bankerInfo = UserModel::select('*')->where('username', $game['banker'])->first();
        if ($userInfo->id == $bankerInfo->id) {
            throw new Exception('庄家不得下注');
        }

        if ($type == 'normal') {
            if ($bet * 100 > $userInfo->bonus * $config['maxZhuRate']) {
                throw new Exception('下注不得超过余额的：' . ($config['maxZhuRate'] * 100) . '%');
            }
            if ($bet < $config['minZhu'] && $bet != 0) {
                throw new Exception('最小下注：' . $config['minZhu'] . '元');
            }
            if ($config['maxZhuType'] == 'banker') {
                $config['maxZhu'] = intval($game['jifen'] * $config['maxZhu'] / 100);
            }
            if ($bet > $config['maxZhu']) {
                throw new Exception('最大下注：' . $config['maxZhu'] . '元');
            }
        } else {
            // 梭哈下注
            if (!$config['showHand']) {
                throw new Exception('未开启梭哈下注');
            }
            if ($bet * 100 > $userInfo->bonus * $config['maxShowHandRate']) {
                throw new Exception('下注不得超过余额的：' . $config['maxShowHandRate'] . '%');
            }
            if ($bet < $config['minShowHand'] && $bet != 0) {
                throw new Exception('最小下注：' . $config['minShowHand'] . '元');
            }
            if ($config['maxShowHandType'] == 'banker') {
                $config['maxShowHand'] = intval($game['jifen'] * $config['maxShowHand'] / 100);
            }
            if ($bet > $config['maxShowHand']) {
                throw new Exception('最大下注：' . $config['maxShowHand'] . '元');
            }

        }
        $joinersKey = self::GAME_NAME . $roomId;
        $tmp = ['bet' => $bet, 'type' => $type, 'userId' => $userId];
        Redis::rpush($joinersKey, json_encode($tmp));
        $ret = '';
        if ($bet == 0) {
            $ret = '撤单成功';
        } else {
            $ret = '下注成功.';
            if ($type == 'showHand') {
                $ret .= '梭哈';
            }
            $ret .= '押【' . $bet . '】';
        }
        return $ret;
    }

    /**
     * 停止下注
     * @param  [type] $roomId [description]
     * @return [type]          [description]
     */
    public static function end ($roomId) {
        $gameInfo = Cache::get(self::GAME_NAME . $roomId, -1);
        if ($gameInfo == -1) {
            throw new Exception("游戏不存在");
        }

        // $cache = [
        //     'timestamp' => $model->timestamp, // 游戏时间
        //     'banker' => $banker, // 本局庄家
        //     'id' => $model->id, // 本局id
        //     'bonusId' => -1, // 本局红包id
        //     'startJifen' => $data['startJifen'], // 上庄积分
        //     'jifen' => $data['jifen'], // 剩余积分
        //     'bankerResult' => $data['bankerResult'], // 庄的结果
        //     'number' => 0, // 本局参与人数
        //     'amount' => 0, // 应发红包额度
        // ];

        $gameInfo = json_decode($gameInfo, true);

        $joinersKey = self::GAME_NAME . $roomId;
        $joiners = Redis::lrange($joinersKey, 0, Redis::llen($joinersKey));
        // ['bet' => $bet, 'type' => $type, 'userId' => $userId];
        // 构造真实的注单,二次下单覆盖前面的下单
        $tmp = [];
        for ($i=0; $i<count($joiners); $i++) {
            $joiners[$i] = json_decode($joiners[$i], true);
            // 0表示撤单
            if ($joiners[$i]['bet'] == 0) {
                unset($tmp[$joiners[$i]['userId']]);
            } else {
                $tmp[$joiners[$i]['userId']] = $joiners[$i];    
            }
        }
        $joiners = $tmp;
        $userIds = array_keys($joiners);
        $number = count($userIds) + 1; // 应发红包个数,+1是因为庄家也要抢包
        // 获取游戏配置
        $config = ChatRoomsModel::select('cfg')->where('roomId', $roomId)->first()->cfg;
        $config = json_decode($config, true);
        $amount = $number * intval($config['bonus'] * 100) + intval($config['bonusRandom'] * 100);
        // 无人下注,不能结束
        if ($number < 2) {
            throw new Exception("没人下注,不能结束");
        }
        $gameInfo['number'] = $number;
        $gameInfo['amount'] = $amount;
        $gameInfo['bonusId'] = 0;
        // 写回游戏信息
        Cache::put(self::GAME_NAME . $roomId, json_encode($gameInfo), self::CACHE_TIME);
        // 构造返回字符串
        $strs = [];
        $strs[] = '⭐⭐停止下注⭐⭐';
        $normalUserCounter = 0; // 普通下注人数统计
        $showHandUserCounter = 0; // 梭哈下注人数统计
        $normalBetCounter = 0; // 普通下注积分统计
        $showHandBetCounter = 0; // 梭哈下注积分统计

        // 获取用户信息
        $userData = UserModel::select(['id', 'nickname'])->whereIn('id', $userIds)->get()->toArray();
        $userData = array_column($userData, 'nickname', 'id');
        $bankerInfo = UserModel::select(['id', 'nickname', 'username'])->where('username', $gameInfo['banker'])->get()->toArray()[0];

        // 将下注单按照下注大小进行排序
        $joiners = array_values($joiners);
        array_multisort(array_column($joiners, 'bet'), SORT_DESC, $joiners);
        foreach ($joiners as $joiner) {
            $str = '';
            if ($joiner['type'] == 'normal') {
                $str = '押:';
                $normalUserCounter++;
                $normalBetCounter += $joiner['bet'];
            } else {
                $str = '梭哈:';
                $showHandUserCounter++;
                $showHandBetCounter += $joiner['bet'];
            }
            $strs[] = '🈶[' . $userData[$joiner['userId']] . ']' . $str . $joiner['bet'];
        }

        $strs[] = '------------------------------------';
        $strs[] = '🈶效下注:' . ($normalUserCounter + $showHandUserCounter);
        $strs[] = '------------------------------------';
        $strs[] = '庄家名字:' . $bankerInfo['nickname'];
        $strs[] = '上庄积分:' . intval($gameInfo['startJifen'] / 100);
        $strs[] = '玩家人数:' . ($normalBetCounter + $showHandUserCounter) . ' 总分:' . ($normalBetCounter + $showHandBetCounter);
        $strs[] = '梭哈总注:' . $showHandBetCounter . ' 总注:' . $normalBetCounter;
        $strs[] = '发包人数:' . $number . ' 应发:' . round($amount/100, 2);
        $strs[] = '------------------------------------';
        $strs[] = '⚠️下注以核对账单为准⚠️';
        $strs[] = '！！各位玩家，红包飞行时请勿闲聊，如违规者罚388！！';

        return implode('<br/>', $strs);
    }

    /**
     * 发送红包
     * @param  [type] $roomId [description]
     * @return [type]          [description]
     */
    public static function sendBonus($roomId) {
        $gameInfo = Cache::get(self::GAME_NAME . $roomId, -1);
        if ($gameInfo == -1) {
            throw new Exception("游戏不存在");
        }

        $gameInfo = json_decode($gameInfo, true);
        if ($gameInfo['bonusId'] != 0) {
            throw new Exception("未发送注单或本局未结束");   
        }

        $config = ChatRoomsModel::select('cfg')->where('roomId', $roomId)->first()->cfg;
        $config = json_decode($config, true);

        // 庄家信息
        $bankerInfo = UserModel::select(['id', 'nickname', 'username'])->where('username', $gameInfo['banker'])->first();

        $groupOwner = Redis::get('groupOwner:' . $roomId); // 取群主
        $groupOwnerModel = UserModel::select('*')->where('username', $groupOwner)->first();

        // 发红包
        $bonusId = 0;
        // 包费扣除方式->庄
        if ($config['bonusFee'] == 'banker') {
            // 给庄先加上红包钱再发红包
            $bankerInfo->bonus += $gameInfo['amount'];
            $bankerInfo->save();
            $bonusId = UserService::groupBonus($bankerInfo['id'], $roomId, 'shouqi', $gameInfo['amount'], $gameInfo['number'], '牛牛红包');
            // 扣掉庄的积分
            $gameInfo['jifen'] -= $gameInfo['amount'];
        }
        // 包费扣除方式->扣群主
        if ($config['bonusFee'] == 'group') {
            $bonusId = UserService::groupBonus($groupOwnerModel->id, $roomId, 'shouqi', $gameInfo['amount'], $gameInfo['number'], '牛牛红包');
        }
        // 包费扣除方式->自认包费
        if ($config['bonusFee'] == 'every') {
            /**
             * 非常重要
             */
            // 上线后需要设置一个专门用于扣钱的账号
            $bonusId = UserService::groupBonus(10, $roomId, 'shouqi', $gameInfo['amount'], $gameInfo['number'], '牛牛红包');
            /**
             * 非常重要
             */
        }

        $ret = [
            'bonusId' => $bonusId,
            'amount' => $gameInfo['amount'],
            'number' => $gameInfo['number'],
            'ext' => '牛牛红包'
        ];

        $gameInfo['bonusId'] = $bonusId;
        $game = NiuniuModel::select('*')->where('roomId', $roomId)->where('status', 0)->first();
        $game->bonusId = $bonusId;
        $game->save();
        Cache::put(self::GAME_NAME . $roomId, json_encode($gameInfo), self::CACHE_TIME);

        return $ret;
    }

    /**
     * 配置游戏规则
     * @param [type] $roomId [description]
     * @param string $config  [description]
     */
    public static function setConfig ($roomId, string $config) {
        // 游戏中不允许设置规则
        $gameExist = NiuniuModel::select('*')->where('roomId', $roomId)->where('status', 0)->count();
        if ($gameExist > 0) {
            throw new Exception("游戏中不允许设置规则");
        }
        $model = ChatRoomsModel::select('*')->where('roomId', $roomId)->first();
        $model->cfg = $config;
        $model->save();
    }

    /**
     * 获取游戏规则
     * @param  [type] $roomId [description]
     * @return [type]          [description]
     */
    public static function getConfig ($roomId) {
        $model = ChatRoomsModel::select('cfg')->where('roomId', $roomId)->first();
        return json_decode($model->cfg);
    }

    /**
     * 开牛牛红包
     * @param  [type] $roomId [用户组id]
     * @param  int    $bonusId [红包id]
     * @param  [type] $userId  [用户id]
     * @return [type]          [description]
     */
    public static function openBonus($roomId, int $bonusId, $userId) {
        $game = Cache::get(self::GAME_NAME . $roomId, -1);
        if ($game == -1) {
            throw new Exception("游戏不存在");
        }
        $game = json_decode($game, true);
        $joinersKey = self::GAME_NAME . $roomId;
        $joiners = Redis::lrange($joinersKey, 0, Redis::llen($joinersKey));
        $tmp = [];
        for ($i=0; $i<count($joiners); $i++) {
            $joiners[$i] = json_decode($joiners[$i], true);
            $tmp[$joiners[$i]['userId']] = $joiners[$i];
        }
        $joiners = $tmp;
        $userIds = array_keys($joiners);
        $bankerInfo = UserModel::select('*')->where('username', $game['banker'])->first();
        if (!in_array($userId, $userIds) && $userId != $bankerInfo->id) {
            // throw new Exception("未下注或该局已结束,不允许开包");
            return UserService::getBonusResult($bonusId);
        }
        // 庄家不能抢头包
        if ($userId == $bankerInfo->id) {
            $bonusJoinersKey = 'bonus-joiners-' . $bonusId;
            if (Redis::llen($bonusJoinersKey) == 0) {
                throw new Exception("庄家不能抢头包");
            }
        }
        return UserService::openGroupBonus($bonusId, $userId);
    }

    /**
     * 结算并发送账单
     * @param  [type] $roomId [description]
     * @return [type]          [description]
     */
    public static function result ($roomId) {
        $timestamp = time();
        $game = Cache::get(self::GAME_NAME . $roomId, -1);
        if ($game == -1) {
            throw new Exception("游戏不存在");
        }

        $game = json_decode($game, true);
        if ($game['bonusId'] < 1) {
            throw new Exception("请先停止下注并发送红包");
        }

        $bonusId = $game['bonusId'];
        // 获取游戏配置
        $config = ChatRoomsModel::select('cfg')->where('roomId', $roomId)->first()->cfg;
        $config = json_decode($config, true);
        
        // 庄信息
        $bankerInfo = UserModel::select('*')->where('username', $game['banker'])->first();
        // 获取下注信息
        $joinersKey = self::GAME_NAME . $roomId;
        $joiners = Redis::lrange($joinersKey, 0, Redis::llen($joinersKey));
        $tmp = [];
        for ($i=0; $i<count($joiners); $i++) {
            $joiners[$i] = json_decode($joiners[$i], true);
            $tmp[$joiners[$i]['userId']] = $joiners[$i];
        }
        $joiners = $tmp;
        $joinersIds = array_keys($joiners);
        $joinersIds[] = $bankerInfo->id; // 把庄也加进去

        // 所有用户的信息
        $userInfos = UserModel::select(['id', 'username', 'nickname', 'bonus', 'avatar'])->whereIn('id', $joinersIds)->get()->toArray();
        $tmp = [];
        foreach ($userInfos as $key => $value) {
            $tmp[$value['id']] = $value;
        }
        $userInfos = $tmp;

        // 获取红包结果
        $bonusJoinersKey = 'bonus-joiners-' . $bonusId;
        $bonus = Redis::lrange($bonusJoinersKey, 0, Redis::llen($bonusJoinersKey));
        for($i=0; $i<count($bonus); $i++) {
            $bonus[$i] = json_decode($bonus[$i], true);
        }
        $bonusJoinersIds = array_column($bonus, 'id');

        $noBonusIds = array_diff($joinersIds, $bonusJoinersIds); // 未抢包用户id集合
        // 红包未抢完
        if (!empty($noBonusIds)) {
            // 未超时,不能结算
            if ($timestamp - $game['timestamp'] < $config['overtime']) {
                throw new Exception("红包未抢完且未超时,不能结算");
            }
        }

        $bonusInfo = BonusModel::select('*')->where('id', $bonusId)->first();

        $activeBonus = []; // 有效的包
        $overtimeBonus = []; // 超时的包
        $noBonus = []; // 无包
        $result = []; // 用于判断结果的包

        foreach ($bonus as $item) {
            if ($item['timestamp'] - $bonusInfo->timestamp < $config['overtime']) {
                $activeBonus[] = $item;
                $result[] = $item;
            } else {
                $overtimeBonus[] = $item;
            }
        }
        // 一个有效包都没有
        if (empty($activeBonus)) {
            $activeBonus = $overtimeBonus;
        }
        // 无包处理
        foreach ($noBonusIds as $id) {
            $overtimeType = $config['userOvertime'];
            if ($id == $bankerInfo->id) {
                $overtimeType = $config['bankerOvertime'];
            }
            switch ($overtimeType) {
                case 0: // 认尾一
                    $tmp = $activeBonus[count($activeBonus) - 1];
                    $tmp['id'] = $id;
                    $tmp['username'] = $userInfos[$id]['username'];
                    $tmp['nickname'] = $userInfos[$id]['nickname'];
                    $tmp['timestamp'] = $timestamp;
                    $tmp['avatar'] = $userInfos[$id]['avatar'];
                    $tmp['overtime'] = 0;
                    $result[] = $tmp;
                    break;
                case 1: // 认尾二
                    $tmp = $activeBonus[count($activeBonus) - 2];
                    $tmp['id'] = $id;
                    $tmp['username'] = $userInfos[$id]['username'];
                    $tmp['nickname'] = $userInfos[$id]['nickname'];
                    $tmp['timestamp'] = $timestamp;
                    $tmp['avatar'] = $userInfos[$id]['avatar'];
                    $tmp['overtime'] = 1;
                    $result[] = $tmp;
                    break;
                case 2: // 认输
                    $result[] = [
                        'id' => $id,
                        'username' => $userInfos[$id]['username'],
                        'nickname' => $userInfos[$id]['nickname'],
                        'amount' => -1,
                        'timestamp' => $timestamp,
                        'avatar' => $userInfos[$id]['avatar'],
                        'overtime' => 2,
                    ];
                    break;
                case 3: // 大平小赔
                    // 无包时大平小赔需要先自动开包
                    $ret = UserService::openGroupBonus($bonusId, $id);
                    $tmp = $ret['joiner'];
                    $tmp = $tmp[array_search($id, array_column($tmp, 'id'))];
                    $tmp['overtime'] = 3;
                    $result[] = $tmp;
                    break;
                case 4: // 自动开包
                    $ret = UserService::openGroupBonus($bonusId, $id);
                    $tmp = $ret['joiner'];
                    $tmp = $tmp[array_search($id, array_column($tmp, 'id'))];
                    $tmp['overtime'] = 4;
                    $result[] = $tmp;
                    break;
            }

        }

        // 超时包处理
        $overtimeBonusIds = array_column($overtimeBonus, 'id');
        foreach ($overtimeBonusIds as $id) {
            $overtimeType = $config['userOvertime'];
            if ($id == $bankerInfo->id) {
                $overtimeType = $config['bankerOvertime'];
            }
            switch ($overtimeType) {
                case 0: // 认尾一
                    $tmp = $activeBonus[count($activeBonus) - 1];
                    $tmp['id'] = $id;
                    $tmp['username'] = $userInfos[$id]['username'];
                    $tmp['nickname'] = $userInfos[$id]['nickname'];
                    $tmp['timestamp'] = $timestamp;
                    $tmp['avatar'] = $userInfos[$id]['avatar'];
                    $tmp['overtime'] = 0;
                    $result[] = $tmp;
                    break;
                case 1: // 认尾二
                    $tmp = $activeBonus[count($activeBonus) - 2];
                    $tmp['id'] = $id;
                    $tmp['username'] = $userInfos[$id]['username'];
                    $tmp['nickname'] = $userInfos[$id]['nickname'];
                    $tmp['timestamp'] = $timestamp;
                    $tmp['avatar'] = $userInfos[$id]['avatar'];
                    $tmp['overtime'] = 1;
                    $result[] = $tmp;
                    break;
                case 2: // 认输
                    $tmp = $overtimeBonus[array_search($id, $overtimeBonusIds)];
                    $tmp['overtime'] = 2;
                    $result[] = $tmp;
                    break;
                case 3: // 大平小赔
                    $tmp = $overtimeBonus[array_search($id, $overtimeBonusIds)];
                    $tmp['overtime'] = 3;
                    $result[] = $tmp;
                    break;
                case 4: // 自动开包
                    $ret = UserService::openGroupBonus($bonusId, $id);
                    $tmp = $ret['joiner'];
                    $tmp = $tmp[array_search($id, array_column($tmp, 'id'))];
                    $tmp['overtime'] = 4;
                    $result[] = $tmp;
                    break;
            }
        }
        
        // 找到banker的红包结果
        $bankerBonus = [];
        foreach ($result as $item) {
            if ($item['id'] == $bankerInfo->id) {
                $bankerBonus = $item;
                break;
            }
        }

        // 构造以id为key的红包结果
        $tmp = [];
        foreach ($result as $item) {
            $tmp[$item['id']] = $item;
        }
        $result = $tmp;

        $strs = [];
        $strs[] = '☀️【第' . $game['id'] . '局明细计算】☀️';

        // 获取庄的结果
        $game['bankerResult'][] = self::getPai($result[$bankerBonus['id']], $config)['name'];
        // dump($game['bankerResult']);

        // 开始计算结果
        DB::beginTransaction();
        $bankerPai = self::getPai($bankerBonus, $config);
        $game['result'][] = $bankerPai['name'];
        try {
            $bankerCounter = 0; // 庄家输赢计算
            foreach ($joiners as $userId => $bet) {
                // 计算结果
                $ret = self::getResult($result[$bankerBonus['id']], $result[$userId], $bet, $config);
                $joiners[$userId]['fee'] = $ret['fee'];
                $joiners[$userId]['banker'] = $ret['banker'];
                $joiners[$userId]['user'] = $ret['user'];
                $joiners[$userId]['zIndex'] = $ret['zIndex'];
                $joiners[$userId]['name'] = $ret['name'];
                $joiners[$userId]['rate'] = $ret['rate'];
                $joiners[$userId]['time'] = $result[$userId]['timestamp'];
                $joiners[$userId]['choushui'] = 0; // 抽水
                $joiners[$userId]['bonus'] = 0; // 红包钱
                $bankerCounter += $joiners[$userId]['banker'];
            }
            $joiners = array_values($joiners);
            // 按牌大小递减排序、抢包时间递增排序
            array_multisort(array_column($joiners, 'zIndex'), SORT_DESC, array_column($joiners, 'time'), SORT_ASC, $joiners);
            
            // dump($result);
            // dump($joiners);

            $bankerJifen = $game['jifen'];
            // 服务费
            $serverFee = $config['serverFee'] * 100;
            // 按下注额度收服务费
            if ($config['serverFeeType'] == 'bets') {
                $serverFee = array_sum(array_column($joiners, 'bet')) * $config['serverFee'];
            }
            // 庄抽水
            $bankerChoushui = 0;
            if ($config['bankerChoushui'] == 'every') {
                $bankerChoushui = abs($bankerCounter) * $config['bankerChoushuiRate'];
            } else {
                if ($bankerCounter > 0) {
                    $bankerChoushui = abs($bankerCounter) * $config['bankerChoushuiRate'];
                }
            }
            $chi = 0;
            $pei = 0;
            $ping = 0;
            $he = 0;
            // 喝水,输光不赔
            if ($bankerJifen + $bankerCounter - $serverFee - $bankerChoushui < 0 && $config['hehsui'] == 0) {
                // 把服务费和抽水扣出来
                $bankerJifen -= $serverFee;
                $bankerJifen -= $bankerChoushui;
                // 先把赢的钱收回来
                for ($i=0; $i<count($joiners); $i++) {
                    $joiner = $joiners[$i];
                    if ($joiner['user'] > 0) {
                        continue;
                    }
                    $bankerJifen += $joiner['banker'];
                }
                // 开始赔
                for ($i=0; $i<count($joiners); $i++) {
                    $joiner = $joiners[$i];
                    // 足够赔
                    if ($bankerJifen > $joiner['user']) {
                        $pei += 1;
                        $tmp = $joiner['user'];
                        $bankerJifen -= $tmp;
                        // 抽水
                        $joiners[$i]['choushui'] = $tmp * $joiner['fee'];
                        // 红包钱
                        if ($config['bonusFee'] == 'every') {
                            $joiners[$i]['bonus'] = $config['bonus'] * 100;
                        }
                        // 减水钱
                        $tmp -= $joiners[$i]['choushui'];
                        // 减红包钱
                        $tmp -= $joiners[$i]['bonus'];

                        $userInfo = UserModel::select('*')->where('id', $joiner['userId'])->first();

                        $strs[] = '------------------------------------';
                        $str = '🉐【' . $userInfo->nickname . '】';
                        if ($joiners[$i]['choushui'] > 0) {
                            $str .= ' 抽水' . round($joiners[$i]['choushui'] / 100, 2);
                        }
                        if ($joiners[$i]['bonus'] > 0) {
                            $str .= ' 包费' . round($joiners[$i]['bonus'] / 100, 2);
                        }
                        $strs[] = $str;
                        $strs[] = '抢:' 
                                    . strval(number_format($result[$joiner['userId']]['amount'] / 100, 2)) 
                                    . '->' 
                                    . $joiner['name'] . ',' . $joiner['rate']
                                    . ' ' . ($joiner['type'] == 'normal' ? '押' : '梭哈') . $joiner['bet']
                                    . ' 赢' . round($tmp / 100, 2);
                        $strs[] = '上局:' . round($userInfo->bonus / 100, 2) . ' 本局:' . round(($userInfo->bonus + $tmp) / 100, 2);
                        $userInfo->bonus += $tmp;
                        $userInfo->save();
                     } else {
                        $pei += 1;
                        // 能赔的最后一个
                        $tmp = $bankerJifen;
                        // 抽水
                        $joiners[$i]['choushui'] = intval($tmp * $joiner['fee']);
                        // 红包钱
                        if ($config['bonusFee'] == 'every') {
                            $joiners[$i]['bonus'] = $config['bonus'] * 100;
                        }
                        // 减水钱
                        $tmp -= $joiners[$i]['choushui'];
                        // 减红包钱
                        $tmp -= $joiners[$i]['bonus'];

                        $userInfo = UserModel::select('*')->where('id', $joiner['userId'])->first();

                        $strs[] = '------------------------------------';
                        $str = '🉐【' . $userInfo->nickname . '】';
                        if ($joiners[$i]['choushui'] > 0) {
                            $str .= ' 抽水' . round($joiners[$i]['choushui'] / 100, 2);
                        }
                        if ($joiners[$i]['bonus'] > 0) {
                            $str .= ' 包费' . round($joiners[$i]['bonus'] / 100, 2);
                        }
                        $strs[] = $str;
                        $strs[] = '抢:' 
                                    . strval(number_format($result[$joiner['userId']]['amount'] / 100, 2)) 
                                    . '->' 
                                    . $joiner['name'] . ',' . $joiner['rate']
                                    . ' ' . ($joiner['type'] == 'normal' ? '押' : '梭哈') . $joiner['bet']
                                    . ' 赢' . round($tmp / 100, 2);
                        $strs[] = '上局:' . round($userInfo->bonus / 100, 2) . ' 本局:' . round(($userInfo->bonus + $tmp) / 100, 2);
                        $userInfo->bonus += $tmp;
                        $userInfo->save();
                        break;
                     }
                }

                // 没得赔了,喝水
                for (; $i<count($joiners); $i++) {
                    $joiner = $joiners[$i];
                    // 赢的喝水
                    if ($joiner['user'] > 0) {
                        $he += 1;
                        $joiners[$i]['choushui'] = 0;
                        // 红包钱
                        if ($config['bonusFee'] == 'every') {
                            $joiners[$i]['bonus'] = $config['bonus'] * 100;
                        }

                        $userInfo = UserModel::select('*')->where('id', $joiner['userId'])->first();
                        $strs[] = '------------------------------------';
                        $strs[] = '💦【' . $userInfo->nickname . '】';

                        $strs[] = '抢:' 
                                    . strval(number_format($result[$joiner['userId']]['amount'] / 100, 2)) 
                                    . '->' 
                                    . $joiner['name'] . ',' . $joiner['rate']
                                    . ' ' . ($joiner['type'] == 'normal' ? '押' : '梭哈') . $joiner['bet']
                                    . ' 喝水';
                        
                        $strs[] = '上局:' . round($userInfo->bonus / 100, 2) . ' 本局:' . round(($userInfo->bonus - $joiners[$i]['bonus']) / 100, 2);
                        $userInfo->bonus -= $joiners[$i]['bonus'];
                        $userInfo->save();
                    }
                    // 平的正常处理
                    if ($joiner['user'] == 0) {
                        $ping += 1;
                        $joiners[$i]['choushui'] = 0;
                        // 红包钱
                        if ($config['bonusFee'] == 'every') {
                            $joiners[$i]['bonus'] = $config['bonus'] * 100;
                        }

                        $userInfo = UserModel::select('*')->where('id', $joiner['userId'])->first();
                        $strs[] = '------------------------------------';
                        $strs[] = '🈴【' . $userInfo->nickname . '】';

                        $strs[] = '抢:' 
                                    . strval(number_format($result[$joiner['userId']]['amount'] / 100, 2)) 
                                    . '->' 
                                    . $joiner['name'] . ',' . $joiner['rate']
                                    . ' ' . ($joiner['type'] == 'normal' ? '押' : '梭哈') . $joiner['bet'];

                        $strs[] = '上局:' . round($userInfo->bonus / 100, 2) . ' 本局:' . round(($userInfo->bonus - $joiners[$i]['bonus']) / 100, 2);
                        $userInfo->bonus -= $joiners[$i]['bonus'];
                        $userInfo->save();
                    }
                    // 输的进行计算
                    if ($joiner['user'] < 0) {
                        $chi += 1;
                        $tmp = $joiner['user'];
                        // 抽水
                        if ($config['xianChoushui'] == 'every') {
                            $joiners[$i]['choushui'] = -$tmp * $joiner['fee'];
                        }
                        // 红包钱
                        if ($config['bonusFee'] == 'every') {
                            $joiners[$i]['bonus'] = $config['bonus'] * 100;
                        }

                        $userInfo = UserModel::select('*')->where('id', $joiner['userId'])->first();
                        $strs[] = '------------------------------------';

                        $str = '💀【' . $userInfo->nickname . '】';
                        if ($joiners[$i]['choushui'] > 0) {
                            $str .= ' 抽水' . round($joiners[$i]['choushui'] / 100, 2);
                        }
                        if ($joiners[$i]['bonus'] > 0) {
                            $str .= ' 包费' . round($joiners[$i]['bonus'] / 100, 2);
                        }
                        $strs[] = $str;
                        $strs[] = '抢:' 
                                    . strval(number_format($result[$joiner['userId']]['amount'] / 100, 2)) 
                                    . '->' 
                                    . $joiner['name'] . ',' . $joiner['rate']
                                    . ' ' . ($joiner['type'] == 'normal' ? '押' : '梭哈') . $joiner['bet']
                                    . ' 输' . round(abs($tmp / 100), 2);
                        $strs[] = '上局:' . round($userInfo->bonus / 100, 2) . ' 本局:' 
                                    . round(($userInfo->bonus + $tmp - $joiners[$i]['choushui'] - $joiners[$i]['bonus']) / 100, 2);
                        
                        $userInfo->bonus = $userInfo->bonus + $tmp - $joiners[$i]['choushui'] - $joiners[$i]['bonus'];
                        $userInfo->save();
                    }
                }
                $game['jifen'] = 0;
            } else {
                // 允许输成负的
                $game['jifen'] = $bankerJifen + $bankerCounter - $serverFee - $bankerChoushui;
                for ($i=0; $i<count($joiners); $i++) {
                    $joiner = $joiners[$i];
                    $userInfo = UserModel::select('*')->where('id', $joiner['userId'])->first();
                    // 赢
                    if ($joiner['user'] > 0) {
                        $pei += 1;
                        $tmp = $joiner['user'];
                        $bankerJifen -= $tmp;
                        // 抽水
                        $joiners[$i]['choushui'] = intval($tmp * $joiner['fee']);
                        // 红包钱
                        if ($config['bonusFee'] == 'every') {
                            $joiners[$i]['bonus'] = $config['bonus'] * 100;
                        }
                        // 减水钱
                        $tmp -= $joiners[$i]['choushui'];
                        // 减红包钱
                        $tmp -= $joiners[$i]['bonus'];

                        $userInfo = UserModel::select('*')->where('id', $joiner['userId'])->first();

                        $strs[] = '------------------------------------';
                        $str = '🉐【' . $userInfo->nickname . '】';
                        if ($joiners[$i]['choushui'] > 0) {
                            $str .= ' 抽水' . round($joiners[$i]['choushui'] / 100, 2);
                        }
                        if ($joiners[$i]['bonus'] > 0) {
                            $str .= ' 包费' . round($joiners[$i]['bonus'] / 100, 2);
                        }
                        $strs[] = $str;
                        $strs[] = '抢:' 
                                    . strval(number_format($result[$joiner['userId']]['amount'] / 100, 2)) 
                                    . '->' 
                                    . $joiner['name'] . ',' . $joiner['rate']
                                    . ' ' . ($joiner['type'] == 'normal' ? '押' : '梭哈') . $joiner['bet']
                                    . ' 赢' . round($tmp / 100, 2);
                        $strs[] = '上局:' . round($userInfo->bonus / 100, 2) . ' 本局:' . round(($userInfo->bonus + $tmp) / 100, 2);
                        $userInfo->bonus += $tmp;
                        $userInfo->save();
                    }
                    // 平
                    if ($joiner['user'] == 0) {
                        $ping += 1;
                        $joiners[$i]['choushui'] = 0;
                        // 红包钱
                        if ($config['bonusFee'] == 'every') {
                            $joiners[$i]['bonus'] = $config['bonus'] * 100;
                        }

                        $userInfo = UserModel::select('*')->where('id', $joiner['userId'])->first();
                        $strs[] = '------------------------------------';
                        $strs[] = '🈴【' . $userInfo->nickname . '】';

                        $strs[] = '抢:' 
                                    . strval(number_format($result[$joiner['userId']]['amount'] / 100, 2)) 
                                    . '->' 
                                    . $joiner['name'] . ',' . $joiner['rate']
                                    . ' ' . ($joiner['type'] == 'normal' ? '押' : '梭哈') . $joiner['bet'];

                        $strs[] = '上局:' . round($userInfo->bonus / 100, 2) . ' 本局:' . round(($userInfo->bonus - $joiners[$i]['bonus']) / 100, 2);
                        $userInfo->bonus -= $joiners[$i]['bonus'];
                        $userInfo->save();
                    }
                    // 输
                    if ($joiner['user'] < 0) {
                        $chi += 1;
                        $tmp = $joiner['user'];
                        // 抽水
                        if ($config['xianChoushui'] == 'every') {
                            $joiners[$i]['choushui'] = -$tmp * $joiner['fee'];
                        }
                        // 红包钱
                        if ($config['bonusFee'] == 'every') {
                            $joiners[$i]['bonus'] = $config['bonus'] * 100;
                        }

                        $userInfo = UserModel::select('*')->where('id', $joiner['userId'])->first();
                        $strs[] = '------------------------------------';
                        $str = '💀【' . $userInfo->nickname . '】';
                        if ($joiners[$i]['choushui'] > 0) {
                            $str .= ' 抽水' . round($joiners[$i]['choushui'] / 100, 2);
                        }
                        if ($joiners[$i]['bonus'] > 0) {
                            $str .= ' 包费' . round($joiners[$i]['bonus'] / 100, 2);
                        }
                        $strs[] = $str;
                        $strs[] = '抢:' 
                                    . strval(number_format($result[$joiner['userId']]['amount'] / 100, 2)) 
                                    . '->' 
                                    . $joiner['name'] . ',' . $joiner['rate']
                                    . ' ' . ($joiner['type'] == 'normal' ? '押' : '梭哈') . $joiner['bet']
                                    . ' 输' . round(abs($tmp / 100), 2);
                        $strs[] = '上局:' . round($userInfo->bonus / 100, 2) . ' 本局:' 
                                    . round(($userInfo->bonus + $tmp - $joiners[$i]['choushui'] - $joiners[$i]['bonus']) / 100, 2);
                        
                        $userInfo->bonus = $userInfo->bonus + $tmp - $joiners[$i]['choushui'] - $joiners[$i]['bonus'];
                        $userInfo->save();
                    }
                }
            }
            $strs[] = '------------------------------------';
            $strs[] = '当前模式:' . ($config['showHand'] ? '梭哈+' : '') 
                        . ($config['heshui'] ? '不喝水+' : '喝水+') 
                        . ($config['gameType'] ? '元角分' : '角分');
            $strs[] = '头包时间:' . date('Y-m-d H:i:s', $activeBonus[0]['timestamp']);
            $strs[] = '尾包时间:' . date('Y-m-d H:i:s', $activeBonus[count($activeBonus) - 1]['timestamp']);
            $strs[] = '超时时间:' . date('Y-m-d H:i:s', $activeBonus[0]['timestamp'] + $config['overtime']);
            $strs[] = '----------------财务统计----------------';
            $strs[] = '本局庄家:' . $bankerInfo->nickname;
            $strs[] = '庄家抢包:' . strval(number_format($result[$bankerInfo->id]['amount'] / 100, 2)) 
                        . ',' . $bankerPai['banker'] . '倍[' . $bankerPai['name'] . ']';  
            $strs[] = '抢包时间:' . date('Y-m-d H:i:s', $bankerBonus['timestamp']);
            $strs[] = '庄输平赢:吃' . $chi . ' 赔' . $pei . ' 平' . $ping . ' 喝' . $he;
            $strs[] = '本局红包:' . round($game['amount'] / 100, 2);
            $strs[] = '本局服务费:' . round($serverFee / 100, 2);
            $strs[] = '上庄积分:' . round($game['startJifen'] / 100, 2);
            $strs[] = '本局盈亏:' . round($bankerCounter / 100, 2);
            $strs[] = '庄总积分:' . round($game['jifen'] / 100, 2);
            $strs[] = '庄剩积分:' . round(($game['jifen'] + $bankerInfo->bonus) / 100, 2);
            $strs[] = '庄家走势:' . implode('->', $game['bankerResult']);
            
            // 写游戏信息
            $game['bonusId'] = -1;
            Cache::put(self::GAME_NAME . $roomId, json_encode($game), self::CACHE_TIME);
            // 写群主信息
            $groupOwner = Redis::get('groupOwner:' . $roomId); // 取群主
            $groupOwnerModel = UserModel::select('*')->where('username', $groupOwner)->first();
            $groupOwnerModel->bonus += intval($serverFee + $bankerChoushui + array_sum(array_column($joiners, 'choushui')));
            $groupOwnerModel->save();
            // 游戏信息写回数据库
            $niuniuModel = NiuniuModel::select('*')->where('id', $game['id'])->first();
            $niuniuModel->status = 1;
            $niuniuModel->result = implode('<br/>', $strs);
            $niuniuModel->save();
            DB::commit();
            return implode('<br/>', $strs);
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }

    }

    /**
     * 获取牌型
     * @param  [type] $bet    [description]
     * @param  [type] $config [description]
     * @return [type]         [description]
     */
    public static function getPai($bonus, $config) {
        $amount = strval(number_format($bonus['amount'] / 100, 2));
        $special = array_column($config['special'], 'pai');
        if (in_array($amount, $special)) {
            return $config['special'][array_search($amount, $special)];
        }
        $amount = intval($bonus['amount']);
        // 角分玩法
        if ($config['gameType'] == 0) {
            $pai = intval(((int)($amount%10) + (int)($amount%100/10)) % 10);
        }
        // 元角分玩法
        if ($config['gameType'] == 1) {
            $pai = intval(((int)($amount%10) + (int)($amount%100/10) + (int)($amount%1000/100)) % 10);
        }
        if ($pai == 0) {
            $pai = 10;
        }
        foreach ($config['niuniu'] as $niuniu) {
            if (intval($pai) == intval($niuniu['pai'])) {
                return $niuniu;
            }
        }
        return [
            'name' => '无包',
            'pai' => -1,
            'zIndex' => -2,
            'fee' => 0,
            'banker' => 0,
            'user' => 0,
            'showHand' => 0
        ];
    }

    /**
     * 判胜负
     * @param  [type] $bankerBonus [description]
     * @param  [type] $userBonus   [description]
     * @param  [type] $bet         [description]
     * @param  [type] $config      [description]
     * @return [type]              [description]
     */
    public static function getResult($bankerBonus, $userBonus, $bet, $config) {
        
        // $bankerPai = [
        //     'name' => '',
        //     'pai' => '',
        //     'zIndex' => -1,
        //     'fee' => -1,
        //     'banker' => -1,
        //     'user' => -1,
        //     'showHand' => -1
        // ];
        $bankerPai = self::getPai($bankerBonus, $config);
        $userPai = self::getPai($userBonus, $config);
        $ret = [
            'fee' => $userPai['fee'],
            'banker' => 0,
            'user' => 0,
            'rate' => 0, // 赔率
            'zIndex' => $userPai['zIndex'], // 用于排序
            'name' => $userPai['name']
        ];

        // 庄超时
        if (isset($bankerBonus['overtime']) && !isset($userBonus['overtime'])) {
            // 庄认输
            if ($config['bankerOvertime'] == 2) {
                $tmp = $bet['bet'];
                if ($bet['type'] == 'showHand') {
                    $ret['rate'] = $userPai['showHand'];
                    $tmp *= $userPai['showHand'];
                } else {
                    $ret['rate'] = $userPai['user'];
                    $tmp *= $userPai['user'];
                }
                $tmp = intval($tmp * 100);
                $ret['banker'] = -$tmp;
                $ret['user'] = $tmp;
                return $ret;
            }
            // 大平小赔
            if ($config['bankerOvertime'] == 3) {
                // 大平
                if ($bankerPai['zIndex'] >= $userPai['zIndex']) {
                    $ret['banker'] = 0;
                    $ret['user'] = 0;
                }
                // 小赔
                if ($bankerPai['zIndex'] < $userPai['zIndex']) {
                    $tmp = $bet['bet'];
                    if ($bet['type'] == 'showHand') {
                        $ret['rate'] = $userPai['showHand'];
                        $tmp *= $userPai['showHand'];
                    } else {
                        $ret['rate'] = $userPai['user'];
                        $tmp *= $userPai['user'];
                    }
                    $tmp = intval($tmp * 100);
                    $ret['banker'] = -$tmp;
                    $ret['user'] = $tmp;
                }
                // 同点
                /*
                if ($bankerPai['zIndex'] == $userPai['zIndex']) {
                    // 打和
                    if ($config['tongdian'] == 'he') {
                        $ret['banker'] = 0;
                        $ret['user'] = 0;
                    }
                    // 庄赢
                    if ($config['tongdian'] == 'banker') {
                        $tmp = $bet['bet'];
                        if ($bet['type'] == 'showHand') {
                            $ret['rate'] = $bankerPai['showHand'];
                            $tmp *= $bankerPai['showHand'];
                        } else {
                            $ret['rate'] = $bankerPai['banker'];
                            $tmp *= $bankerPai['banker'];
                        }
                        $tmp = intval($tmp * 100);
                        $ret['banker'] = $tmp;
                        $ret['user'] = -$tmp;
                    }
                    // 闲赢
                    if ($config['tongdian'] == 'xian') {
                        $tmp = $bet['bet'];
                        if ($bet['type'] == 'showHand') {
                            $ret['rate'] = $userPai['showHand'];
                            $tmp *= $userPai['showHand'];
                        } else {
                            $ret['rate'] = $userPai['user'];
                            $tmp *= $userPai['user'];
                        }
                        $tmp = intval($tmp * 100);
                        $ret['banker'] = -$tmp;
                        $ret['user'] = $tmp;
                    }
                    // 比金额
                    if ($config['tongdian'] == 'bonus') {
                        // 大于等于的时候庄赢
                        if ($bankerBonus['amount'] >= $userBonus['amount']) {
                            $tmp = $bet['bet'];
                            if ($bet['type'] == 'showHand') {
                                $ret['rate'] = $bankerPai['showHand'];
                                $tmp *= $bankerPai['showHand'];
                            } else {
                                $ret['rate'] = $bankerPai['banker'];
                                $tmp *= $bankerPai['banker'];
                            }
                            $tmp = intval($tmp * 100);
                            $ret['banker'] = $tmp;
                            $ret['user'] = -$tmp;
                        } else {
                            // 小于的时候闲赢
                            $tmp = $bet['bet'];
                            if ($bet['type'] == 'showHand') {
                                $ret['rate'] = $userPai['showHand'];
                                $tmp *= $userPai['showHand'];
                            } else {
                                $ret['rate'] = $userPai['user'];
                                $tmp *= $userPai['user'];
                            }
                            $tmp = intval($tmp * 100);
                            $ret['banker'] = -$tmp;
                            $ret['user'] = $tmp;
                        }

                    }
                }
                */
                return $ret;
            }
        }

        // 闲超时
        if (!isset($bankerBonus['overtime']) && isset($userBonus['overtime'])) {
            // 闲认输
            if ($config['userOvertime'] == 2) {
                $tmp = $bet['bet'];
                if ($bet['type'] == 'showHand') {
                    $ret['rate'] = $bankerPai['showHand'];
                    $tmp *= $bankerPai['showHand'];
                } else {
                    $ret['rate'] = $bankerPai['user'];
                    $tmp *= $bankerPai['user'];
                }
                $tmp = intval($tmp * 100);
                $ret['banker'] = $tmp;
                $ret['user'] = -$tmp;
                return $ret;
            }
            // 大平小赔
            if ($config['userOvertime'] == 3) {
                // 大平
                if ($bankerPai['zIndex'] <= $userPai['zIndex']) {
                    $ret['banker'] = 0;
                    $ret['user'] = 0;
                }
                // 小赔
                if ($bankerPai['zIndex'] > $userPai['zIndex']) {
                    $tmp = $bet['bet'];
                    if ($bet['type'] == 'showHand') {
                        $ret['rate'] = $bankerPai['showHand'];
                        $tmp *= $bankerPai['showHand'];
                    } else {
                        $ret['rate'] = $bankerPai['user'];
                        $tmp *= $bankerPai['user'];
                    }
                    $tmp = intval($tmp * 100);
                    $ret['banker'] = $tmp;
                    $ret['user'] = -$tmp;
                }
                // 同点
                /*
                if ($bankerPai['zIndex'] == $userPai['zIndex']) {
                    // 打和
                    if ($config['tongdian'] == 'he') {
                        $ret['banker'] = 0;
                        $ret['user'] = 0;
                    }
                    // 庄赢
                    if ($config['tongdian'] == 'banker') {
                        $tmp = $bet['bet'];
                        if ($bet['type'] == 'showHand') {
                            $ret['rate'] = $bankerPai['showHand'];
                            $tmp *= $bankerPai['showHand'];
                        } else {
                            $ret['rate'] = $bankerPai['banker'];
                            $tmp *= $bankerPai['banker'];
                        }
                        $tmp = intval($tmp * 100);
                        $ret['banker'] = $tmp;
                        $ret['user'] = -$tmp;
                    }
                    // 闲赢
                    if ($config['tongdian'] == 'xian') {
                        $tmp = $bet['bet'];
                        if ($bet['type'] == 'showHand') {
                            $ret['rate'] = $userPai['showHand'];
                            $tmp *= $userPai['showHand'];
                        } else {
                            $ret['rate'] = $userPai['user'];
                            $tmp *= $userPai['user'];
                        }
                        $tmp = intval($tmp * 100);
                        $ret['banker'] = -$tmp;
                        $ret['user'] = $tmp;
                    }
                    // 比金额
                    if ($config['tongdian'] == 'bonus') {
                        // 大于等于的时候庄赢
                        if ($bankerBonus['amount'] >= $userBonus['amount']) {
                            $tmp = $bet['bet'];
                            if ($bet['type'] == 'showHand') {
                                $ret['rate'] = $bankerPai['showHand'];
                                $tmp *= $bankerPai['showHand'];
                            } else {
                                $ret['rate'] = $bankerPai['banker'];
                                $tmp *= $bankerPai['banker'];
                            }
                            $tmp = intval($tmp * 100);
                            $ret['banker'] = $tmp;
                            $ret['user'] = -$tmp;
                        } else {
                            // 小于的时候闲赢
                            $tmp = $bet['bet'];
                            if ($bet['type'] == 'showHand') {
                                $ret['rate'] = $userPai['showHand'];
                                $tmp *= $userPai['showHand'];
                            } else {
                                $ret['rate'] = $userPai['user'];
                                $tmp *= $userPai['user'];
                            }
                            $tmp = intval($tmp * 100);
                            $ret['banker'] = -$tmp;
                            $ret['user'] = $tmp;
                        }

                    }
                }
                */
                return $ret;
            }
            
        }
        // 同时超时
        if (isset($bankerBonus['overtime']) && isset($userBonus['overtime'])) {
            // 打和
            if ($config['bothOvertime'] == 0) {
                $ret['banker'] = 0;
                $ret['user'] = 0;
                return $ret;
            }
            // 庄赢
            if ($config['bothOvertime'] == 1) {
                // 庄无包
                if ($bankerBonus['amount'] == -1) {
                    $ret['banker'] = 0;
                    $ret['user'] = 0;
                    return $ret;
                }
                $tmp = $bet['bet'];
                if ($bet['type'] == 'showHand') {
                    $ret['rate'] = $bankerPai['showHand'];
                    $tmp *= $bankerPai['showHand'];
                } else {
                    $ret['rate'] = $bankerPai['banker'];
                    $tmp *= $bankerPai['banker'];
                }
                $tmp = intval($tmp * 100);
                $ret['banker'] = $tmp;
                $ret['user'] = -$tmp;
                return $ret;
            }
            // 闲赢
            if ($config['bothOvertime'] == 2) {
                // 闲无包
                if ($userBonus['amount'] == -1) {
                    $ret['banker'] = 0;
                    $ret['user'] = 0;
                    return $ret;
                }
                $tmp = $bet['bet'];
                if ($bet['type'] == 'showHand') {
                    $ret['rate'] = $userPai['showHand'];
                    $tmp *= $userPai['showHand'];
                } else {
                    $ret['rate'] = $userPai['user'];
                    $tmp *= $userPai['user'];
                }
                $tmp = intval($tmp * 100);
                $ret['banker'] = -$tmp;
                $ret['user'] = $tmp;
            }
            return $ret;
        }

        // 正常比较
        // 闲几点以下自杀
        if ($userPai['zIndex'] <= $config['kill']) {
            $tmp = $bet['bet'];
            if ($bet['type'] == 'showHand') {
                $ret['rate'] = $bankerPai['showHand'];
                $tmp *= $bankerPai['showHand'];
            } else {
                $ret['rate'] = $bankerPai['banker'];
                $tmp *= $bankerPai['banker'];
            }
            $tmp = intval($tmp * 100);
            $ret['banker'] = $tmp;
            $ret['user'] = -$tmp;
            return $ret;
        }

        // 闲赢
        if ($userPai['zIndex'] > $bankerPai['zIndex']) {
            $tmp = $bet['bet'];
            if ($bet['type'] == 'showHand') {
                $ret['rate'] = $userPai['showHand'];
                $tmp *= $userPai['showHand'];
            } else {
                $ret['rate'] = $userPai['user'];
                $tmp *= $userPai['user'];
            }
            $tmp = intval($tmp * 100);
            $ret['banker'] = -$tmp;
            $ret['user'] = $tmp;
            return $ret;
        }
        // 庄赢
        if ($userPai['zIndex'] < $bankerPai['zIndex']) {
            $tmp = $bet['bet'];
            if ($bet['type'] == 'showHand') {
                $ret['rate'] = $bankerPai['showHand'];
                $tmp *= $bankerPai['showHand'];
            } else {
                $ret['rate'] = $bankerPai['banker'];
                $tmp *= $bankerPai['banker'];
            }
            $tmp = intval($tmp * 100);
            $ret['banker'] = $tmp;
            $ret['user'] = -$tmp;
            return $ret;
        }
        // 同点
        if ($userPai['zIndex'] == $bankerPai['zIndex']) {
            // 同点几点以下庄赢
            if ($userPai['zIndex'] <= $config['tongdianBankerWin']) {
                $tmp = $bet['bet'];
                if ($bet['type'] == 'showHand') {
                    $ret['rate'] = $bankerPai['showHand'];
                    $tmp *= $bankerPai['showHand'];
                } else {
                    $ret['rate'] = $bankerPai['banker'];
                    $tmp *= $bankerPai['banker'];
                }
                $tmp = intval($tmp * 100);
                $ret['banker'] = $tmp;
                $ret['user'] = -$tmp;
                return $ret;
            }
            // 打和
            if ($config['tongdian'] == 'he') {
                $ret['banker'] = 0;
                $ret['user'] = 0;
                return $ret;
            }
            // 庄赢
            if ($config['tongdian'] == 'banker') {
                $tmp = $bet['bet'];
                if ($bet['type'] == 'showHand') {
                    $ret['rate'] = $bankerPai['showHand'];
                    $tmp *= $bankerPai['showHand'];
                } else {
                    $ret['rate'] = $bankerPai['banker'];
                    $tmp *= $bankerPai['banker'];
                }
                $tmp = intval($tmp * 100);
                $ret['banker'] = $tmp;
                $ret['user'] = -$tmp;
                return $ret;
            }
            // 闲赢
            if ($config['tongdian'] == 'xian') {
                $tmp = $bet['bet'];
                if ($bet['type'] == 'showHand') {
                    $ret['rate'] = $userPai['showHand'];
                    $tmp *= $userPai['showHand'];
                } else {
                    $ret['rate'] = $userPai['user'];
                    $tmp *= $userPai['user'];
                }
                $tmp = intval($tmp * 100);
                $ret['banker'] = -$tmp;
                $ret['user'] = $tmp;
                return $ret;
            }
            // 比金额
            if ($config['tongdian'] == 'bonus') {
                // 大于等于的时候庄赢
                if ($bankerBonus['amount'] >= $userBonus['amount']) {
                    $tmp = $bet['bet'];
                    if ($bet['type'] == 'showHand') {
                        $ret['rate'] = $bankerPai['showHand'];
                        $tmp *= $bankerPai['showHand'];
                    } else {
                        $ret['rate'] = $bankerPai['banker'];
                        $tmp *= $bankerPai['banker'];
                    }
                    $tmp = intval($tmp * 100);
                    $ret['banker'] = $tmp;
                    $ret['user'] = -$tmp;
                } else {
                    // 小于的时候闲赢
                    $tmp = $bet['bet'];
                    if ($bet['type'] == 'showHand') {
                        $ret['rate'] = $userPai['showHand'];
                        $tmp *= $userPai['showHand'];
                    } else {
                        $ret['rate'] = $userPai['user'];
                        $tmp *= $userPai['user'];
                    }
                    $tmp = intval($tmp * 100);
                    $ret['banker'] = -$tmp;
                    $ret['user'] = $tmp;
                }
                return $ret;
            }
        }

    }

    /**
     * 获取富豪榜
     * @return [type] [description]
     */
    public static function bang($roomId) {
        // 获取群组成员
        $data = UserModel::select('nickname', 'bonus')->where([['bonus', '>', 0]])->orderBy('bonus', 'desc')->limit(1000)->get()->toArray();
        $strs = [];
        $strs[] = '====🎩土豪排行榜🎩====';
        $strs[] = '人数:👤' . count($data);
        $strs[] = '总积分:💰' . round(array_sum(array_column($data, 'bonus')) / 100, 2);
        $strs[] = '------------------------------------';
        for ($i=0; $i<count($data); $i++) {
            $strs[] = ($i + 1) . '[' . $data[$i]['nickname'] . ']积分:' . round($data[$i]['bonus'] / 100, 2);
        }
        return implode('<br/>', $strs);
    }

    /**
     * 获取所有的牛群列表
     * @return [type] [description]
     */
    public static function list() {
        $groups = ChatRoomsModel::select('roomId', 'avatar')->where('type', 'niuniu')->get()->toArray();
        $data = EaseService::getGroupsInfo(array_column($groups, 'roomId'));
        $data = array_column($data, 'name', 'id');
        for ($i=0; $i<count($groups); $i++) {
            $groups[$i]['nickname'] = $data[$groups[$i]['roomId']];
            $groups[$i]['username'] = $groups[$i]['roomId'];
        }
        return $groups;

    }

    /**
     * 重推游戏
     * @param  [type] $roomId [description]
     * @return [type]          [description]
     */
    public static function reset($roomId) {
        $game = Cache::get(self::GAME_NAME . $roomId, -1);
        if ($game == -1) {
            throw new Exception("游戏不存在");
        }
        $game = json_decode($game, true);
        // 还没有发送红包,此时可以重推
        if ($game['bonusId'] < 1) {
            $joinersKey = self::GAME_NAME . $roomId;
            Redis::expire($joinersKey, 0);
            // 结束本局游戏
            $niuniuModel = NiuniuModel::select('*')->where('roomId', $roomId)->where('status', 0)->first();
            $niuniuModel->status = -1; // -1表示重推结束
            $niuniuModel->save();
            return 'ok';
        }      
        // 红包发送后不可以重推
        throw new Exception("红包已发送,不可以重推");
    }



}

?>
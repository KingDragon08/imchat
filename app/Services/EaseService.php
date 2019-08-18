<?php 
namespace App\Services;

use GuzzleHttp\Client as HttpClient;
use App\Models\UserModel;
use App\Models\GroupsModel;
use App\Models\ChatRoomsModel;
use App\Models\AddFriendsModel;

use Cache;
use Exception;
use DB;

class EaseService {

    const EASE_TOKEN_KEY = 'ease_token_key';

    /**
     * 从环信获取好友列表
     * @param  string   $username
     * @return array
     */
    public static function friends(string $username) {
        $token = self::getEaseToken();
        $ret = [];
        // 个人
        $url = env('EASE_HOST') . 'users/' . $username . '/contacts/users';
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        $response = $client->request('GET', $url, $headers);
        $friends = json_decode($response->getBody(), true)['data'];
        $avatars = UserModel::select(['id', 'username', 'avatar', 'nickname'])->whereIn('username', $friends)
                    ->get()->toArray();
        $ret['chat'] = $avatars;
        // 群组
        $url = env('EASE_HOST') . 'users/' . $username . '/joined_chatgroups';
        $response = $client->request('GET', $url, $headers);
        $groups = json_decode($response->getBody(), true)['data'];
        $groupsInfoUrl = env('EASE_HOST') . 'chatgroups/' . implode(',', array_column($groups, 'groupid'));
        $res = $client->request('GET', $groupsInfoUrl, $headers);
        $groupsInfo = json_decode($res->getBody(), true)['data'];
        $groupsMysqlInfo = GroupsModel::select(['groupId', 'avatar', 'owner', 'type'])->whereIn('groupId', array_column($groups, 'groupid'))
                    ->get()->toArray();
        $groupsMysqlAvatarInfo = array_column($groupsMysqlInfo, 'avatar', 'groupId');
        $groupsMysqlTypeInfo = array_column($groupsMysqlInfo, 'type', 'groupId');
        $avatars = [];
        foreach ($groupsInfo as $group) {
            $tmp = [
                'groupId' => $group['id'],
                'avatar' => [],
                'customAvatar' => $groupsMysqlAvatarInfo[$group['id']] ?? '',
                'owner' => '',
                'type' => $groupsMysqlTypeInfo[$group['id']] ?? '',
                'username' => $group['id'],
                'nickname' => $group['name'],
                'maxusers' => $group['maxusers'],
                'created' => $group['created'],
                'users' => []
            ];
            if (strpos($tmp['customAvatar'], 'http://via.placeholder.com') !== false) {
                $tmp['customAvatar'] = '';
            }
            $ta = UserModel::select(['avatar', 'username'])->whereIn('username', array_values($group['affiliations']))
                    ->get()->toArray();
            $tmp['avatar'] = array_column($ta, 'avatar', 'username');
            foreach ($group['affiliations'] as $member) {
                if (isset($member['owner'])) {
                    $tmp['owner'] = $member['owner'];
                    $tmp['users'][] = $member['owner'];
                } else {
                    $tmp['users'][] = $member['member'];
                }
            }
            $avatars[] = $tmp;
        }
        
        // foreach ($avatars as &$avatar) {
        //     foreach ($groups as $group) {
        //         if ($group['groupid'] == $avatar['groupId']) {
        //             $avatar['username'] = $avatar['groupId'];
        //             $avatar['nickname'] = $group['groupname'];
        //             break;
        //         }
        //     }
        // }
        $ret['groupChat'] = $avatars;
        return $ret;
    }

    /**
     * 获取指定id的群组信息
     * @param  [type] $groupIds [description]
     * @return [type]           [description]
     */
    public static function getGroupsInfo (array $groupIds) {
        $token = self::getEaseToken();
        $url = env('EASE_HOST') . 'chatgroups/' . implode(',', $groupIds);
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        $response = $client->request('GET', $url, $headers);
        $groups = json_decode($response->getBody(), true)['data'];
        return $groups;
    }

    /**
     * 获取环信接口需要的token
     * @return string
     */
    public static function getEaseToken() {
        $token = Cache::get(self::EASE_TOKEN_KEY);
        if (empty($token)) {
            $client = new HttpClient();
            $url = env('EASE_HOST') . 'token';
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => env('EASE_CLIENT_ID'),
                    'client_secret' => env('EASE_CLIENT_SECRET')
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            $token = $data['access_token'];
            Cache::put(self::EASE_TOKEN_KEY, $token, intval($data['expires_in']) / 100);
        }
        return $token;
    }

    /**
     * 请求添加好友
     * @param int    $id      请求方id
     * @param int    $userId  对方id
     * @param string $message 留言信息
     */
    public static function add(int $id, int $userId, $message='') {
        $count = AddFriendsModel::select('*')->where('from', $id)->where('to', $userId)
                ->where('status', '<' ,2)->count();
        // 存在加好友请求或已同意的加好友请求
        if ($count > 0) {
            return;
        }
        $addFriendsModel = new AddFriendsModel();
        $addFriendsModel->from = $id;
        $addFriendsModel->to = $userId;
        $addFriendsModel->message = $message;
        $addFriendsModel->status = 0;
        $addFriendsModel->timestamp = time();
        $addFriendsModel->save();
    }

    /**
     * 同意添加好友
     * @param int $id     添加好友申请的id
     * @param int $from   请求方id
     * @param int $to     对方id
     */
    public static function agree(int $id, int $from, int $to) {
        $data = AddFriendsModel::select('*')->where('id', $id)->where('from', $from)
                ->where('to', $to)->where('status', 0);
        if ($data->count() < 1) {
            throw new Exception("非法请求");
        }
        $data = $data->first();
        $data->status = 1;
        $data->save();
        // 环信建立好友关系
        $token = self::getEaseToken();
        $owner = UserModel::select('username')->where('id', $from)->first();
        $target = UserModel::select('username')->where('id', $to)->first();
        $url = env('EASE_HOST') . 'users/' . $owner->username . '/contacts/users/' . $target->username;
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        $response = $client->request('POST', $url, $headers);
        if ($response->getStatusCode() != 200) {
            throw new Exception("添加好友失败");
        }
    }

    /**
     * 拒绝添加好友
     * @param int $id     添加好友申请的id
     * @param int $from   请求方id
     * @param int $to     对方id
     */
    public static function decline(int $id, int $from, int $to) {
        $data = AddFriendsModel::select('*')->where('id', $id)->where('from', $from)
                ->where('to', $to)->where('status', 0);
        if ($data->count() < 1) {
            throw new Exception("非法请求");
        }
        $data = $data->first();
        $data->status = 2;
        $data->save();
    }

    /**
     * 获取加好友历史
     * @param  int    $id 用户ID
     * @return array
     */
    public static function history(int $id, int $page, int $size) {
        $select = ['addfriends.*', 'user.username', 'user.nickname', 'user.avatar', DB::raw('user.id as userId')];
        $data = AddFriendsModel::select($select)->where('addfriends.to', $id)
                ->join('user', function ($join) {
                    $join->on('addfriends.from', 'user.id');
                })->orderBy('addfriends.id', 'desc')
                ->offset(($page - 1) * $size)->limit($size)
                ->get()->toArray();
        return $data;
    }

    /**
     * 从环信获取黑名单
     * @param  int    $id [description]
     * @return [type]     [description]
     */
    public static function getBlackList(int $id) {
        $token = self::getEaseToken();
        $user = UserModel::select('username')->where('id', $id)->first();
        $url = env('EASE_HOST') . 'users/' . $user->username . '/blocks/users';
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        $response = $client->request('GET', $url, $headers);
        $data = json_decode($response->getBody(), true);
        $users = $data['data'];
        $ret = UserModel::select(['id', 'username', 'nickname', 'avatar'])
                ->whereIn('username', $users)->get()->toArray();
        return $ret;
    }

    /**
     * 添加黑名单
     * @param int $from [description]
     * @param int $to   [description]
     */
    public static function addBlackList(int $from, int $to) {
        $token = self::getEaseToken();
        $from = UserModel::select('username')->where('id', $from)->first();
        $to = UserModel::select('username')->where('id', $to)->first();
        $url = env('EASE_HOST') . 'users/' . $from->username . '/blocks/users';
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'usernames' => [$to->username]
            ]
        ];
        $response = $client->request('POST', $url, $headers);
    }

    /**
     * 移出黑名单
     * @param int $from [description]
     * @param int $to   [description]
     */
    public static function deleteBlackList(int $from, int $to) {
        $token = self::getEaseToken();
        $from = UserModel::select('username')->where('id', $from)->first();
        $to = UserModel::select('username')->where('id', $to)->first();
        $url = env('EASE_HOST') . 'users/' . $from->username . '/blocks/users/' . $to->username;
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        $response = $client->request('DELETE', $url, $headers);
    }

    /**
     * 创建群组
     * @param  int    $id      owner
     * @param  [type] $groupId 环信返回的群组id
     * @return [type]          [description]
     */
    public static function createGroup(int $id, $groupname, $desc, $owner, $members) {
        $members = json_decode($members, true);
        $token = self::getEaseToken();
        $url = env('EASE_HOST') . 'chatgroups';
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'groupname' => $groupname,
                'desc' => $desc,
                'public' => true,
                'maxusers' => 200,
                'members_only' => false,
                'allowinvites' => true,
                'owner' => $owner,
                'members' => $members
            ]
        ];
        $response = $client->request('POST', $url, $headers);
        $data = json_decode($response->getBody(), true)['data'];
        $groupId = $data['groupid'];
        $groupsModel = new GroupsModel();
        $groupsModel->groupId = $groupId;
        $groupsModel->owner = $id;
        $groupsModel->avatar = 'http://via.placeholder.com/200/ffff00/333333?text=group';
        $groupsModel->save();
        return $groupId;
    }

    /**
     * 获取群组信息
     * @param  [type] $groupId [description]
     * @return [type]          [description]
     */
    public static function groupInfo($groupId) {
        $token = self::getEaseToken();
        $url = env('EASE_HOST') . 'chatgroups/' . $groupId;
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        $response = $client->request('GET', $url, $headers);
        $data = json_decode($response->getBody(), true)['data'][0];
        $members = [];
        foreach ($data['affiliations'] as $item) {
            if (isset($item['member'])) {
                $members[] = $item['member'];
            }
        }
        $ret = [
            'id' => $data['id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'maxusers' => $data['maxusers'],
            'owner' => $data['owner'],
            'members' => $members,
            'timestamp' => $data['created'],
            'avatar' => '',
        ];
        $owner = UserModel::select(['id', 'username', 'nickname', 'avatar'])->where('username', $ret['owner'])->get()->toArray()[0];
        $members = UserModel::select(['id', 'username', 'nickname', 'avatar'])->whereIn('username', $ret['members'])->get()->toArray();
        $ret['owner'] = $owner;
        $ret['members'] = $members;
        $tmp = GroupsModel::select(['avatar', 'type', 'cfg'])->where('groupId', $groupId)->get()->toArray()[0];
        $ret['avatar'] = $tmp['avatar'];
        $ret['type'] = $tmp['type'];
        $ret['cfg'] = $tmp['cfg'];
        return $ret;
    }

    /**
     * 更改群组信息
     * @param  [type] $gourpId [description]
     * @param  string $name    [description]
     * @param  string $desc    [description]
     * @return [type]          [description]
     */
    public static function changeGroupInfo ($groupId, string $name, string $desc) {
        $token = self::getEaseToken();
        $url = env('EASE_HOST') . 'chatgroups/' . $groupId;
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'groupname' => $name,
                'description' => $desc
            ]
        ];
        $response = $client->request('PUT', $url, $headers);
    }

    /**
     * 更改群组头像
     * @param  [type] $groupId [description]
     * @param  string $url     [description]
     * @return [type]          [description]
     */
    public static function changeGroupAvatar($groupId, string $url) {
        $model = GroupsModel::select('*')->where('groupId', $groupId)->first();
        $model->avatar = $url;
        $model->save();
    }

    /**
     * 邀请群成员入群
     * @param [type] $groupId [description]
     * @param string $members [description]
     */
    public static function addMembers($groupId, string $members) {
        $token = self::getEaseToken();
        $url = env('EASE_HOST') . 'chatgroups/' . $groupId . '/users';
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'usernames' => json_decode($members, true)
            ]
        ];
        $response = $client->request('POST', $url, $headers);
    }

    /**
     * 获取聊天室详情
     * @param  [type] $rooms [description]
     * @return [type]        [description]
     */
    public static function getRoomsInfo($roomIds) {
        $token = self::getEaseToken();
        if (is_array($roomIds)) {
            $roomIds = implode(',', $roomIds);
        }
        $url = env('EASE_HOST') . 'chatrooms/' . $roomIds;
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        $response = $client->request('GET', $url, $headers);
        $data = json_decode($response->getBody(), true)['data'];
        return $data;
    }

    /**
     * 加入聊天室
     * @param  [type] $roomId   [description]
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
    public static function joinChatRoom($roomId, $username) {
        $token = self::getEaseToken();
        $url = env('EASE_HOST') . 'chatrooms/' . $roomId . '/users/' . $username;
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        $response = $client->request('POST', $url, $headers);
    }

    /**
     * 获取聊天室管理员
     * @param  [type] $roomId [description]
     * @return [type]         [description]
     */
    public static function getRoomAdmin($roomId) {
        $token = self::getEaseToken();
        $url = env('EASE_HOST') . 'chatrooms/' . $roomId . '/admin';
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        $response = $client->request('GET', $url, $headers);
        $data = json_decode($response->getBody(), true)['data'];
        return $data;
    }

    /**
     * 获取聊天室成员信息
     * @param  [type] $roomId [description]
     * @return [type]         [description]
     */
    public static function roomInfo($roomId) {
        $data = self::getRoomsInfo($roomId);
        if (empty($data)) {
            throw new Exception("error room");
        }
        $data = $data[0];
        $members = array_values($data['affiliations']);
        $admin = self::getRoomAdmin($roomId);
        $ret = [
            'id' => $data['id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'maxusers' => $data['maxusers'],
            'owner' => $data['owner'],
            'members' => $members,
            'admin' => $admin,
            'timestamp' => $data['created'],
            'avatar' => '',
        ];
        $owner = UserModel::select(['id', 'username', 'nickname', 'avatar'])->where('username', $ret['owner'])->get()->toArray()[0];
        $members = UserModel::select(['id', 'username', 'nickname', 'avatar'])->whereIn('username', $ret['members'])->get()->toArray();
        $admin = UserModel::select(['id', 'username', 'nickname', 'avatar'])->whereIn('username', $ret['admin'])->get()->toArray();
        $ret['owner'] = $owner;
        $ret['members'] = $members;
        $ret['admin'] = $admin;
        $tmp = ChatRoomsModel::select(['avatar', 'type', 'cfg'])->where('roomId', $roomId)->get()->toArray()[0];
        $ret['avatar'] = $tmp['avatar'];
        $ret['type'] = $tmp['type'];
        $ret['cfg'] = $tmp['cfg'];
        return $ret;
    }

    /**
     * 注册新用户
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
    public static function register($username) {
        $token = self::getEaseToken();
        $url = env('EASE_HOST') . 'users';
        $client = new HttpClient();
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'username' => env('APP_NAME') . $username,
                'password' => '123456',
                'nickname' => $username
            ]
        ];
        $response = $client->request('POST', $url, $headers);
    }



}

?>